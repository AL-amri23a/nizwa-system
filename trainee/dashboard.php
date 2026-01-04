<?php
session_start();
require '../config/db.php';

// التأكد من تسجيل الدخول كمتدرب
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainee'){
    header("Location: ../auth/login.php");
    exit;
}

// تحديد اللغة الحالية من الجلسة أو الافتراضية
if(isset($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'ar';
}
$current = $_SESSION['lang'] ?? 'ar';

// جلب بيانات المشروع الأخير للمتدرب (لتحديث المشاريع)
$stmt = $pdo->prepare("SELECT * FROM projects WHERE trainee_id=? ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$lastProject = $stmt->fetch();

// جلب الشهادة الأخيرة
$stmt2 = $pdo->prepare("SELECT * FROM certificates WHERE trainee_id=? ORDER BY created_at DESC LIMIT 1");
$stmt2->execute([$_SESSION['user_id']]);
$lastCert = $stmt2->fetch();

include '../inc/trainee_header.php';
?>

<style>
body {
    direction: <?= $current == 'ar' ? 'rtl' : 'ltr' ?>;
    background: linear-gradient(135deg, #E3F2FD 0%, #FFFFFF 100%);
    font-family: 'Segoe UI', Tahoma, sans-serif;
}

.dashboard-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 28px;
    margin-top: 50px;
    padding: 10px;
}

/* الكروت */
.dashboard-card {
    background-color: #FFFFFF;
    border-radius: 18px;
    padding: 35px 22px;
    text-align: center;
    border-left: 6px solid #1E88E5;
    box-shadow: 0 10px 22px rgba(21,101,192,0.15);
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(21,101,192,0.25);
}

/* الأيقونات */
.dashboard-card i {
    font-size: 55px;
    color: #1565C0;
    margin-bottom: 18px;
}

/* العناوين */
.dashboard-card h4 {
    font-weight: 700;
    color: #1565C0;
    margin-bottom: 8px;
}

/* الوصف */
.dashboard-card p {
    color: #546E7A;
    font-size: 14.5px;
    margin-bottom: 0;
}

/* روابط */
a.text-decoration-none {
    color: inherit;
}

</style>

<div class="container position-relative">

<div class="dashboard-container">
        <!-- متابعة الحضور -->
        <a href="attendance_form.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-calendar-check-fill"></i>
                <h4><?= $current=='ar'?'متابعة الحضور':'Attendance Tracking' ?></h4>
                <p><?= $current=='ar'?'عرض حضورك اليومي والتقارير الأسبوعية':'View your daily attendance and weekly reports' ?></p>
            </div>
        </a>

        <!-- رفع خطاب التدريب -->
        <a href="upload_letter.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-file-earmark-arrow-up-fill"></i>
                <h4><?= $current=='ar'?'رفع خطاب التدريب':'Upload Training Letter' ?></h4>
                <p><?= $current=='ar'?'تحميل خطاب التدريب الخاص بك':'Upload your training letter' ?></p>
            </div>
        </a>

        <!-- عرض الشهادة -->
        <a href="certificate.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-award-fill"></i>
                <h4><?= $current=='ar'?'عرض الشهادة':'View Certificate' ?></h4>
                <p>
                    <?php
                    if($lastCert){
                        echo $current=='ar'?"تحميل أو عرض شهادة التدريب بعد الانتهاء":"Download or view your training certificate";
                    } else {
                        echo $current=='ar'?"لم يتم إصدار شهادة بعد":"No certificate issued yet";
                    }
                    ?>
                </p>
            </div>
        </a>

        <!-- مشاريعي -->
        <a href="my_projects.php" class="text-decoration-none">
            <div class="dashboard-card">
                <i class="bi bi-file-earmark-text-fill"></i>
                <h4><?= $current=='ar'?'مشاريعي':'My Projects' ?></h4>
                <p>
                    <?php
                    if($lastProject){
                        echo $current=='ar'?"عرض آخر مشروع أو تعديل المشاريع السابقة":"View last project or edit previous projects";
                    } else {
                        echo $current=='ar'?"لم يتم إرسال أي مشروع بعد":"No projects submitted yet";
                    }
                    ?>
                </p>
            </div>
        </a>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
