<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$trainee_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$stmt = $pdo->prepare("UPDATE attendance SET logout_time=NOW() WHERE trainee_id=? AND date=? AND logout_time IS NULL");
$stmt->execute([$trainee_id, $today]);

header("Location: dashboard.php");
exit;
?>
