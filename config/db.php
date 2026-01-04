<?php
// بدء الجلسة تلقائيًا
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "training_system";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
