<?php
/*
 * 用户操作接口入口文件
 * @since 1.0.0 (2012-08-14)
 * @version 1.0.0 (2012-08-14)
 * @author jun <huanghaijun@mykj.com>
 */
header("content-type:text/html;charset=utf-8");
//加载公共文件
require 'comm.php';


//获取GET参数
$ac = get('ac');

if($ac == ''){
	$ac = get('method');
}

//允许的方法
$acs = array('login','main','menu','captcha','welcome','logout'
		,'report','report_sub','report_count','report_paydetail','report_excel'
		,'set_person','reset_Pwd','band_email'
		,'user_list','user_add','user_edit'
		,'cacheList'
		,'check','check_email'
		,'setting_payway','setting_payconfig','setting_ipay_rate','setting_yd_rate','setting_yd_channel','setting_lt_rate','setting_base','graph'
                ,'company_list','company_edit'
                ,'mychannel'
                ,'findpwd','getchannel'
                ,'graph','graph_show','graph_draw'
		);

//判断方法是否有效
/*if (empty($ac) || !in_array($ac, $acs)) {
	//处理代码
	$ac = 'main';
}
*/
if (empty($ac)||!in_array($ac, $acs)) {
	//处理代码
	$ac = 'login';
}
if(!in_array($ac,array('login', 'captcha', 'logout','check_email'))){
	include SROOT.'/source/inc/check.php';
}


//加载逻辑处理程序]
include SROOT.'/source/index_'.$ac.'.php';
?>