<?php
require_once __DIR__ . '/../config.php';

class Notification {
    private $db;
    private $table = 'notifications';

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Lấy tất cả thông báo của user
     */
    public function getNotifications($user_id, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$user_id, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Notification::getNotifications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy số thông báo chưa đọc
     */
    public function getUnreadCount($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = ? AND is_read = 0
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['count'] : 0;
        } catch (PDOException $e) {
            error_log("Error in Notification::getUnreadCount: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tạo thông báo mới
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} 
                (user_id, type, title, message, link, is_read, created_at)
                VALUES (?, ?, ?, ?, ?, 0, NOW())
            ");
            
            return $stmt->execute([
                $data['user_id'],
                $data['type'], // 'message', 'review', 'post_approved', 'post_rejected', 'system', 'comment', 'reply', 'rating'
                $data['title'],
                $data['message'] ?? null,
                $data['link'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error in Notification::create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo thông báo cho comment
     */
    public function notifyComment($post_id, $comment_id, $commenter_id, $commenter_name, $post_title) {
        try {
            // Get landlord of the post
            $stmt = $this->db->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post || $post['user_id'] == $commenter_id) {
                return false; // Don't notify if commenter is the post author
            }
            
            $link = "../../Views/posts/detail.php?id={$post_id}#comment-{$comment_id}";
            
            return $this->create([
                'user_id' => $post['user_id'],
                'type' => 'comment',
                'title' => "{$commenter_name} đã bình luận về bài viết của bạn",
                'message' => "Bài viết: {$post_title}",
                'link' => $link
            ]);
        } catch (PDOException $e) {
            error_log("Error in Notification::notifyComment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo thông báo cho rating/đánh giá
     */
    public function notifyRating($post_id, $comment_id, $rater_id, $rater_name, $rating, $post_title) {
        try {
            // Get landlord of the post
            $stmt = $this->db->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post || $post['user_id'] == $rater_id) {
                return false;
            }
            
            $link = "../../Views/posts/detail.php?id={$post_id}#comment-{$comment_id}";
            
            return $this->create([
                'user_id' => $post['user_id'],
                'type' => 'rating',
                'title' => "{$rater_name} đã đánh giá {$rating}/5 sao cho bài viết của bạn",
                'message' => "Bài viết: {$post_title}",
                'link' => $link
            ]);
        } catch (PDOException $e) {
            error_log("Error in Notification::notifyRating: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo thông báo cho reply/phản hồi
     */
    public function notifyReply($comment_id, $reply_id, $replier_id, $replier_name, $post_id, $post_title) {
        try {
            // Get original comment author
            $stmt = $this->db->prepare("SELECT user_id FROM comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$comment || $comment['user_id'] == $replier_id) {
                return false;
            }
            
            $link = "../../Views/posts/detail.php?id={$post_id}#comment-{$comment_id}";
            
            return $this->create([
                'user_id' => $comment['user_id'],
                'type' => 'reply',
                'title' => "{$replier_name} đã phản hồi bình luận của bạn",
                'message' => "Bài viết: {$post_title}",
                'link' => $link
            ]);
        } catch (PDOException $e) {
            error_log("Error in Notification::notifyReply: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo thông báo tin nhắn
     */
    public function notifyMessage($receiver_id, $sender_name, $message_preview, $conversation_id) {
        try {
            $link = "../../Views/chat/chat.php?conversation_id={$conversation_id}";
            
            return $this->create([
                'user_id' => $receiver_id,
                'type' => 'message',
                'title' => "{$sender_name} đã gửi tin nhắn",
                'message' => substr($message_preview, 0, 100),
                'link' => $link
            ]);
        } catch (PDOException $e) {
            error_log("Error in Notification::notifyMessage: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo thông báo cho like/lượt thích
     */
    public function notifyLike($post_id, $liker_id, $liker_name, $post_title) {
        try {
            // Get post owner
            $stmt = $this->db->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post || $post['user_id'] == $liker_id) {
                return false; // Don't notify if liker is the post author
            }
            
            $link = "../../Views/posts/detail.php?id={$post_id}";
            
            return $this->create([
                'user_id' => $post['user_id'],
                'type' => 'post_like',
                'title' => "{$liker_name} đã thích bài viết của bạn",
                'message' => "Bài viết: {$post_title}",
                'link' => $link
            ]);
        } catch (PDOException $e) {
            error_log("Error in Notification::notifyLike: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead($notification_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1 
                WHERE id = ?
            ");
            return $stmt->execute([$notification_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::markAsRead: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead($user_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_read = 1 
                WHERE user_id = ? AND is_read = 0
            ");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::markAllAsRead: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa thông báo
     */
    public function delete($notification_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table} 
                WHERE id = ?
            ");
            return $stmt->execute([$notification_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::delete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa tất cả thông báo của user
     */
    public function deleteAll($user_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table} 
                WHERE user_id = ?
            ");
            return $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            error_log("Error in Notification::deleteAll: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy thông báo theo ID
     */
    public function getById($notification_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE id = ?
            ");
            $stmt->execute([$notification_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in Notification::getById: " . $e->getMessage());
            return false;
        }
    }
}
?>
