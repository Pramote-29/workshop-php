<header
    class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
    <div class="col-md-3 mb-2 mb-md-0">
        <a href="index.php" class="d-inline-flex link-body-emphasis text-decoration-none fs-3 fw-bold">
            Book.store
        </a>
    </div>

    <div class="col-md-3 text-end">
        <?php if(!isset($_SESSION['user_id'])){?>
        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
        <a href="register.php" class="btn btn-primary">Sign-up</a>
        <?php } else {?>
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <?php }?>

    </div>
</header>