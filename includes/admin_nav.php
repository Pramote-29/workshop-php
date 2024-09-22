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
        <a href="login.php" class="btn btn-outline-primary me-2 d-inline-block">Login</a>
        <a href="register.php" class="btn btn-primary d-inline-block">Sign-up</a>
        <?php } else { ?>
        <!-- ปุ่มสำหรับ admin: เพิ่มปุ่ม Admin Manage Book และ Back to View Books -->
        <?php if ($_SESSION['role'] == 'admin') { ?>
        <a href="admin_view-books.php" class="btn btn-outline-secondary me-2 d-inline-block">Back</a>
        <a href="admin_dashboard.php" class="btn btn-primary me-2 d-inline-block">Admin</a>
        <?php } ?>
        <!-- ปุ่ม Logout -->
        <a href="logout.php" class="btn btn-danger d-inline-block">Logout</a>
        <?php } ?>
    </div>
</header>