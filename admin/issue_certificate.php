<?php
session_start();
require '../config/db.php';

// تحقق من المشرف
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$trainee_id = $_GET['id'] ?? null;
if(!$trainee_id){
    die("Trainee not found");
}

// تحقق هل الشهادة موجودة مسبقًا
$check = $pdo->prepare("SELECT id FROM certificates WHERE trainee_id=?");
$check->execute([$trainee_id]);

if($check->rowCount() == 0){
    // إصدار شهادة
    $insert = $pdo->prepare("
        INSERT INTO certificates (trainee_id, created_at)
        VALUES (?, NOW())
    ");
    $insert->execute([$trainee_id]);
}

header("Location: certificates.php");
exit;
