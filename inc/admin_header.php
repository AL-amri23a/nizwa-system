<?php
if(session_status() === PHP_SESSION_NONE) session_start();
require '../config/db.php';

// إعداد اللغة
if(!isset($_SESSION['lang'])) $_SESSION['lang'] = 'ar';
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang']==='en' ? 'en' : 'ar';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
$current = $_SESSION['lang'];

// نصوص اللغات
$lang = [
    'ar'=>['title'=>'لوحة تحكم المشرف','logout'=>'تسجيل الخروج','change_lang'=>'English','back'=>'رجوع','notifications'=>'الإشعارات'],
    'en'=>['title'=>'Admin Dashboard','logout'=>'Logout','change_lang'=>'عربي','back'=>'Back','notifications'=>'Notifications']
];

// إشعارات المستخدم
$user_id = $_SESSION['user_id'] ?? 0;
$stmt = $pdo->prepare("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id=? AND is_read=0");
$stmt->execute([$user_id]);
$unreadCount = $stmt->fetch()['unread_count'] ?? 0;

$stmt2 = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC");
$stmt2->execute([$user_id]);
$notifications = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $current ?>" dir="<?= $current=='ar'?'rtl':'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $lang[$current]['title'] ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@500;700&display=swap" rel="stylesheet">
<style>
body{font-family:'Cairo',sans-serif;background:linear-gradient(135deg,#e3f2fd,#bbdefb);margin:0;min-height:100vh;}
.header-bar{background:linear-gradient(135deg,#0d47a1,#1976d2);color:white;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
.header-bar .logo{display:flex;align-items:center;gap:10px;font-weight:700;font-size:1.4rem;}
.header-bar .logo i{font-size:1.8rem;}
.header-bar .user-info{display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.lang-btn{background: rgba(255,255,255,0.95);color:#0d47a1;border-radius:20px;padding:6px 15px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px;transition:0.3s;}
.lang-btn:hover{background:#fff;color:#0d47a1;transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.15);}
.notification-btn{position:relative;color:white;font-size:1.5rem;margin-right:10px;text-decoration:none;}
.notification-btn .badge{position:absolute;top:0;start:100%;transform:translate(-50%,-50%);font-size:0.75rem;}
.dropdown-menu{max-height:300px;overflow-y:auto;}
.user-info span{display:flex;align-items:center;gap:5px;font-weight:600;}
.logout-btn{background:#e53935;border:none;color:white;padding:6px 15px;border-radius:30px;font-weight:bold;transition:0.3s;}
.logout-btn:hover{background:#c62828;}
.page-title{text-align:center;font-weight:bold;font-size:1.5rem;margin-top:20px;color:#0d47a1;}
.back-btn{text-align:center;margin:20px 0;}
.back-btn a{background:#0d47a1;color:#fff;padding:10px 25px;border-radius:50px;font-weight:bold;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:0.3s;}
.back-btn a:hover{background:#1565c0;}
</style>
</head>
<body>

<div class="header-bar">
    <img src="../assets/images/images.jpg" alt="Ministry of Education Oman" height="60">
    <div class="user-info">
        <a href="?lang=<?= $current=='ar'?'en':'ar' ?>" class="lang-btn"><i class="bi bi-globe2"></i> <?= $current=='ar'?'English':'عربي' ?></a>

        <div class="dropdown">
            <a class="notification-btn" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-bell-fill"></i>
                <?php if($unreadCount>0): ?>
                    <span class="badge rounded-pill bg-danger"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-2">
                <?php if(count($notifications)>0): ?>
                    <?php foreach($notifications as $n): ?>
                        <li class="border-bottom mb-1 pb-1"><?= htmlspecialchars($n['message']) ?> <br><small class="text-muted"><?= $n['created_at'] ?></small></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>لا توجد إشعارات</li>
                <?php endif; ?>
            </ul>
        </div>

        <span><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['fullname'] ?? '') ?></span>

        <form action="../auth/logout.php" method="POST" style="margin:0;">
            <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> <?= $lang[$current]['logout'] ?></button>
        </form>
    </div>
</div>

<div class="page-title"><?= $lang[$current]['title'] ?></div>
