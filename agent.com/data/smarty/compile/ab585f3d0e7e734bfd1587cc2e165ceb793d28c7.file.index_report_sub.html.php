<?php /* Smarty version Smarty-3.0.6, created on 2013-08-09 10:48:21
         compiled from "/work/website/agent.com/template/default/index_report_sub.html" */ ?>
<?php /*%%SmartyHeaderCode:94684357352045875e17459-91764925%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ab585f3d0e7e734bfd1587cc2e165ceb793d28c7' => 
    array (
      0 => '/work/website/agent.com/template/default/index_report_sub.html',
      1 => 1375838017,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '94684357352045875e17459-91764925',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
 <!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>分销后台</title>
<link rel="stylesheet" type="text/css" href="/template/default/css/general.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/main.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/calendar.css" />
<link rel="stylesheet" type="text/css" href="template/default/css/page.css" />

</head>

<body>
<script type="text/javascript" src="/template/default/js/common.js"></script>
<script type="text/javascript" src="/template/default/js/jquery-1.4.2-min.js"></script>
<script type="text/javascript" src="/template/default/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/template/default/js/page.js"></script>

<script type="text/javascript">
$(function(){
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
	var $checkboxs=$("#list-table2 input:checkbox");
	
	if(obj.checked===true){
		
		$checkboxs.attr("checked",true);
	}else{
		
		$checkboxs.attr("checked",false);
	}
}

function btnDisabled(){
	var t=false;
	$("#list-table2 input:checkbox").each(function(){
		if(this.checked){
			$("#s").val("正在导出Excel，请稍等...").attr("disabled",true);
	        t=true;		
			return false;
		}
	});
	
	if(t){setTimeout('$("#s").val("导出Excel").attr("disabled",false);',5000);}else {alert("请选择您要导出的数据行");}
	return t;	
}



function showpaydetail(dateStart,dateEnd,name,clientid,subclientid,gameid){
	/*asyncbox.open({
	　　　url : '/index.php/reportnew/paydetail?dateStart='+dateStart+'&dateEnd='+dateEnd+'&clientid='+clientid+'&clientid='+clientid+'&gameid='+gameid+'&name='+name+'',
	　　　width : 450,
	　　　height : 600,
		 title : '消费金额明细'
	});*/
	var url = '/index.php?ac=report_paydetail&dateStart='+dateStart+'&dateEnd='+dateEnd+'&clientid='+clientid+'&subclientid='+subclientid+'&gameid='+gameid+'&name='+encodeURIComponent(name)+'';
	window.open(url,'paydetail');
}
</script>

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
<span class="action-span1">查询统计报表</span><span id="search_id"
	class="action-span1"> - 查询统计报表</span>
<div style="clear: both"></div>
</h1>
<div id="tab-list">
	<ul>
	    <li <?php if ($_smarty_tpl->getVariable('gameid')->value==0){?>class="cur"<?php }?> ><a href="/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&gameid=0&keyword=<?php echo $_smarty_tpl->getVariable('keyword')->value;?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
" hidefocus="true">综合统计</a></li>
        <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('games')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
	    <li <?php if ($_smarty_tpl->getVariable('gameid')->value==$_smarty_tpl->tpl_vars['key']->value){?>class="cur"<?php }?> ><a href="/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&gameid=<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
&keyword=<?php echo $_smarty_tpl->getVariable('keyword')->value;?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
" hidefocus="true"><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
统计</a></li>
        <?php }} ?>
	</ul>
</div> 
<div class="tab-content">
<form name="form1" method="get" action=""><div class="form-div">
  <input name="clientid" type="hidden" value="<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
" />
  <input type="hidden" name="gameid" class="btn" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">
  <input type="hidden" name="ac" class="btn" value="<?php echo $_smarty_tpl->getVariable('ac')->value;?>
">	
  渠道号：<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日期：从
  <input name="dateStart" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'dateEnd\')||\'2020-10-01\'}'})"/>
  到
  <input name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'dateStart\')}',maxDate:'2020-10-01'})"/>
  <input type="submit" name="button" class="btn" id="button" value="查询">
  <input type="button" name="button2" class="btn" id="button2" value="昨天" onClick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&channeltype=&selectDate=yesterday'">
  <input type="button" name="button3" class="btn" id="button3" value="最近7天"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&channeltype=&selectDate=hebdomad'">
  <input type="button" name="button4" class="btn" id="button4" value="本月"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&channeltype=&selectDate=month'">
  <input type="button" name="button4" class="btn" id="button5" value="查看图表" onClick="location.href='/index.php?sac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&shape=1&ac=graph_show&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&dateStart='+this.form.dateStart.value+'&dateEnd='+this.form.dateEnd.value+'&selectDate=<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
'" >
  </div>
  

<div>
  <table width="100%" border="0">
    <tr>
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

     <div class="list-div" id="listDiv">
     <div class="list-com-name"><?php echo $_smarty_tpl->getVariable('agentname')->value;?>
报表</div>
<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
    <tr>
      <!--<th>日期</th>-->
      <th>渠道号</th>
      <th>公司名称</th>
      <th>新注册用户数</th>
      <th>登录用户数</th>
      <th>消费订单数</th>
      <th>消费用户数</th>
      <th>消费金额</th>
      <!--<th>分成收益总金额</th>
      <?php if ($_REQUEST['selectDate']=='month'){?>
      <th>本月活跃登录用户数</th>
      <th>本月用户平均游戏时长</th>
      <?php }?>-->
    </tr>
    <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('lists')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
?>
    <tr>
      <!--<td><?php echo $_smarty_tpl->getVariable('thedates')->value;?>
 </td>-->
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
</td>
      <td><a href="/index.php?ac=report_count&clientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
&subclientid=-1&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['i']->value['name'];?>
</a></td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['registerusers'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['loginusers'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumeorders'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumeusers'];?>
</td>
      <td><a href="javascript:;" onClick="showpaydetail('<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
','<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
','<?php echo $_smarty_tpl->tpl_vars['i']->value['name'];?>
',<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
,'',<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
);"><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney'];?>
</a></td>
      <!--<td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney']*$_smarty_tpl->tpl_vars['i']->value['intoratio']/10;?>
</td>
      <?php if ($_REQUEST['selectDate']=='month'){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['activeusermonth'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgplaygamemonth'];?>
</td>
      <?php }?>-->
    </tr>
    <?php }} ?>
  </table>
  </div>
</form>

<?php if ($_smarty_tpl->getVariable('totalrow')->value==0&&(($tmp = @$_smarty_tpl->getVariable('totalrows')->value)===null||$tmp==='' ? 1 : $tmp)==0){?>
<div style="padding:5px;">子渠道没有数据</div>
<?php }else{ ?>
<br>
<form id="form2" name="form2" method="post" action="/index.php/reportnewDown" onSubmit="return btnDisabled()">
<input type="hidden"  name="thedates" value="<?php echo $_smarty_tpl->getVariable('thedates')->value;?>
" />
<input type="hidden"  name="selectDate" value="<?php echo $_REQUEST['selectDate'];?>
" id="selectDate" />
    <input type="hidden" name="gameid" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">	
  <div class="list-div" id="listDiv">
     <div class="list-com-name">子渠道列表</div>
<table width="100%" cellspacing="1" cellpadding="2" id="list-table2">
    <tr>
      <!--<th>日期</th>-->
      <th>渠道号</th>
      <th>子渠道号</th>
      <th>公司名称</th>
      <th>新注册用户数</th>
      <th>登录用户数</th>
      <th>消费订单数</th>
      <th>消费用户数</th>
      <th>消费金额</th>
      <!--<th>分成收益总金额</th>
      <?php if ($_REQUEST['selectDate']=='month'){?>
      <th>本月活跃登录用户数</th>
      <th>本月用户平均游戏时长</th>
      <?php }?> -->  
       </tr>
    <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('sublist')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
?>
    <tr>
      <!--<td><?php echo $_smarty_tpl->getVariable('thedates')->value;?>
 </td>-->
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientids'];?>
</td>
      <td><a href="/index.php?ac=report_count&clientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
&subclientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientids'];?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['i']->value['name'];?>
</a></td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['registerusers'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['loginusers'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumeorders'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumeusers'];?>
</td>
      <td><a href="javascript:;" onClick="showpaydetail('<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
','<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
','<?php echo $_smarty_tpl->tpl_vars['i']->value['name'];?>
',<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
,<?php echo $_smarty_tpl->tpl_vars['i']->value['clientids'];?>
,<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
);"><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney'];?>
</a></td>
      <!--<td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney']*$_smarty_tpl->tpl_vars['i']->value['intoratio']/10;?>
</td>
      <?php if ($_REQUEST['selectDate']=='month'){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['activeusermonth'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgplaygamemonth'];?>
</td>
      <?php }?> -->   </tr>
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
      <td width="300"><input type="button" name="button5"  class="btn" id="s" value="导出Excel" /></td>
      <td align="left"></td>
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
<script language="javascript" type="text/javascript">
//$(document).ready(function(){
	 $("#list-table tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	 $("#list-table2 tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");})
//});
</script>