<?php /* Smarty version Smarty-3.0.6, created on 2013-08-07 09:17:14
         compiled from "/work/website/agent.com/template/default/index_main.html" */ ?>
<?php /*%%SmartyHeaderCode:9503622675201a01a327588-89801404%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '624071222d079384a4a30a52201b86584435ef7a' => 
    array (
      0 => '/work/website/agent.com/template/default/index_main.html',
      1 => 1375838016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9503622675201a01a327588-89801404',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>同楼渠道商管理后台</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="/template/default/css/common.css" />
<link rel="stylesheet" href="/template/default/css/dtree.css" />
<script type="text/javascript" src="/template/default/js/dtree.js"></script>
<script type="text/javascript" src="/template/default/js/jquery-1.4.2-min.js"></script>
<!--[if IE 6]>
    <script src="/template/default/js/DD_belatedPNG_0.0.8a-min.js"></script>
    <script>
        DD_belatedPNG.fix('img,.png');
    </script>
<![endif]-->
</head>
<body>
<div class="wrap">
<div class="header">
	<h1><img src="/template/default/img/logo.png" alt="" /><img src="/template/default/img/txt.png" alt="系统管理" /></h1>
	<div class="info-wrap">
		<div class="info fl pt5 mt15">
			<span class="user">当前用户：</span><span class="c1"><?php echo $_smarty_tpl->getVariable('userinfo')->value['username'];?>
</span>&nbsp;&nbsp;&nbsp;&nbsp;
			<span>角色：</span><span class="c1"><?php echo $_smarty_tpl->getVariable('userinfo')->value['rolename'];?>
</span>&nbsp;&nbsp;&nbsp;&nbsp;
			<span>登录时间：</span><span class="c1"><?php echo $_smarty_tpl->getVariable('userinfo')->value['dateline'];?>
</span>
		</div>
		<a href="?ac=logout" class="btn">
			<span class="exit png">退出系统</span>
		</a>
	</div>
</div>
<div class="main">
	<div class="aside">
		<script type="text/javascript">						
			var d = new dTree('d');
			
			<?php if ($_smarty_tpl->getVariable('userinfo')->value['level']>=200){?>
			d.add(0,-1,"同楼渠道商管理后台","/index.php?ac=welcome");
			d.add(1,0,"查询统计报表","");
			d.add(2,0,"用户管理","");
			d.add(3,0,"渠道商管理","");
			d.add(4,0,"个人信息","");
			d.add(5,0,"系统设置","");
			
			d.add(6,1,"自主渠道商","/index.php?ac=report&channeltype=1");
			d.add(7,1,"移动渠道商","/index.php?ac=report&channeltype=2");
			d.add(8,1,"导出对帐单","/index.php?ac=report_excel");
			//d.add(8,1,"爱贝交易统计","/index.php?ac=report_ipay");
			
				
			d.add(9,2,"自主渠管理员列表","/index.php?ac=user_list&channeltype=1");		
			d.add(10,2,"移动渠道管理员列表","/index.php?ac=user_list&channeltype=2");		
			d.add(11,2,"添加用户","/index.php?ac=user_add");	
			
			d.add(12,3,"自主渠道商","/index.php?ac=company_list&channeltype=1");
			d.add(13,3,"移动渠道商","/index.php?ac=company_list&channeltype=2");
			
			d.add(14,4,"个人设置","/index.php?ac=set_person");
			d.add(15,4,"修改密码","/index.php?ac=reset_Pwd");
			d.add(16,4,"邮箱绑定","/index.php?ac=band_email");
			
			d.add(17,5,"图形参数设置","/index.php?ac=graph");
			d.add(18,5,"第三方支付方式配置","/index.php?ac=setting_payway");
			d.add(19,5,"我方他方支付方式配置","/index.php?ac=setting_payconfig");
			d.add(20,5,"移动平台分成比率配置","/index.php?ac=setting_yd_rate");
			d.add(21,5,"爱贝渠道分成比率配置","/index.php?ac=setting_ipay_rate");
			d.add(22,5,"移动自有渠道费率配置","/index.php?ac=setting_yd_channel");
			d.add(23,5,"联通分成比率配置","/index.php?ac=setting_lt_rate");
			d.add(24,5,"对帐单基本信息配置","/index.php?ac=setting_base");
			<?php }?>


			<?php if ($_smarty_tpl->getVariable('userinfo')->value['level']==30){?>
			d.add(0,-1,"同楼渠道商管理后台","/index.php?ac=welcome");
			d.add(1,0,"查询统计报表","");
			d.add(2,0,"用户管理","");
			d.add(3,0,"渠道商管理","");
			d.add(4,0,"个人信息","");
			
			d.add(6,1,"查询统计报表","/index.php?ac=report");
			
				
			d.add(9,2,"管理员列表","/index.php?ac=user_list");		
			d.add(11,2,"添加用户","/index.php?ac=user_add");	
			
			d.add(12,3,"渠道商列表","/index.php?ac=company_list");
			
			d.add(14,4,"个人设置","/index.php?ac=set_person");
			d.add(15,4,"修改密码","/index.php?ac=reset_Pwd");
			d.add(16,4,"邮箱绑定","/index.php?ac=band_email");
			<?php }?>

			<?php if ($_smarty_tpl->getVariable('userinfo')->value['level']==10){?>
			d.add(0,-1,"同楼渠道商管理后台","/index.php?ac=welcome");
			d.add(1,0,"查询统计报表","");
			d.add(3,0,"渠道商管理","");
			d.add(4,0,"个人信息","");
			
			d.add(6,1,"查询统计报表","/index.php?ac=report");
				
			d.add(12,3,"我的渠道","/index.php?ac=mychannel");
			
			d.add(14,4,"个人设置","/index.php?ac=set_person");
			d.add(15,4,"修改密码","/index.php?ac=reset_Pwd");
			d.add(16,4,"邮箱绑定","/index.php?ac=band_email");
			<?php }?>

		    document.write(d);
		   		
		</script>
	</div>
	<div class="frame-wrap">
		<div class="arrow"><span></span></div>
		<iframe name="frame" src="index.php?ac=report" frameborder="0"></iframe>
	</div>	
</div>

</div>
<script type="text/javascript">
	$(function(){
		var $aside = $(".aside");
		var $frame = $(".frame-wrap");
		var $arrow = $(".arrow");
		$(".arrow").toggle(function(){
			$aside.animate({width:"0px"},"fast");
			$frame.animate({marginLeft:"0px"},"fast");
			$arrow.addClass("arrow-right");
		},function(){
			$aside.animate({width:"220px"},"fast");
			$frame.animate({marginLeft:"221px"},"fast");
			$arrow.removeClass("arrow-right");
		});
		
		var $main = $(".main");
		$(window).resize(function(){
			if($(window).height() > 650){
				$main.height($(window).height() - 72);
			} else {
				$main.height("650px");
			}
		});
		$(window).resize();
		
	});
</script>
</body>
</html>
