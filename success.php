<?php
session_start();
require('config.php');

if (!isset($_SESSION['order_id'])) {
    header('location: index.php');
    exit();
}

// ลบสินค้าที่อยู่ในตะกร้า
$stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);

// แสดงหน้าสำเร็จ
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="alert alert-success text-center">
            <h1>Order Placed Successfully!</h1>
            <p>Your order has been placed successfully. Thank you for shopping with us!</p>
            <a href="user_dashboard.php" class="btn btn-primary mt-3">Continue Shopping</a>
        </div>
    </div>
</body>

</html>