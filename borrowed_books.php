<?php
session_start();
require('config.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

// ดึงข้อมูลหนังสือที่ผู้ใช้ยืม
$stmt = $pdo->prepare('SELECT borrow_records.id AS borrow_id, books.title, books.author, books.price, books.cover_image, borrow_records.borrow_date FROM borrow_records JOIN books ON borrow_records.book_id = books.id WHERE borrow_records.user_id = ? AND borrow_records.status = "borrowed"');
$stmt->execute([$_SESSION['user_id']]);
$borrowed_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Borrowed Books</title>
</head>

<body>
    <div class="container">
        <?php include('./includes/borrow-back.php'); ?>
    </div>
    <div class="container">
        <h1 class="my-5">Borrowed Books</h1>

        <?php if (!empty($borrowed_books)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Borrow Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrowed_books as $book): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($book['cover_image']) ?>" alt="Cover" style="height: 100px;">
                    </td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td>$<?= number_format($book['price'], 2) ?></td>
                    <td><?= htmlspecialchars($book['borrow_date']) ?></td>
                    <td>
                        <!-- ปุ่ม Return สำหรับคืนหนังสือ -->
                        <form action="return_book.php" method="post">
                            <input type="hidden" name="borrow_id" value="<?= $book['borrow_id'] ?>">
                            <button type="submit" class="btn btn-primary">Return</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center">You have no borrowed books at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>