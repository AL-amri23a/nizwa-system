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
    echo "Trainee not found";
    exit;
}

// جلب بيانات المتدرب
$stmt = $pdo->prepare("
    SELECT u.fullname, u.email, c.created_at
    FROM users u
    JOIN certificates c ON u.id = c.trainee_id
    WHERE u.id = ?
");
$stmt->execute([$trainee_id]);
$data = $stmt->fetch();

if(!$data){
    echo "No certificate issued.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>شهادة تدريب</title>
<style>
body{
    font-family: 'Tahoma';
    background:#f5f7fa;
}
.certificate{
    width:800px;
    margin:40px auto;
    padding:40px;
    border:10px solid #0d47a1;
    background:#fff;
    text-align:center;
}
h1{
    color:#0d47a1;
    margin-bottom:30px;
}
.name{
    font-size:26px;
    font-weight:bold;
    margin:20px 0;
}
.footer{
    margin-top:50px;
    display:flex;
    justify-content:space-between;
}
</style>
</head>

<body>

<div class="certificate">
    <h1>شهادة إتمام تدريب</h1>

    <p>تشهد مديرية التعليم بأن المتدرب</p>

    <div class="name"><?= htmlspecialchars($data['fullname']) ?></div>

    <p>قد أتم برنامج التدريب بنجاح</p>

    <p>تاريخ الإصدار: <?= date('Y-m-d', strtotime($data['created_at'])) ?></p>

    <div class="footer">
        <div>توقيع المشرف</div>
        <div>ختم الجهة</div>
    </div>
</div>

</body>
</html>
