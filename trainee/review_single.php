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
        'page_title' => 'مراجعة مشروع',
        'trainee' => 'المتدرب',
        'project_title' => 'عنوان المشروع',
        'download_file' => 'تنزيل الملف',
        'status' => 'الحالة',
        'received' => 'مستلم',
        'reviewed' => 'تمت المراجعة',
        'rejected' => 'مرفوض',
        'feedback' => 'ملاحظات المدرب',
        'save_review' => 'حفظ المراجعة',
        'back' => 'رجوع',
    ],
    'en' => [
        'page_title' => 'Project Review',
        'trainee' => 'Trainee',
        'project_title' => 'Project Title',
        'download_file' => 'Download File',
        'status' => 'Status',
        'received' => 'Received',
        'reviewed' => 'Reviewed',
        'rejected' => 'Rejected',
        'feedback' => 'Trainer Feedback',
        'save_review' => 'Save Review',
        'back' => 'Back',
    ]
];

function t($key){
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// التحقق من تسجيل المدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer'){
    header("Location: ../auth/login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: review_projects.php");
    exit;
}

$id = $_GET['id'];

// جلب بيانات المشروع
$stmt = $pdo->prepare("
    SELECT projects.*, users.fullname 
    FROM projects 
    JOIN users ON users.id = projects.trainee_id
    WHERE projects.id=?
");
$stmt->execute([$id]);
$project = $stmt->fetch();

if(!$project){
    header("Location: review_projects.php");
    exit;
}

$pageTitle = t('page_title');
include '../inc/trainer_header.php';
?>

<div class="container mt-4">

    <a href="review_projects.php" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-right"></i> <?= t('back') ?>
    </a>

    <div class="card shadow p-4 text-end">
        <h3><?= t('page_title') ?></h3>
        <p><strong><?= t('trainee') ?>:</strong> <?= htmlspecialchars($project['fullname']) ?></p>
        <p><strong><?= t('project_title') ?>:</strong> <?= htmlspecialchars($project['title']) ?></p>

        <a href="../uploads/<?= htmlspecialchars($project['file_path']) ?>" 
           class="btn btn-primary w-100 mb-3" download>
           <i class="bi bi-download"></i> <?= t('download_file') ?>
        </a>

        <form action="save_review.php" method="POST">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">

            <label class="form-label"><?= t('status') ?>:</label>
            <select name="status" class="form-select mb-3">
                <option value="مستلم" <?= $project['status']=="مستلم"?"selected":"" ?>><?= t('received') ?></option>
                <option value="تمت المراجعة" <?= $project['status']=="تمت المراجعة"?"selected":"" ?>><?= t('reviewed') ?></option>
                <option value="مرفوض" <?= $project['status']=="مرفوض"?"selected":"" ?>><?= t('rejected') ?></option>
            </select>

            <label class="form-label"><?= t('feedback') ?>:</label>
            <textarea name="feedback" class="form-control mb-3" rows="4"><?= htmlspecialchars($project['feedback']) ?></textarea>

            <button class="btn btn-success w-100">
                <i class="bi bi-check-circle"></i> <?= t('save_review') ?>
            </button>
        </form>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
