<?php
session_start();
require '../config/db.php';

// تحديد اللغة (افتراضي عربي)
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])){
    $_SESSION['lang'] = $_GET['lang'];
}
$language = $_SESSION['lang'] ?? 'ar';

// نصوص لكل لغة
$texts = [
    'ar' => [
        'page_title' => 'تحديد مدة التدريب',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'save' => 'حفظ',
        'success_insert' => 'تم تسجيل مدة التدريب بنجاح.',
        'success_update' => 'تم تحديث مدة التدريب بنجاح.',
        'back' => 'العودة للوحة المتدرب'
    ],
    'en' => [
        'page_title' => 'Set Training Duration',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'save' => 'Save',
        'success_insert' => 'Training duration recorded successfully.',
        'success_update' => 'Training duration updated successfully.',
        'back' => 'Back to Trainee Dashboard'
    ]
];

function t($key){
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// تأكد أن المستخدم متدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$trainee_id = $_SESSION['user_id'];
$message = "";

// عند الضغط على حفظ
if(isset($_POST['save'])){
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // تحقق إذا يوجد سجل مسبق
    $stmt = $pdo->prepare("SELECT * FROM trainee_trainings WHERE trainee_id = ?");
    $stmt->execute([$trainee_id]);
    $existing = $stmt->fetch();

    if($existing){
        // تحديث
        $stmt = $pdo->prepare("UPDATE trainee_trainings SET start_date = ?, end_date = ? WHERE trainee_id = ?");
        $stmt->execute([$start_date, $end_date, $trainee_id]);
        $message = t('success_update');
    } else {
        // إدخال جديد
        $stmt = $pdo->prepare("INSERT INTO trainee_trainings (trainee_id, start_date, end_date) VALUES (?,?,?)");
        $stmt->execute([$trainee_id, $start_date, $end_date]);
        $message = t('success_insert');
    }
}

include '../inc/trainee_header.php';
?>

<div class="container" style="margin-top:50px; max-width:500px;">
    <h3><?= t('page_title') ?></h3>

    <?php if($message): ?>
        <p style="color:green; font-weight:bold;"><?=$message?></p>
    <?php endif; ?>

    <form method="post">
        <label><?= t('start_date') ?>:</label>
        <input type="date" name="start_date" class="form-control" required><br>

        <label><?= t('end_date') ?>:</label>
        <input type="date" name="end_date" class="form-control" required><br>

        <button type="submit" name="save" class="btn btn-primary"><?= t('save') ?></button>
    </form>

    <div class="mt-3">
        <a href="dashboard.php" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left-circle-fill"></i> <?= t('back') ?>
        </a>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
