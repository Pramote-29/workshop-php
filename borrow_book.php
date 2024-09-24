<?php
session_start();
require('config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// รับข้อมูลจาก AJAX
$input = json_decode(file_get_contents('php://input'), true);
$book_id = $input['book_id'];

// ตรวจสอบว่าหนังสือมีสต็อกเพียงพอ
$stmt = $pdo->prepare('SELECT stock FROM books WHERE id = ?');
$stmt->execute([$book_id]);
$book = $stmt->fetch();

if ($book && $book['stock'] > 0) {
    // บันทึกข้อมูลการยืมหนังสือ
    $stmt = $pdo->prepare('INSERT INTO borrow_records (user_id, book_id, borrow_date) VALUES (?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $book_id]);

    // อัปเดตสต็อกหนังสือ
    $stmt = $pdo->prepare('UPDATE books SET stock = stock - 1 WHERE id = ?');
    $stmt->execute([$book_id]);

    // ดึงจำนวนหนังสือที่ยืมทั้งหมด
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM borrow_records WHERE user_id = ? AND status = "borrowed"');
    $stmt->execute([$_SESSION['user_id']]);
    $borrowed_count = $stmt->fetchColumn();

    echo json_encode(['success' => true, 'borrowed_count' => $borrowed_count]);
} else {
    echo json_encode(['success' => false, 'message' => 'Book is out of stock']);
}
?>