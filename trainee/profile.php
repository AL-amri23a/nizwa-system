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
        'profile_title' => 'ملفي الشخصي',
        'profile_desc' => 'يمكنك تحديث بياناتك الشخصية هنا',
        'fullname' => 'الاسم الكامل',
        'national_id' => 'الرقم المدني',
        'university' => 'الجامعة / الكلية',
        'specialization' => 'نوع التخصص',
        'education_status' => 'الحالة التعليمية',
        'student' => 'طالب',
        'graduate' => 'خريج',
        'phone' => 'رقم الهاتف',
        'update_btn' => 'تحديث البيانات',
        'back_dashboard' => 'العودة للوحة المتدرب',
        'success_update' => 'تم تحديث البيانات بنجاح!',
        'error_update' => 'حدث خطأ أثناء تحديث البيانات.',
    ],
    'en' => [
        'profile_title' => 'My Profile',
        'profile_desc' => 'You can update your personal information here',
        'fullname' => 'Full Name',
        'national_id' => 'National ID',
        'university' => 'University / College',
        'specialization' => 'Specialization',
        'education_status' => 'Education Status',
        'student' => 'Student',
        'graduate' => 'Graduate',
        'phone' => 'Phone Number',
        'update_btn' => 'Update Information',
        'back_dashboard' => 'Back to Trainee Dashboard',
        'success_update' => 'Information updated successfully!',
        'error_update' => 'An error occurred while updating.',
    ]
];

// دالة استدعاء النصوص
function t($key) {
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// التحقق من تسجيل المتدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

$success = $error = '';
$user_id = $_SESSION['user_id'];

// جلب بيانات المستخدم
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// تحديث البيانات عند الإرسال
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = trim($_POST['fullname']);
    $national_id = trim($_POST['national_id']);
    $university = trim($_POST['university']);
    $specialization = trim($_POST['specialization']);
    $education_status = $_POST['education_status'];
    $phone = trim($_POST['phone']);

    $update = $pdo->prepare("
        UPDATE users SET fullname=?, national_id=?, university=?, specialization=?, education_status=?, phone=? 
        WHERE id=?
    ");
    if($update->execute([$fullname, $national_id, $university, $specialization, $education_status, $phone, $user_id])){
        $success = t('success_update');
        $_SESSION['fullname'] = $fullname;
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } else {
        $error = t('error_update');
    }
}

include '../inc/trainee_header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-sm-10">
            <div class="card shadow-lg p-4 rounded-4" style="background: linear-gradient(145deg, #e3f2fd, #bbdefb);">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle" style="font-size:50px; color:#0d47a1;"></i>
                    <h3 class="mt-2 fw-bold"><?= t('profile_title') ?></h3>
                    <p class="text-muted"><?= t('profile_desc') ?></p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger rounded-3"><?= $error ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success rounded-3"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= t('fullname') ?></label>
                        <input type="text" name="fullname" class="form-control form-control-lg" required value="<?= htmlspecialchars($user['fullname']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= t('national_id') ?></label>
                        <input type="text" name="national_id" class="form-control form-control-lg" value="<?= htmlspecialchars($user['national_id']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= t('university') ?></label>
                        <input type="text" name="university" class="form-control form-control-lg" value="<?= htmlspecialchars($user['university']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= t('specialization') ?></label>
                        <input type="text" name="specialization" class="form-control form-control-lg" value="<?= htmlspecialchars($user['specialization']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= t('education_status') ?></label>
                        <select name="education_status" class="form-select form-select-lg">
                            <option value="طالب" <?= $user['education_status']=='طالب' ? 'selected' : '' ?>><?= t('student') ?></option>
                            <option value="خريج" <?= $user['education_status']=='خريج' ? 'selected' : '' ?>><?= t('graduate') ?></option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= t('phone') ?></label>
                        <input type="text" name="phone" class="form-control form-control-lg" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><?= t('update_btn') ?></button>
                    </div>

                    <div class="col-12 mt-2">
                        <a href="dashboard.php" class="btn btn-outline-dark w-100 py-2">
                            <i class="bi bi-arrow-left-circle-fill"></i> <?= t('back_dashboard') ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
