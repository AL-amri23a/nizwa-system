<?php
session_start();
require '../config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-PHPMailer-19debc7/src/PHPMailer.php';
require '../PHPMailer-PHPMailer-19debc7/src/SMTP.php';
require '../PHPMailer-PHPMailer-19debc7/src/Exception.php';

$error = '';
$success = '';

// تحديد اللغة من الجلسة أو GET
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$language = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';

// النصوص لكل لغة
$translations = [
    'ar' => [
        'forgot_title' => 'نسيت كلمة المرور',
        'instruction' => 'أدخل بريدك الإلكتروني لإرسال رمز التحقق.',
        'email_placeholder' => 'أدخل البريد الإلكتروني',
        'send_btn' => 'إرسال الرمز',
        'back_login' => 'العودة إلى تسجيل الدخول',
        'email_not_found' => 'البريد الإلكتروني غير مسجل لدينا.',
        'mail_error' => 'حدث خطأ أثناء إرسال البريد الإلكتروني. حاول مرة أخرى.',
        'language' => 'اللغة',
        'arabic' => 'العربية',
        'english' => 'English',
    ],
    'en' => [
        'forgot_title' => 'Forgot Password',
        'instruction' => 'Enter your email to receive a verification code.',
        'email_placeholder' => 'Enter your email',
        'send_btn' => 'Send Code',
        'back_login' => 'Back to Login',
        'email_not_found' => 'Email not registered.',
        'mail_error' => 'Error sending email. Please try again.',
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

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user){
        $otp_code = rand(100000,999999);
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $update = $pdo->prepare("UPDATE users SET otp_code=?, otp_expiry=? WHERE id=?");
        $update->execute([$otp_code, $otp_expiry, $user['id']]);

        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'collegeme23@gmail.com';
            $mail->Password = 'bnypaqhfxsrokfdf';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('collegeme23@gmail.com', 'Training System');
            $mail->addAddress($user['email'], $user['fullname']);

            $mail->isHTML(true);
            $mail->Subject = $language=='ar'?'رمز التحقق لإعادة تعيين كلمة المرور':'Password Reset Verification Code';
            $mail->Body = $language=='ar'?
                "<h3>رمز التحقق الخاص بك هو:</h3><h2 style='color:#0d47a1;'>$otp_code</h2><p>الرمز صالح لمدة 10 دقائق.</p>"
                :
                "<h3>Your verification code is:</h3><h2 style='color:#0d47a1;'>$otp_code</h2><p>The code is valid for 10 minutes.</p>";

            $mail->send();

            header("Location: verify_otp.php?email=".urlencode($email));
            exit;
        } catch(Exception $e){
            $error = t('mail_error');
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
<title><?= t('forgot_title') ?></title>
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
    .forgot-card {
        background: #fff;
        border-radius: 20px;
        padding: 40px 30px;
        width: 100%;
        max-width: 420px;
        text-align: center;
        position: relative;
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }
    .forgot-card h3 {
        color: #0d47a1;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .forgot-card p {
        color: #555;
        margin-bottom: 25px;
    }
    .form-control {
        border-radius: 50px;
        padding: 12px;
        margin-bottom: 20px;
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

<div class="forgot-card">
    <!-- اختيار اللغة -->
    <div class="language-switch">
        <?= t('language') ?>:
        <a href="?lang=ar"><?= t('arabic') ?></a> | 
        <a href="?lang=en"><?= t('english') ?></a>
    </div>

    <div class="icon-circle"><i class="bi bi-envelope-paper"></i></div>
    <h3><?= t('forgot_title') ?></h3>
    <p><?= t('instruction') ?></p>

    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST">
        <input type="email" name="email" class="form-control" placeholder="<?= t('email_placeholder') ?>" required>
        <button type="submit" class="btn btn-primary"><?= t('send_btn') ?></button>
    </form>

    <div class="mt-3">
        <a href="login.php" class="text-decoration-none"><?= t('back_login') ?></a>
    </div>
</div>

</body>
</html>
