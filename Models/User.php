<?php
require_once __DIR__ . '/../config.php';

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = getDB();
    }
    /********************/
    /*Tìm user theo ID*/
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in User::findById: " . $e->getMessage());
            return false;
        }
    }

    /*Tìm user theo email*/
    public function findByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in User::findByEmail: " . $e->getMessage());
            return false;
        }
    }

    /*Tìm user theo username*/
    public function findByUsername($username) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in User::findByUsername: " . $e->getMessage());
            return false;
        }
    }

    /*Tìm user theo số điện thoại*/
    public function findByPhone($phone) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE phone = ?");
            $stmt->execute([$phone]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in User::findByPhone: " . $e->getMessage());
            return false;
        }
    }

    /*Tìm user theo email, username hoặc số điện thoại*/
    public function findByEmailOrUsernameOrPhone($credential) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE email = ? OR username = ? OR phone = ?
                LIMIT 1
            ");
            $stmt->execute([$credential, $credential, $credential]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error in User::findByEmailOrUsernameOrPhone: " . $e->getMessage());
            return false;
        }
    }

    /********************/
    /*Đăng ký user mới*/
    public function register($data) {
        try {
            // Kiểm tra email tồn tại
            if ($this->findByEmail($data['email'])) {
                return ['success' => false, 'message' => 'Email đã tồn tại'];
            }

            // Kiểm tra username tồn tại
            if ($this->findByUsername($data['username'])) {
                return ['success' => false, 'message' => 'Username đã tồn tại'];
            }

            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (username, email, password, full_name, phone, role, status)
                VALUES (?, ?, ?, ?, ?, ?, 'active')
            ");

            $result = $stmt->execute([
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['full_name'],
                $data['phone'] ?? null,
                $data['role'] ?? 'tenant'
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Đăng ký thành công',
                    'user_id' => $this->db->lastInsertId()
                ];
            }

            return ['success' => false, 'message' => 'Lỗi khi đăng ký'];
        } catch (PDOException $e) {
            error_log("Error in User::register: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /********************/
    /*Đăng nhập user bằng email, username hoặc số điện thoại*/
    public function login($credential, $password) {
        try {
            $user = $this->findByEmailOrUsernameOrPhone($credential);

            if (!$user) {
                return [
                    'success' => false, 
                    'message' => 'Email, username hoặc số điện thoại không tồn tại',
                    'errorType' => 'user_not_found'
                ];
            }

            if ($user['status'] === 'banned') {
                return [
                    'success' => false, 
                    'message' => 'Tài khoản của bạn đã bị khóa',
                    'errorType' => 'account_banned'
                ];
            }

            if (password_verify($password, $user['password'])) {
                // Cập nhật session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                return [
                    'success' => true,
                    'message' => 'Đăng nhập thành công',
                    'user' => $user
                ];
            }

            return [
                'success' => false, 
                'message' => 'Mật khẩu không đúng, vui lòng thử lại',
                'errorType' => 'password_wrong'
            ];
        } catch (PDOException $e) {
            error_log("Error in User::login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /******************/
    /*Cập nhật profile user*/
    public function updateProfile($id, $data) {
        try {
            $fields = [];
            $values = [];

            if (isset($data['full_name'])) {
                $fields[] = "full_name = ?";
                $values[] = $data['full_name'];
            }
            if (isset($data['phone'])) {
                $fields[] = "phone = ?";
                $values[] = $data['phone'];
            }

            if (empty($fields)) {
                return ['success' => false, 'message' => 'Không có dữ liệu cập nhật'];
            }

            $values[] = $id;
            $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";

            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($values);

            if ($result) {
                return ['success' => true, 'message' => 'Cập nhật thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];
        } catch (PDOException $e) {
            error_log("Error in User::updateProfile: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /*************/
    /*Đổi mật khẩu*/
    public function changePassword($id, $oldPassword, $newPassword) {
        try {
            $user = $this->findById($id);

            if (!$user) {
                return ['success' => false, 'message' => 'User không tồn tại'];
            }

            if (!password_verify($oldPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Mật khẩu cũ không đúng'];
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ? WHERE id = ?");
            $result = $stmt->execute([$hashedPassword, $id]);

            if ($result) {
                return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
            }

            return ['success' => false, 'message' => 'Lỗi khi đổi mật khẩu'];
        } catch (PDOException $e) {
            error_log("Error in User::changePassword: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()];
        }
    }

    /*Lấy danh sách users (admin)*/
    public function getAllUsers($limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table}
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in User::getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    /*Đếm tổng users*/
    public function countAll() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error in User::countAll: " . $e->getMessage());
            return 0;
        }
    }
}
?>
