<?php
session_start();
require '../config/db.php';

$error = '';
$email = $_GET['email'] ?? '';

// تحديد اللغة من الجلسة أو GET
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$language = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';

// النصوص لكل لغة
$translations = [
    'ar' => [
        'verify_title' => 'تحقق من الرمز',
        'instruction' => 'أدخل رمز التحقق المرسل إلى بريدك الإلكتروني.',
        'otp_placeholder' => 'أدخل رمز التحقق',
        'verify_btn' => 'تحقق',
        'resend' => 'إعادة إرسال الرمز',
        'otp_invalid' => 'رمز التحقق غير صحيح أو انتهت صلاحيته.',
        'email_not_found' => 'البريد الإلكتروني غير موجود.',
        'language' => 'اللغة',
        'arabic' => 'العربية',
        'english' => 'English',
    ],
    'en' => [
        'verify_title' => 'Verify OTP',
        'instruction' => 'Enter the verification code sent to your email.',
        'otp_placeholder' => 'Enter verification code',
        'verify_btn' => 'Verify',
        'resend' => 'Resend Code',
        'otp_invalid' => 'Invalid or expired verification code.',
        'email_not_found' => 'Email not found.',
        'language' => 'Language',
        'arabic' => 'Arabic',
        'english' => 'English',
    ]
];

// دالة مساعدة لاستدعاء النصوص
function t($key) {
    global $translations, $language;
    return $translations[$language][$key] ?? $key;
}

// معالجة التحقق من OTP
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $otp = trim($_POST['otp']);
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user){
        if($user['otp_code'] == $otp && strtotime($user['otp_expiry']) > time()){
            header("Location: reset_password.php?email=".urlencode($email));
            exit;
        } else {
            $error = t('otp_invalid');
        }
    } else {
        $error = t('email_not_found');
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>" dir="<?= $language=='ar'?'rtl':'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('verify_title') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
body {
    font-family: 'Cairo', sans-serif;
    background: linear-gradient(135deg, #2196f3, #0d47a1);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.verify-card {
    background: #fff;
    border-radius: 20px;
    padding: 40px 30px;
    width: 100%;
    max-width: 420px;
    text-align: center;
    position: relative;
    box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}
.verify-card h3 {
    color: #0d47a1;
    font-weight: 700;
    margin-bottom: 15px;
}
.verify-card p {
    color: #555;
    margin-bottom: 25px;
}
.form-control {
    border-radius: 50px;
    padding: 12px;
    margin-bottom: 20px;
    text-align: center;
}
.btn-primary {
    border-radius: 50px;
    background-color: #0d47a1;
    border: none;
    padding: 10px;
    font-weight: 600;
    width: 100%;
}
.btn-primary:hover {
    background-color: #1565c0;
}
.icon-circle {
    background: #0d47a1;
    color: white;
    font-size: 2.2rem;
    width: 70px;
    height: 70px;
    line-height: 70px;
    border-radius: 50%;
    display: inline-block;
    margin-bottom: 15px;
}
.language-switch {
    position: absolute;
    top: 10px;
    <?= $language=='ar'?'left:10px;':'right:10px;' ?>
    font-weight: 600;
}
.language-switch a {
    margin-left: 10px;
    margin-right: 10px;
}
</style>
</head>
<body>

<div class="verify-card">
    <!-- اختيار اللغة -->
    <div class="language-switch">
        <?= t('language') ?>:
        <a href="?lang=ar&email=<?= urlencode($email) ?>"><?= t('arabic') ?></a> | 
        <a href="?lang=en&email=<?= urlencode($email) ?>"><?= t('english') ?></a>
    </div>

    <div class="icon-circle"><i class="bi bi-shield-lock"></i></div>
    <h3><?= t('verify_title') ?></h3>
    <p><?= t('instruction') ?></p>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="text" name="otp" class="form-control" placeholder="<?= t('otp_placeholder') ?>" required maxlength="6">
        <button type="submit" class="btn btn-primary"><?= t('verify_btn') ?></button>
    </form>

    <div class="mt-3">
        <a href="forgot_password.php" class="text-decoration-none"><?= t('resend') ?></a>
    </div>
</div>

</body>
</html>
