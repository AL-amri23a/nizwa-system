<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT p.*, u.fullname 
    FROM projects p 
    JOIN users u ON p.trainee_id = u.id 
    WHERE p.id=?
");
$stmt->execute([$id]);
$project = $stmt->fetch();

if(!$project){
    die("المشروع غير موجود");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $note = $_POST['instructor_note'];

    $update = $pdo->prepare("UPDATE projects SET instructor_note=?, status='تمت المراجعة' WHERE id=?");
    $update->execute([$note, $id]);

    echo "<div class='alert alert-success text-center'>تم إرسال الملاحظة للمتدرب.</div>";
}
?>

<div class="container mt-4">
    <h3 class="text-end mb-3">مراجعة مشروع: <?= $project['title'] ?></h3>

    <div class="card p-4 shadow">

        <p><strong>اسم المتدرب:</strong> <?= $project['fullname'] ?></p>

        <p><strong>ملف المشروع:</strong> <a href="../uploads/projects/<?= $project['file_path'] ?>" target="_blank">فتح</a></p>

        <?php if($project['trainee_note']): ?>
        <div class="alert alert-info">
            <strong>ملاحظة المتدرب:</strong><br>
            <?= nl2br(htmlspecialchars($project['trainee_note'])) ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label">ملاحظة المدرب:</label>
            <textarea name="instructor_note" class="form-control" rows="5"><?= $project['instructor_note'] ?></textarea>

            <button class="btn btn-success mt-3 w-100">إرسال الملاحظة</button>
        </form>

    </div>
</div>

<?php include '../inc/footer.php'; ?>
