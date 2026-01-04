١<?php
session_start();
require '../config/db.php';

/* =========================
   Language Handling
========================= */
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$language = $_SESSION['lang'] ?? 'ar';

$texts = [
    'ar' => [
        'certificate_title' => '📜 الشهادة',
        'download_text' => 'يمكنك تحميل شهادتك من خلال الرابط التالي:',
        'download_btn' => 'تحميل الشهادة',
        'not_issued' => 'لم يتم إصدار شهادة حتى الآن.',
        'back' => 'العودة',
    ],
    'en' => [
        'certificate_title' => '📜 Certificate',
        'download_text' => 'You can download your certificate from the link below:',
        'download_btn' => 'Download Certificate',
        'not_issued' => 'Certificate has not been issued yet.',
        'back' => 'Back',
    ]
];

function t($key) {
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

/* =========================
   Auth Check
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainee') {
    header("Location: ../auth/login.php");
    exit;
}

$pageTitle = t('certificate_title');
include '../inc/trainee_header.php';

/* =========================
   Fetch Certificate
========================= */
$stmt = $pdo->prepare("
    SELECT file_path 
    FROM certificates 
    WHERE trainee_id = ? 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$cert = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="card shadow p-4 text-end" style="border-radius:18px;">
        <h3 class="mb-4 text-center" style="color:#0d47a1; font-weight:bold;">
            <?= t('certificate_title') ?>
        </h3>

        <?php if ($cert && !empty($cert['file_path'])): ?>

            <p class="text-center" style="font-size:1.1rem;">
                <?= t('download_text') ?>
            </p>

            <div class="text-center mt-4">
                <a href="../uploads/certificates/<?= basename($cert['file_path']) ?>"
                   target="_blank"
                   class="btn btn-primary px-4 py-2"
                   style="font-size:1.1rem; border-radius:30px;">
                    <?= t('download_btn') ?>
                </a>
            </div>

        <?php else: ?>

            <p class="text-center text-danger" style="font-size:1.1rem;">
                <?= t('not_issued') ?>
            </p>

        <?php endif; ?>
    </div>
</div>

<div class="container mt-3">
    <a href="dashboard.php" class="btn btn-secondary w-100">
        <?= t('back') ?>
    </a>
</div>

<?php include '../inc/footer.php';?>
