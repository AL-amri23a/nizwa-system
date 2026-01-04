<?php
session_start();
require '../config/db.php';

// تأكد أن المستخدم متدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// نصوص اللغة
$language = $_SESSION['lang'] ?? 'ar';
$texts = [
    'ar'=>[
        'page_title'=>'إرسال مشروع جديد',
        'project_title'=>'عنوان المشروع',
        'choose_file'=>'اختر ملف المشروع',
        'submit'=>'إرسال المشروع',
        'success'=>'تم إرسال المشروع بنجاح!',
        'error_file'=>'حدث خطأ أثناء رفع الملف.',
        'error_choose'=>'الرجاء اختيار ملف المشروع.',
        'back'=>'العودة للوحة المتدرب'
    ],
    'en'=>[
        'page_title'=>'Submit New Project',
        'project_title'=>'Project Title',
        'choose_file'=>'Choose Project File',
        'submit'=>'Submit Project',
        'success'=>'Project submitted successfully!',
        'error_file'=>'Error uploading the file.',
        'error_choose'=>'Please choose a project file.',
        'back'=>'Back to Trainee Dashboard'
    ]
];

function t($key){
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// إرسال المشروع
if(isset($_POST['submit_project'])){
    $title = trim($_POST['title']);
    if(isset($_FILES['project_file']) && $_FILES['project_file']['error']===0){
        $fileName = time().'_'.$_FILES['project_file']['name'];
        $fileTmp = $_FILES['project_file']['tmp_name'];
        $destination = '../uploads/'.$fileName;
        if(move_uploaded_file($fileTmp, $destination)){
            $stmt = $pdo->prepare("INSERT INTO projects (trainee_id, title, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $title, $fileName]);

            // إرسال إشعار للمدرب
            $stmtTrainer = $pdo->prepare("SELECT trainer_id FROM trainee_trainings WHERE trainee_id=? LIMIT 1");
            $stmtTrainer->execute([$user_id]);
            $trainer = $stmtTrainer->fetch();
            if($trainer){
                $trainer_id = $trainer['trainer_id'];
                $message = "المتدرب ".$_SESSION['fullname']." أرسل مشروع جديد: ".$title;
                $stmtNotif = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $stmtNotif->execute([$trainer_id, $message]);
            }

            $success = t('success');
        } else {
            $error = t('error_file');
        }
    } else {
        $error = t('error_choose');
    }
}

include '../inc/trainee_header.php';
?>

<div class="container mt-4">
    <h3 class="text-center mb-3"><?= t('page_title') ?></h3>

    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto">
        <div class="mb-3">
            <label><?= t('project_title') ?></label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label><?= t('choose_file') ?></label>
            <input type="file" name="project_file" class="form-control" required>
        </div>
        <button type="submit" name="submit_project" class="btn btn-primary w-100"><?= t('submit') ?></button>
    </form>

    <div class="mt-3 w-50 mx-auto">
        <a href="dashboard.php" class="btn btn-outline-dark w-100"><i class="bi bi-arrow-left-circle-fill"></i> <?= t('back') ?></a>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
