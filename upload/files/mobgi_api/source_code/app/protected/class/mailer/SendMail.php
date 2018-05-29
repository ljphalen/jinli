<?php
require 'PHPMailerAutoload.php';
class SendMail{
    public  $errorinfo;
    private  $email;
    private  $subject;
    private  $body;
    private  $attachment;
    private  $mailconfig;
    public function  __construct($email,$subject,$body,$mailconfig,$attachment=null) {
        $this->email=$email;
        $this->subject=$subject;
        $this->body=$body;
        $this->attachment=$attachment;
        $this->mailconfig=$mailconfig;
    }
    public function send(){
        $mail = new PHPMailer;

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->mailconfig["host"];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $this->mailconfig["username"];                 // SMTP username
        $mail->Password = $this->mailconfig["password"];                           // SMTP password
        $mail->Port=25;
        $mail->CharSet="utf-8";
        $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

        $mail->From = $this->mailconfig["from"];
        $mail->FromName = $this->mailconfig['fromname'];
        if(is_array($this->email)){
            foreach($this->email as $email){
                $mail->addAddress($email);     // Add a recipient
            }
        }else{
            $mail->addAddress($this->email);     // Add a recipient
        }
        $mail->addReplyTo($this->mailconfig["from"], $this->mailconfig['fromname']);
        //$mail->addAddress('ellen@example.com');               // Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject =$this->subject;
        $mail->Body    = $this->body;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            $this->errorinfo=$mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }
}