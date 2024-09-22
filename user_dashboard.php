<?php
session_start();
require('config.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('location: login.php');
    exit();
}

// ดึงข้อมูลหนังสือทั้งหมดจากฐานข้อมูล
$stmt = $pdo->query('SELECT * FROM books');
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>User Dashboard - Book Store</title>
</head>

<body>
    <div class="container">
        <?php include('./includes/nav-cart.php'); ?>
    </div>
    <div class="container">
        <h1 class="my-5 text-center">Book Store</h1>

        <div class="row">
            <?php foreach ($books as $book): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="<?= htmlspecialchars($book['cover_image']) ?>" class="card-img-top" alt="Book Cover"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                        <p class="card-text"><strong>Price:</strong> $<?= htmlspecialchars($book['price']) ?></p>
                        <p class="card-text"><strong>Stock:</strong> <?= htmlspecialchars($book['stock']) ?></p>
                        <!-- ปุ่ม Add to Cart -->
                        <button class="btn btn-primary" onclick="addToCart(<?= $book['id'] ?>)">Add to Cart</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ฟังก์ชัน Add to Cart ผ่าน AJAX -->
    <script>
    function addToCart(book_id) {
        // ส่งข้อมูลด้วย AJAX
        fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    book_id: book_id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // อัปเดตยอดรวมในตะกร้าสินค้า
                    document.getElementById('cart-total').innerText = data.total;
                } else {
                    alert('Failed to add to cart.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>