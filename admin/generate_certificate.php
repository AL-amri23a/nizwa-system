<?php
require '../config/db.php';
require '../lib/fpdf.php';

$trainee_id = $_GET['id'] ?? 0;

// جلب بيانات المتدرب
$stmt = $pdo->prepare("
    SELECT u.fullname, e.start_date, e.end_date
    FROM users u
    LEFT JOIN enrollments e ON u.id = e.trainee_id
    WHERE u.id = ?
");
$stmt->execute([$trainee_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Trainee not found");
}

// إنشاء PDF
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

// إطار الشهادة
$pdf->SetDrawColor(30,136,229);
$pdf->SetLineWidth(2);
$pdf->Rect(10, 10, 190, 277);

// شعار المديرية
$pdf->Image('../assets/images/ministry_logo.png', 80, 20, 50);
$pdf->Ln(50);

// عنوان
$pdf->SetFont('Arial','B',22);
$pdf->SetTextColor(21,101,192);
$pdf->Cell(0,15,'Certificate of Completion',0,1,'C');
$pdf->Ln(10);

// النص
$pdf->SetFont('Arial','',14);
$pdf->SetTextColor(0,0,0);
$pdf->MultiCell(0,10,
    "This is to certify that",
    0,'C'
);

$pdf->Ln(5);

// اسم المتدرب
$pdf->SetFont('Arial','B',20);
$pdf->SetTextColor(30,136,229);
$pdf->Cell(0,12, strtoupper($data['fullname']),0,1,'C');

$pdf->Ln(5);

// تفاصيل
$pdf->SetFont('Arial','',14);
$pdf->SetTextColor(0,0,0);
$pdf->MultiCell(0,10,
    "has successfully completed the training program\n".
    "at the Directorate of Education in Nizwa\n\n".
    "Training Period: ".$data['start_date']."  to  ".$data['end_date'],
    0,'C'
);

$pdf->Ln(15);

// التاريخ
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,'Issued Date: '.date('Y-m-d'),0,1,'C');

// حفظ الشهادة
$filename = "certificate_".$trainee_id.".pdf";
$path = "uploads/certificates/".$filename;
$pdf->Output('F', "../".$path);

// حفظ في قاعدة البيانات
$stmt = $pdo->prepare("
    INSERT INTO certificates (trainee_id, file_path, created_at)
    VALUES (?, ?, NOW())
");
$stmt->execute([$trainee_id, $path]);

echo "Certificate Generated Successfully";
