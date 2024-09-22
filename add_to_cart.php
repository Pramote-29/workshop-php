<?php
session_start();
require('config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// รับข้อมูลจาก AJAX
$input = json_decode(file_get_contents('php://input'), true);
$book_id = $input['book_id'];

// เพิ่มสินค้าลงในตะกร้า
$stmt = $pdo->prepare('INSERT INTO cart_items (user_id, book_id, quantity) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1');
$stmt->execute([$_SESSION['user_id'], $book_id]);

// คำนวณยอดรวมสินค้าในตะกร้า
$stmt = $pdo->prepare('SELECT SUM(books.price * cart_items.quantity) AS total FROM cart_items JOIN books ON cart_items.book_id = books.id WHERE cart_items.user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$total = $stmt->fetchColumn();

// ส่งข้อมูลยอดรวมกลับไปให้ AJAX
echo json_encode(['success' => true, 'total' => '฿' . number_format($total, 2)]);