<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$success = $error = '';
$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$now_time = date('H:i:s');

// جلب سجل اليوم
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE trainee_id=? AND date=?");
$stmt->execute([$user_id, $today]);
$record = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['mark_entry'])){
        if($record){
            $error = "لقد تم تسجيل الدخول اليوم مسبقًا.";
        } else {
            $status = ($now_time <= "09:00:00") ? 'حاضر' : 'متأخر';
            $insert = $pdo->prepare("INSERT INTO attendance (trainee_id, date, time, status) VALUES (?, ?, ?, ?)");
            if($insert->execute([$user_id, $today, $now_time, $status])){
                $success = "تم تسجيل الدخول بنجاح! الحالة: $status";
            } else {
                $error = "حدث خطأ أثناء تسجيل الدخول.";
            }
        }
    } elseif(isset($_POST['mark_exit'])){
        if(!$record){
            $error = "يجب تسجيل الدخول أولًا قبل تسجيل الخروج.";
        } elseif($record['exit_time']){
            $error = "لقد تم تسجيل الخروج مسبقًا اليوم.";
        } else {
            $update = $pdo->prepare("UPDATE attendance SET exit_time=? WHERE id=?");
            if($update->execute([$now_time, $record['id']])){
                $success = "تم تسجيل الخروج بنجاح!";
            } else {
                $error = "حدث خطأ أثناء تسجيل الخروج.";
            }
        }
    }
}

include '../inc/header.php';
?>

<div class="container mt-5 text-end" dir="rtl" style="font-family: 'Arial', sans-serif;">
    <div class="card shadow p-4">
        <h3 class="mb-4">تسجيل الحضور</h3>

        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <p>اليوم: <b><?= $today ?></b> | الوقت الحالي: <b><?= $now_time ?></b></p>

        <form method="POST" class="d-flex flex-column gap-2">
            <button type="submit" name="mark_entry" class="btn btn-primary w-100" <?= $record && !$record['exit_time'] ? 'disabled' : '' ?>>تسجيل الدخول</button>
            <button type="submit" name="mark_exit" class="btn btn-warning w-100" <?= !$record || $record['exit_time'] ? 'disabled' : '' ?>>تسجيل الخروج</button>
        </form>

        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-secondary w-100">العودة للوحة المتدرب</a>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
