<?php /* Smarty version Smarty-3.0.6, created on 2013-07-18 14:35:45
         compiled from "D:\www\trunk\agent.com/template/default/index_setting_base.html" */ ?>
<?php /*%%SmartyHeaderCode:293251e78cc13e7493-45198907%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '161412852ffdd039b32128c53977de5b20cca1fc' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_setting_base.html',
      1 => 1368005392,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '293251e78cc13e7493-45198907',
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
        <table id="list-table__" style="border:1px;">
            <tbody>
                <tr>
                  <td align="right" nowrap="nowrap">移动费率（加坏帐）：</td>
                    <td align="left" nowrap="nowrap"><input name="yd_pay_rate" type="text" id="yd_pay_rate" maxlength="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('yd_pay_rate')->value)===null||$tmp==='' ? '' : $tmp);?>
" /></td>
                </tr>
                <tr>
                  <td align="right" nowrap="nowrap">移动税率：</td>
                    <td align="left" nowrap="nowrap"><input name="yd_pay_tax_rate" type="text" id="yd_pay_tax_rate" maxlength="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('yd_pay_tax_rate')->value)===null||$tmp==='' ? '' : $tmp);?>
" /></td>
                </tr>
                <tr>
                  <td align="right" nowrap="nowrap">联通费率（加坏帐）：</td>
                    <td align="left" nowrap="nowrap"><input name="lt_pay_rate" type="text" id="lt_pay_rate" maxlength="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('lt_pay_rate')->value)===null||$tmp==='' ? '' : $tmp);?>
" /></td>
                </tr>
                <tr>
                  <td align="right" nowrap="nowrap">联通税率：</td>
                    <td align="left" nowrap="nowrap"><input name="lt_pay_tax_rate" type="text" id="lt_pay_tax_rate" maxlength="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('lt_pay_tax_rate')->value)===null||$tmp==='' ? '' : $tmp);?>
" /></td>
                </tr>
                <tr>
                  <td align="right" nowrap="nowrap">爱贝手续费率：</td>
                    <td align="left" nowrap="nowrap"><input name="ipay_pay_rate" type="text" id="ipay_pay_rate" maxlength="30" value="<?php echo (($tmp = @$_smarty_tpl->getVariable('ipay_pay_rate')->value)===null||$tmp==='' ? '' : $tmp);?>
" /></td>
                </tr>
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