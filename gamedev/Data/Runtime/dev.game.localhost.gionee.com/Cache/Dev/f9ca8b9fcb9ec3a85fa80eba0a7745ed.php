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

<div class="mainw mainw3">
	<div class="mok">
		<div class="successw mt <?php if(($status) == "0"): ?>mt-wrong<?php else: ?>success<?php endif; ?>">
    		<h2><?php echo ($msgTitle); ?></h2>
       		<p><?php echo ($message); echo ($error); ?></p>
    	</div>
		
		<?php if(isset($closeWin)): ?><p class="mb">系统将在<em class="fblue" id="wait"><?php echo ($waitSecond); ?></em>秒后自动跳转，如果不想等待，直接<a id="href" href="<?php echo ($jumpUrl); ?>" class="fblue">点击这里</a>跳转</p>
		</div><?php endif; ?>
		
		<?php if(isset($closeWin)): ?><p class="mb">系统将在<em class="fblue" id="wait"><?php echo ($waitSecond); ?></em>秒后自动关闭，如果不想等待，直接<a id="href" href="<?php echo ($jumpUrl); ?>" class="fblue">点击这里</a>跳转</p><?php endif; ?>
		
		<?php if(!isset($closeWin)): ?><p class="mb">系统将在<em class="fblue" id="wait"><?php echo ($waitSecond); ?></em>秒后自动跳转，如果不想等待，直接<a id="href" href="<?php echo ($jumpUrl); ?>" class="fblue">点击这里</a>跳转</p><?php endif; ?>
    	
	</div>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time == 0) {
		location.href = href;
		clearInterval(interval);
	};
}, <?php echo ($waitSecond); ?>000);
})();
</script>
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