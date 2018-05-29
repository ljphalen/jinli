<?php 
require("../class.phpmailer.php");
$mail = new PHPMailer();
$mail->IsSMTP(); // send via SMTP
$mail->Host = "smtp.163.com"; // SMTP servers
$mail->Port =25;
$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->Username = "ljphalen@163.com"; // SMTP username
$mail->Password = "10101019ljph"; // SMTP password
$mail->From = "ljphalen@163.com";
$mail->FromName = "Mailer";
$mail->AddAddress("369775049@qq.com","Josh Adams");
//$mail->AddAddress("ellen@site.com"); // optional name
//$mail->AddReplyTo("info@site.com","Information");
 $mail->WordWrap = 50; // set word wrap
//$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); */
$mail->IsHTML(true); // send as HTML
$mail->Subject = "Here is the subject";
$mail->Body = "This is the <b>HTML body</b>";
$mail->AltBody = "This is the text-only body";
if(!$mail->Send())
{
	echo "Message was not sent <p>";
	echo "Mailer Error: " . $mail->ErrorInfo;
	exit;
}
echo "Message has been sent";