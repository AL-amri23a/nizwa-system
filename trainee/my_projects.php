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
        'my_projects' => 'مشاريعي',
        'add_project' => 'إضافة مشروع جديد',
        'no_projects' => 'لا توجد مشاريع بعد.',
        'status' => 'الحالة',
        'last_update' => 'آخر تحديث',
        'back' => 'العودة',
    ],
    'en' => [
        'my_projects' => 'My Projects',
        'add_project' => 'Add New Project',
        'no_projects' => 'No projects yet.',
        'status' => 'Status',
        'last_update' => 'Last Update',
        'back' => 'Back',
    ]
];

// دالة استدعاء النصوص
function t($key) {
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// التأكد من تسجيل المتدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$pageTitle = t('my_projects');
include '../inc/trainee_header.php';

// جلب المشاريع
$stmt = $pdo->prepare("SELECT * FROM projects WHERE trainee_id=? ORDER BY submitted_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll();
?>

<div class="container text-center mt-4">
    
    <a href="submit_project.php" class="btn btn-primary mb-4">
        <i class="bi bi-plus-circle"></i> <?= t('add_project') ?>
    </a>

    <?php if(!$projects): ?>
        <p class="text-center text-muted"><?= t('no_projects') ?></p>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-center gap-4">
        <?php foreach($projects as $p): ?>
            <a href="update_project.php?id=<?= $p['id'] ?>" class="text-decoration-none">
                <div class="dashboard-card" style="width:280px;">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <h4><?= htmlspecialchars($p['title']) ?></h4>
                    <p><?= t('status') ?>: <?= htmlspecialchars($p['status']) ?></p>
                    <p style="font-size:12px; color:#555;"><?= t('last_update') ?>: <?= $p['submitted_at'] ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<a href="dashboard.php" class="btn btn-secondary w-100">
    <?= t('back') ?>
</a>
<?php include '../inc/footer.php'; ?>
