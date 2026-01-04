<?php
session_start();
require '../config/db.php';

// التأكد أن المستخدم مشرف
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $trainee_id = $_POST['trainee_id'];
    if(isset($_FILES['certificate']) && $_FILES['certificate']['error'] === 0){
        $allowed = ['pdf'];
        $file_name = $_FILES['certificate']['name'];
        $file_tmp = $_FILES['certificate']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if(in_array($file_ext, $allowed)){
            $new_name = 'certificate_'.$trainee_id.'_'.time().'.'.$file_ext;
            $upload_path = '../certificates/'.$new_name;

            if(move_uploaded_file($file_tmp, $upload_path)){
                // حفظ المسار في قاعدة البيانات
                $stmt = $pdo->prepare("INSERT INTO certificates (trainee_id, file_path) VALUES (?, ?)");
                $stmt->execute([$trainee_id, 'certificates/'.$new_name]);

                $success = "تم رفع الشهادة بنجاح!";
            } else {
                $error = "حدث خطأ أثناء رفع الملف.";
            }
        } else {
            $error = "نوع الملف غير مدعوم. يُسمح فقط بـ PDF.";
        }
    } else {
        $error = "الرجاء اختيار ملف للشهادة.";
    }
}

// جلب جميع المتدربين
$stmt2 = $pdo->query("SELECT id, fullname FROM users WHERE role='trainee'");
$trainees = $stmt2->fetchAll();

include '../inc/admin_header.php';
?>

<div class="container mt-5">
    <div class="col-md-6 mx-auto card p-4">
        <div class="card-header text-end">رفع شهادة متدرب</div>
        <div class="card-body text-end">

            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="text-end">
                <div class="mb-3">
                    <label class="form-label">اختر المتدرب</label>
                    <select name="trainee_id" class="form-select" required>
                        <option value="">-- اختر المتدرب --</option>
                        <?php foreach($trainees as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['fullname']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">اختر ملف الشهادة (PDF)</label>
                    <input type="file" name="certificate" class="form-control" accept=".pdf" required>
                </div>

                <button type="submit" class="btn btn-success w-100">رفع الشهادة</button>
            </form>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
