<?php 
session_start();
require 'config.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
}

if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Please enter the information you applied';
    header('location:login.php');
    exit();
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email';
    header('location:login.php');
    exit();
} else {
    try {
        // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $userData = $stmt->fetch();

        // ตรวจสอบว่าผู้ใช้และรหัสผ่านถูกต้องหรือไม่
        if ($userData && password_verify($password, $userData['password'])) {
            // เก็บข้อมูล user_id และ role ไว้ใน session
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['role'] = $userData['role'];  // เก็บบทบาทของผู้ใช้ใน session

            // ตรวจสอบบทบาทของผู้ใช้และเปลี่ยนเส้นทางไปยังหน้า dashboard ที่ถูกต้อง
            if ($userData['role'] == 'admin') {
                header('location: admin_view-books.php');
            } else {
                header('location: user_dashboard.php');
            }
            exit();
        } else {
            // หากข้อมูลผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
            $_SESSION['error'] = 'Invalid email or password';
            header('location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        // หากเกิดข้อผิดพลาด
        $_SESSION['error'] = 'Something went wrong. Please try again';
        header('location: login.php');
        exit();
    }
}
?>