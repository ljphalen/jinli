<?php /* Smarty version Smarty-3.0.6, created on 2013-04-26 15:51:27
         compiled from "D:\www\trunk\agent.com/template/default/index_graph.html" */ ?>
<?php /*%%SmartyHeaderCode:26764517a31ff596512-65990773%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '21c47600beb5756f7b9c8c573d86f6bb991b21ed' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_graph.html',
      1 => 1365616438,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '26764517a31ff596512-65990773',
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
               参数设置
            </div>
        </h1>
        <table id="list-table__" width="100%">
            <tbody>
                <tr>
                    <td class="label_2" width="150">图标类型：</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="label_2" width="150">饼状图模块个数：</td>
                    <td><input type="text" name="pie" value="<?php echo $_smarty_tpl->getVariable('mypie')->value;?>
" /></td>
                    <td>宽：<input type="text" name="pie_w" value="<?php echo $_smarty_tpl->getVariable('pie_w')->value;?>
" /></td>
                    <td>高：<input type="text" name="pie_h" value="<?php echo $_smarty_tpl->getVariable('pie_h')->value;?>
" /></td>
                </tr>
                <tr>
                    <td class="label_2" width="150">柱状图条形个数：</td>
                    <td><input type="text" name="bar" value="<?php echo $_smarty_tpl->getVariable('mybar')->value;?>
" /></td>
                    <td>宽：<input type="text" name="bar_w" value="<?php echo $_smarty_tpl->getVariable('bar_w')->value;?>
" /></td>
                    <td>高：<input type="text" name="bar_h" value="<?php echo $_smarty_tpl->getVariable('bar_h')->value;?>
" /></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="smt" id="smt" value="提交"
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