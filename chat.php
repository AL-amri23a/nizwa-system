<?php
session_start();
require '../config/db.php';

$me = $_SESSION['user_id'];
$other = $_GET['id']; // الشخص الذي ستتحدث معه

// جلب الرسائل
$stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY id");
$stmt->execute([$me, $other, $other, $me]);
$chat = $stmt->fetchAll();

// إرسال رسالة جديدة
if(isset($_POST['send'])){
    $msg = $_POST['message'];
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id,receiver_id,message) VALUES (?,?,?)");
    $stmt->execute([$me, $other, $msg]);

    // إضافة إشعار للمتلقي
    require '../helpers/notify.php';
    addNotification($pdo, $other, "لديك رسالة جديدة من ".$_SESSION['fullname']);

    header("Location: chat.php?id=$other");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>المحادثة</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<style>
.my-msg { background:#1976d2; color:white; padding:10px 15px; border-radius:15px; width:fit-content; margin-left:auto; }
.other-msg { background:#e3e3e3; padding:10px 15px; border-radius:15px; width:fit-content; }
</style>
</head>
<body class="p-3">

<h4 class="mb-3">المحادثة</h4>

<div class="border p-3 mb-3" style="height:400px; overflow-y:scroll;">
    <?php foreach($chat as $m): ?>
        <?php if($m['sender_id'] == $me): ?>
            <div class="my-msg mb-2"><?= $m['message'] ?></div>
        <?php else: ?>
            <div class="other-msg mb-2"><?= $m['message'] ?></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<form method="POST">
    <div class="input-group">
        <input type="text" name="message" class="form-control" required>
        <button class="btn btn-primary" name="send">إرسال</button>
    </div>
</form>

</body>
</html>
