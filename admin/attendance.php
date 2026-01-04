<?php
require 'inc/config.php';
require 'inc/auth.php';
require_login();

$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = intval($_POST['enrollment_id']);
    $att_date = $_POST['attend_date'] ?: date('Y-m-d');
    $status = $_POST['status'] ?? 'present';
    $note = trim($_POST['note'] ?? null);
    $ins = $pdo->prepare("INSERT INTO attendance (enrollment_id, attend_date, status, note) VALUES (:enrol,:ad,:st,:note)");
    $ins->execute([':enrol'=>$enrollment_id, ':ad'=>$att_date, ':st'=>$status, ':note'=>$note]);
    header('Location: attendance.php');
    exit;
}

// fetch user's enrollments
$stmt = $pdo->prepare("SELECT e.id,e.start_date,e.end_date,c.title FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.user_id = :uid");
$stmt->execute([':uid'=>$uid]);
$enrolls = $stmt->fetchAll();
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>الحضور والغياب</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<div class="container">
  <h1>نموذج الحضور والغياب</h1>
  <?php if(empty($enrolls)){ echo "<p>ليس لديك اشتراكات في دورات حالياً.</p>"; } else { ?>
  <form method="post">
    <label>اختر الدورة</label>
    <select name="enrollment_id" required>
      <?php foreach($enrolls as $e){ echo "<option value='{$e['id']}'>".htmlspecialchars($e['title'])." (".htmlspecialchars($e['start_date']).")</option>"; } ?>
    </select>
    <label>تاريخ الحضور</label>
    <input type="date" name="attend_date" value="<?=date('Y-m-d')?>">
    <label>الحالة</label>
    <select name="status"><option value="present">حاضر</option><option value="absent">غائب</option></select>
    <label>ملاحظة</label>
    <input type="text" name="note">
    <button type="submit">حفظ</button>
  </form>
  <?php } ?>
</div>
</body>
</html>
<?php include 'inc/footer.php'; ?>
