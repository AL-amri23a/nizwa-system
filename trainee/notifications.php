<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();

// تحديد الكل كمقروء
$pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$user_id]);
?>
<!DOCTYPE html>
<html>
<head>
<title>الإشعارات</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h3>الإشعارات</h3>
<hr>

<?php if(empty($notifications)): ?>
<div class="alert alert-secondary">لا توجد إشعارات جديدة</div>
<?php else: ?>
<?php foreach($notifications as $n): ?>
<div class="alert alert-info">
    <?= htmlspecialchars($n['message']) ?>
    <span class="text-muted float-end"><?= $n['created_at'] ?></span>
</div>
<?php endforeach; ?>
<?php endif; ?>

</body>
</html>
