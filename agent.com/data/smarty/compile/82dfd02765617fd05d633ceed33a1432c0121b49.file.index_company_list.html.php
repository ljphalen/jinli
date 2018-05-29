<?php /* Smarty version Smarty-3.0.6, created on 2013-07-29 11:35:08
         compiled from "D:\www\trunk\agent.com/template/default/index_company_list.html" */ ?>
<?php /*%%SmartyHeaderCode:1774651f5e2ec51c4d8-10665903%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '82dfd02765617fd05d633ceed33a1432c0121b49' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_company_list.html',
      1 => 1375068902,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1774651f5e2ec51c4d8-10665903',
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
<script type="text/javascript">
function delPrompt_com(clientid,clientids,obj){
	$.post("/index.php/companys/chksubs",{clientid:clientid},function(result){
			if(result.counts > 0 && clientids == 0){
				if(!confirm("该渠道商有 "+result.counts+" 个子渠道商，您真的要一并删除吗？不可恢复")){
					return false;
				}
				document.location.href=obj.href;
			}else{
				if(!confirm("您真的要删除吗？不可恢复")){
					return false;
				}
				document.location.href=obj.href;
			}
			
	},'json');	 	
	return false;
}

function renew_com(){
	$.get("/task.php?ac=update&forcecom=1&t="+new Date().getTime(),function(result){
			if(result!=""){
				alert('更新成功！');	
			}
	},'text');	 	
	return false;
}

</script>
<h1><!--<span class="action-span"><a href="/index.php/company/add">添加公司</a>--></span>
<span class="action-span1">渠道商管理</span><span id="search_id"
	class="action-span1"> - 
    <?php if ($_smarty_tpl->getVariable('channeltype')->value==1){?>
    自主渠道商列表
    <?php }elseif($_smarty_tpl->getVariable('channeltype')->value==2){?>
    移动渠道商列表
    <?php }else{ ?>
    渠道商列表
    <?php }?>
    </span>
<div style="clear: both"></div>
</h1>

<div class="form-div">
  <form action="/index.php" name="searchForm" id="searchForm" method="get">
    <img src="/template/default/img/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    渠道号/公司名称：
    <input type="text" name="condition" id="condition" value="<?php echo $_GET['condition'];?>
" />
    <input type="hidden"value="company_list"  name="ac" />
    <input type="hidden" value="<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
"  name="channeltype" />
    <input type="submit" value="搜索" class="button" name="search" />
    
    <?php if ($_smarty_tpl->getVariable('channeltype')->value==2){?>
    <!--<input type="button" value="移动渠道商导入" class="button" name="import" onclick="document.location.href='/index.php/companyimport'" />-->
    <?php }?>
    
    <?php if ($_smarty_tpl->getVariable('admin')->value['level']>=200){?>
    <input type="button" value="更新统计报表渠道商缓存" class="button" name="import" onclick="renew_com();" />
    <?php }?>
  </form>
</div>
<div class="list-div" id="listDiv">
<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
	<tr align="center">
		<th>公司名称</th>
		<th>渠道号</th>
		<th>子渠道号</th>
		<th>角色类型</th>
		<th>联系人</th>
        <th>手机/电话</th>
		<th>分成比例</th>
		<th>备注</th>
		<th>创建时间</th>
		<th width="100">操作</th>
	</tr>
	<?php  $_smarty_tpl->tpl_vars['one'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['one']->key => $_smarty_tpl->tpl_vars['one']->value){
?>
	<tr align="center">
		<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['one']->value['name'])===null||$tmp==='' ? '未知' : $tmp);?>
</td>
		<td><?php echo (($tmp = @$_smarty_tpl->tpl_vars['one']->value['clientid'])===null||$tmp==='' ? '无' : $tmp);?>
</td>
		<td><?php echo $_smarty_tpl->tpl_vars['one']->value['clientids'];?>
</td>
		<td>
        <?php if ($_smarty_tpl->tpl_vars['one']->value['clientid']==0){?>
        管理员
        <?php }elseif($_smarty_tpl->tpl_vars['one']->value['clientid']>0&&$_smarty_tpl->tpl_vars['one']->value['clientids']==0){?>
        <?php echo $_smarty_tpl->getVariable('channeltypeArr')->value[30];?>

        <?php }else{ ?>
        <?php echo $_smarty_tpl->getVariable('channeltypeArr')->value[10];?>

        <?php }?>
        </td>
		<td><?php echo $_smarty_tpl->tpl_vars['one']->value['linkman'];?>
</td>
		<td><?php echo (($tmp = @(($tmp = @$_smarty_tpl->tpl_vars['one']->value['mobile'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['one']->value['phone'] : $tmp))===null||$tmp==='' ? '' : $tmp);?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['one']->value['intoratio'];?>
</td>
        <td><?php echo $_smarty_tpl->tpl_vars['one']->value['describe'];?>
</td>
        <td><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['one']->value['dateline'],"%Y-%m-%d %H:%M");?>
</td>
		<td align="center"><a href="/index.php?ac=company_edit&method=edit&cid=<?php echo $_smarty_tpl->tpl_vars['one']->value['id'];?>
&channel=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
"
			title="编辑"><img src="/template/default/img/icon_edit.gif" border="0" />编辑</a>
        <?php if ($_smarty_tpl->tpl_vars['one']->value['clientid']!=0&&(($tmp = @$_smarty_tpl->getVariable('admin')->value['agentid'])===null||$tmp==='' ? 0 : $tmp)!=$_smarty_tpl->tpl_vars['one']->value['id']){?>    
            &nbsp;&nbsp; 
		<a href="/index.php?ac=company_edit&method=del&cid=<?php echo $_smarty_tpl->tpl_vars['one']->value['id'];?>
&channel=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
" title="删除"
			onclick="return delPrompt(<?php echo $_smarty_tpl->tpl_vars['one']->value['clientid'];?>
,<?php echo $_smarty_tpl->tpl_vars['one']->value['clientids'];?>
,this)"><img
			src="/template/default/img/icon_drop.gif" border="0" />删除</a>
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
            window.location.href = encodeURI("/index.php?ac=company_list&channeltype="+channeltype+"&condition="+condition+"&page=" + pageNum);
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
