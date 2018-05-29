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

<div class="mainw mainw2 clearfix">
	<div class="rmain regm fl">
	<form class="form-horizontal regform" id="regform" role="form" action="__URL__/regsub/" method="post" autocomplete="off" />
   	  <h2 class="yyname">合作伙伴注册</h2>
      <div class="flow-w flow-w1">
          <div class="flow"><span class="on">注册帐号</span><span>激活帐号</span><span>提交资料</span><span>提交成功</span></div>
        </div>
      <div class="tip">提示：为了保证软件质量，目前只接受企业开发者注册，请谅解</div>
      <div class="formd formd3 mt30">
      	<div class="lid">
        	<label class="tlab"><em>* </em>邮箱：</label>
        	<div class="colr fl">
      	 	  <input type="text" class="int" id="email" tabindex="1" name="email" placeholder="@" autocomplete="off" />
         	  <span id="emailTip" class="twrong fl"></span>
              <span class="help-block">此邮箱地址作为登录帐户，注册后不允许更改</span>
         	</div>
        </div>
        <div class="lid">
        	<label class="tlab"><em>* </em>密码：</label>
        	<div class="colr fl">
      	 	  <input type="password" id="pwd" tabindex="1" name="password" placeholder="Password" class="int" autocomplete="off" />
         	  <span id="pwdTip" class="tright fl"></span>
              <span class="help-block">密码长度6-20位，字母区分大小写</span>
         	</div>
        </div>
        <div class="lid">
        	<label class="tlab"><em>* </em>确认密码：</label>
        	<div class="colr fl">
      	 	  <input type="password" id="repwd" tabindex="1" name="repassword" placeholder="Password" class="int" autocomplete="off" />
      	 	  <span id="repwdTip" class="twrong fl"></span>
              <span class="help-block">密码长度6-20位，字母区分大小写</span>
         	</div>
        </div>
        <div class="lid">
        	<label class="tlab"><em>* </em>验证码：</label>
        	<div class="colr fl">
      	 	  <input type="text" id="authcode" maxlength="4" name="authcode" tabindex="1" placeholder="验证码" class="int yzm" autocomplete="off" />
      	 	  <img id="verifyImg" src="__APP__/Auth/verify/"
								onClick="gioneeReg.fleshVerify();$('#authcode').val('');" border="0" alt="点击刷新验证码"
								style="cursor: pointer" align="absmiddle" class="checkimg fl" />
      	 	  <a href="javascript:void(0)" onClick="gioneeReg.fleshVerify();$('#authcode').val('');" class="nosee fl"><em>看不清？</em>换一张</a>
      	 	  <span id=authcodeTip class="twrong fl"></span>
         	</div>
        </div>
      	<div class="btnw loginregbtn"><button type="submit" class="btn-bred">同意开发者协议并注册</button></div>
      	<div class="lid">
        	<div class="colr" style="text-align:left; margin-top:10px;padding-left: 200px;">
              <span class="help-block"><a href="<?php echo U('help/page', array('id'=>7));?>" style="color: #232323; text-decoration: underline;" target="_blank" >阅读开发者协议全文</a></span>
         	</div>
        </div>
      </div>
    </div>
    </form>
</div>
<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/app/validate/formValidator-4.1.1.js"></script>
<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/app/validate/themes/126/js/theme.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo cdn('PUBLIC');?>/app/validate/themes/126/style/style.css" />
<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/app/validate/formValidatorRegex.js"></script>
<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/common/js/validate.js"></script>
<script type="text/javascript" src="<?php echo cdn('PUBLIC');?>/common/js/form/reg.js"></script>
<script type="text/javascript">
if ($ != jQuery) {
    $ = jQuery.noConflict();
}
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