<?php
session_start();
require('config.php');

// การจัดการตะกร้าสินค้า (เพิ่ม/ลบ)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add') {
        $book_id = $_GET['id'];
        $stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();

        // เพิ่มหนังสือลงตะกร้า
        if ($book) {
            $stmt = $pdo->prepare('INSERT INTO cart_items (user_id, book_id, quantity) VALUES (?, ?, 1)');
            $stmt->execute([$_SESSION['user_id'], $book['id']]);
        }
    } elseif ($_GET['action'] == 'remove') {
        $cart_item_id = $_GET['id'];
        $stmt = $pdo->prepare('DELETE FROM cart_items WHERE id = ?');
        $stmt->execute([$cart_item_id]);
    }
}

// ดึงข้อมูลตะกร้าสินค้าของผู้ใช้
$stmt = $pdo->prepare('SELECT cart_items.*, books.title, books.price FROM cart_items JOIN books ON cart_items.book_id = books.id WHERE user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณราคารวม
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// ฟังก์ชันการชำระเงิน
if (isset($_POST['checkout'])) {
    // บันทึกข้อมูลการสั่งซื้อ
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_price, order_date) VALUES (?, ?, NOW())');
    $stmt->execute([$_SESSION['user_id'], $total]);
    $order_id = $pdo->lastInsertId();

    // บันทึกรายละเอียดการสั่งซื้อ
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare('INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)');
        $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
    }

    // ลบสินค้าที่อยู่ในตะกร้า
    $stmt = $pdo->prepare('DELETE FROM cart_items WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);

    // หลังจากชำระเงินเสร็จสิ้น นำผู้ใช้ไปที่หน้าคำสั่งซื้อ
    $_SESSION['message'] = 'Your order has been placed successfully!';
    header('location: orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include('./includes/back-cart.php'); ?>
    </div>
    <div class="container">
        <h1 class="my-5">Your Cart</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td>$<?= htmlspecialchars($item['price']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td>$<?= htmlspecialchars($item['price'] * $item['quantity']) ?></td>
                    <td><a href="cart.php?action=remove&id=<?= $item['id'] ?>" class="btn btn-danger">Remove</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total: $<?= number_format($total, 2) ?></h3>

        <!-- ปุ่มชำระเงิน -->
        <form method="post" action="cart.php">
            <button type="submit" name="checkout" class="btn btn-success">Proceed to Checkout</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>