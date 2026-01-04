<?php
session_start();
require '../config/db.php';


if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])){
    $_SESSION['lang'] = $_GET['lang'];
}
$language = $_SESSION['lang'] ?? 'ar';

$texts = [
    'ar' => [
        'page_title' => 'إضافة شهادة جديدة',
        'trainee' => 'اختر المتدرب',
        'choose_file' => 'اختر ملف الشهادة',
        'submit' => 'إضافة الشهادة',
        'success' => 'تم إضافة الشهادة بنجاح!',
        'error_file' => 'حدث خطأ أثناء رفع الملف.',
        'error_choose' => 'الرجاء اختيار ملف الشهادة.',
        'back' => 'العودة للوحة التحكم'
    ],
    'en' => [
        'page_title' => 'Add New Certificate',
        'trainee' => 'Select Trainee',
        'choose_file' => 'Choose Certificate File',
        'submit' => 'Add Certificate',
        'success' => 'Certificate added successfully!',
        'error_file' => 'Error uploading the file.',
        'error_choose' => 'Please choose a certificate file.',
        'back' => 'Back to Dashboard'
    ]
];

function t($key){
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

$stmt = $pdo->query("SELECT id, fullname FROM users WHERE role='trainee'");
$trainees = $stmt->fetchAll();

$error = '';
$success = '';

if(isset($_POST['submit'])){
    $trainee_id = $_POST['trainee_id'] ?? 0;

    if(isset($_FILES['certificate_file']) && $_FILES['certificate_file']['error'] === 0){
        $uploadDir = '../certificates/';
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }

        $filename = time().'_'.basename($_FILES['certificate_file']['name']);
        $destination = $uploadDir . $filename;

        if(move_uploaded_file($_FILES['certificate_file']['tmp_name'], $destination)){
            $stmt2 = $pdo->prepare("INSERT INTO certificates (trainee_id, file_path) VALUES (?, ?)");
            $stmt2->execute([$trainee_id, $filename]);
            $success = t('success');

            $message = "تمت إضافة شهادة جديدة لك.";
            $stmt3 = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt3->execute([$trainee_id, $message]);

        } else {
            $error = t('error_file');
        }
    } else {
        $error = t('error_choose');
    }
}

include '../inc/admin_header.php';
?>

<div class="container mt-4">
    <h3 class="text-center mb-3"><?= t('page_title') ?></h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="w-50 mx-auto">
        <div class="mb-3">
            <label><?= t('trainee') ?></label>
            <select name="trainee_id" class="form-control" required>
                <option value="">-- <?= t('trainee') ?> --</option>
                <?php foreach($trainees as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['fullname']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label><?= t('choose_file') ?></label>
            <input type="file" name="certificate_file" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary w-100"><?= t('submit') ?></button>
    </form>

    <div class="mt-3 w-50 mx-auto">
        <a href="dashboard.php" class="btn btn-outline-dark w-100">
            <i class="bi bi-arrow-left-circle-fill"></i> <?= t('back') ?>
        </a>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
