<?php /* Smarty version Smarty-3.0.6, created on 2013-07-18 14:35:41
         compiled from "D:\www\trunk\agent.com/template/default/index_setting_lt_rate.html" */ ?>
<?php /*%%SmartyHeaderCode:1834851e78cbdd4cb05-67845683%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c217ec2f330dfaeed5df5d9ba180915658b455c7' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_setting_lt_rate.html',
      1 => 1368005392,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1834851e78cbdd4cb05-67845683',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?>
<?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<link href="/template/default/css/validate.css" rel="stylesheet" type="text/css" />
<script src="/template/default/js/jquery.validate.js" type="text/javascript"
	language="javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#theForm").validate({
	});	
});
</script>
<h1><span class="action-span"></span>
<span class="action-span1">系統设置</span><span id="search_id"
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
        <div>未设置渠道分成比率的默认值与渠道号为0的相同</div>
        <table id="list-table__" style="border:1px;">
            <tbody>
                <tr>
                     <td align="center" nowrap="nowrap">渠道号</td>
                      <td align="center" nowrap="nowrap">渠道分成比率</td>
                </tr>
            <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('configs')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
                <tr>
                  <td align="center" nowrap="nowrap"><input name="clientid[]" type="text" id="clientid" size="10" maxlength="4" value="<?php if ($_smarty_tpl->tpl_vars['item']->value!=''){?><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php }?>" onkeypress="return checkthenum(event);" /></td>
                    <td align="center" nowrap="nowrap"><input name="rate[]" type="text" id="rate" maxlength="30" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value)===null||$tmp==='' ? '' : $tmp);?>
" /></td>
                </tr>
            <?php }} ?>
            </tbody>
        </table>
    </div>    </td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" name="smt" id="smt" value="保存配置"
            class="button" /></td>
    </tr>
  </tbody>
</table>
</form>
</div>

<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/template/default/js/common.js" type="text/javascript" language="javascript"></script>