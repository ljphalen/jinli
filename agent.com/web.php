<?php
/****************************************** 系统配置 ******************************************/

//调试(*)
define('DEBUG', true);
define('LOG_ON', true);
define('ERROR_URL', '');
define('LOG_PATH', SROOT.'/data/log/');

//系统检查(*)
define('CHECK', true);

//字符集(*)
define('CHARSET', 'utf-8');

//模板目录(*)
$ini_template = 'default';

//系统日志路径(*)
define('SYS_LOG_FILE', SROOT.'/data/log/sys.log');

//显示错误的模板(*)
define('TEMPLATE_ERROR_UI', SROOT.'/template/'.$ini_template.'/inc_errui.htm');		//显示给用户
define('TEMPLATE_ERROR_DEV', SROOT.'/template/'.$ini_template.'/inc_errdev.htm');	//显示给程序员

//系统错误信息(*)
$ini_error = array('0'=>'发生未知错误', '1'=>'非法调用页面'
	,'101'=>'配置文件 conf/web.php 的 ini_db 数组中未找到键'
	,'102'=>''
	,'204'=>''
);



/****************************************** 应用配置 ******************************************/
//站点名称
define('SITE_NAME', '名游渠道商管理后台');


//数据库配置(*)：格式 array('key1'=>array('host'=>server, 'port'=>port, 'uid'=>userid, 'pwd'=>password, 'db'=>dbname, 'type'=>dbtype)...)
$ini_db = array(
	 'db1'=>array(
	 		'host'=>'119.134.251.199', 
	 		'port'=>3306, 
	 		'uid'=>'webpay', 
	 		'pwd'=>'sT@dmin<>2012', 
	 		'db'=>'agent', 
	 		'type'=>'mysqli')
	 //'db1'=>array('host'=>'192.168.5.48', 'port'=>3306, 'uid'=>'root', 'pwd'=>'123456', 'db'=>'agent_new', 'type'=>'2')
	//,'db2'=>array('host'=>'localhost', 'port'=>3306, 'uid'=>'test', 'pwd'=>'123456', 'db'=>'test', 'type'=>'mysqli')
);

//缓存文件后缀名
define('CACHE_FILE_EXT', '.dat');


//各游戏配置
$ini_games = array(
	
);


//渠道类型
$ini_channeltypes = array(1=>'自主渠道', 2=>'移动渠道');

//爱贝详细支付方式
$ini_ipay_paytypes = array(
		 50 => '未知'
		,51 => '支付宝'
		,52 => '充值卡'
		,53 => '移动话费'
		,54 => '联通话费'
		,55 => '电信话费'
		,56 => '银行卡'
		,57 => '爱贝币'
		,58 => '游戏点卡'
		,59 => '财付通'
);

//爱贝各支付交易费率
$ini_ipay_payway_rates = array(
		 50 => 0.5
		,51 => 0.027
		,52 => 0.04
		,53 => 0.5
		,54 => 0.5
		,55 => 0.5
		,56 => 0.01
		,57 => 0.05
		,58 => 0.2
		,59 => 0.05
);

//爱贝手续费率
$ini_ipay_pay_rate = 0.06;

//移动费率（加坏帐）
$ini_yd_pay_rate = 0.32;
//移动税率
$ini_yd_pay_tax_rate = 0.06;

//报表数据开始日期
$ini_report_startdate = '2012-04-01';
?>