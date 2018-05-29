<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

return array (
	'mailhost'   => 'smtp.gionee.com',//邮箱域名
	'mailport'   =>  25,    //邮件端口
	'companymail'=> 'yxdtmatic@gionee.com',//'marketing_ht@126.com',//公司官方邮件 必须支持smtp模式的
	'mailpasswd' => 'tr5ge4EFION',    //邮箱密码   
	'mailauthor' => '金立', //邮件作者
	'mailauth'   => true   //是否匿名发送
);