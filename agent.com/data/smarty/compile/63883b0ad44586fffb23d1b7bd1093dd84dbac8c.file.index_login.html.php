<?php /* Smarty version Smarty-3.0.6, created on 2013-08-13 11:35:36
         compiled from "/work/website/agent.com/template/default/index_login.html" */ ?>
<?php /*%%SmartyHeaderCode:5109571585209a9885dcf72-73492702%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '63883b0ad44586fffb23d1b7bd1093dd84dbac8c' => 
    array (
      0 => '/work/website/agent.com/template/default/index_login.html',
      1 => 1376364907,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5109571585209a9885dcf72-73492702',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>登录——同楼渠道商管理后台管理系统</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="/template/default/css/logo.css" />
<script type="text/javascript" src="/template/default/js/jquery-1.4.2-min.js"></script>
<script type="text/javascript" src="/template/default/js/jquery.validate.js"></script>
<!--[if IE 6]>
    <script src="js/DD_belatedPNG_0.0.8a-min.js"></script>
    <script type="text/javascript">
    	DD_belatedPNG.fix('.ie6png');
    </script>
<![endif]-->
</head>
<script type="text/javascript">
$(function(){
	
	$("#loginForm").validate({
		errorPlacement:function(error,element){
			error.appendTo(element.parents("tr").next("tr").children(".error-tips"));
		},
		errorElement:"span",
		rules:{
			"username":"required",
			"password":"required",
			"safecode":"required"
		},
		messages:{
			"username":"用户名不能为空",
			"password":"密码不能为空",
			"safecode":"验证码不能为空"
		}
	});
	
	$(document).keyup(function(evt){
		if(evt.keyCode == 13){
			//$("#loginForm").submit();
			$("#submitBtn").submit();
		}
	});
	
});
</script>
<body>
<div class="wrap tc mt">
	<div class="logo-wrap">
		<span class="f28">同楼渠道商管理后台</span>
	</div>
	<br/>
	<div class="form-wrap">
		<form id="loginForm" method="post" action="index.php?ac=login">
			<div class="input-wrap" style="text-align:left;<?php if ($_smarty_tpl->getVariable('loginerrtimes')->value<3){?>margin-top:18px;<?php }?>">
				<table class="mc">
					<tr>
						<td><label for="username">用户名</label></td>
						<td colspan="2"><input id="username" autocomplete="off" type="text" name="username" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="error-tips" colspan="2"></td>
					</tr>
					<tr>
						<td><label for="password">密　码</label></td>
						<td colspan="2"><input id="password" autocomplete="off" type="password" name="password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td class="error-tips"  colspan="2"></td>
					</tr>
					 
                    <?php if ($_smarty_tpl->getVariable('loginerrtimes')->value>=3){?>
					<tr>
						<td><label for="safecode">验证码</label></td>
						<td><input id="safecode" autocomplete="off" type="text" name="captchStr" style="width:55px;" /></td>
						<td align="right">
						<img src="index.php?ac=captcha" border="0" align="absmiddle" style="cursor:pointer;" title="点击刷新验证码" onclick="javascript:this.src='index.php?ac=captcha&t='+new Date().getTime()" />
					</tr>
                    <?php }?>
                  
					<tr>
						<td>&nbsp;</td>
						<td class="error-tips"  colspan="2"></td>
					</tr>
				</table>
                <p><a href="/index.php?ac=findpwd">忘记密码？点击重置密码</a></p>
			</div>
			<div class="btn-wrap">
				<input class="btn" name="submitBtn" id="submitBtn" type="submit" value="登 录" />
			</div>
		</form>		
	</div>
</div>
</body>
</html>
