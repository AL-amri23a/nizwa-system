<?php
session_start();
require '../config/db.php';

// -----------------------
// التأكد من أن المستخدم مشرف
// -----------------------
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// -----------------------
// اللغة
// -----------------------
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])){
    $_SESSION['lang'] = $_GET['lang'];
}
$language = $_SESSION['lang'] ?? 'ar';

// -----------------------
// نصوص لكل لغة
// -----------------------
$texts = [
    'ar' => [
        'title' => 'الشهادات',
        'list' => 'قائمة المتدربين',
        'trainee_name' => 'اسم المتدرب',
        'created_at' => 'تاريخ الإنشاء',
        'certificate_link' => 'رابط الشهادة',
        'actions' => 'الإجراءات',
        'create_certificate' => 'إنشاء شهادة',
        'download' => 'تحميل / طباعة',
        'delete' => 'حذف الشهادة',
        'no_certificates' => 'لا توجد شهادات حتى الآن.'
    ],
    'en' => [
        'title' => 'Certificates',
        'list' => 'Trainees List',
        'trainee_name' => 'Trainee Name',
        'created_at' => 'Creation Date',
        'certificate_link' => 'Certificate Link',
        'actions' => 'Actions',
        'create_certificate' => 'Create Certificate',
        'download' => 'Download / Print',
        'delete' => 'Delete Certificate',
        'no_certificates' => 'No certificates available.'
    ]
];

function t($key){
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// -----------------------
// استدعاء TCPDF
// -----------------------
require_once __DIR__ . '/TCPDF-main/tcpdf.php';

// -----------------------
// إنشاء شهادة
// -----------------------
if(isset($_GET['create_certificate']) && isset($_GET['trainee_id'])){
    $trainee_id = intval($_GET['trainee_id']);

    $stmt = $pdo->prepare("SELECT fullname FROM users WHERE id=?");
    $stmt->execute([$trainee_id]);
    $user = $stmt->fetch();

    if($user){
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Organization');
        $pdf->SetTitle('شهادة تدريب');
        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(false,0);
        $pdf->AddPage();

        // -----------------------
        // خط عربي DejaVuSans
        // -----------------------
        $pdf->SetFont('dejavusans', 'B', 36);

        // خلفية الشهادة
        $pdf->Image('../certificates/certificate_bg.png', 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);

        // عنوان الشهادة
        $pdf->SetXY(0, 40);
        $pdf->Cell(297, 20, 'شهادة تدريب', 0, 1, 'C');

        // اسم المتدرب
        $pdf->SetXY(0, 80);
        $pdf->Cell(297, 20, $user['fullname'], 0, 1, 'C');

        // نص إضافي
        $pdf->SetFont('dejavusans', '', 20);
        $pdf->SetXY(20, 115);
        $pdf->MultiCell(257, 10, 'نمنح هذه الشهادة للمتدرب المذكور أعلاه لإتمامه البرنامج التدريبي بنجاح', 0, 'C');

        // التاريخ ورقم الشهادة
        $certificate_number = "CERT-" . $trainee_id . "-" . time();
        $pdf->SetXY(20, 145);
        $pdf->Cell(0, 10, 'التاريخ: ' . date('Y-m-d'), 0, 1, 'C');
        $pdf->SetXY(20, 155);
        $pdf->Cell(0, 10, 'رقم الشهادة: ' . $certificate_number, 0, 1, 'C');

        // توقيع وختم
        $pdf->Image('../certificates/signature.png', 220, 150, 60, 40, '', '', '', false, 300, '', false, false, 0);

        // -----------------------
        // مسار مطلق لمجلد certificates
        // -----------------------
        $certificates_dir = __DIR__ . '/../certificates/';
        if(!is_dir($certificates_dir)){
            mkdir($certificates_dir, 0777, true); // إنشاء المجلد إذا غير موجود
        }

        $file_name = "certificate_" . $trainee_id . "_" . time() . ".pdf";
        $file_path = $certificates_dir . $file_name;

        // حفظ PDF
        $pdf->Output($file_path, 'F');

        // مسار نسبي لحفظه في قاعدة البيانات
        $relative_path = 'certificates/' . $file_name;
        $stmt = $pdo->prepare("INSERT INTO certificates (trainee_id, file_path, certificate_number) VALUES (?, ?, ?)");
        $stmt->execute([$trainee_id, $relative_path, $certificate_number]);

        header("Location: print_certificates.php?success=1");
        exit;
    }
}

// -----------------------
// حذف شهادة
// -----------------------
if(isset($_GET['delete_certificate']) && isset($_GET['cert_id'])){
    $cert_id = intval($_GET['cert_id']);
    $stmt = $pdo->prepare("SELECT file_path FROM certificates WHERE id=?");
    $stmt->execute([$cert_id]);
    $cert = $stmt->fetch();
    if($cert){
        $absolute_file_path = __DIR__ . '/../' . $cert['file_path'];
        if(file_exists($absolute_file_path)){
            unlink($absolute_file_path); // حذف الملف
        }
        $stmt = $pdo->prepare("DELETE FROM certificates WHERE id=?");
        $stmt->execute([$cert_id]);
    }
    header("Location: print_certificates.php");
    exit;
}

// -----------------------
// جلب المتدربين مع شهاداتهم
// -----------------------
$stmt = $pdo->query("SELECT u.id, u.fullname, c.file_path, c.id as cert_id, c.created_at
                     FROM users u
                     LEFT JOIN certificates c ON u.id = c.trainee_id
                     ORDER BY u.fullname ASC");
$trainees = $stmt->fetchAll();

include '../inc/admin_header.php';
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="container mt-5">
    <h2 class="mb-4 text-center"><?= t('title') ?></h2>

    <div class="card p-3">
        <div class="card-header text-center"><?= t('list') ?></div>
        <div class="card-body">
            <table class="table table-striped table-bordered text-center" id="traineesTable">
                <thead>
                    <tr>
                        <th><?= t('trainee_name') ?></th>
                        <th><?= t('created_at') ?></th>
                        <th><?= t('certificate_link') ?></th>
                        <th><?= t('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
<?php if($trainees): ?>
    <?php foreach($trainees as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['fullname']) ?></td>
            <td><?= $t['created_at'] ?? '-' ?></td>
            <td>
                <?php if($t['file_path'] && file_exists(__DIR__ . '/../' . $t['file_path'])): ?>
                    <a href="<?= '../'.$t['file_path'] ?>" target="_blank">
                        <?= t('download') ?>
                    </a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td>
                <a href="?create_certificate=1&trainee_id=<?= $t['id'] ?>" 
                   class="btn btn-success btn-sm">
                   <?= t('create_certificate') ?>
                </a>
                <?php if($t['file_path']): ?>
                <a href="?delete_certificate=1&cert_id=<?= $t['cert_id'] ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('هل أنت متأكد من حذف هذه الشهادة؟');">
                   <?= t('delete') ?>
                </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="4"><?= t('no_certificates') ?></td>
    </tr>
<?php endif; ?>
<!-- زر الرجوع -->
<div style="margin: 10px 0;">
    <button onclick="history.back();" class="btn btn-secondary">
        ⬅ رجوع
    </button>
</div>

</tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#traineesTable').DataTable({
        "language": {
            "url": "<?= $language === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' : '' ?>"
        },
        "order": [[0, "asc"]]
    });
});
</script>
<?php include '../inc/footer.php'; ?>
