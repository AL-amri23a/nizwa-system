<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// اللغة الافتراضية
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}

// تغيير اللغة عند الضغط على الزر
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] == 'en' ? 'en' : 'ar';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$currentLang = $_SESSION['lang'];
?>

<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $currentLang == 'ar' ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: #003366;
            padding: 15px;
        }
        .navbar-custom .navbar-brand {
            color: #ffffff;
            font-size: 22px;
            font-weight: bold;
        }
        .navbar-custom .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar-custom .user-info span {
            color: #ffffff;
            font-size: 16px;
        }
        .logout-btn {
            border: none;
            background: #cc0000;
            color: white;
            padding: 6px 14px;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background: #aa0000;
        }
    </style>

</head>
<body>

    <nav class="navbar navbar-custom">
        <div class="container-fluid d-flex justify-content-between">

            <!-- عنوان اللوحة -->
            <a class="navbar-brand">Trainer Dashboard</a>

            <div class="user-info">

                <!-- زر تغيير اللغة -->
                <?php if ($currentLang == 'ar'): ?>
                    <a href="?lang=en" class="btn btn-light btn-sm">
                        English
                    </a>
                <?php else: ?>
                    <a href="?lang=ar" class="btn btn-light btn-sm">
                        عربي
                    </a>
                <?php endif; ?>

                <!-- اسم المستخدم -->
                <span>
                    <i class="bi bi-person-circle"></i>
                    <?= htmlspecialchars($_SESSION['fullname']) ?>
                </span>

                <!-- زر تسجيل الخروج -->
                <form action="../auth/logout.php" method="POST" style="margin:0;">
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

