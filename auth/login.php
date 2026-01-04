<?php
session_start();
require '../config/db.php';

$error = '';

// تحديد اللغة من الجلسة أو GET
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$language = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';

// النصوص لكل لغة
$translations = [
    'ar' => [
        'login_title' => 'تسجيل الدخول',
        'email_placeholder' => 'البريد الإلكتروني',
        'password_placeholder' => 'كلمة المرور',
        'login_btn' => 'تسجيل الدخول',
        'forgot_password' => 'نسيت كلمة المرور؟',
        'register' => 'ليس لديك حساب؟ سجل هنا',
        'invalid_credentials' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        'language' => 'اللغة',
        'arabic' => 'العربية',
        'english' => 'English',
    ],
    'en' => [
        'login_title' => 'Login',
        'email_placeholder' => 'Email Address',
        'password_placeholder' => 'Password',
        'login_btn' => 'Login',
        'forgot_password' => 'Forgot Password?',
        'register' => "Don't have an account? Register",
        'invalid_credentials' => 'Email or password is incorrect.',
        'language' => 'Language',
        'arabic' => 'Arabic',
        'english' => 'English',
    ]
];

function t($key) {
    global $translations, $language;
    return $translations[$language][$key] ?? $key;
}

// معالجة تسجيل الدخول
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = strtolower($user['role']);
        $_SESSION['fullname'] = $user['fullname'];

        if($_SESSION['role'] === 'trainee'){
            header("Location: ../trainee/dashboard.php");
            exit;
        } elseif($_SESSION['role'] === 'admin'){
            header("Location: ../admin/dashboard.php");
            exit;
        }
    } else {
        $error = t('invalid_credentials');
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $language ?>" dir="<?= $language=='ar'?'rtl':'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('login_title') ?></title>
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
    .login-card {
        background: #fff;
        border-radius: 20px;
        padding: 50px 35px 30px 35px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        transition: transform 0.3s;
        position: relative;
    }
    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 40px rgba(0,0,0,0.4);
    }
    .login-card h3 {
        text-align: center;
        margin-bottom: 25px;
        color: #0d47a1;
        font-weight: 700;
    }
    .form-control {
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
    .input-group {
        position: relative;
        margin-bottom: 20px;
    }
    .btn-primary {
        background: #0d47a1;
        border-radius: 50px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-primary:hover {
        background: #1565c0;
    }
    a {
        text-decoration: none;
        color: #0d47a1;
    }
    a:hover {
        text-decoration: underline;
    }
    /* زر اختيار اللغة أسفل البطاقة */
    .language-dropdown {
        display: flex;
        justify-content: center;
        margin-top: 25px;
    }
    .language-dropdown .dropdown-toggle {
        border-radius: 50px;
        font-weight: 600;
    }
</style>
</head>
<body>

<div class="login-card">

    <h3><?= t('login_title') ?></h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <i class="bi bi-envelope form-icon"></i>
            <input type="email" name="email" class="form-control" placeholder="<?= t('email_placeholder') ?>" required>
        </div>

        <div class="input-group">
            <i class="bi bi-lock form-icon"></i>
            <input type="password" name="password" class="form-control" placeholder="<?= t('password_placeholder') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary w-100"><?= t('login_btn') ?></button>

        <div class="text-center mt-3">
            <a href="forgot_password.php"><?= t('forgot_password') ?></a><br>
            <a href="register.php"><?= t('register') ?></a>
        </div>
    </form>

    <!-- زر اختيار اللغة أسفل النموذج -->
    <div class="language-dropdown">
        <div class="dropdown">
            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-translate"></i> <?= t('language') ?>
            </button>
            <ul class="dropdown-menu text-center">
                <li>
                    <a class="dropdown-item <?= $language=='ar'?'active':'' ?>" href="?lang=ar">
                        <i class="bi bi-flag-fill text-danger"></i> <?= t('arabic') ?>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item <?= $language=='en'?'active':'' ?>" href="?lang=en">
                        <i class="bi bi-flag-fill text-primary"></i> <?= t('english') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
