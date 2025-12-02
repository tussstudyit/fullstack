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
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (post_id, user_id, content, rating, created_at) " .
            "VALUES (?, ?, ?, ?, NOW())"
        );
        
        return $stmt->execute([
            $data['post_id'],
            $data['user_id'],
            $data['content'] ?? null,
            $data['rating'] ?? 0
        ]);
    }

    /**
     * Get all comments for a post
     */
    public function getByPost($post_id, $limit = 10, $offset = 0) {
        $stmt = $this->conn->prepare(
            "SELECT c.*, u.username, u.id as user_id_display, " .
            "(SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote = 1) as upvotes, " .
            "(SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.id AND vote = -1) as downvotes " .
            "FROM {$this->table} c " .
            "JOIN users u ON c.user_id = u.id " .
            "WHERE c.post_id = ? " .
            "ORDER BY c.created_at DESC " .
            "LIMIT ? OFFSET ?"
        );
        
        $stmt->execute([$post_id, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get comment count for a post
     */
    public function getCountByPost($post_id) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE post_id = ?"
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
            "SELECT c.*, u.username " .
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
