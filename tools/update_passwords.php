<?php
// تحديث كلمات المرور لمستخدمي الاختبار إلى '123456' (استخدم مرة واحدة ثم احذف الملف)
// ضع الملف في مجلد آمن ثم افتحه عبر المتصفح: http://localhost/nizwa-system/tools/update_passwords.php

session_start();
require __DIR__ . '/../config/db.php'; // عدّل المسار إذا وضعته في مكان آخر

try {
    // المستخدمون الذين نريد تحديث كلمات المرور لهم
    $users = [
        'trainee@test.com' => '123456',
        'admin@test.com' => '123456'
    ];

    $updated = 0;
    foreach ($users as $email => $plain) {
        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hash, $email]);
        if ($stmt->rowCount() > 0) $updated++;
    }

    echo "<h3>تم تحديث كلمات المرور للمستخدمين التجريبيين.</h3>";
    echo "<p>عدد المستخدمين المحدثين: $updated</p>";
    echo "<p>الآن جرّب تسجيل الدخول بـ:</p>";
    echo "<ul><li>trainee@test.com / 123456</li><li>admin@test.com / 123456</li></ul>";
    echo "<p><strong>مهم:</strong> احذف هذا الملف فورًا بعد التأكد لأمان المشروع.</p>";

} catch (Exception $e) {
    echo "<h3>حدث خطأ:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
