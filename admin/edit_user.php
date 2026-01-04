<?php
session_start();
require '../config/db.php';

// التأكد من أن المستخدم مشرف
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$error = '';
$success = '';

$id = $_GET['id'] ?? 0;

// جلب بيانات المتدرب
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='trainee'");
$stmt->execute([$id]);
$user = $stmt->fetch();

if(!$user){
    header("Location: manage_users.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // التحقق من البريد إذا تم تغييره
    $stmt_check = $pdo->prepare("SELECT * FROM users WHERE email=? AND id<>?");
    $stmt_check->execute([$email, $id]);
    if($stmt_check->rowCount() > 0){
        $error = "هذا البريد الإلكتروني مستخدم من قبل متدرب آخر.";
    } else {
        if(!empty($password)){
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt_update = $pdo->prepare("UPDATE users SET fullname=?, email=?, password=? WHERE id=?");
            $stmt_update->execute([$fullname, $email, $hashed, $id]);
        } else {
            $stmt_update = $pdo->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
            $stmt_update->execute([$fullname, $email, $id]);
        }
        $success = "تم تحديث بيانات المتدرب بنجاح!";
        // إعادة جلب البيانات المحدثة
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id=? AND role='trainee'");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
    }
}

include '../inc/admin_header.php';
?>

<style>
body {
    font-family: 'Cairo', sans-serif;
    direction: rtl;
    background-color: #f4f6f9;
}
.card-header {
    background-color: #198754; /* أخضر للمشرف */
    color: #fff;
    font-weight: bold;
    font-size: 1.1rem;
}
.card {
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.btn {
    border-radius: 50px;
    font-weight: bold;
}
</style>

<div class="container mt-5">
    <h2 class="mb-4 text-end">تعديل بيانات المتدرب</h2>

    <?php if($error): ?>
        <div class="alert alert-danger text-end"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success text-end"><?= $success ?></div>
    <?php endif; ?>

    <div class="card p-4">
        <div class="card-header text-end">بيانات المتدرب</div>
        <div class="card-body text-end">
            <form method="POST" class="text-end row g-3">
                <div class="col-md-6">
                    <label class="form-label">الاسم الكامل</label>
                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" name="password" class="form-control" placeholder="اتركها فارغة إذا لم ترغب بالتغيير">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success w-100">تحديث بيانات المتدرب</button>
                </div>
            </form>

            <div class="text-end mt-3">
                <a href="manage_users.php" class="btn btn-outline-secondary">العودة لقائمة المتدربين</a>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
