<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail-> Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 465;
$mail->Username = "your-email@gmail.com"; //your gmail
$mail->Password = ""; //your gmail app password
$mail->SMTPSecure = 'ssl';
$mail->isHTML(true);

return $mail;
?>
