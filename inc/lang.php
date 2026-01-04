<?php
if(session_status() === PHP_SESSION_NONE){ session_start(); }

// تعيين اللغة الافتراضية
if(!isset($_SESSION['lang'])) $_SESSION['lang'] = 'ar';

// تبديل اللغة عبر الرابط
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang']==='en' ? 'en' : 'ar';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

$lang = [
    'ar'=>[
        'dashboard'=>'لوحة المتدرب',
        'trainer_dashboard'=>'لوحة المدرب',
        'manage_users'=>'إدارة المستخدمين',
        'approvals'=>'الموافقات والمتابعة',
        'attendance'=>'متابعة الحضور',
        'certificates'=>'طباعة الشهادات',
        'projects'=>'مشاريع المتدربين',
        'submit_project'=>'إرسال مشروع جديد',
        'logout'=>'خروج',
        'back'=>'رجوع',
        'review_projects'=>'مراجعة المشاريع'
    ],
    'en'=>[
        'dashboard'=>'Trainee Dashboard',
        'trainer_dashboard'=>'Trainer Dashboard',
        'manage_users'=>'Manage Users',
        'approvals'=>'Approvals & Follow-up',
        'attendance'=>'Trainee Attendance',
        'certificates'=>'Print Certificates',
        'projects'=>'Trainee Projects',
        'submit_project'=>'Submit New Project',
        'logout'=>'Logout',
        'back'=>'Back',
        'review_projects'=>'Review Projects'
    ]
];

$current = $_SESSION['lang'];
