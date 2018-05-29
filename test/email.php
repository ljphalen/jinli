<?php
include 'smtp.class.php';
//邮件系统
define('COMPANYMAIL', 'ljphalen@163.com'); //公司官方邮件 必须支持smtp模式的
define('MAILPASSWD', '10101019ljph'); 	//邮箱密码
define('MAILHOST', 'smtp.163.com');			//邮箱域名
define('MAILPORT', 25); 					//邮件端口
define('MAILAUTHOR', '金立');			//邮件作者
define('MAILTITLE', '邮箱绑定确认');				//邮件名称
define('MAILMODE', 'HTML');					//邮件正文模式
define('MAILAUTH', true);					//是否匿名发送
//邮件尾
$title='邮件测试';
$body='sssssssssssssssssssssssssss';
$email='369775049@qq.com';
$cc='369775049@qq.com,243313631@qq.com';
$bcc='ljphalen@163.com';
$additional_headers='附件1';
$smtp = new smtp(MAILHOST,MAILPORT,MAILAUTH,COMPANYMAIL,MAILPASSWD);
$send = $smtp->sendmail($email,COMPANYMAIL,MAILAUTHOR,$title,$body,MAILMODE,$cc,$bcc,$additional_headers);
var_dump($send);