<?php
session_start();
require '../config/db.php';

// تأكد أن المستخدم مسجل وأنه مشرف/مدرب
if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin','trainer'])){
    echo 0;
    exit;
}

$user_id = $_SESSION['user_id'];

// جلب عدد الإشعارات الغير مقروءة
$stmt = $pdo->prepare("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id=? AND is_read=0");
$stmt->execute([$user_id]);
$count = $stmt->fetch()['unread_count'] ?? 0;

echo $count;
?>
