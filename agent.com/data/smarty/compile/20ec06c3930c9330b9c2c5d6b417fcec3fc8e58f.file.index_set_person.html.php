<?php /* Smarty version Smarty-3.0.6, created on 2013-08-07 11:16:29
         compiled from "/work/website/agent.com/template/default/index_set_person.html" */ ?>
<?php /*%%SmartyHeaderCode:9275117185201bc0d289764-16950696%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '20ec06c3930c9330b9c2c5d6b417fcec3fc8e58f' => 
    array (
      0 => '/work/website/agent.com/template/default/index_set_person.html',
      1 => 1375838019,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9275117185201bc0d289764-16950696',
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
function clen(s) {
  var l = 0;
  var a = s.split("");
   for (var i=0;i<a.length;i++) {
     l++;
   }
  return l;
}

jQuery.validator.addMethod("isLenNickname", function(value, element){   
	   var length = clen(value);
	   return length>=2&&length<=9 ;
   }, "长度为2-9个字");

$(document).ready(function() {
	$("#theForm").validate({
		rules: {
			nickname:{
				required:true,
				//isLenNickname:true,
				minlength:2,
				maxlength:9
			}
		},
		messages: {
			nickname:{
				required:"请输入姓名",
				//isLenNickname:"长度为2-9个字",
				minlength:"最小2个字",
				maxlength:"最大9个字"
			}
		}
	});	
	
	
	$("#role_id").change(function(){
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
<form action="/index.php?ac=set_person" method="post"
	enctype="multipart/form-data" name="theForm" id="theForm">
    <input type="hidden" name="userid" value="<?php echo $_smarty_tpl->getVariable('admin')->value['userid'];?>
" />
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
                用户设置
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <td class="label_2" width="336">登录帐号：</td>
                    <td width="733"><label><?php echo $_smarty_tpl->getVariable('personName')->value;?>
</label></td>
                </tr>
                <tr>
                    <td class="label_2" width="336">角色类型：</td>
                    <td><label><?php echo $_smarty_tpl->getVariable('personRole')->value;?>
</label></td>
                </tr>
                <tr>
                    <td class="label_2" width="336">姓名：</td>
                    <td><input type="text"  name="nickname" id="nickname" value="<?php echo $_smarty_tpl->getVariable('personNickname')->value;?>
" /></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    </td>
    </tr>
    <tr>
        <td colspan="2" align="center"><input type="submit" name="smt" id="smt" value="提交" class="button" /></td>
    </tr>
  </table>
</form>
</div>

<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/template/default/js/common.js" type="text/javascript"></script>
