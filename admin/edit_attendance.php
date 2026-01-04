<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$error = $success = '';

// جلب سجل الحضور
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE id=?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if(!$record){
    die("سجل الحضور غير موجود.");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $status = $_POST['status'];
    $update = $pdo->prepare("UPDATE attendance SET status=? WHERE id=?");
    if($update->execute([$status, $id])){
        $success = "تم تعديل الحالة بنجاح.";
        // إعادة تحميل البيانات
        $stmt->execute([$id]);
        $record = $stmt->fetch();
    } else {
        $error = "حدث خطأ أثناء التعديل.";
    }
}

include '../inc/admin_header.php';
?>

<h3 class="mb-4 text-end">تعديل حضور المتدرب</h3>

<?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST" class="text-end">
    <p>المتدرب: <?= htmlspecialchars($record['trainee_id']) ?> | التاريخ: <?= $record['date'] ?> | الوقت: <?= $record['time'] ?></p>
    <div class="mb-3">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
            <option value="حاضر" <?= $record['status']=='حاضر'?'selected':'' ?>>حاضر</option>
            <option value="متأخر" <?= $record['status']=='متأخر'?'selected':'' ?>>متأخر</option>
            <option value="غائب" <?= $record['status']=='غائب'?'selected':'' ?>>غائب</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">تحديث الحالة</button>
</form>

<?php include '../inc/footer.php'; ?>
