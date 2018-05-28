<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once '../../vendor/autoload.php';

class Mailer {
    public static function mail($to, $subject, $body) {
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->isSMTP();
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "n1plcpnl0080.prod.ams1.secureserver.net";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = "noreply@flinkaj.me";
        $mail->Password = "*~Y9vNB6eevVG%ms";
        $mail->SetFrom("webshop@flinkaj.me");
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
        /* send activation email */
        $mail->Send();
    }
}
?> 