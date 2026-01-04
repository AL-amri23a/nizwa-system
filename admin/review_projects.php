<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// 🔹 ضبط اللغة الحالية
$current = $_SESSION['lang'] ?? 'ar';
if(!in_array($current, ['ar','en'])) $current = 'ar'; // fallback

// 🔹 مصفوفة النصوص
$lang = [
    'ar' => [
        'title' => 'مراجعة مشاريع المتدربين',
        'trainee' => 'المتدرب',
        'project_title' => 'عنوان المشروع',
        'current_status' => 'الحالة الحالية',
        'view_project' => 'عرض المشروع',
        'feedback' => 'الملاحظات',
        'change_status' => 'تغيير الحالة',
        'save_feedback' => 'حفظ الملاحظات',
        'no_projects' => 'لا توجد مشاريع بعد.',
        'status_received' => 'مستلم',
        'status_reviewed' => 'تمت المراجعة',
        'status_rejected' => 'مرفوض',
    ],
    'en' => [
        'title' => 'Review Trainee Projects',
        'trainee' => 'Trainee',
        'project_title' => 'Project Title',
        'current_status' => 'Current Status',
        'view_project' => 'View Project',
        'feedback' => 'Feedback',
        'change_status' => 'Change Status',
        'save_feedback' => 'Save Feedback',
        'no_projects' => 'No projects yet.',
        'status_received' => 'Received',
        'status_reviewed' => 'Reviewed',
        'status_rejected' => 'Rejected',
    ]
];

// 🔹 دالة مساعدة للوصول للنصوص
function t($key, $lang, $current){
    return $lang[$current][$key] ?? $key;
}

// تحديث الملاحظات إذا تم الإرسال
if(isset($_POST['submit_feedback'])){
    $project_id = $_POST['project_id'];
    $feedback = trim($_POST['feedback']);
    $status = $_POST['status']; // يجب أن يكون "received", "reviewed" أو "rejected"
    $stmt = $pdo->prepare("UPDATE projects SET feedback=?, status=? WHERE id=?");
    $stmt->execute([$feedback, $status, $project_id]);
}

// جلب جميع المشاريع
$stmt = $pdo->query("SELECT p.*, u.fullname FROM projects p JOIN users u ON p.trainee_id = u.id ORDER BY p.submitted_at DESC");
$projects = $stmt->fetchAll();

include '../inc/admin_header.php';
?>

<style>
.card-project {
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 25px;
    background-color: #f4f6f9;
    transition: transform 0.2s, box-shadow 0.2s;
}
.card-project:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.card-project h5 {
    font-weight: bold;
    color: #0d47a1;
}
.card-project p {
    margin-bottom: 8px;
}
.card-project a.btn-view {
    margin-top: 10px;
    background-color: #1976d2;
    border: none;
    color: white;
    transition: 0.3s;
}
.card-project a.btn-view:hover {
    background-color: #0d47a1;
}
textarea.form-control {
    resize: vertical;
}
.form-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.btn-save {
    align-self: flex-start;
}

/* ألوان حسب الحالة */
.status-badge {
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 12px;
    color: #fff;
    display: inline-block;
}
.status-received { background-color: #ffc107; } /* مستلم */
.status-reviewed { background-color: #28a745; } /* تمت المراجعة */
.status-rejected { background-color: #dc3545; } /* مرفوض */
</style>

<!-- زر الرجوع -->
<div style="margin: 10px 0;">
    <button onclick="history.back();" class="btn btn-secondary">
        ⬅ رجوع
    </button>
</div>

<div class="container mt-4">
    <h3 class="text-center mb-4"><?= t('title', $lang, $current) ?></h3>

    <?php if($projects): ?>
        <?php foreach($projects as $p): ?>
            <?php
                // تعيين الكلاس حسب الحالة
                $status_class = '';
                if($p['status']=='received') $status_class='status-received';
                elseif($p['status']=='reviewed') $status_class='status-reviewed';
                elseif($p['status']=='rejected') $status_class='status-rejected';
            ?>
            <div class="card-project mx-auto" style="max-width:700px;">
                <h5><?= t('trainee', $lang, $current) ?>: <?= htmlspecialchars($p['fullname']) ?></h5>
                <p><?= t('project_title', $lang, $current) ?>: <?= htmlspecialchars($p['title']) ?></p>
                <p><?= t('current_status', $lang, $current) ?>: 
                    <span class="status-badge <?= $status_class ?>"><?= t('status_'.$p['status'], $lang, $current) ?></span>
                </p>
                <a href="../uploads/<?= $p['file_path'] ?>" target="_blank" class="btn btn-view btn-sm"><?= t('view_project', $lang, $current) ?></a>

                <form method="POST" class="mt-3 form-section">
                    <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                    <label><?= t('feedback', $lang, $current) ?>:</label>
                    <textarea name="feedback" class="form-control" rows="3" placeholder="<?= t('feedback', $lang, $current) ?>"><?= htmlspecialchars($p['feedback']) ?></textarea>

                    <label><?= t('change_status', $lang, $current) ?>:</label>
                    <select name="status" class="form-control w-50">
                        <option value="received" <?= $p['status']=='received'?'selected':'' ?>><?= t('status_received',$lang,$current) ?></option>
                        <option value="reviewed" <?= $p['status']=='reviewed'?'selected':'' ?>><?= t('status_reviewed',$lang,$current) ?></option>
                        <option value="rejected" <?= $p['status']=='rejected'?'selected':'' ?>><?= t('status_rejected',$lang,$current) ?></option>
                    </select>

                    <button type="submit" name="submit_feedback" class="btn btn-primary btn-save mt-2"><?= t('save_feedback', $lang, $current) ?></button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center"><?= t('no_projects', $lang, $current) ?></p>
    <?php endif; ?>
</div>

<?php include '../inc/footer.php'; ?>
