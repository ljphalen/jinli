<?php /* Smarty version Smarty-3.0.6, created on 2013-07-12 17:11:29
         compiled from "D:\www\trunk\agent.com/template/default/index_user_list.html" */ ?>
<?php /*%%SmartyHeaderCode:8837517a03b5907754-32558865%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a788c9158e44f2527378efc50c97f93ed290d1ee' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_user_list.html',
      1 => 1368156124,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8837517a03b5907754-32558865',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include 'D:\www\trunk\agent.com\plugin\smartys\plugins\modifier.date_format.php';
?><?php  $_config = new Smarty_Internal_Config("site.conf", $_smarty_tpl->smarty, $_smarty_tpl);$_config->loadConfigVars(null, 'local'); ?> <?php $_template = new Smarty_Internal_Template("public/pageHead.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<link rel="stylesheet" href="template/default/css/page.css" />
<script type="text/javascript" src="/template/default/js/common.js"></script>
<script type="text/javascript" src="/template/default/js/page.js"></script>
<h1><span class="action-span"><a href="/index.php?ac=user_add">添加管理员</a></span>
<span class="action-span1">用户管理</span><span id="search_id"
	class="action-span1"> - 
    <?php if ($_smarty_tpl->getVariable('channeltype')->value==1){?>
    自主渠道商管理员列表
    <?php }elseif($_smarty_tpl->getVariable('channeltype')->value==2){?>
    移动渠道商管理员列表
    <?php }else{ ?>
    管理员列表
    <?php }?>
    </span>
<div style="clear: both"></div>
</h1>

<div class="form-div">
  <form action="/index.php?ac=user_list" name="searchForm" id="searchForm" method="get">
    <img src="/template/default/img/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    渠道号/公司名称：
    <input type="text" name="condition" id="condition" value="<?php echo $_GET['condition'];?>
" />
    <input type="hidden"value="user_list"  name="ac" />
    <input type="hidden" value="<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
"  name="channeltype" />
    <input type="submit" value="搜索" class="button" name="search" />
  </form>
</div>
<div class="list-div" id="listDiv">
<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
	<tr align="center">
		<th>渠道号</th>
                <th>子渠道号</th>
		<th>公司名称</th>
		<th>用户名</th>
		<th>姓名</th>
		<th>邮箱</th>
		<th>角色类型</th>
		<th>最后登录时间</th>
		<th>最后登录IP</th>
		<th>是否可用</th>
		<th width="100">操作</th>
	</tr>
        <?php  $_smarty_tpl->tpl_vars['one'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('userList')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['one']->key => $_smarty_tpl->tpl_vars['one']->value){
?>
	<tr align="center">
        <td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['one']->value['clientid'])===null||$tmp==='' ? '未知' : $tmp);?>
</td>
		<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['one']->value['clientids'])===null||$tmp==='' ? '未知' : $tmp);?>
</td>
		<td><a href="/index.php?ac=user_edit&method=edit&uid=<?php echo $_smarty_tpl->tpl_vars['one']->value['userid'];?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
" title="点击查看公司"><?php echo $_smarty_tpl->tpl_vars['one']->value['name'];?>
</a></td>
		<td><?php echo $_smarty_tpl->tpl_vars['one']->value['username'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['one']->value['nickname'];?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['one']->value['email'];?>
</td>
        <td><?php echo $_smarty_tpl->getVariable('role')->value[$_smarty_tpl->tpl_vars['one']->value['levels']];?>
</td>
        <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['one']->value['dateline'],"%Y-%m-%d %H:%M");?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['one']->value['logonip'];?>
</td>
        <td><?php if ($_smarty_tpl->tpl_vars['one']->value['available']==1){?>是<?php }else{ ?>否<?php }?></td>
		<td align="center">
        <?php if ((($tmp = @$_smarty_tpl->getVariable('admin')->value['userid'])===null||$tmp==='' ? 0 : $tmp)!=$_smarty_tpl->tpl_vars['one']->value['userid']){?>
        <a href="/index.php?ac=user_edit&method=edit&uid=<?php echo $_smarty_tpl->tpl_vars['one']->value['userid'];?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
"
			title="编辑"><img src="/template/default/img/icon_edit.gif" border="0" />编辑</a>&nbsp;&nbsp;
		<a href="/index.php?ac=user_edit&method=del&uid=<?php echo $_smarty_tpl->tpl_vars['one']->value['userid'];?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
" title="删除"
			onclick="return delPrompt()"><img
			src="/template/default/img/icon_drop.gif" border="0" />删除</a>
        <?php }else{ ?>
        <?php echo $_smarty_tpl->getConfigVariable('loginstate');?>

        <?php }?>
        </td>
	</tr>
	<?php }} ?>
</table>
<div style="text-align:center"></div>


</div>
<div class="wrapper inb ml">
    <div class="page mb20" id="page-js-2"></div>
    <script type="text/javascript">
        var curpage = <?php echo $_smarty_tpl->getVariable('curpage')->value;?>
;
        var pages = <?php echo $_smarty_tpl->getVariable('pages')->value;?>
;
        var channeltype = "<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
";
        var condition = "<?php echo $_smarty_tpl->getVariable('condition')->value;?>
";
        PPage("page-js-2",curpage,pages,"hoho2.go",true);
        function hoho2(){};
        hoho2.go=function(pageNum){
            window.location.href = encodeURI("/index.php?ac=user_list&channeltype="+channeltype+"&condition="+condition+"&page=" + pageNum);
            PPage("page-js-2",pageNum,pages,"hoho2.go",true);
        }
    </script>
</div>
<?php $_template = new Smarty_Internal_Template("public/pageFoot.html", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
<script>
	$(function(){
		$(".page-txt").keyup(function(event){
			if(event.keyCode ==13){
				$(".page-btn").trigger("click");
			}
		 });
	});
</script>
