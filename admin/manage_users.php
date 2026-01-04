<?php
session_start();
require '../config/db.php';

// التأكد من أن المستخدم مشرف
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// اللغة الحالية حسب الهيدر
$current = $_SESSION['lang'] ?? 'ar';

// الترجمة الخاصة بهذه الصفحة فقط
$lang_page = [
    'ar' => [
        'title' => "إدارة المتدربين",
        'add_new' => "إضافة متدرب جديد",
        'fullname' => "الاسم الكامل",
        'email' => "البريد الإلكتروني",
        'password' => "كلمة المرور",
        'add_btn' => "إضافة المتدرب",
        'list' => "قائمة المتدربين",
        'reg_date' => "تاريخ التسجيل",
        'actions' => "الإجراءات",
        'edit' => "تعديل",
        'delete' => "حذف",
        'delete_confirm' => "هل أنت متأكد من حذف المتدرب؟",
        'no_trainees' => "لا يوجد متدربين حتى الآن.",
        'error_email' => "هذا البريد الإلكتروني مسجل مسبقاً.",
        'success_add' => "تم إضافة المتدرب بنجاح!",
        'success_delete' => "تم حذف المتدرب بنجاح!"
    ],
    'en' => [
        'title' => "Trainee Management",
        'add_new' => "Add New Trainee",
        'fullname' => "Full Name",
        'email' => "Email",
        'password' => "Password",
        'add_btn' => "Add Trainee",
        'list' => "Trainees List",
        'reg_date' => "Registration Date",
        'actions' => "Actions",
        'edit' => "Edit",
        'delete' => "Delete",
        'delete_confirm' => "Are you sure you want to delete this trainee?",
        'no_trainees' => "No trainees found.",
        'error_email' => "Email already exists.",
        'success_add' => "Trainee added successfully!",
        'success_delete' => "Trainee deleted successfully!"
    ]
];

$error = '';
$success = '';

// إضافة متدرب جديد
if(isset($_POST['add_user'])){
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'trainee';

    $check = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $check->execute([$email]);

    if($check->rowCount() > 0){
        $error = $lang_page[$current]['error_email'];
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$fullname, $email, $hashed, $role]);
        $success = $lang_page[$current]['success_add'];
    }
}

// حذف متدرب
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='trainee'");
    $stmt->execute([$id]);
    $success = $lang_page[$current]['success_delete'];
}

// جلب جميع المتدربين
$stmt2 = $pdo->query("SELECT * FROM users WHERE role='trainee' ORDER BY created_at DESC");
$trainees = $stmt2->fetchAll();

// إدراج الهيدر
include '../inc/admin_header.php';
?>

<style>
body {
    font-family: 'Cairo', sans-serif;
    background-color: #f4f6f9;
}
.container {
    margin-top: 60px;
}
h2 {
    color: #0d47a1;
    font-weight: 700;
}
.card {
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: none;
}
.card-header {
    background: #0d47a1;
    color: #fff;
    font-weight: bold;
    font-size: 1.1rem;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
.btn {
    border-radius: 50px;
    font-weight: bold;
}
.table th {
    background-color: #0d47a1;
    color: #fff;
    text-align: center;
}
.table td {
    text-align: center;
}
</style>

<div class="container">
    <h2 class="mb-4 text-end"><?= $lang_page[$current]['title'] ?></h2>

    <?php if($error): ?>
        <div class="alert alert-danger text-end"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success text-end"><?= $success ?></div>
    <?php endif; ?>
    <!-- زر الرجوع -->
<div style="margin: 10px 0;">
    <button onclick="history.back();" class="btn btn-secondary">
        ⬅ رجوع
    </button>
</div>
    <!-- إضافة متدرب جديد -->
    <div class="card mb-4">
        <div class="card-header text-end"><?= $lang_page[$current]['add_new'] ?></div>
        <div class="card-body text-end">
            <form method="POST" class="row g-3 text-end">
                <div class="col-md-4">
                    <input type="text" name="fullname" class="form-control" placeholder="<?= $lang_page[$current]['fullname'] ?>" required>
                </div>

                <div class="col-md-4">
                    <input type="email" name="email" class="form-control" placeholder="<?= $lang_page[$current]['email'] ?>" required>
                </div>

                <div class="col-md-4">
                    <input type="password" name="password" class="form-control" placeholder="<?= $lang_page[$current]['password'] ?>" required>
                </div>

                <div class="col-12">
                    <button type="submit" name="add_user" class="btn btn-success w-100 py-2">
                        <?= $lang_page[$current]['add_btn'] ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- قائمة المتدربين -->
    <div class="card">
        <div class="card-header text-end"><?= $lang_page[$current]['list'] ?></div>
        <div class="card-body text-end">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?= $lang_page[$current]['fullname'] ?></th>
                        <th><?= $lang_page[$current]['email'] ?></th>
                        <th><?= $lang_page[$current]['reg_date'] ?></th>
                        <th><?= $lang_page[$current]['actions'] ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($trainees as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['fullname']) ?></td>
                            <td><?= htmlspecialchars($t['email']) ?></td>
                            <td><?= htmlspecialchars($t['created_at']) ?></td>

                            <td>
                                <a href="edit_user.php?id=<?= $t['id'] ?>" class="btn btn-primary btn-sm">
                                    <?= $lang_page[$current]['edit'] ?>
                                </a>

                                <a href="?delete=<?= $t['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('<?= $lang_page[$current]['delete_confirm'] ?>');">
                                    <?= $lang_page[$current]['delete'] ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if(empty($trainees)): ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                <?= $lang_page[$current]['no_trainees'] ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
