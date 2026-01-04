<?php
session_start();
require '../config/db.php';

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
        'register_title' => 'تسجيل حساب جديد',
        'fullname' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'role' => 'اختر الدور',
        'role_trainee' => 'متدرب',
        'role_admin' => 'مشرف / مدرب',
        'create_btn' => 'إنشاء الحساب',
        'have_account' => 'هل لديك حساب؟ تسجيل الدخول',
        'email_exists' => 'هذا البريد الإلكتروني مسجل مسبقاً.',
        'success_msg' => 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.',
        'language' => 'اللغة',
        'arabic' => 'العربية',
        'english' => 'English',
    ],
    'en' => [
        'register_title' => 'Register New Account',
        'fullname' => 'Full Name',
        'email' => 'Email',
        'password' => 'Password',
        'role' => 'Select Role',
        'role_trainee' => 'Trainee',
        'role_admin' => 'Admin / Trainer',
        'create_btn' => 'Create Account',
        'have_account' => 'Already have an account? Login',
        'email_exists' => 'This email is already registered.',
        'success_msg' => 'Account created successfully! You can now login.',
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

// معالجة التسجيل
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = strtolower($_POST['role']);

    $check = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $check->execute([$email]);

    if($check->rowCount() > 0){
        $error = t('email_exists');
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$fullname, $email, $hashed_password, $role]);
        $success = t('success_msg');
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>" dir="<?= $language=='ar'?'rtl':'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('register_title') ?></title>
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
.register-card {
    background: #fff;
    border-radius: 20px;
    padding: 40px 30px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    transition: transform 0.3s;
    position: relative;
}
.register-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.4);
}
.register-card h3 {
    text-align: center;
    margin-bottom: 25px;
    color: #0d47a1;
    font-weight: 700;
}
.input-group {
    position: relative;
    margin-bottom: 20px;
}
.form-control, .form-select {
    border-radius: 50px;
    padding-left: 40px;
}
.form-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #0d47a1;
    font-size: 1.2rem;
}
.btn-success {
    border-radius: 50px;
    font-weight: 600;
    background-color: #0d47a1;
    border: none;
    transition: background 0.3s;
    width: 100%;
}
.btn-success:hover {
    background-color: #1565c0;
}
a {
    text-decoration: none;
    color: #0d47a1;
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

<div class="register-card">
    <!-- اختيار اللغة -->
    <div class="language-switch">
        <?= t('language') ?>:
        <a href="?lang=ar"><?= t('arabic') ?></a> | 
        <a href="?lang=en"><?= t('english') ?></a>
    </div>

    <h3><?= t('register_title') ?></h3>

    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <i class="bi bi-person form-icon"></i>
            <input type="text" name="fullname" class="form-control" placeholder="<?= t('fullname') ?>" required>
        </div>

        <div class="input-group">
            <i class="bi bi-envelope form-icon"></i>
            <input type="email" name="email" class="form-control" placeholder="<?= t('email') ?>" required>
        </div>

        <div class="input-group">
            <i class="bi bi-lock form-icon"></i>
            <input type="password" name="password" class="form-control" placeholder="<?= t('password') ?>" required minlength="6">
        </div>

        <div class="input-group">
            <i class="bi bi-person-badge form-icon"></i>
            <select name="role" class="form-select" required>
                <option value=""><?= t('role') ?></option>
                <option value="trainee"><?= t('role_trainee') ?></option>
                <option value="admin"><?= t('role_admin') ?></option>
            </select>
        </div>

        <button type="submit" class="btn btn-success"><?= t('create_btn') ?></button>

        <div class="text-center mt-3">
            <a href="login.php"><?= t('have_account') ?></a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
