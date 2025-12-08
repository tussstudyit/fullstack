<?php
// API endpoint để clear login error sessions
session_start();

// Xóa error sessions
if (isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
}
if (isset($_SESSION['login_error_type'])) {
    unset($_SESSION['login_error_type']);
}

// Trả về response (sendBeacon không cần response)
http_response_code(200);
?>
