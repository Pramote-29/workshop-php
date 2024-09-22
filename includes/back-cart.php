<header
    class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
    <div class="col-md-3 mb-2 mb-md-0">
        <a href="index.php" class="d-inline-flex link-body-emphasis text-decoration-none fs-3 fw-bold">
            Book.store
        </a>
    </div>

    <div class="col-md-3 text-end">
        <?php if (!isset($_SESSION['user_id'])) { ?>
        <!-- ปุ่ม Login และ Sign-up สำหรับผู้ที่ยังไม่ได้ล็อกอิน -->
        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
        <a href="register.php" class="btn btn-primary">Sign-up</a>
        <?php } else { 
            // ดึงยอดรวมสินค้าจากตะกร้าสำหรับผู้ใช้ที่ล็อกอินแล้ว
            $stmt = $pdo->prepare('SELECT SUM(books.price * cart_items.quantity) AS total FROM cart_items JOIN books ON cart_items.book_id = books.id WHERE cart_items.user_id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $cart_total = $stmt->fetchColumn() ?: 0.00; // ถ้าไม่มีสินค้าให้แสดง 0.00
        ?>
        <a href="user_dashboard.php" class="btn btn-primary">Back</a>
        <!-- ปุ่ม Logout และไอคอนตะกร้าสินค้า -->
        <a href="cart.php" class="btn btn-outline-secondary me-2">
            <i class="bi bi-cart"></i> <!-- ไอคอนตะกร้าสินค้า -->
            ฿<span id="cart-total"><?= number_format($cart_total, 2) ?></span>
        </a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <?php } ?>
    </div>
</header>