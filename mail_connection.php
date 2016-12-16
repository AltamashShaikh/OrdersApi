<?php

require './PHPMailer/PHPMailerAutoload.php';

class MailConnection {

    function get_mail_connection_obj() {
        $mail = new PHPMailer;

        $mail->SMTPDebug = 0;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'yourgmailid';                 // SMTP username
        $mail->Password = 'yourgmailpassword';                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;
        
        return $mail;
    }

}
