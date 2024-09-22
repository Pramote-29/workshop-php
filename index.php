<?php
session_start();
require('config.php');

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
    <title>Book Store - Home</title>
</head>

<body>
    <div class="container">
        <?php include('./includes/user_nav.php'); ?>
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
                        <?php if (!isset($_SESSION['user_id'])) { ?>
                        <!-- ถ้ายังไม่ได้เข้าสู่ระบบ จะมี alert แจ้งเตือน -->
                        <button class="btn btn-primary" onclick="alert('Please login first to add to cart!')">Add to
                            Cart</button>
                        <?php } else { ?>
                        <!-- ถ้าเข้าสู่ระบบแล้ว สามารถกดเพิ่มลงในตะกร้าได้ -->
                        <a href="cart.php?action=add&id=<?= $book['id'] ?>" class="btn btn-primary">Add to Cart</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="container">
        <?php include('./includes/footer.php'); ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>