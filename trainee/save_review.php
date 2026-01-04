<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer'){
    header("Location: ../auth/login.php");
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];
$feedback = $_POST['feedback'];

$stmt = $pdo->prepare("UPDATE projects SET status=?, feedback=? WHERE id=?");
$stmt->execute([$status, $feedback, $id]);

header("Location: review_projects.php");
exit;
