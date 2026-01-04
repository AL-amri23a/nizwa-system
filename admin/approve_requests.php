<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$current = $_SESSION['lang'] ?? 'ar';

$pageLang = [
    'ar' => [
        'title' => 'الموافقات والمتابعة',
        'list' => 'قائمة الطلبات',
        'trainee_name' => 'اسم المتدرب',
        'request_type' => 'نوع الطلب',
        'details' => 'التفاصيل',
        'date' => 'تاريخ الطلب',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'no_requests' => 'لا توجد طلبات حالياً.',
        'approved_success' => 'تمت الموافقة على الطلب بنجاح!',
        'rejected_success' => 'تم رفض الطلب بنجاح!',
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق',
        'rejected' => 'مرفوض',
    ],

    'en' => [
        'title' => 'Approvals & Follow-up',
        'list' => 'Requests List',
        'trainee_name' => 'Trainee Name',
        'request_type' => 'Request Type',
        'details' => 'Details',
        'date' => 'Request Date',
        'status' => 'Status',
        'actions' => 'Actions',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'no_requests' => 'No requests available.',
        'approved_success' => 'Request approved successfully!',
        'rejected_success' => 'Request rejected successfully!',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ]
];

if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE training_requests SET status=? WHERE id=?");
    $stmt->execute([$pageLang['ar']['approved'], $id]);
    $success = $pageLang[$current]['approved_success'];
}

if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $stmt = $pdo->prepare("UPDATE training_requests SET status=? WHERE id=?");
    $stmt->execute([$pageLang['ar']['rejected'], $id]);
    $success = $pageLang[$current]['rejected_success'];
}

$stmt2 = $pdo->query("SELECT tr.*, u.fullname FROM training_requests tr 
                      JOIN users u ON tr.trainee_id = u.id 
                      ORDER BY tr.created_at DESC");
$requests = $stmt2->fetchAll();

include '../inc/admin_header.php';
?>

<div style="margin: 10px 0;">
    <button onclick="history.back();" class="btn btn-secondary">
        ⬅ رجوع
    </button>
</div>

<div class="container" style="margin-top:70px;">
    <h2 class="mb-4 text-end"><?= $pageLang[$current]['title'] ?></h2>

    <?php if(!empty($success)): ?>
        <div class="alert alert-success text-end"><?= $success ?></div>
    <?php endif; ?>

    <div class="card p-4">
        <div class="card-header text-end"><?= $pageLang[$current]['list'] ?></div>

        <div class="card-body text-end">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?= $pageLang[$current]['trainee_name'] ?></th>
                        <th><?= $pageLang[$current]['request_type'] ?></th>
                        <th><?= $pageLang[$current]['details'] ?></th>
                        <th><?= $pageLang[$current]['date'] ?></th>
                        <th><?= $pageLang[$current]['status'] ?></th>
                        <th><?= $pageLang[$current]['actions'] ?></th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($requests as $r): ?>

                    <?php
                        $status_display = $r['status'];
                        if ($r['status'] == "قيد الانتظار") $status_display = $pageLang[$current]['pending'];
                        if ($r['status'] == "موافق")        $status_display = $pageLang[$current]['approved'];
                        if ($r['status'] == "مرفوض")       $status_display = $pageLang[$current]['rejected'];
                    ?>

                    <tr>
                        <td><?= htmlspecialchars($r['fullname']) ?></td>
                        <td><?= htmlspecialchars($r['request_type']) ?></td>
                        <td><?= htmlspecialchars($r['details']) ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>

                        <td>
                            <?php if($r['status'] == "موافق"): ?>
                                <span class="badge bg-success"><?= $status_display ?></span>
                            <?php elseif($r['status'] == "مرفوض"): ?>
                                <span class="badge bg-danger"><?= $status_display ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning"><?= $status_display ?></span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if($r['status'] == "قيد الانتظار"): ?>
                                <a href="?approve=<?= $r['id'] ?>" class="btn btn-success btn-sm"><?= $pageLang[$current]['approve'] ?></a>
                                <a href="?reject=<?= $r['id'] ?>" class="btn btn-danger btn-sm"><?= $pageLang[$current]['reject'] ?></a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($requests)): ?>
                    <tr>
                        <td colspan="6" class="text-center"><?= $pageLang[$current]['no_requests'] ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
