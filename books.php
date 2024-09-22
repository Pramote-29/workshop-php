<?php
session_start();
require('config.php');

// ดึงข้อมูลหนังสือจากฐานข้อมูล
$stmt = $pdo->query('SELECT * FROM books');
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="my-5">Book Store</h1>
        <div class="row">
            <?php foreach ($books as $book): ?>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="<?= htmlspecialchars($book['cover_image']) ?>" class="card-img-top" alt="Book Cover">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($book['author']) ?></p>
                        <p class="card-text">$<?= htmlspecialchars($book['price']) ?></p>
                        <a href="cart.php?action=add&id=<?= $book['id'] ?>" class="btn btn-primary">Add to Cart</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>