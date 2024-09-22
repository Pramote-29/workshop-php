<?php 
session_start();
require('config.php');

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];  // ดึงบทบาทจาก session
} else {
    // หากไม่มีค่าใน session ให้เปลี่ยนเส้นทางกลับไปที่หน้า login
    header('location:login.php');
    exit();
}

// ตรวจสอบบทบาทของผู้ใช้ ถ้าไม่ใช่ user จะ redirect ไปหน้า login
if ($role != 'user') {
    // ถ้าไม่ใช่ user ให้ redirect กลับไปหน้า login
    header('location:login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>User Dashboard</title>
</head>

<body>

    <div class="container">
        <?php include('./includes/user_nav.php'); ?>
    </div>

    <div class="px-4 py-5 my-5 text-center">
        <?php
        try {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $userData = $stmt->fetch();

            if ($userData) {
                echo "<h1 class='display-5 fw-bold text-body-emphasis'>Welcome : " . htmlspecialchars($userData['username']) . "</h1>";
                echo "<p class='lead mb-4'>Email : " . htmlspecialchars($userData['email']) . "</p>";

                // แสดงบทบาทของผู้ใช้
                echo "<p class='lead mb-4'>Role : " . htmlspecialchars($userData['role']) . "</p>";

                // เนื้อหาที่เฉพาะสำหรับ user
                echo "<p>Welcome to your dashboard, user!</p>";
            } else {
                echo "<p>User data not found.</p>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>

    <div class="container">
        <?php include('./includes/footer.php'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>