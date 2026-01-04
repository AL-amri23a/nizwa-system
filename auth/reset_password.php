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
        'reset_title' => 'إعادة تعيين كلمة المرور',
        'new_password' => 'كلمة المرور الجديدة',
        'new_password_placeholder' => 'أدخل كلمة المرور الجديدة',
        'update_btn' => 'تحديث كلمة المرور',
        'back_login' => 'العودة إلى تسجيل الدخول',
        'email_not_found' => 'حدث خطأ، البريد غير موجود.',
        'language' => 'اللغة',
        'arabic' => 'العربية',
        'english' => 'English',
    ],
    'en' => [
        'reset_title' => 'Reset Password',
        'new_password' => 'New Password',
        'new_password_placeholder' => 'Enter new password',
        'update_btn' => 'Update Password',
        'back_login' => 'Back to Login',
        'email_not_found' => 'Error, email not found.',
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

// معالجة تحديث كلمة المرور
if($_SERVER['REQUEST_METHOD']==='POST'){
    $new_password = trim($_POST['new_password']);
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user){
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password=?, otp_code=NULL, otp_expiry=NULL WHERE id=?");
        $update->execute([$hashed, $user['id']]);

        header("Location: login.php?reset=success");
        exit;
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
<title><?= t('reset_title') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Cairo', sans-serif;
    background: linear-gradient(135deg, #1e69de, #6fb1fc);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.reset-box {
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    padding: 40px 30px;
    width: 100%;
    max-width: 420px;
    position: relative;
}
h3 {
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
    width: 100%;
}
.btn-primary:hover {
    background-color: #1452b0;
}
.alert {
    border-radius: 10px;
    text-align: center;
}
a {
    color: #1e69de;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
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

<div class="reset-box">
    <!-- اختيار اللغة -->
    <div class="language-switch">
        <?= t('language') ?>:
        <a href="?lang=ar&email=<?= urlencode($email) ?>"><?= t('arabic') ?></a> | 
        <a href="?lang=en&email=<?= urlencode($email) ?>"><?= t('english') ?></a>
    </div>

    <h3><?= t('reset_title') ?></h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <div class="mb-3">
            <label class="form-label"><?= t('new_password') ?></label>
            <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="<?= t('new_password_placeholder') ?>">
        </div>
        <button type="submit" class="btn btn-primary"><?= t('update_btn') ?></button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php"><?= t('back_login') ?></a>
    </div>
</div>

</body>
</html>
