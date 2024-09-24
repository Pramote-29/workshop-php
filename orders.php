<?php
session_start();
require('config.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

// ดึงข้อมูลตะกร้าสินค้าของผู้ใช้
$stmt = $pdo->prepare('SELECT cart_items.*, books.title, books.price FROM cart_items JOIN books ON cart_items.book_id = books.id WHERE cart_items.user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ตรวจสอบว่ามีสินค้าในตะกร้าหรือไม่
if (empty($cart_items)) {
    echo "<script>alert('Your cart is empty. Please add some books before checking out.'); window.location.href = 'cart.php';</script>";
    exit();
}

// คำนวณราคารวม
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// ถ้าผู้ใช้ยืนยันการชำระเงิน
if (isset($_POST['confirm_order'])) {
    try {
        // บันทึกข้อมูลการสั่งซื้อ
        $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_price, order_date) VALUES (?, ?, NOW())');
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $pdo->lastInsertId();

        // บันทึกรายละเอียดการสั่งซื้อ
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare('INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)');
            $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
        }

        // ลบสินค้าที่อยู่ในตะกร้าหลังจากยืนยันคำสั่งซื้อแล้ว
        $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
        $stmt->execute([$_SESSION['user_id']]);

        $_SESSION['message'] = 'Order placed successfully!';
        header('location: success.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .order-summary {
        background-color: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
    }

    .checkout-btn {
        background-color: #28a745;
        color: white;
        border-radius: 25px;
        padding: 10px 20px;
        font-size: 1.2rem;
        transition: 0.3s;
    }

    .checkout-btn:hover {
        background-color: #218838;
        color: white;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #28a745;
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Order Summary</h1>

        <div class="row">
            <!-- แสดงรายละเอียดสินค้าในตะกร้า -->
            <div class="col-md-7">
                <div class="order-summary shadow-sm">
                    <h3 class="mb-3">Your Order</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['title']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h4 class="text-end">Total: $<?= number_format($total, 2) ?></h4>
                </div>
            </div>

            <!-- ฟอร์มกรอกข้อมูลสำหรับชำระเงิน -->
            <div class="col-md-5">
                <div class="order-summary shadow-sm">
                    <h3 class="mb-3">Payment Details</h3>
                    <form method="post">
                        <button type="submit" name="confirm_order" class="checkout-btn w-100">Confirm Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>