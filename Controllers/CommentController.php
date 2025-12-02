<?php

class CommentController {
    private $commentModel;
    private $userModel;
    private $postModel;

    public function __construct($db) {
        require_once __DIR__ . '/../Models/Comment.php';
        require_once __DIR__ . '/../Models/User.php';
        require_once __DIR__ . '/../Models/Post.php';

        $this->commentModel = new Comment($db);
        $this->userModel = new User($db);
        $this->postModel = new Post($db);
    }

    /**
     * Get all comments for a post with pagination
     * GET /api/comments.php?action=getComments&post_id=X&limit=10&offset=0
     */
    public function getComments() {
        try {
            $post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

            if (!$post_id) {
                return $this->error('Post ID is required', 400);
            }

            $comments = $this->commentModel->getByPost($post_id, $limit, $offset);
            $total = $this->commentModel->getTotalCommentCount($post_id);
            $avgRating = $this->commentModel->getAverageRating($post_id);

            return $this->success([
                'comments' => $comments,
                'total' => $total,
                'avg_rating' => $avgRating['avg_rating'] ?? 0,
                'comment_count' => $avgRating['count'] ?? 0
            ]);
        } catch (Exception $e) {
            return $this->error('Error fetching comments: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Add a new comment
     * POST /api/comments.php?action=addComment
     * Body: {post_id, content, rating (optional)}
     */
    public function addComment() {
        try {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return $this->error('You must be logged in to comment', 401);
            }

            // Check role - only tenant and admin can comment
            if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['tenant', 'admin'])) {
                return $this->error('Chủ trọ không có quyền bình luận', 403);
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['post_id']) || !isset($data['content'])) {
                return $this->error('Post ID and content are required', 400);
            }

            $data['post_id'] = (int)$data['post_id'];
            $data['user_id'] = $_SESSION['user_id'];
            $data['content'] = trim($data['content']);
            $data['rating'] = isset($data['rating']) ? (int)$data['rating'] : 0;

            if (empty($data['content'])) {
                return $this->error('Comment content cannot be empty', 400);
            }

            if (strlen($data['content']) > 5000) {
                return $this->error('Comment cannot exceed 5000 characters', 400);
            }

            if ($data['rating'] < 0 || $data['rating'] > 5) {
                return $this->error('Rating must be between 0 and 5', 400);
            }

            // Verify post exists
            $post = $this->postModel->findById($data['post_id']);
            if (!$post) {
                return $this->error('Post not found', 404);
            }

            $this->commentModel->create($data);
            
            return $this->success([
                'message' => 'Comment added successfully'
            ], 201);
        } catch (Exception $e) {
            return $this->error('Error adding comment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Add a reply to a comment
     * POST /api/comments.php?action=addReply
     * Body: {parent_id, post_id, content}
     */
    public function addReply() {
        try {
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return $this->error('You must be logged in to reply', 401);
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['parent_id']) || !isset($data['post_id']) || !isset($data['content'])) {
                return $this->error('Parent ID, Post ID and content are required', 400);
            }

            $data['parent_id'] = (int)$data['parent_id'];
            $data['post_id'] = (int)$data['post_id'];
            $data['user_id'] = $_SESSION['user_id'];
            $data['content'] = trim($data['content']);
            $data['rating'] = 0; // Reply không có rating

            if (empty($data['content'])) {
                return $this->error('Reply content cannot be empty', 400);
            }

            if (strlen($data['content']) > 5000) {
                return $this->error('Reply cannot exceed 5000 characters', 400);
            }

            // Verify parent comment exists
            $parent = $this->commentModel->findById($data['parent_id']);
            if (!$parent) {
                return $this->error('Parent comment not found', 404);
            }

            // Verify post exists
            $post = $this->postModel->findById($data['post_id']);
            if (!$post) {
                return $this->error('Post not found', 404);
            }

            $this->commentModel->create($data);
            
            return $this->success([
                'message' => 'Reply added successfully'
            ], 201);
        } catch (Exception $e) {
            return $this->error('Error adding reply: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/comments.php?action=deleteComment
     * Body: {id}
     */
    public function deleteComment() {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->error('You must be logged in', 401);
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $comment_id = isset($data['id']) ? (int)$data['id'] : 0;

            if (!$comment_id) {
                return $this->error('Comment ID is required', 400);
            }

            $comment = $this->commentModel->findById($comment_id);
            if (!$comment) {
                return $this->error('Comment not found', 404);
            }

            // Check if user is comment author or admin
            if ($comment['user_id'] != $_SESSION['user_id']) {
                return $this->error('You can only delete your own comments', 403);
            }

            $result = $this->commentModel->delete($comment_id);
            if (!$result) {
                return $this->error('Failed to delete comment', 500);
            }

            return $this->success(['message' => 'Comment deleted successfully']);
        } catch (Exception $e) {
            return $this->error('Error deleting comment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Vote on a comment
     * POST /api/comments.php?action=voteComment
     * Body: {comment_id, vote} where vote is 1 (upvote) or -1 (downvote)
     */
    public function voteComment() {
        try {
            if (!isset($_SESSION['user_id'])) {
                return $this->error('You must be logged in to vote', 401);
            }

            $data = json_decode(file_get_contents("php://input"), true);
            $comment_id = isset($data['comment_id']) ? (int)$data['comment_id'] : 0;
            $vote = isset($data['vote']) ? (int)$data['vote'] : 0;

            if (!$comment_id) {
                return $this->error('Comment ID is required', 400);
            }

            if ($vote !== 1 && $vote !== -1) {
                return $this->error('Vote must be 1 (upvote) or -1 (downvote)', 400);
            }

            $comment = $this->commentModel->findById($comment_id);
            if (!$comment) {
                return $this->error('Comment not found', 404);
            }

            $result = $this->commentModel->vote($comment_id, $_SESSION['user_id'], $vote);
            if (!$result) {
                return $this->error('Failed to process vote', 500);
            }

            // Get updated vote counts
            $updated = $this->commentModel->findById($comment_id);

            return $this->success([
                'message' => 'Vote recorded successfully',
                'upvotes' => $updated['upvotes'] ?? 0,
                'downvotes' => $updated['downvotes'] ?? 0,
                'user_vote' => $this->commentModel->getUserVote($comment_id, $_SESSION['user_id'])
            ]);
        } catch (Exception $e) {
            return $this->error('Error processing vote: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Helper: Success response
     */
    private function success($data, $code = 200) {
        http_response_code($code);
        return json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Helper: Error response
     */
    private function error($message, $code = 400) {
        http_response_code($code);
        return json_encode(['success' => false, 'error' => $message]);
    }
}
?>
