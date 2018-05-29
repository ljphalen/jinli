<?php
echo "dddddddddd";
include 'class.smtp.php';
//邮件系统
define('COMPANYMAIL', 'system@hldgames.com'); //公司官方邮件 必须支持smtp模式的
define('MAILPASSWD', '4Wcuv9hQ'); 	//邮箱密码
define('MAILHOST', 'mail.hldgames.com');			//邮箱域名
define('MAILPORT', 25); 					//邮件端口
define('MAILAUTHOR', '同楼');			//邮件作者
define('MAILTITLE', '邮箱绑定确认');				//邮件名称
define('MAILMODE', 'HTML');					//邮件正文模式
define('MAILAUTH', true);					//是否匿名发送
//邮件尾
$title='test';
$msg='sssssssssssssssssssssssssss';
$email='369775049@qq.com';

$smtp = new smtp(MAILHOST,MAILPORT,MAILAUTH,COMPANYMAIL,MAILPASSWD);
$send = $smtp -> sendmail($email,COMPANYMAIL,MAILAUTHOR,$title,$msg,MAILMODE);
var_dump($send);