<?php
session_start();
require '../config/db.php';

// التأكد أن المستخدم متدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

include '../inc/trainee_header.php';

// اللغة الحالية
$lang = $_SESSION['lang'] ?? 'ar';

// النصوص حسب اللغة
$texts = [
    'ar' => [
        'title' => 'رفع خطاب التدريب',
        'desc' => 'يمكنك رفع ملف PDF الخاص بخطاب التدريب',
        'choose' => 'اختر الملف',
        'upload' => 'رفع الملف',
        'back' => 'العودة للوحة المتدرب',
        'success' => 'تم رفع خطاب التدريب بنجاح!',
        'error_upload' => 'حدث خطأ أثناء رفع الملف.',
        'error_no_file' => 'يرجى اختيار ملف للرفع.'
    ],
    'en' => [
        'title' => 'Upload Training Letter',
        'desc' => 'You can upload your training letter (PDF)',
        'choose' => 'Choose File',
        'upload' => 'Upload File',
        'back' => 'Back to Trainee Dashboard',
        'success' => 'Training letter uploaded successfully!',
        'error_upload' => 'An error occurred during file upload.',
        'error_no_file' => 'Please select a file to upload.'
    ]
];

$t = $texts[$lang];

$error = $success = "";

// عند رفع الملف
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_FILES['training_letter']) && $_FILES['training_letter']['error'] == 0){

        $filename = time() . "_" . $_FILES['training_letter']['name']; // تجنب التكرار
        $tmpname = $_FILES['training_letter']['tmp_name'];
        $folder = '../uploads/letters/' . $filename;

        if(move_uploaded_file($tmpname, $folder)){
            $stmt = $pdo->prepare("UPDATE users SET training_letter=? WHERE id=?");
            $stmt->execute([$filename, $_SESSION['user_id']]);
            $success = $t['success'];
        } else {
            $error = $t['error_upload'];
        }
    } else {
        $error = $t['error_no_file'];
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-sm-10">
            <div class="card shadow-lg p-4 rounded-4 border-0" 
                 style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">

                <div class="text-center mb-4">
                    <i class="bi bi-file-earmark-arrow-up-fill" 
                       style="font-size: 50px; color:#0d47a1;"></i>

                    <h3 class="mt-3" style="font-weight:700;"><?= $t['title'] ?></h3>
                    <p class="text-muted"><?= $t['desc'] ?></p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger rounded-3"><?= $error ?></div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="alert alert-success rounded-3"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?= $t['choose'] ?></label>
                        <input type="file" name="training_letter" 
                               class="form-control form-control-lg" 
                               accept=".pdf" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <?= $t['upload'] ?>
                    </button>
                </form>

                <hr class="my-4">

                <a href="dashboard.php" class="btn btn-outline-dark w-100 py-2">
                    <i class="bi bi-arrow-left-circle-fill"></i> <?= $t['back'] ?>
                </a>

            </div>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
