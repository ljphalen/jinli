<?php
$config['level'] = array(10=>'三级渠道商',30=>'二级渠道商',200=>'管理员',222=>'超級管理员');

$config['menu'][0] = array('note'=>0,'parentNode'=>-1,'value'=>'渠道后台','url'=>'');
$config['menu'][1] = array('note'=>0,'parentNode'=>-1,'value'=>'xxx','url'=>'');
$config['menu'][2] = array('note'=>0,'parentNode'=>-1,'value'=>'xxx','url'=>'');
								 
//这里是禁止显示的地方
$config['menu_not_show'][1] = array(10); 
//$config['menu_not_show'][2] = array(10,30);

//允许操作权限
$config['priv_page']['test'] = array(30,10);
$config['priv_page']['set_person'] = array(30,10);
$config['priv_page']['reset_Pwd'] = array(30,10);
$config['priv_page']['band_email'] = array(30,10);
$config['priv_page']['user_list'] = array(30,10);
$config['priv_page']['user_add'] = array(30,10);
$config['priv_page']['welcome'] = array(30,10);
$config['priv_page']['main'] = array(30,10);

$config['priv_page']['report'] = array(30,10);
$config['priv_page']['report_count'] = array(30,10);
$config['priv_page']['report_paydetail'] = array(30,10);
$config['priv_page']['report_sub'] = array(30,10);
$config['priv_page']['report_excel'] = array();

$config['priv_page']['setting_payway'] = array();
$config['priv_page']['setting_payconfig'] = array();
$config['priv_page']['setting_ipay_rate'] = array();
$config['priv_page']['setting_yd_rate'] = array();

$config['priv_page']['mychannel'] = array(30,10);
$config['priv_page']['company_edit'] = array(30,10);
$config['priv_page']['company_list'] = array(30,10);
$config['priv_page']['findpwd'] = array(30,10);
$config['priv_page']['check'] = array(30,10);
$config['priv_page']['user_edit'] = array(30);
$config['priv_page']['graph'] = array();
$config['priv_page']['graph_draw'] = array(30);
$config['priv_page']['graph_show'] = array(30);


$config['priv_page']['nocheck'] = array('findpwd','check_email','getchannel');

//添加渠道商基本信息
$config['role'] = array(200=>'管理员',30=>'二级渠道商',10=>'三级渠道商');
$config['channel'] = array(1=>'自主渠道',2=>'移动渠道');

//错误信息
$config['error'] = array(1=>'账号或密码错误'
						,2=>'验证码错误'	
						,3=>'用户未登陆'
						,4=>'用户旧密码错误'
						,5=>'修改密码失败'
						,6=>'修改姓名失败'
						,7=>'修改姓名成功'
						,8=>'修改密码成功'	
						,9=>'两次输入的密码不一致'
						,10=>'请选择一种角色'
						,11=>'添加角色成功'
						,12=>'添加角色失败'
						,13=>'缺少公司名称'
						,14=>'缺少渠道号'
						,15=>'缺少子渠道号'
						,16=>'缺少分成比例'
						,17=>'缺少帐号'
						,18=>'缺少密码'
						,19=>'缺少姓名'
						,20=>'缺少邮箱地址'
						,21=>'权限不足'
		);


//对特殊渠道处理（8170 	360助手)，非移动支付都算入cmnet支付
$config['paytocmnet'] = array(8170);

//消费金额明细显示我方支付方式起日期
$config['paydetailenddate'] = '2013-05-03';