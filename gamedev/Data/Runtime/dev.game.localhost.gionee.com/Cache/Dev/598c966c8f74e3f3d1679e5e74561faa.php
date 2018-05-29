<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo C('SITENAME');?></title>
<meta name="keywords" content=""/>
<meta name="description" content=""/>
<script src="<?php echo cdn('PUBLIC');?>/common/js/jquery-1.10.2.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/app/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo cdn('PUBLIC');?>/common/js/jquery-migrate-1.2.1.js"></script>
<link rel="stylesheet" href="<?php echo cdn('PUBLIC');?>/common/css/base.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo cdn('PUBLIC');?>/common/css/common.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo cdn('PUBLIC');?>/common/css/page.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
var onShowHtml="";
</script>
</head>
<body>

<?php if(strtolower(ACTION_NAME) != "perfect"): ?>
	<div class="head clearfix">
		<div class="w">
	    	<h1 class="fl logo"><a href="<?php echo U('/');?>" title="<?php echo C('SITENAME');?>" ><?php echo C("SITENAME");?></a></h1>
	        <div class="nav fr">
				<a href="<?php echo U('help/index');?>" target="_help">帮助</a>
				<?php if(empty($uid)): ?><a href="<?php echo U('login/reg');?>">注册</a>
				<a href="<?php echo U('login/login');?>">登录</a>
				<?php else: ?>
				<a href="<?php echo U('user/info');?>">个人中心</a>
				<a href="<?php echo U('message/index');?>">消息<?php if($message_count > 0): ?><b style="color:red;">(<?php echo ($message_count); ?>)</b><?php endif; ?></a>
				<a href="<?php echo U('login/logout');?>">退出</a><?php endif; ?>
	        </div>
	    </div>
	</div>
<?php else: ?>
	<div class="head clearfix">
		<div class="w">
	    	<h1 class="fl logo"><?php echo C("SITENAME");?></h1>
	        <div class="nav fr">
	        	<notmpty name="uid">
				<a href="<?php echo U('login/logout');?>">退出</a>
				</notmpty>
	        </div>
	    </div>
	</div>
<?php endif; ?>

<?php if($uid > 0 && in_array(strtolower(MODULE_NAME), array("user", "apps", "union", "chart", "message", "help"))): ?>
	<div class="sec-nav">
		<div class="shadow"></div>
		<div class="w">
			<a href="<?php echo U('Apps/index');?>" id="apps_tab1"><span>我的应用</span><em></em></a><ins>|</ins>
			<a href="<?php echo U('Union/index');?>" id="union_tab1"><span>游戏联运</span><em></em></a><ins>|</ins>
			<a href="<?php echo U('Chart/index');?>" id="chart_tab1"><span>数据统计</span><em></em></a><ins>|</ins>
			<a href="<?php echo U('Help/sdk');?>" id="sdk_tab1"><span>sdk下载</span><em></em></a>
	  	</div>
	</div>
	<script>$("#<?php echo strtolower(MODULE_NAME);?>_tab1").addClass("on");</script>
<?php endif; ?>

<div id="wrap">
	<div class="container">

<div class="mainw mainw2 ">
	<div class="rmain fl">
		<div class="panel">
			<h2 class="yyname">注册成功，请验证您的邮件进行激活</h2>
			<div class="article-c">
				<p>系统已经发送一封激活邮件到您的邮箱：<?php echo ($email); ?>，请注意查收。<?php if($validateEamil): ?><a href="http://<?php echo ($validateEamil); ?>" target="_blank" style="color: #0060FF;font-size: 16px;">点这里快速访问您的邮箱</a><?php endif; ?></p>
				<p>点击邮件中的链接用以激活您的帐户。如果没有收到激活邮件，您可以点 <a href="__APP__/login/resend/email/<?php echo ($email); ?>"  style="color: #0060FF;font-size: 16px;">重新发送激活邮件</a>
				</p>
			</div>
		</div>
	</div>
</div>
	</div>
</div>

<div class="footer clearfix">
	<div class="footerc">
		<span>
			增值电信业务经营许可证：<a href="http://www.gionee.com/licence3.shtml" target="_blank">粤B2-20120350</a>
		        <a href="http://www.gionee.com/licence1.shtml" target="_blank">粤网文[2013]029-029号</a>
		        <a href="http://www.gionee.com/licence2.shtml" target="_blank">粤文市审[2012]196号</a>
		        <a href="http://www.gionee.com/licence4.shtml" target="_blank" title="营业执照">营业执照</a>
		</span>
		<span>如有疑问或需要帮助，请邮件至dev.game@gionee.com</span>
	</div>
</div>


</body>
</html>