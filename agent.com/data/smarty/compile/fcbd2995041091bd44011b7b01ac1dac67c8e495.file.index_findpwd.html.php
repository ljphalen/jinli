<?php /* Smarty version Smarty-3.0.6, created on 2013-08-07 11:14:07
         compiled from "/work/website/agent.com/template/default/index_findpwd.html" */ ?>
<?php /*%%SmartyHeaderCode:20709138185201bb7f6aab93-86016130%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fcbd2995041091bd44011b7b01ac1dac67c8e495' => 
    array (
      0 => '/work/website/agent.com/template/default/index_findpwd.html',
      1 => 1375838016,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20709138185201bb7f6aab93-86016130',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $_smarty_tpl->getConfigVariable('siteName');?>
找回密码 - 管理中心</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/template/default/css/logo.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/validate.css" />
<script type="text/javascript" src="/template/default/js/jquery-1.4.2-min.js"></script>
<script type="text/javascript" src="/template/default/js/jquery.validate.js"></script>
<style type="text/css">
body {color:white;}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$("#theForm").validate({
		rules: {
			username: "required",
			email: "required",
			//captcha: "required"
		},
		messages: {
			username: "请输入登录帐号",
			email: "请输入邮箱",
			//captcha: "验证码"
		}
	});	
});
<?php if ($_smarty_tpl->getVariable('error')->value){?>alert('<?php echo $_smarty_tpl->getVariable('error')->value;?>
');<?php }?>
</script>
</head>
<body>
<div class="wrap tc mt">
    <form method="post" action="/index.php?ac=findpwd" id='theForm'>
    <table cellspacing="0" cellpadding="0" style="margin-top:100px" align="center">
        <tr>
            <td></td>
            <td style="padding-left: 50px">
            <table>
               <?php if (!(($tmp = @$_smarty_tpl->getVariable('sucess')->value)===null||$tmp==='' ? 0 : $tmp)){?>
                <tr>
                  <td>登录帐号：</td>
                    <td align="left"><input type="text" name="username" id="username" /></td>
                </tr>
                <tr>
                  <td>邮箱地址：</td>
                    <td align="left"><input type="text" name="email" id="email" /></td>
                </tr>
    
                <tr>
                  <td>&nbsp;</td>
                    <td align="left"><input type="submit" value="发送密码到邮箱" class="button" name="sub"/></td>
                </tr> 
                <?php }?>
                <tr>
                  <td>&nbsp;</td>
                  <td align="left">&nbsp;</td>
                </tr>
                <tr>
                  <td><?php if ((($tmp = @$_smarty_tpl->getVariable('sucess')->value)===null||$tmp==='' ? 0 : $tmp)){?>您的密码已经发送到邮箱，请注意查收<?php }?>&nbsp;</td>
                    <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<a style="color:#FFFFFF" href="/index.php"><<返回登录</a></td>
                </tr>
            </table>
          </td>
        </tr>
    </table>
    </form>
</div>
</body>