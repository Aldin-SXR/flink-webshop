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
        $mail->SetFrom("activation@flinkaj.me");
        $mail->Subject = $subject;
  /*      $mail->Body = "
        <h3>Thank you for registering to our Flink Web Shop</h3>
        <hr>As a registered user, you will enjoy a permanent <strong>5% discount</strong> on all Flink products, <br>
        have the ability to save your cart for later shopping, be able to rate and review our products and have an insight into
        our latest products and technologies via the official newsletter.<br>
        <p>Your account has been successfully created and you can log into the shop using your chosen credentials, <br>
        after you activate your account by clicking on the bottom link.</p>
        <hr>
        Flink team wishes you happy and comfortable shopping!
        <hr>
        Please click on the bottom link to activate your account:<br>
        http://payscan-api.herokuapp.com/rest/verify/?email=".$email."&hash=".$hash."
        ";*/
        $mail->Body = $body;
        $mail->AddAddress($to);
        /* send activation email */
        $mail->Send();
    }
}
Mailer::mail("aldin.kovacevic.97@gmail.com", md5(rand(1, 10000)));
?> 