<?php
// websocket/server.php → CHẠY NGON 100% TRÊN XAMPP WINDOWS + CHAT REALTIME
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Factory;
use React\Socket\Server as Reactor;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Chat implements MessageComponentInterface {
    protected $clients = [];  // Store all active connections
    protected $users = [];    // Map user_id to connection
    protected $conversations = []; // Track active conversations
    protected $db;

    public function __construct() {
        try {
            $this->db = getDB();
        } catch (Exception $e) {
            echo "Database connection failed: " . $e->getMessage() . "\n";
        }
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients[$conn->resourceId] = $conn;
        echo "[" . date('Y-m-d H:i:s') . "] New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        try {
            $data = json_decode($msg, true);
            
            if (!isset($data['type'])) {
                $from->send(json_encode(['error' => 'Invalid message format']));
                return;
            }

            switch ($data['type']) {
                case 'authenticate':
                    $this->handleAuthenticate($from, $data);
                    break;
                case 'message':
                    $this->handleMessage($from, $data);
                    break;
                case 'typing':
                    $this->handleTyping($from, $data);
                    break;
                default:
                    $from->send(json_encode(['error' => 'Unknown message type']));
            }
        } catch (Exception $e) {
            echo "Error handling message: " . $e->getMessage() . "\n";
            $from->send(json_encode(['error' => 'Server error']));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Find and remove user
        $user_id = array_search($conn, $this->users);
        if ($user_id !== false) {
            unset($this->users[$user_id]);
            // Notify others that user went offline
            $this->broadcast([
                'type' => 'user_offline',
                'user_id' => $user_id,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            echo "[" . date('Y-m-d H:i:s') . "] User {$user_id} disconnected\n";
        }
        
        unset($this->clients[$conn->resourceId]);
        echo "[" . date('Y-m-d H:i:s') . "] Connection closed: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "[" . date('Y-m-d H:i:s') . "] Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function handleAuthenticate(ConnectionInterface $conn, $data) {
        if (!isset($data['user_id']) || !isset($data['username'])) {
            $conn->send(json_encode(['error' => 'Missing credentials']));
            return;
        }

        $user_id = (int)$data['user_id'];
        $this->users[$user_id] = $conn;
        
        $conn->send(json_encode([
            'type' => 'authenticated',
            'user_id' => $user_id,
            'username' => $data['username'],
            'timestamp' => date('Y-m-d H:i:s')
        ]));

        // Notify others
        $this->broadcast([
            'type' => 'user_online',
            'user_id' => $user_id,
            'username' => $data['username'],
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        echo "[" . date('Y-m-d H:i:s') . "] User {$user_id} ({$data['username']}) authenticated\n";
    }

    private function handleMessage(ConnectionInterface $from, $data) {
        if (!isset($data['conversation_id']) || !isset($data['text']) || !isset($data['sender_id'])) {
            $from->send(json_encode(['error' => 'Missing message data']));
            return;
        }

        $conversation_id = (int)$data['conversation_id'];
        $sender_id = (int)$data['sender_id'];
        $text = trim($data['text']);

        if (empty($text)) {
            $from->send(json_encode(['error' => 'Message cannot be empty']));
            return;
        }

        // Save message to database
        try {
            $stmt = $this->db->prepare("
                INSERT INTO messages (conversation_id, sender_id, message, is_read, created_at)
                VALUES (?, ?, ?, 0, NOW())
            ");
            $stmt->execute([$conversation_id, $sender_id, $text]);
            $message_id = $this->db->lastInsertId();

            // Get recipient user_id from conversation
            $stmt = $this->db->prepare("
                SELECT landlord_id, tenant_id FROM conversations WHERE id = ?
            ");
            $stmt->execute([$conversation_id]);
            $conv = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$conv) {
                $from->send(json_encode(['error' => 'Conversation not found']));
                return;
            }

            $recipient_id = ($conv['landlord_id'] == $sender_id) ? $conv['tenant_id'] : $conv['landlord_id'];

            // Prepare message object
            $msgObj = [
                'type' => 'message',
                'message_id' => $message_id,
                'conversation_id' => $conversation_id,
                'sender_id' => $sender_id,
                'recipient_id' => $recipient_id,
                'text' => $text,
                'timestamp' => date('Y-m-d H:i:s'),
                'is_read' => 0
            ];

            // Send to recipient if online
            if (isset($this->users[$recipient_id])) {
                $this->users[$recipient_id]->send(json_encode($msgObj));
            }

            // Confirm to sender
            $from->send(json_encode(array_merge($msgObj, ['type' => 'message_sent'])));

            echo "[" . date('Y-m-d H:i:s') . "] Message {$message_id} from user {$sender_id} to {$recipient_id}\n";

        } catch (Exception $e) {
            echo "Error saving message: " . $e->getMessage() . "\n";
            $from->send(json_encode(['error' => 'Failed to save message']));
        }
    }

    private function handleTyping(ConnectionInterface $from, $data) {
        if (!isset($data['conversation_id']) || !isset($data['sender_id'])) {
            return;
        }

        $conversation_id = (int)$data['conversation_id'];
        $sender_id = (int)$data['sender_id'];

        // Get recipient
        try {
            $stmt = $this->db->prepare("
                SELECT landlord_id, tenant_id FROM conversations WHERE id = ?
            ");
            $stmt->execute([$conversation_id]);
            $conv = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$conv) return;

            $recipient_id = ($conv['landlord_id'] == $sender_id) ? $conv['tenant_id'] : $conv['landlord_id'];

            // Send typing indicator to recipient if online
            if (isset($this->users[$recipient_id])) {
                $this->users[$recipient_id]->send(json_encode([
                    'type' => 'typing',
                    'conversation_id' => $conversation_id,
                    'sender_id' => $sender_id,
                    'username' => $data['username'] ?? 'User'
                ]));
            }
        } catch (Exception $e) {
            echo "Error handling typing: " . $e->getMessage() . "\n";
        }
    }

    private function broadcast($data) {
        $msg = json_encode($data);
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }
}

$loop   = Factory::create();
$socket = new Reactor('0.0.0.0:8080', $loop);

$server = IoServer::factory(
    new HttpServer(new WsServer(new Chat())),
    $socket,
    $loop
);

echo "\n╔════════════════════════════════════════════════════════╗\n";
echo "║          CHAT REALTIME WEBSOCKET SERVER               ║\n";
echo "║  WebSocket: ws://localhost:8080                        ║\n";
echo "║  HTTP:      http://localhost:3000                     ║\n";
echo "║  Status:    🟢 RUNNING                                 ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

$loop->run();
