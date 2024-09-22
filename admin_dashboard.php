<?php
session_start();
require('config.php');

// ตรวจสอบบทบาทผู้ใช้ ถ้าไม่ใช่ admin ให้ redirect
if ($_SESSION['role'] != 'admin') {
    header('location: login.php');
    exit();
}

// การจัดการเพิ่ม, ลบ, และแก้ไขหนังสือ
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $stmt = $pdo->prepare('INSERT INTO books (title, author, price, stock, cover_image) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_POST['title'], $_POST['author'], $_POST['price'], $_POST['stock'], $_POST['cover_image']]);
    } elseif ($_POST['action'] == 'delete') {
        $stmt = $pdo->prepare('DELETE FROM books WHERE id = ?');
        $stmt->execute([$_POST['id']]);
    } elseif ($_POST['action'] == 'edit') {
        $stmt = $pdo->prepare('UPDATE books SET title = ?, author = ?, price = ?, stock = ?, cover_image = ? WHERE id = ?');
        $stmt->execute([$_POST['title'], $_POST['author'], $_POST['price'], $_POST['stock'], $_POST['cover_image'], $_POST['id']]);
    }
}

// ดึงรายการหนังสือทั้งหมด
$stmt = $pdo->query('SELECT * FROM books');
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php include('./includes/admin_nav.php'); ?>
    </div>
    <div class="container">
        <h1 class="my-5">Admin Dashboard - Manage Books</h1>

        <!-- Form สำหรับเพิ่มหนังสือใหม่ -->
        <h2>Add New Book</h2>
        <form action="admin_dashboard.php" method="post">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="author">Author</label>
                <input type="text" name="author" id="author" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="stock">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="cover_image">Cover Image URL</label>
                <input type="text" name="cover_image" id="cover_image" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>

        <!-- ตารางแสดงรายการหนังสือที่มีอยู่ -->
        <h2 class="my-5">Current Books</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td>$<?= htmlspecialchars($book['price']) ?></td>
                    <td><?= htmlspecialchars($book['stock']) ?></td>
                    <td>
                        <!-- ปุ่มสำหรับลบหนังสือ -->
                        <form action="admin_dashboard.php" method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $book['id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>

                        <!-- ปุ่ม Edit จะเปิด Modal เพื่อให้แก้ไขข้อมูล -->
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $book['id'] ?>">Edit</button>

                        <!-- Modal สำหรับแก้ไขหนังสือ -->
                        <div class="modal fade" id="editModal<?= $book['id'] ?>" tabindex="-1"
                            aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Book</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="admin_dashboard.php" method="post">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?= $book['id'] ?>">

                                            <div class="mb-3">
                                                <label for="title">Title</label>
                                                <input type="text" name="title"
                                                    value="<?= htmlspecialchars($book['title']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="author">Author</label>
                                                <input type="text" name="author"
                                                    value="<?= htmlspecialchars($book['author']) ?>"
                                                    class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="price">Price</label>
                                                <input type="text" name="price"
                                                    value="<?= htmlspecialchars($book['price']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="stock">Stock</label>
                                                <input type="number" name="stock"
                                                    value="<?= htmlspecialchars($book['stock']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="cover_image">Cover Image URL</label>
                                                <input type="text" name="cover_image"
                                                    value="<?= htmlspecialchars($book['cover_image']) ?>"
                                                    class="form-control">
                                            </div>

                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>