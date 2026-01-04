<?php
session_start();
require '../config/db.php';

// التحقق من تسجيل الدخول
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

// جلب الشهادة
$stmt = $pdo->prepare("SELECT * FROM certificates WHERE trainee_id=? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$cert = $stmt->fetch();


// تحديد نوع الملف
$filepath = '../' . $cert['file_path'];
$filename = basename($filepath);

// إرسال رؤوس التحميل
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

// قراءة الملف
readfile($filepath);
exit;
