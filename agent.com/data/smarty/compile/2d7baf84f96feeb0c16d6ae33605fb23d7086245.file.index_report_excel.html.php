<?php /* Smarty version Smarty-3.0.6, created on 2013-08-19 15:48:12
         compiled from "D:\www\trunk\agent.com/template/default/index_report_excel.html" */ ?>
<?php /*%%SmartyHeaderCode:31989518b19e530d9a9-35140513%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d7baf84f96feeb0c16d6ae33605fb23d7086245' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_report_excel.html',
      1 => 1375855922,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31989518b19e530d9a9-35140513',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?>
<?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<link href="/template/default/css/validate.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/template/default/js/jquery.validate.js"></script>
<script type="text/javascript" src="/template/default/css/main.css"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#theForm").validate({
	});	
});

function create_excel(obj,ym){
	$(obj).attr('disabled',true);
	$(obj).val('正在生成...');
	$.get("index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&ym="+ym+"&opt=create&t="+new Date().getTime(),function(data){
		if(data.status=='1'){
			alert('生成对帐单['+ym+'对帐单成功]');
			document.location.reload();
		}else{
			alert('生成对帐单失败]');
		}
		$(obj).val('生成对帐单');
		$(obj).attr("disabled",false);
	},'json');
}
</script>
<h1><span class="action-span"></span>
<span class="action-span1">查询统计报表</span><span id="search_id"
	class="action-span1"> - <?php echo $_smarty_tpl->getVariable('actionText')->value;?>
</span>
<div style="clear: both"></div>
</h1>

<div class="main-div">
<form action="index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
" method="post" enctype="multipart/form-data" name="theForm" id="theForm">
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
                <?php echo $_smarty_tpl->getVariable('actionText')->value;?>
            </div>
        </h1>
        <table border="0" cellpadding="4" cellspacing="1" id="list-table__" style="border:1px;">
            <tbody>
                <tr>
                     <td align="center" nowrap="nowrap">日期</td>
                     <td align="center" nowrap="nowrap">我方接入计费平台对帐单</td>
                     <td align="center" nowrap="nowrap">爱贝计费平台对帐单</td>
                     <td align="center" nowrap="nowrap">渠道自有支付对帐单</td>
					 <td align="center" nowrap="nowrap">移动自有渠道对帐单</td>
                      <td align="center" nowrap="nowrap">操作</td>
                </tr>
            <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('dates')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
                <tr>
                  <td align="center" nowrap="nowrap"><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</td>
                    <td align="center" nowrap="nowrap"><?php if (isset($_smarty_tpl->getVariable('files',null,true,false)->value[$_smarty_tpl->tpl_vars['item']->value])){?><a href="index.php?ac=report_excel&opt=export&ym=<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
&type=1">导出对帐单</a><?php }?></td>
                    <td align="center" nowrap="nowrap"><?php if (isset($_smarty_tpl->getVariable('files',null,true,false)->value[$_smarty_tpl->tpl_vars['item']->value])){?><a href="index.php?ac=report_excel&opt=export&ym=<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
&type=2">导出对帐单</a><?php }?></td>
                    <td align="center" nowrap="nowrap"><?php if (isset($_smarty_tpl->getVariable('files',null,true,false)->value[$_smarty_tpl->tpl_vars['item']->value])){?><a href="index.php?ac=report_excel&opt=export&ym=<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
&type=3">导出对帐单</a><?php }?></td>
                    <td align="center" nowrap="nowrap"><?php if (isset($_smarty_tpl->getVariable('files',null,true,false)->value[$_smarty_tpl->tpl_vars['item']->value])){?><a href="index.php?ac=report_excel&opt=export&ym=<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
&type=4">导出对帐单</a><?php }?></td>
                    <td align="center" nowrap="nowrap"><input type="button" value="<?php if (isset($_smarty_tpl->getVariable('files',null,true,false)->value[$_smarty_tpl->tpl_vars['item']->value])){?>重新<?php }?>生成对帐单" onclick="create_excel(this,'<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
');" /></td>
                </tr>
            <?php }} ?>
            </tbody>
        </table>
    </div>    </td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>
  </tbody>
</table>
</form>
</div>

<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/template/default/js/common.js" type="text/javascript" language="javascript"></script>