<?php

$name = $_POST['name'];
$email = $_POST['email'];
$number = $_POST['number'];
$subject = $_POST['subject'];
$message = $_POST["message"];

require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

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

$mail->setFrom($email, $name);
$mail->addAddress("gladiohan16@gmail.com", "Chrono");

$mail->Subject = $subject;
$mail->Body = $message;

$mail->send();

echo "Email sent.";


