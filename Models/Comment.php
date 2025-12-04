<?php
class Comment {
    private $conn;
    private $table = 'comments';

    public function __construct() {
        $this->conn = getDB();
    }

    /**
     * Create a new comment
     */
    public function create($data) {
        // Check if this is a reply (has parent_id)
        if (isset($data['parent_id']) && $data['parent_id'] > 0) {
            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->table} (post_id, user_id, parent_id, content, rating, created_at) " .
                "VALUES (?, ?, ?, ?, ?, NOW())"
            );
            
            $stmt->execute([
                $data['post_id'],
                $data['user_id'],
                $data['parent_id'],
                $data['content'] ?? null,
                $data['rating'] ?? 0
            ]);
            
            return $this->conn->lastInsertId();
        } else {
            // Regular comment
            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->table} (post_id, user_id, content, rating, created_at) " .
                "VALUES (?, ?, ?, ?, NOW())"
            );
            
            $stmt->execute([
                $data['post_id'],
                $data['user_id'],
                $data['content'] ?? null,
                $data['rating'] ?? 0
            ]);
            
            return $this->conn->lastInsertId();
        }
    }

    /**
     * Get all comments for a post
     */
    public function getByPost($post_id, $limit = 10, $offset = 0) {
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        
        $stmt = $this->conn->prepare(
            "SELECT c.*, u.username, u.id as user_id_display, u.role, " .
            "(SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote = 1) as upvotes, " .
            "(SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote = -1) as downvotes, " .
            "(SELECT vote FROM comment_votes WHERE comment_id = c.id AND user_id = ? LIMIT 1) as user_vote " .
            "FROM {$this->table} c " .
            "JOIN users u ON c.user_id = u.id " .
            "WHERE c.post_id = ? AND c.parent_id IS NULL " .
            "ORDER BY c.created_at DESC " .
            "LIMIT ? OFFSET ?"
        );
        
        $stmt->execute([$user_id, $post_id, $limit, $offset]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Load replies for each comment
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getReplies($comment['id']);
        }
        
        return $comments;
    }

    /**
     * Get replies for a comment (recursive - includes nested replies)
     */
    public function getReplies($parent_id) {
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        
        $stmt = $this->conn->prepare(
            "SELECT c.*, u.username, u.id as user_id_display, u.role, " .
            "(SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote = 1) as upvotes, " .
            "(SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote = -1) as downvotes, " .
            "(SELECT vote FROM comment_votes WHERE comment_id = c.id AND user_id = ? LIMIT 1) as user_vote " .
            "FROM {$this->table} c " .
            "JOIN users u ON c.user_id = u.id " .
            "WHERE c.parent_id = ? " .
            "ORDER BY c.created_at ASC"
        );
        
        $stmt->execute([$user_id, $parent_id]);
        $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recursively load nested replies for each reply
        foreach ($replies as &$reply) {
            $reply['replies'] = $this->getReplies($reply['id']);
        }
        
        return $replies;
    }

    /**
     * Get comment count for a post (only parent comments, not replies)
     */
    public function getCountByPost($post_id) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE post_id = ? AND parent_id IS NULL"
        );
        $stmt->execute([$post_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Get comment by ID
     */
    public function findById($id) {
        $stmt = $this->conn->prepare(
            "SELECT c.*, u.username, u.role " .
            "FROM {$this->table} c " .
            "JOIN users u ON c.user_id = u.id " .
            "WHERE c.id = ?"
        );
        
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update comment
     */
    public function update($id, $data) {
        $updates = [];
        $values = [];
        
        if (isset($data['content'])) {
            $updates[] = "content = ?";
            $values[] = $data['content'];
        }
        
        if (isset($data['rating'])) {
            $updates[] = "rating = ?";
            $values[] = $data['rating'];
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'Không có gì để cập nhật'];
        }
        
        $values[] = $id;
        $updateStr = implode(', ', $updates);
        
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET $updateStr WHERE id = ?"
        );
        
        return ['success' => $stmt->execute($values), 'message' => 'Cập nhật thành công'];
    }

    /**
     * Delete comment
     */
    public function delete($id) {
        // Delete associated votes
        $stmt = $this->conn->prepare("DELETE FROM comment_votes WHERE comment_id = ?");
        $stmt->execute([$id]);
        
        // Delete comment
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Add vote to comment
     */
    public function vote($comment_id, $user_id, $vote) {
        // Check if user already voted
        $stmt = $this->conn->prepare(
            "SELECT id FROM comment_votes WHERE comment_id = ? AND user_id = ?"
        );
        $stmt->execute([$comment_id, $user_id]);
        
        if ($stmt->rowCount() > 0) {
            // Update existing vote
            $stmt = $this->conn->prepare(
                "UPDATE comment_votes SET vote = ? WHERE comment_id = ? AND user_id = ?"
            );
            return $stmt->execute([$vote, $comment_id, $user_id]);
        } else {
            // Insert new vote
            $stmt = $this->conn->prepare(
                "INSERT INTO comment_votes (comment_id, user_id, vote) VALUES (?, ?, ?)"
            );
            return $stmt->execute([$comment_id, $user_id, $vote]);
        }
    }

    /**
     * Get average rating for post
     */
    public function getAverageRating($post_id) {
        $stmt = $this->conn->prepare(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as count " .
            "FROM {$this->table} " .
            "WHERE post_id = ? AND rating > 0"
        );
        
        $stmt->execute([$post_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user's vote on comment
     */
    public function getUserVote($comment_id, $user_id) {
        $stmt = $this->conn->prepare(
            "SELECT vote FROM comment_votes WHERE comment_id = ? AND user_id = ?"
        );
        
        $stmt->execute([$comment_id, $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['vote'] : 0;
    }

    /**
     * Get total comment count for a post (alias for getCountByPost)
     */
    public function getTotalCommentCount($post_id) {
        return $this->getCountByPost($post_id);
    }
}
?>
