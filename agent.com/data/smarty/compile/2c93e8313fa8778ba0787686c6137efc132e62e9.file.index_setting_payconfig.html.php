<?php /* Smarty version Smarty-3.0.6, created on 2013-08-07 11:05:58
         compiled from "/work/website/agent.com/template/default/index_setting_payconfig.html" */ ?>
<?php /*%%SmartyHeaderCode:7181454395201b99664ea70-75801814%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2c93e8313fa8778ba0787686c6137efc132e62e9' => 
    array (
      0 => '/work/website/agent.com/template/default/index_setting_payconfig.html',
      1 => 1375838018,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7181454395201b99664ea70-75801814',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/work/website/agent.com/plugin/smartys/plugins/function.html_options.php';
?><?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?>
<?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<link href="/template/default/css/validate.css" rel="stylesheet" type="text/css" />
<script src="/template/default/js/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	/*$("#theForm").validate({
		rules: {
			clientid:{
                digits:true
            }
		},
		messages: {
			clientid:{
                digits:"请输入数字"
            },
		}
	});	*/
});

function checkthenum(e)
{
	var key = window.event ? e.keyCode:e.which;
	var keychar = String.fromCharCode(key);
	//reg = /[\d,\.]/;
	var reg = /[\d]/;
	if(key>20){
		return reg.test(keychar);
	}
	return true;
}
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
                我方支付方式</div>
        </h1>
        <table cellpadding="5" id="list-table__" style="border:1px;">
            <tbody>
                <tr>
             <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('configs')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['configs']['iteration']=0;
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['configs']['iteration']++;
?>
                 <td nowrap="nowrap"><label style="padding:2px;"><input name="ours_way[]" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['key']->value,$_smarty_tpl->getVariable('our_paytype')->value)){?>checked<?php }?> /><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</label></td>
                 <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['configs']['iteration']%5==0){?>
                </tr><tr>
                 <?php }?>
             <?php }} ?>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="main-div">
        <h1>
            <div class="section_title">
                渠道自有支付方式</div>
        </h1>
        <table id="list-table__" style="border:1px;">
            <tbody>
                <tr>
                     <td align="center" nowrap="nowrap">渠道号</td>
                      <td align="center" nowrap="nowrap">支付方式</td>
                </tr>
            <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('channel_paytype')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
                <tr>
                  <td align="center" nowrap="nowrap"><input name="clientid[]" type="text" id="clientid" size="10" maxlength="4" value="<?php if ($_smarty_tpl->tpl_vars['item']->value!=''){?><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
<?php }?>" onkeypress="return checkthenum(event);" /></td>
                    <td align="center" nowrap="nowrap"><select name="payway[]"><option value="">-请选择-</option><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->getVariable('payways_theirs')->value,'selected'=>$_smarty_tpl->tpl_vars['item']->value),$_smarty_tpl);?>
</select></td>
                </tr>
            <?php }} ?>
            </tbody>
        </table>
    </div>
        </td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" name="smt" id="smt" value="保存配置"
            class="button" /></td>
    </tr>
  <td width="1%"></tbody>
</table>
</form>
</div>

<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script src="/template/default/js/common.js" type="text/javascript" language="javascript"></script>