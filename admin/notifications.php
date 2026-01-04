<?php
session_start();
require '../config/db.php';

// تأكد أن المستخدم متدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$trainee_name = $_SESSION['fullname'] ?? 'متدرب';
$trainer_id = 32; // رقم المدرب الذي سيستقبل الإشعارات

$error = '';
$success = '';

if(isset($_POST['submit_project'])){
    $title = trim($_POST['title']);

    if(isset($_FILES['project_file']) && $_FILES['project_file']['error'] === 0){
        $fileName = time().'_'.$_FILES['project_file']['name'];
        $fileTmp = $_FILES['project_file']['tmp_name'];
        $destination = '../uploads/'.$fileName;

        if(move_uploaded_file($fileTmp, $destination)){
            // إضافة المشروع
            $stmt = $pdo->prepare("INSERT INTO projects (trainee_id, title, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $title, $fileName]);

            // إرسال إشعار للمدرب
            $message = "المتدرب $trainee_name أرسل مشروعًا جديدًا بعنوان: $title";
            $stmt2 = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt2->execute([$trainer_id, $message]);

            $success = 'تم إرسال المشروع بنجاح!';
        } else {
            $error = 'حدث خطأ أثناء رفع الملف.';
        }
    } else {
        $error = 'الرجاء اختيار ملف المشروع.';
    }
}

include '../inc/trainee_header.php';
?>

<div class="container mt-4">
    <h3 class="text-center mb-3">إرسال مشروع جديد</h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto">
        <div class="mb-3">
            <label>عنوان المشروع</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>اختر ملف المشروع</label>
            <input type="file" name="project_file" class="form-control" required>
        </div>
        <button type="submit" name="submit_project" class="btn btn-primary w-100">إرسال المشروع</button>
    </form>

    <div class="mt-3 w-50 mx-auto">
        <a href="dashboard.php" class="btn btn-outline-dark w-100">
            <i class="bi bi-arrow-left-circle-fill"></i> العودة للوحة المتدرب
        </a>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
