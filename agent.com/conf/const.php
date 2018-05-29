<?php
$__str = explode('.php', basename($_SERVER['SCRIPT_FILENAME']));
define('FILENAME', $__str[0]);
//密钥
define('ENCRYPTKEY','channel.v^5');
//日志地址
define('SYSLOG', SROOT.'/data/log/');
//图形参数配置地址
define('GRAPHCONFIG', SROOT.'/data/config/graph.php');
//渠道后台地址：
define('CHANNELHOST', 'http://www.channelnew.com');

define('WLECOME','/index.php?ac=login');

//邮件系统
define('COMPANYMAIL', 'system@hldgames.com'); //公司官方邮件 必须支持smtp模式的
define('MAILPASSWD', '4Wcuv9hQ'); 	//邮箱密码
define('MAILHOST', 'mail.hldgames.com');			//邮箱域名
define('MAILPORT', 25); 					//邮件端口
define('MAILAUTHOR', '同楼');			//邮件作者
define('MAILTITLE', '邮箱绑定确认');				//邮件名称
define('MAILMODE', 'HTML');					//邮件正文模式
define('MAILAUTH', true);					//是否匿名发送
define('MAILBODYHEAD', '您好:<br />请您点击以下链接以验证您的移动棋牌电子邮箱地址,');				//邮件头
define('MAILBODYTAIL', '<br>感谢您的支持！<br />');	
define('MAILWARNING','(如果无法点击，请完整的复制下面地址到您的浏览器地址栏中并打开)。<br />');												//邮件尾


define(SUCCESS, 0);//正确返回
define(NOTLOGIN, 1);//未登陆
define(PASSWDERR, 2);//密码错误

define('PAGESIZE', 15);
define('SELFCHANNEL', 1);
define('MOBILECHANNEL', 2);
define('CACHE_USER_LIST',SROOT.'/data/cache/userList/');
define('WLECOME','/index.php?ac=login');