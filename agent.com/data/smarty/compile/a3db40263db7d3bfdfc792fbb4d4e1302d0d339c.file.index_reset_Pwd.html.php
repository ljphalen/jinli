<?php /* Smarty version Smarty-3.0.6, created on 2013-08-10 15:11:00
         compiled from "/work/website/agent.com/template/default/index_reset_Pwd.html" */ ?>
<?php /*%%SmartyHeaderCode:11967576285205e784a9b1b2-97753799%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a3db40263db7d3bfdfc792fbb4d4e1302d0d339c' => 
    array (
      0 => '/work/website/agent.com/template/default/index_reset_Pwd.html',
      1 => 1375838017,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11967576285205e784a9b1b2-97753799',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?> <?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<link href="/template/default/css/validate.css" rel="stylesheet"
	type="text/css" />
<script src="/template/default/js/jquery.validate.js" type="text/javascript"
	language="javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#theForm").validate({
		rules: {
			oldpwd:"required",
			newpwd:{
				required:true,
				minlength:6,
				maxlength:32
			},
			repwd: {
				required:true,
				equalTo:"#newpwd"
			}
		},
		messages: {
			oldpwd: "请输入旧密码",
			newpwd:{
				required:"请输入新密码",
				minlength:"密码不能小于6个字符",
				maxlength:"密码不能多于32个字符"
			},
			repwd: {
				required:"请再次输入密码",
				equalTo:"再次输入密码不正确"
			}
		}
	});	
});
</script>
<h1><span class="action-span"></span>
<span class="action-span1">个人信息</span><span id="search_id"
	class="action-span1"> - <?php echo $_smarty_tpl->getVariable('actionText')->value;?>
</span>
<div style="clear: both"></div>
</h1>

<div class="main-div">
<form action="/index.php?ac=reset_Pwd" method="post"
	enctype="multipart/form-data" name="theForm" id="theForm">
<table width="100%">
	<?php if ($_smarty_tpl->getVariable('error')->value){?>
	<tr align="center">
		<td colspan="2" style="color: red"><?php echo $_smarty_tpl->getVariable('error')->value;?>
</td>
	</tr>
	<?php }?>
	<tr>
    <td colspan="3">
    <div class="main-div">
        <h1>
            <div class="section_title">
                密码设置
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <td class="label_2" width="301">旧密码：</td>
                    <td width="768"><input id="oldpwd" type="password" name="oldpwd" /><label><span>*</span>请输入密码</label></td>
                </tr>
                <tr>
                    <td class="label_2" width="301">新密码：</td>

                    <td><input id="newpwd" type="password" name="newpwd" /><label><span>*</span>6~32字符英文字母、数字或特殊字符任意组合</label></td>
                </tr>
                <tr>
                    <td class="label_2" width="301">确认密码：</td>
                    <td><input id="repwd" type="password" name="repwd" /><label><span>*</span>请再次输入密码</label></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    </td>
    </tr>
    <tr>
        <td colspan="2" align="center"><input type="submit" name="smt" id="smt" value="提交"
            class="button" /></td>
    </tr>
  </tbody>
</table>
</form>
</div>

<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/template/default/js/common.js" type="text/javascript"
	language="javascript"></script>