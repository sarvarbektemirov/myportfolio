<?php
/**
 * Email Tizimini Tekshirish Sahifasi (Debug bilan)
 */
require_once 'mail_helper.php';

echo "<h2>Email yuborishni tekshirish (Debugging)...</h2>";

$test_email = 'sarvarbektemirov422@gmail.com'; 
$test_code = '123456';

echo "Yuborilmoqda: <strong>$test_email</strong> manzili qabul qilishi kerak...<br><hr>";

// Capture output to show debug info
ob_start();
$result = sendVerificationEmail($test_email, $test_code);
$debug_output = ob_get_clean();

if ($result) {
    echo "<h3 style='color: green;'>MUVAFFAQIYAT! Xabar yuborildi.</h3>";
    echo "Pochtangizni tekshiring (Spam papkasini ham ko'rib chiqing).";
} else {
    echo "<h3 style='color: red;'>Xatolik yuz berdi!</h3>";
    echo "<strong>Xato sababi bo'lishi mumkin bo'lgan ma'lumotlar:</strong><br>";
    echo "<pre style='background: #eee; padding: 10px; border: 1px solid #ccc;'>" . htmlspecialchars($debug_output) . "</pre>";
    
    echo "<br><strong>Tekshirib ko'ring:</strong>";
    echo "<ul>
        <li>Gmail App Password (16 xonali kod) to'g'ri yozilganmi?</li>
        <li>OpenSSL extension PHPda yoqilganmi?</li>
        <li>Antivirus yoki Firewall 587-portni to'sib qo'ymaganmi?</li>
    </ul>";
}
?>

