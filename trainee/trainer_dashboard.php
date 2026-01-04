<?php
session_start();
require '../config/db.php';

// التأكد أن المستخدم مدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'trainer'){
    header("Location: ../auth/login.php");
    exit;
}

// إدارة اللغة
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])){
    $_SESSION['lang'] = $_GET['lang'];
}
$language = $_SESSION['lang'] ?? 'ar';

// نصوص للغات
$texts = [
    'ar' => [
        'page_title' => 'داشبورد المدرب',
        'manage_users' => 'إدارة المستخدمين',
        'manage_users_desc' => 'عرض، تعديل، أو حذف حسابات المتدربين',
        'approvals' => 'الموافقات والمتابعة',
        'approvals_desc' => 'مراجعة طلبات التدريب والموافقة عليها',
        'attendance' => 'متابعة الحضور',
        'attendance_desc' => 'عرض تقارير حضور وانصراف المتدربين',
        'review_projects' => 'مراجعة مشاريع المتدربين',
        'review_projects_desc' => 'عرض مشاريع المتدربين وإضافة ملاحظات'
    ],
    'en' => [
        'page_title' => 'Trainer Dashboard',
        'manage_users' => 'Manage Users',
        'manage_users_desc' => 'View, edit, or delete trainee accounts',
        'approvals' => 'Approvals & Follow-up',
        'approvals_desc' => 'Review training requests and approve them',
        'attendance' => 'Attendance Tracking',
        'attendance_desc' => 'View trainee attendance reports',
        'review_projects' => 'Review Trainee Projects',
        'review_projects_desc' => 'View projects and add feedback'
    ]
];

function t($key){
    global $texts, $language;
    return $texts[$language][$key] ?? $key;
}

// جلب عدد المشاريع الجديدة لكل متدرب
$stmt = $pdo->query("SELECT COUNT(*) as new_projects FROM projects WHERE status='مستلم'");
$newProjects = $stmt->fetch()['new_projects'] ?? 0;

$pageTitle = t('page_title');
include '../inc/header.php';
?>

<style>
.dashboard-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-top: 30px;
}
.dashboard-card {
    background: linear-gradient(145deg, #e3f2fd, #bbdefb);
    border-radius: 20px;
    padding: 35px 20px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    cursor: pointer;
}
.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.25);
    background: linear-gradient(145deg, #bbdefb, #90caf9);
}
.dashboard-card i {
    font-size: 60px;
    color: #0d47a1;
    margin-bottom: 20px;
}
.dashboard-card h4 {
    font-weight: 700;
    color: #0d47a1;
    margin-bottom: 10px;
}
.dashboard-card p {
    color: #333;
    font-weight: 500;
    font-size: 15px;
}
.badge {
    position: absolute;
    top: 15px;
    right: 20px;
    background: #f44336;
    color: #fff;
    font-size: 0.8rem;
    padding: 3px 7px;
    border-radius: 50%;
}
</style>

<div class="container">
    <div class="text-end mb-3">
        <!-- زر تبديل اللغة -->
        <a href="?lang=ar" class="btn btn-outline-primary btn-sm">عربي</a>
        <a href="?lang=en" class="btn btn-outline-secondary btn-sm">English</a>
    </div>

    <div class="dashboard-container">
        <a href="manage_users.php" class="text-decoration-none position-relative">
            <div class="dashboard-card">
                <i class="bi bi-people-fill"></i>
                <h4><?= t('manage_users') ?></h4>
                <p><?= t('manage_users_desc') ?></p>
            </div>
        </a>

        <a href="approve_requests.php" class="text-decoration-none position-relative">
            <div class="dashboard-card">
                <i class="bi bi-check-circle-fill"></i>
                <h4><?= t('approvals') ?></h4>
                <p><?= t('approvals_desc') ?></p>
            </div>
        </a>

        <a href="view_attendance.php" class="text-decoration-none position-relative">
            <div class="dashboard-card">
                <i class="bi bi-calendar-check-fill"></i>
                <h4><?= t('attendance') ?></h4>
                <p><?= t('attendance_desc') ?></p>
            </div>
        </a>

        <a href="review_projects.php" class="text-decoration-none position-relative">
            <div class="dashboard-card">
                <i class="bi bi-folder2-open"></i>
                <h4><?= t('review_projects') ?></h4>
                <p><?= t('review_projects_desc') ?></p>
                <?php if($newProjects > 0): ?>
                    <span class="badge"><?= $newProjects ?></span>
                <?php endif; ?>
            </div>
        </a>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
