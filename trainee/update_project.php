<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

// إدارة اللغة
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])){
    $_SESSION['lang'] = $_GET['lang'];
}
$language = $_SESSION['lang'] ?? 'ar';

// نصوص اللغات
$texts = [
    'ar' => [
        'project_not_found' => 'المشروع غير موجود.',
        'not_allowed' => 'المشروع غير موجود أو غير مصرح لك بالاطلاع عليه.',
        'status' => 'الحالة',
        'view_project' => 'عرض المشروع',
        'feedback_title' => 'ملاحظات المدرب:',
        'no_feedback' => 'لا توجد ملاحظات من المدرب بعد.'
    ],
    'en' => [
        'project_not_found' => 'Project not found.',
        'not_allowed' => 'Project not found or you are not authorized to view it.',
        'status' => 'Status',
        'view_project' => 'View Project',
        'feedback_title' => "Trainer's Feedback:",
        'no_feedback' => 'No feedback from trainer yet.'
    ]
];

function t($key){
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

$project_id = $_GET['id'] ?? null;
if(!$project_id){
    echo t('project_not_found');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM projects WHERE id=? AND trainee_id=?");
$stmt->execute([$project_id, $_SESSION['user_id']]);
$project = $stmt->fetch();

if(!$project){
    echo t('not_allowed');
    exit;
}

include '../inc/trainee_header.php';
?>

<div class="container mt-4">
    <h3><?= htmlspecialchars($project['title']) ?></h3>
    <p><?= t('status') ?>: <?= htmlspecialchars($project['status']) ?></p>

    <a href="../uploads/<?= $project['file_path'] ?>" target="_blank" class="btn btn-primary">
        <?= t('view_project') ?>
    </a>

    <?php if(!empty($project['feedback'])): ?>
        <div class="mt-3 p-3 border rounded" style="background-color:#f0f8ff;">
            <h5><?= t('feedback_title') ?></h5>
            <p><?= nl2br(htmlspecialchars($project['feedback'])) ?></p>
        </div>
    <?php else: ?>
        <p class="mt-3 text-muted"><?= t('no_feedback') ?></p>
    <?php endif; ?>
</div>

<?php include '../inc/footer.php'; ?>
