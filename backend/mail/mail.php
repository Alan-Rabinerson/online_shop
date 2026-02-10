<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/PHPMailer/src/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/PHPMailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_email($address, $name, $subject, $body_html, $body_text, $attachments = []) {
    try {
        // Crear instancia de la clase PHPMailer
        $mail = new PHPMailer(true);
        // Autentificación con SMTP
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        // Login
        $mail->Host = "smtp.remotehost.es";
        $mail->Port = 587;
        $mail->Username = "no-reply@remotehost.es";
        $mail->Password = "Justfortesting26#";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->setFrom('no-reply@remotehost.es', 'RemoteHost');
        $mail->addAddress($address, $name);
        // Copia
        //$mail->addCC('info@example.com');
        // Copia oculta
        //$mail->addBCC('info@example.com', 'name');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body_html;
        $mail->AltBody = $body_text;
        //$mail->addAttachment($_SERVER['DOCUMENT_ROOT'].'/student024/Shop/assets/logos/logo_sin_fondo.png', 'logo.png');
        $mail->send();
        header("Location: /student024/Shop/backend/views/my_orders.php?message=" . urlencode("Email confirmation sent successfully to $name."));
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: ".$e->getMessage();
    }
}

?>