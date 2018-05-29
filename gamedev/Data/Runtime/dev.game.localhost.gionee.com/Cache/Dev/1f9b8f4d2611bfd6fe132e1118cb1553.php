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

<div class="head indexh clearfix">
	<div class="w">
    	<h1 class="fl logo"><a href="<?php echo U('/');?>" title="<?php echo C('SMTP.SMTP_NAME');?>" ><?php echo C("SMTP.SMTP_NAME");?></a></h1>
        <div class="rega fr">还不是我们的用户？<a href="<?php echo U('login/reg');?>">立即注册</a> <a href="<?php echo U('help/index');?>">帮助</a> </div>
    </div>
</div>
<div class="banner" <?php if(!empty($slideimage)): ?>style="background: #f4f4f4 url(<?php echo ($slideimage); ?>) center 14px no-repeat;"<?php endif; ?>>
	<div class="loginw">
		<div class="login">
	        <h2></h2>
	        <div class="loginc">
	        <form class="form-horizontal" method="post" id="loginForm" onsubmit="return false;" autocomplete="off" onsubmit="if(event.keyCode==13||event.which==13){return false;}">
	            <p><input type="text" id="email" name="email" placeholder="邮箱" class="int mail inton" tabindex="1" /></p>
	            <p><input type="password" id="password" name="password" placeholder="登录密码" class="int pass" tabindex="2" /></p>
	            <p><input type="text" id="authcode" name="authcode" placeholder="验证码" class="int yzm" tabindex="3" /><img id="verifyImg" src="<?php echo U("Auth/verify");?>"
	onClick="gioneeLogin.fleshVerify();$('#authcode').val('');" border="0" alt="点击刷新验证码"
	style="cursor: pointer" align="absmiddle" alt="" class="checkimg fl" /><a href="javascript:void(0)" onClick="gioneeLogin.fleshVerify();$('#authcode').val('');" class="nosee fl"><em>看不清？</em>换一张</a></p>
	            <p class="checks">
	                <span class="fl"><a href="<?php echo U("Login/reg");?>" class="forget">新用户注册</a></span>
	                <span class="fl"><a href="<?php echo U("Auth/index");?>" class="forget">忘记密码？</a></span>
	            </p>
	            <p><input type="submit" value="立即登录" class="loginbtn" id="Sub" tabindex="4" /></p>
	        </form>
	        <div class="wrong" style="display:hidden;" id="errorMsg"></div>
	        <script>$("#errorMsg").hide();</script>
	        </div>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/app/validate/formValidator-4.1.1.js"></script>
	<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/app/validate/themes/Default/js/theme.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo cdn('PUBLIC');?>/app/validate/themes/Default/style/style.css" />
	<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/app/validate/formValidatorRegex.js"></script>
	<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/common/js/form/login.js"></script>
</div>
<div class="tool clearfix">
	<div class="toolc">
	<?php $info1 = D('Article')->where(array("category"=>1, "status"=>1))->find(); $info2 = D('Article')->where(array("category"=>2, "status"=>1))->find(); $info3 = D('Article')->where(array("category"=>3, "status"=>1))->find(); $info4 = D('Article')->where(array("category"=>4, "status"=>1))->find(); ?>
    	<a href="<?php echo U('help/page', array('id'=>$info1['id']));?>" class="ico1"><em></em>注册申请</a>
        <a href="<?php echo U('help/page', array('id'=>$info2['id']));?>" class="ico2"><em ></em>应用发布</a>
        <a href="<?php echo U('help/page', array('id'=>$info3['id']));?>" class="ico3"><em></em>游戏联运</a>
        <a href="<?php echo U('help/page', array('id'=>$info4['id']));?>" class="ico4"><em></em>其它文档</a>
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