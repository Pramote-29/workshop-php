<?php
session_start();
require('config.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

if (isset($_POST['borrow_id'])) {
    $borrow_id = $_POST['borrow_id'];

    // ดึงข้อมูลการยืม
    $stmt = $pdo->prepare('SELECT book_id FROM borrow_records WHERE id = ? AND user_id = ? AND status = "borrowed"');
    $stmt->execute([$borrow_id, $_SESSION['user_id']]);
    $borrow = $stmt->fetch();

    if ($borrow) {
        // อัปเดตสถานะการคืนหนังสือ
        $stmt = $pdo->prepare('UPDATE borrow_records SET status = "returned", return_date = NOW() WHERE id = ?');
        $stmt->execute([$borrow_id]);

        // คืนจำนวนสต็อกหนังสือ
        $stmt = $pdo->prepare('UPDATE books SET stock = stock + 1 WHERE id = ?');
        $stmt->execute([$borrow['book_id']]);

        $_SESSION['message'] = 'Book returned successfully!';
    } else {
        $_SESSION['error'] = 'Invalid return request!';
    }

    header('location: borrowed_books.php');
    exit();
}
?>