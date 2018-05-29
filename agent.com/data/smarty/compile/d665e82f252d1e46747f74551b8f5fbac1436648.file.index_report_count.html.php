<?php /* Smarty version Smarty-3.0.6, created on 2013-08-12 11:48:20
         compiled from "/work/website/agent.com/template/default/index_report_count.html" */ ?>
<?php /*%%SmartyHeaderCode:163252116552085b0480df80-49591092%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd665e82f252d1e46747f74551b8f5fbac1436648' => 
    array (
      0 => '/work/website/agent.com/template/default/index_report_count.html',
      1 => 1376041052,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '163252116552085b0480df80-49591092',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_date_format')) include '/work/website/agent.com/plugin/smartys/plugins/modifier.date_format.php';
?> <!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>每日/每月统计报表</title>
<link rel="stylesheet" type="text/css" href="/template/default/css/general.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/main.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/page.css" />

<script type="text/javascript" src="/template/default/js/common.js"></script>
<script type="text/javascript" src="/template/default/js/jquery-1.4.2-min.js"></script>
<script type="text/javascript" src="/template/default/js/page.js"></script>
<script type="text/javascript" src="/template/default/js/DatePicker/WdatePicker.js"></script>
</head>

<body>
<script type="text/javascript">
$(function(){
	$("#form1").submit(function(){
		$("#selectDate").val('');
	});
	
	$("#s").click(function(){
		document.location.href= '<?php echo $_smarty_tpl->getVariable('pageurl')->value;?>
&toexcel=1';
	});

});

function formatdate(t)
{	
	if(isNaN(t)){t=0;}
	t=parseInt(t);
	var d=parseInt(t/(60*60*24));
	var h=parseInt((t-d*60*60*24)/(60*60));
	var M=parseInt((t-d*(60*60*24)-h*60*60)/60);
	var s=parseInt(t-d*(60*60*24)-h*60*60-M*60);
	var str="";
	if(d>0){str=str+d+"天";}
	if(h>0){str=str+h+"小时";}
	if(M>0){str=str+M+"分";}
	if(s>0){str=str+s+"秒";}
	document.write(str);
}
function selectAll(obj){
	var $checkboxs=$("#list-table input:checkbox");
	
	if(obj.checked===true){
		
		$checkboxs.attr("checked",true);
	}else{
		
		$checkboxs.attr("checked",false);
	}
}
function btnDisabled(){
	var t=false;
	$("#list-table input:checkbox").each(function(){
		if(this.checked){
			$("#s").val("正在导出Excel，请稍等...").attr("disabled",true);
	        t=true;		
			return false;
		}
	});
	
	if(t){setTimeout('$("#s").val("导出Excel").attr("disabled",false);',5000);}else {alert("请选择您要导出的数据行");}
	return t;	
}


function change_rptype(val){
	$("#reporttype").val(val);
	var act_url='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&reporttype='+val+'&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&subclientid=<?php echo $_smarty_tpl->getVariable('clientids')->value;?>
';	
	document.form1.action = act_url;
	document.form1.submit();	
}
</script>
<link rel="stylesheet" type="text/css" href="/template/default/css/calendar.css" />

<style>
#list-table td,#list-table2 td{
	text-align:center;
}

#period_count{
	font-weight:bold;
	line-height:150%;
	padding-left:5px;
}

#tab-list {
	position:relative;
	z-index:10;
}
#tab-list ul { position:relative; overflow:hidden; position:relative; list-style:none; padding:0px; margin:0px; margin-bottom:-1px; height:30px;}
#tab-list ul li { float:left; zoom:1; border:1px solid #BBDDE5; margin-right:1px;}
#tab-list ul li.cur { float:left; border-bottom:1px solid #F4FAFB; background:#F4FAFB;}
#tab-list ul li a { display:inline-block; zoom:1; _display:inline; height:28px; padding:0 20px; text-decoration:none; color:#335B64; font-weight:bold; line-height:28px;}

.tab-content { background:#F4FAFB; padding-top:10px; border:1px solid #BBDDE5; padding:5px; }
</style>

<h1>
<span class="action-span1">查询统计报表</span>
	<!--<span id="search_id" class="action-span1"> - 查询统计报表</span>-->
  <label><input name="reporttypes" id="reporttype_day" type="radio" value="day" onClick="change_rptype(this.value);" <?php if ($_smarty_tpl->getVariable('reporttype')->value=='day'){?>checked<?php }?>>日报表</label>
  <label><input name="reporttypes" id="reporttype_month" type="radio" value="month" onClick="change_rptype(this.value);" <?php if ($_smarty_tpl->getVariable('reporttype')->value=='month'){?>checked<?php }?>>月报表</label>
<div style="clear: both"></div>
</h1>
<div id="tab-list">
	<ul>
	    <li <?php if ($_smarty_tpl->getVariable('gameid')->value==0){?>class="cur"<?php }?> ><a href="/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&reporttype=<?php echo $_smarty_tpl->getVariable('reporttype')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&subclientid=<?php echo $_smarty_tpl->getVariable('clientids')->value;?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&gameid=0" hidefocus="true">综合统计</a></li>
        <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('games')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
	    <li <?php if ($_smarty_tpl->getVariable('gameid')->value==$_smarty_tpl->tpl_vars['key']->value){?>class="cur"<?php }?> ><a href="/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&reporttype=<?php echo $_smarty_tpl->getVariable('reporttype')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&subclientid=<?php echo $_smarty_tpl->getVariable('clientids')->value;?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&gameid=<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" hidefocus="true"><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
统计</a></li>
        <?php }} ?>
	</ul>
</div> 

<div class="tab-content">
<form name="form1" id="form1" method="get" action=""><div class="form-div">
    <input name="reporttype" type="hidden" id="reporttype" value="<?php echo $_smarty_tpl->getVariable('reporttype')->value;?>
">
    <input type="hidden" name="gameid" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">	
    <input type="hidden" name="ac" value="<?php echo $_smarty_tpl->getVariable('ac')->value;?>
">	
    <input type="hidden" name="clientid" value="<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
">	
    <input type="hidden" name="subclientid" value="<?php echo $_smarty_tpl->getVariable('clientids')->value;?>
">	
<?php if ($_smarty_tpl->getVariable('levels')->value>=30){?>
  <?php if ($_smarty_tpl->getVariable('levels')->value==200){?>渠道号<?php }else{ ?>子渠道号<?php }?>/公司
  <input name="keyword" type="text" id="keyword" value="<?php echo $_smarty_tpl->getVariable('keyword')->value;?>
">
<?php }else{ ?>  
  <input name="keyword" type="hidden" id="keyword" value="">
<?php }?>  
  日期:从
  <!--<input name="dateStart" type="text" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateStart', '%Y-%m-%d', '24', false,'dateStart');" readonly />-->
  <input name="dateStart" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'dateEnd\')||\'2020-10-01\'}'})"/> 
  到
  <input name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'dateEnd\')}',maxDate:'2020-10-01'})"/>
  <!--<input type="text" name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateEnd', '%Y-%m-%d', '24', false,'dateEnd');" readonly />
  <input type="hidden"  name="selectDate" value="<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
" id="selectDate" />-->
  <input type="submit" name="button" id="button" class="btn" value="查询">
  <?php if ($_smarty_tpl->getVariable('levels')->value>=30){?>
  <input type="button" name="button4" id="button4" class="btn" value="查看图表" onclick="location.href='/index.php?reporttype=<?php echo $_smarty_tpl->getVariable('reporttype')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&subclientid=<?php echo $_smarty_tpl->getVariable('clientids')->value;?>
&sac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&shape=1&ac=graph_show&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&dateStart='+this.form.dateStart.value+'&dateEnd='+this.form.dateEnd.value+'&selectDate=<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
&keyword='+this.form.keyword.value" >

  <?php }?>
</div>

<div id="period_count">
    注册人数总计：<span class="red"><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('period_count')->value['registerusers'])===null||$tmp==='' ? 0 : $tmp));?>
</span><br>
  登录人数总计：<span class="red"><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('period_count')->value['loginusers'])===null||$tmp==='' ? 0 : $tmp));?>
</span><br>   
    付费人数总计：<span class="red"><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('period_count')->value['consumeusers'])===null||$tmp==='' ? 0 : $tmp));?>
</span><br>
    消费金额总计：<span class="red"><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('period_count')->value['consumemoney'])===null||$tmp==='' ? 0 : $tmp));?>
</span><br>
</div>
<div>
  <table width="100%" border="0">
    <tr style="background:none;">
      <td><?php echo $_smarty_tpl->getVariable('dataPrompt')->value;?>
 </td>
      <td width="120">
      
      <?php if (!empty($_smarty_tpl->getVariable('list',null,true,false)->value)){?>
      <select name="pagesize" id="pagesize" onChange="this.form.submit();">
          <option value="10">每页显示10条</option>
          <option value="15">每页显示15条</option>
          <option value="20">每页显示20条</option>
          <option value="50">每页显示50条</option>
        </select>
        <script>document.getElementById("pagesize").value='<?php echo $_smarty_tpl->getVariable('pagesize')->value;?>
'</script>
        <?php }?>
      </td>
    </tr>
  </table>
  </div>
</form>

<?php if ($_smarty_tpl->getVariable('totalrow')->value==0){?>
没有数据
<?php }else{ ?>
<form id="form2" name="form2" method="post" action=""  onSubmit="return btnDisabled()">
<input type="hidden"  name="act" value="export" />
  <div class="list-div" id="listDiv">
<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
    <tr>
      <th><?php if ($_smarty_tpl->getVariable('reporttype')->value=='day'){?>日期<?php }else{ ?>月份<?php }?></th>
      <?php if ($_smarty_tpl->getVariable('levels')->value>=30){?>
      <th>渠道号</th>
      <?php }?>
      <?php if ($_smarty_tpl->getVariable('clientids')->value>=0){?>
      <th>子渠道号</th>
      <?php }?>
      <th>公司名称</th>
      <th>新注册用户数</th>
      <th>登录用户数</th>
      <th>消费订单数</th>
      <th>消费用户数</th>
      <th>消费金额</th>
      <!--<th bgcolor="#0099CC">分成收益总金额</th>
      <?php if ($_smarty_tpl->getVariable('levels')->value==200&&$_smarty_tpl->getVariable('reporttype')->value=='month'){?>
      <th>月活跃登录用户数</th>
      <th>月用户平均游戏时长</th>
      <?php }?>-->
      <?php if ($_smarty_tpl->getVariable('levels')->value==200){?>
      <th>所属类型</th>
      <?php }?>    </tr>
    <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
?>
    <tr>
      <td><?php if ($_smarty_tpl->getVariable('reporttype')->value=='day'){?><?php echo $_smarty_tpl->tpl_vars['i']->value['today'];?>
<?php }else{ ?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['i']->value['today'],"%Y-%m");?>
<?php }?></td>
      <?php if ($_smarty_tpl->getVariable('levels')->value>=30){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
</td>
      <?php }?>
      <?php if ($_smarty_tpl->getVariable('clientids')->value>=0){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientids'];?>
</td>
      <?php }?>
      <td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['i']->value['name'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['registerusers'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['loginusers'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumeorders'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumeusers'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney'];?>
</td>
      <!--<td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney']*$_smarty_tpl->tpl_vars['i']->value['intoratio']/10;?>
</td>
      <?php if ($_smarty_tpl->getVariable('levels')->value==200&&$_smarty_tpl->getVariable('reporttype')->value=='month'){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['activeusermonth'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgplaygamemonth'];?>
</td>
      <?php }?>-->
      <?php if ($_smarty_tpl->getVariable('levels')->value==200){?>
      <td><?php echo $_smarty_tpl->getVariable('channel_types')->value[$_smarty_tpl->tpl_vars['i']->value['channeltype']];?>
</td></td>
      <?php }?>    </tr>
    <?php }} ?>
  </table>
</div>

<div class="wrapper inb ml">
    <div class="page mb20" id="page-js-2"></div>
    <script type="text/javascript">
        var curpage = <?php echo $_smarty_tpl->getVariable('page')->value;?>
;
        var pages = <?php echo $_smarty_tpl->getVariable('pages')->value;?>
;
        PPage("page-js-2",curpage,pages,"hoho2.go",true);
        function hoho2(){};
        hoho2.go=function(pageNum){
            window.location.href = "<?php echo $_smarty_tpl->getVariable('pageurl')->value;?>
&page=" + pageNum;
            PPage("page-js-2",pageNum,pages,"hoho2.go",true);
        }
    </script>
</div>

  <table width="100%" border="0">
    <tr style="background:none;">
      <td width="300"><input type="button" name="button5" id="s" class="btn" value="导出Excel" /></td>
      <td><?php echo $_smarty_tpl->getVariable('pagination')->value;?>
</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php }?>
<br><br>
</div>
<style type="text/css">
tr.over td {
	background: #bcd4ec; /*这个将是鼠标高亮行的背景色*/
	cursor: pointer;
}
</style>
<script type="text/javascript">
//$(document).ready(function(){
	 $("#list-table tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	 $("#list-table2 tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");})
//});
</script>
