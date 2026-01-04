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
        'page_title' => 'مشاريع المتدربين',
        'trainee' => 'المتدرب',
        'status' => 'الحالة',
        'submitted_at' => 'تاريخ التسليم',
        'download_file' => 'تنزيل الملف',
        'review_project' => 'مراجعة المشروع',
        'no_projects' => 'لا توجد مشاريع حالياً.',
        'back_dashboard' => 'رجوع',
    ],
    'en' => [
        'page_title' => 'Trainee Projects',
        'trainee' => 'Trainee',
        'status' => 'Status',
        'submitted_at' => 'Submitted At',
        'download_file' => 'Download File',
        'review_project' => 'Review Project',
        'no_projects' => 'No projects available.',
        'back_dashboard' => 'Back',
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

$pageTitle = t('page_title');
include '../inc/trainer_header.php';

// جلب المشاريع والمتدربين
$stmt = $pdo->query("
    SELECT projects.*, users.fullname 
    FROM projects 
    JOIN users ON users.id = projects.trainee_id
    ORDER BY submitted_at DESC
");
$projects = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="text-end mb-4">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-right"></i> <?= t('back_dashboard') ?>
        </a>
    </div>

    <h3 class="text-center mb-4"><?= t('page_title') ?></h3>

    <div class="row justify-content-center">
        <?php if ($projects): ?>
            <?php foreach ($projects as $p): ?>
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm p-3 text-end">
                        <h5><?= htmlspecialchars($p['title']) ?></h5>
                        <p><strong><?= t('trainee') ?>:</strong> <?= htmlspecialchars($p['fullname']) ?></p>
                        <p><strong><?= t('status') ?>:</strong> <?= $p['status'] ?></p>
                        <p><strong><?= t('submitted_at') ?>:</strong> <?= $p['submitted_at'] ?></p>

                        <a href="../uploads/<?= htmlspecialchars($p['file_path']) ?>" 
                           class="btn btn-primary w-100 mb-2" download>
                           <i class="bi bi-download"></i> <?= t('download_file') ?>
                        </a>

                        <a href="review_single.php?id=<?= $p['id'] ?>" 
                           class="btn btn-success w-100">
                           <i class="bi bi-pencil-square"></i> <?= t('review_project') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center"><?= t('no_projects') ?></p>
        <?php endif; ?>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
