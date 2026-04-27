<?php
/**
 * SMTP Mail Helper using PHPMailer
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';
require_once 'config_mail.php';

function sendVerificationEmail($to, $code) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        //Recipients
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Sizning tasdiqlash kodingiz: ' . $code;
        
        $mail->Body = '
        <div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e1e8ed; border-radius: 12px; overflow: hidden;">
            <div style="background: #1D9E75; padding: 30px; text-align: center; color: white;">
                <h1 style="margin: 0;">Portfolio SaaS</h1>
            </div>
            <div style="padding: 30px; background: white;">
                <h2>Emailingizni tasdiqlang</h2>
                <p>Salom! Ro\'yxatdan o\'tganingiz uchun rahmat. Tizimga kirish uchun quyidagi 6 xonali tasdiqlash kodini kiriting:</p>
                <div style="background: #f0fdf4; border: 2px dashed #1D9E75; border-radius: 8px; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1D9E75; margin: 20px 0;">
                    ' . $code . '
                </div>
                <p>Bu kod 10 daqiqa davomida amal qiladi.</p>
                <p>Agar siz bu so\'rovni yubormagan bo\'lsangiz, ushbu xabarga e\'tibor bermang.</p>
            </div>
            <div style="padding: 20px; background: #f8fafc; text-align: center; color: #64748b; font-size: 12px;">
                &copy; ' . date('Y') . ' Portfolio SaaS Platform. Barcha huquqlar himoyalangan.
            </div>
        </div>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>

