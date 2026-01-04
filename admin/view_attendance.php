<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$current = $_SESSION['lang'] ?? 'ar';

// الترجمة الخاصة بالصفحة
$lang_page = [
    'ar' => [
        'title' => "متابعة حضور المتدربين",
        'trainee_name' => "اسم المتدرب",
        'date' => "التاريخ",
        'entry' => "وقت الدخول",
        'exit' => "وقت الخروج",
        'status' => "الحالة",
        'edit_status' => "تعديل",
        'no_records' => "لا توجد سجلات حضور حتى الآن."
    ],
    'en' => [
        'title' => "Trainee Attendance Records",
        'trainee_name' => "Trainee Name",
        'date' => "Date",
        'entry' => "Entry Time",
        'exit' => "Exit Time",
        'status' => "Status",
        'edit_status' => "Edit",
        'no_records' => "No attendance records found."
    ]
];

$stmt = $pdo->query("
    SELECT a.id, u.fullname, a.date, a.time AS entry_time, a.exit_time, a.status
    FROM attendance a
    JOIN users u ON a.trainee_id = u.id
    ORDER BY a.date DESC, a.time DESC
");
$records = $stmt->fetchAll();

include '../inc/admin_header.php';
?>

<style>
body {
    background-color: #f4f6f9;
    font-family: 'Cairo', sans-serif;
}
h3 {
    color: #0d47a1;
    font-weight: 700;
}
.table {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.table th {
    background-color: #0d47a1;
    color: #fff;
    text-align: center;
}
.table td {
    text-align: center;
}
.btn-warning {
    background-color: #ff9800;
    border: none;
    border-radius: 50px;
    font-weight: bold;
}
.btn-warning:hover {
    background-color: #fb8c00;
}
</style>
<!-- زر الرجوع -->
<div style="margin: 10px 0;">
    <button onclick="history.back();" class="btn btn-secondary">
        ⬅ رجوع
    </button>
</div>
<div class="container mt-5">

    <h3 class="mb-4 text-end"><?= $lang_page[$current]['title'] ?></h3>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $lang_page[$current]['trainee_name'] ?></th>
                <th><?= $lang_page[$current]['date'] ?></th>
                <th><?= $lang_page[$current]['entry'] ?></th>
                <th><?= $lang_page[$current]['exit'] ?></th>
                <th><?= $lang_page[$current]['status'] ?></th>
                <th><?= $lang_page[$current]['edit_status'] ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($records as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['entry_time'] ?></td>
                <td><?= $row['exit_time'] ?? '-' ?></td>
                <td><?= $row['status'] ?></td>

                <td>
                    <a href="edit_attendance.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                        <?= $lang_page[$current]['edit_status'] ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if(empty($records)): ?>
            <tr>
                <td colspan="6" class="text-center">
                    <?= $lang_page[$current]['no_records'] ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../inc/footer.php'; ?>
