<?php
session_start();
require '../config/db.php';

// التأكد من أن المستخدم مشرف / مدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// تحديد اللغة من الجلسة أو الافتراضية
if(isset($_GET['lang']) && in_array($_GET['lang'], ['ar','en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$language = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';

// النصوص لكل لغة
$translations = [
    'ar' => [
        'dashboard_title' => 'لوحة تحكم المشرف',
        'manage_users' => 'إدارة المستخدمين',
        'manage_users_desc' => 'عرض، تعديل، أو حذف حسابات المتدربين',
        'approve_requests' => 'الموافقات والمتابعة',
        'approve_requests_desc' => 'مراجعة طلبات التدريب والموافقة عليها',
        'view_attendance' => 'متابعة الحضور',
        'view_attendance_desc' => 'عرض تقارير حضور وانصراف المتدربين',
        'print_certificates' => 'طباعة الشهادات',
        'print_certificates_desc' => 'طباعة أو تحميل شهادات التدريب النهائية',
        'review_projects' => 'مراجعة مشاريع المتدربين',
        'review_projects_desc' => 'عرض مشاريع المتدربين وإضافة ملاحظات',
        'language' => 'اللغة',
        'arabic' => 'العربية',
        'english' => 'English',
    ],
    'en' => [
        'dashboard_title' => 'Admin Dashboard',
        'manage_users' => 'Manage Users',
        'manage_users_desc' => 'View, edit, or delete trainees accounts',
        'approve_requests' => 'Approvals & Follow-up',
        'approve_requests_desc' => 'Review training requests and approve them',
        'view_attendance' => 'Attendance Tracking',
        'view_attendance_desc' => 'View trainees attendance reports',
        'print_certificates' => 'Print Certificates',
        'print_certificates_desc' => 'Print or download final training certificates',
        'review_projects' => 'Review Trainees Projects',
        'review_projects_desc' => 'View projects and add notes',
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

$pageTitle = t('dashboard_title');
include '../inc/admin_header.php';
?>

<style>
body {
    direction: <?= $language == 'ar' ? 'rtl' : 'ltr' ?>;
    background: linear-gradient(135deg, #E3F2FD 0%, #FFFFFF 100%);
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

.dashboard-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 28px;
    margin-top: 60px;
    padding: 20px;
}

/* الكروت */
.dashboard-card {
    background-color: #FFFFFF;
    border-radius: 18px;
    padding: 38px 25px;
    text-align: center;
    border-left: 6px solid #1E88E5;
    box-shadow: 0 10px 22px rgba(21,101,192,0.15);
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(21,101,192,0.25);
}

/* الأيقونة */
.dashboard-card i {
    font-size: 58px;
    color: #1565C0;
    margin-bottom: 18px;
}

/* العنوان */
.dashboard-card h4 {
    font-weight: 700;
    color: #1565C0;
    margin-bottom: 8px;
}

/* النص */
.dashboard-card p {
    color: #546E7A;
    font-size: 14.5px;
}

/* الروابط */
a.text-decoration-none {
    color: inherit;
}

</style>

<div class="container">

    <div class="dashboard-container">
        <a href="manage_users.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-people-fill"></i>
                <h4><?= t('manage_users') ?></h4>
                <p><?= t('manage_users_desc') ?></p>
            </div>
        </a>

        <!-- الموافقات والمتابعة -->
        <a href="approve_requests.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-check-circle-fill"></i>
                <h4><?= t('approve_requests') ?></h4>
                <p><?= t('approve_requests_desc') ?></p>
            </div>
        </a>

        <a href="view_attendance.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-calendar-check-fill"></i>
                <h4><?= t('view_attendance') ?></h4>
                <p><?= t('view_attendance_desc') ?></p>
            </div>
        </a>

        <a href="print_certificates.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-award-fill"></i>
                <h4><?= t('print_certificates') ?></h4>
                <p><?= t('print_certificates_desc') ?></p>
            </div>
        </a>

        <a href="review_projects.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-folder2-open"></i>
                <h4><?= t('review_projects') ?></h4>
                <p><?= t('review_projects_desc') ?></p>
            </div>
        </a>
    </div>
</div>
<?php include '../inc/footer.php'; ?>
