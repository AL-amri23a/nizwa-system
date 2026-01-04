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
        'daily_attendance' => 'تسجيل الحضور اليومي',
        'date' => 'تاريخ اليوم:',
        'status' => 'الحالة:',
        'present' => 'حاضر',
        'absent' => 'غائب',
        'late' => 'متأخر',
        'save' => 'حفظ',
        'attendance_recorded' => 'تم تسجيل الحضور بنجاح.',
        'attendance_updated' => 'تم تحديث الحضور بنجاح.',
        'training_not_set' => 'لم يتم تحديد مدة التدريب بعد. الرجاء الذهاب إلى <a href="set_training.php">صفحة تحديد مدة التدريب</a>.',
        'language' => 'اللغة',
        'arabic' => 'العربية',
        'english' => 'English',
        'back' => 'العودة',
    ],
    'en' => [
        'daily_attendance' => 'Daily Attendance',
        'date' => 'Date:',
        'status' => 'Status:',
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'save' => 'Save',
        'attendance_recorded' => 'Attendance recorded successfully.',
        'attendance_updated' => 'Attendance updated successfully.',
        'training_not_set' => 'Training duration not set yet. Please go to the <a href="set_training.php">set training page</a>.',
        'language' => 'Language',
        'arabic' => 'Arabic',
        'english' => 'English',
        'back' => 'Back',
    ]
];

// دالة استدعاء النصوص
function t($key) {
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// التأكد من صلاحية المتدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$trainee_id = $_SESSION['user_id'];
$message = "";

// جلب مدة التدريب
$stmt = $pdo->prepare("SELECT * FROM trainee_trainings WHERE trainee_id = ?");
$stmt->execute([$trainee_id]);
$training = $stmt->fetch();

if(!$training){
    echo "<p style='color:red; font-weight:bold;'>".t('training_not_set')."</p>";
    exit;
}

// تسجيل الحضور عند الإرسال
if(isset($_POST['submit'])){
    $date = $_POST['date'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("SELECT * FROM attendance WHERE trainee_id = ? AND date = ?");
    $stmt->execute([$trainee_id, $date]);
    $existing = $stmt->fetch();

    if($existing){
        $stmt = $pdo->prepare("UPDATE attendance SET status = ? WHERE trainee_id = ? AND date = ?");
        $stmt->execute([$status, $trainee_id, $date]);
        $message = t('attendance_updated');
    } else {
        $stmt = $pdo->prepare("INSERT INTO attendance (trainee_id, date, status) VALUES (?,?,?)");
        $stmt->execute([$trainee_id, $date, $status]);
        $message = t('attendance_recorded');
    }
}

$pageTitle = t('daily_attendance');
include '../inc/trainee_header.php';
?>

<style>
.attendance-box {
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    padding: 40px 30px;
    max-width: 500px;
    margin: 50px auto;
}

.attendance-box h3 {
    text-align: center;
    color: #1e69de;
    font-weight: bold;
    margin-bottom: 25px;
}

.form-control {
    border-radius: 10px;
    padding: 12px;
}

.btn-primary {
    background-color: #1e69de;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    padding: 12px;
    transition: background 0.3s;
}

.btn-primary:hover {
    background-color: #1452b0;
}

.alert {
    border-radius: 10px;
    text-align: center;
}

.btn-secondary {
    border-radius: 10px;
    font-weight: bold;
    padding: 10px 0;
}
</style>

<div class="attendance-box">
    <h3><?= htmlspecialchars($pageTitle) ?></h3>

    <?php if($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label><?= t('date') ?></label>
            <input type="date" name="date" class="form-control" required
                   min="<?= $training['start_date'] ?>" max="<?= $training['end_date'] ?>"
                   value="<?= date('Y-m-d') ?>">
        </div>

        <div class="mb-3">
            <label><?= t('status') ?></label>
            <select name="status" class="form-control" required>
                <option value="Present"><?= t('present') ?></option>
                <option value="Absent"><?= t('absent') ?></option>
                <option value="Late"><?= t('late') ?></option>
            </select>
        </div>
         <a href="dashboard.php" class="btn btn-secondary w-100">
    <?= t('back') ?>
</a>
        <button type="submit" name="submit" class="btn btn-primary w-100 mb-3"><?= t('save') ?></button>
    </form>
</div>
<?php include '../inc/footer.php'; ?>
