<?php
// Wraps PHPMailer for sending SMTP emails

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\Users\RH\Desktop\PHPMailer-master\src\Exception.php';
require 'C:\Users\RH\Desktop\PHPMailer-master\src\PHPMailer.php';
require 'C:\Users\RH\Desktop\PHPMailer-master\src\SMTP.php';
require 'C:\Users\RH\Desktop\PHPMailer-master\src\OAuth.php';


require __DIR__ . '/vendor/autoload.php';

function sendMail(string $to, string $subject, string $body): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com';     // Update: SMTP host
        $mail->SMTPAuth   = true;
        $mail->Username   = 'smtp-user@example.com';// Update: SMTP user
        $mail->Password   = 'smtp-password';        // Update: SMTP pass
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('no-reply@example.com', 'Event Portal');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->isHTML(false);

        return $mail->send();
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>
