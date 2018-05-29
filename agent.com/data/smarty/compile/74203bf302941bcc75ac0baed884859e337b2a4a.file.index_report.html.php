<?php /* Smarty version Smarty-3.0.6, created on 2013-08-19 18:10:15
         compiled from "D:\www\trunk\agent.com/template/default/index_report.html" */ ?>
<?php /*%%SmartyHeaderCode:313605211ef07196349-68256315%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '74203bf302941bcc75ac0baed884859e337b2a4a' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_report.html',
      1 => 1376906990,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '313605211ef07196349-68256315',
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
<link rel="stylesheet" type="text/css" href="/template/default/css/page.css" />

<script type="text/javascript" src="/template/default/js/common.js"></script>
<script type="text/javascript" src="/template/default/js/jquery-1.4.2-min.js"></script>
<script type="text/javascript" src="/template/default/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="/template/default/js/page.js"></script>
<script type="text/javascript">
$(function(){
	$("#form1").submit(function(){
		$("#selectDate").val('');
	});
	
	$("#find-btn").click(function(){
		var str = $("#form1").serialize();
		//window.location.href = "/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&"+str+"&graph=1";
		//alert(111);
		//$.post("/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&graph=1",str,function(){});
		//alert(3333);
	});
	
	var $tab_list = $("#tab-list li");
	$("#tab-list li a").click(function(){
		$tab_list.removeClass("cur");
		$(this).parent().addClass("cur");
	});
	
	$("#btn_excel").click(function(){
		document.location.href= '<?php echo $_smarty_tpl->getVariable('pageurl')->value;?>
&toexcel=1&detail=1';
	});
	$("#s").click(function(){
		/*$("#detail").val('');
		
		if(btnDisabled())
		$("#form2").submit();*/
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
	var detail=$("#detail").val();
	if(detail=='1'){
		var t=false;
		$("#list-table input:checkbox").each(function(){
			if(this.checked){
				$("#btn_excel").val("正在导出Excel...").attr("disabled",true);
				t=true;		
				return false;
			}
		});
		
		if(t){setTimeout('$("#btn_excel").val("导出详细Excel").attr("disabled",false);',5000);}else {alert("请选择您要导出的数据行");}
		return t;			
	}else{
		var t=false;
		$("#list-table input:checkbox").each(function(){
			if(this.checked){
				$("#s").val("正在导出Excel...").attr("disabled",true);
				t=true;		
				return false;
			}
		});
		
		if(t){setTimeout('$("#s").val("导出Excel").attr("disabled",false);',5000);}else {alert("请选择您要导出的数据行");}
		return t;			
	}
}

function showpaydetail(dateStart,dateEnd,name,clientid,subclientid,gameid){
	/*asyncbox.open({
	　　　url : '/index.php?ac=report_paydetail&dateStart='+dateStart+'&dateEnd='+dateEnd+'&clientid='+clientid+'&subclientid='+subclientid+'&gameid='+gameid+'&name='+name+'',
	　　　width : 450,
	　　　height : 600,
		 title : '消费金额明细'
	});*/
	return false;
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
</head>

<body>
<h1>
<span class="action-span1">查询统计报表</span><span id="search_id"
	class="action-span1"> - 
    <?php if ($_smarty_tpl->getVariable('channeltype')->value==1&&$_smarty_tpl->getVariable('levels')->value>=200){?>
    自主渠道商统计报表
    <?php }elseif($_smarty_tpl->getVariable('channeltype')->value==2&&$_smarty_tpl->getVariable('levels')->value>=200){?>
    移动渠道商统计报表
    <?php }else{ ?>
    查询统计报表
    <?php }?>
    </span>
<div style="clear: both"></div>
</h1>
<div id="tab-list">
	<ul>
	    <li <?php if ($_smarty_tpl->getVariable('gameid')->value==0){?>class="cur"<?php }?> ><a href="/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=0&keyword=<?php echo $_smarty_tpl->getVariable('keyword')->value;?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&selectDate=<?php echo $_smarty_tpl->getVariable('selectDate')->value;?>
" hidefocus="ture">综合统计</a></li>
        <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('games')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
	    <li <?php if ($_smarty_tpl->getVariable('gameid')->value==$_smarty_tpl->tpl_vars['key']->value){?>class="cur"<?php }?> ><a href="/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
&keyword=<?php echo $_smarty_tpl->getVariable('keyword')->value;?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&selectDate=<?php echo $_smarty_tpl->getVariable('selectDate')->value;?>
" hidefocus="ture"><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
统计</a></li>
        <?php }} ?>
	</ul>
</div>
<div class="tab-content">

<!-- 三级渠道的数据 -->
<?php if ($_smarty_tpl->getVariable('levels')->value=="10"){?>
<form name="form1" method="get" action="">
<div class="form-div">
  日期:从
  <!--<input name="dateStart" type="text" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateStart', '%Y-%m-%d', '24', false,'dateStart');" readonly />-->
  <input name="dateStart" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'dateEnd\')||\'2020-10-01\'}'})"/> 
  到
  <input name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'dateStart\')}',maxDate:'2020-10-01'})"/>
<!--  <input type="text" name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateEnd', '%Y-%m-%d', '24', false,'dateEnd');" readonly />
  <input type="hidden"  name="selectDate" value="<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
" id="selectDate" />-->
  <input type="submit" name="button" class="btn" id="button" value="查询">
  <input type="button" name="button2" class="btn" id="button2" value="昨天" onClick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=&selectDate=yesterday'">
  <input type="button" name="button3" class="btn" id="button3" value="最近7天"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=&selectDate=hebdomad'">
  <input type="button" name="button4" class="btn" id="button4" value="本月"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=&selectDate=month'">
  <input type="hidden" name="gameid" class="btn" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
"  >	
  <input type="hidden" name="ac" class="btn" value="<?php echo $_smarty_tpl->getVariable('ac')->value;?>
">	
</div>

<div id="period_count">
    注册人数总计：<span class="red"><?php echo number_format((($tmp = @$_smarty_tpl->getVariable('period_count')->value['registerusers'])===null||$tmp==='' ? 0 : $tmp));?>
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
<form id="form2" name="form2" method="post" action="/index.php/reportnewDown"  onSubmit="return btnDisabled()">
<input type="hidden"  name="thedates" value="<?php echo $_smarty_tpl->getVariable('thedates')->value;?>
" />
<input type="hidden"  name="selectDate" value="<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
" id="selectDate" />
  <input type="hidden" name="gameid" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">	
   <div class="list-div" id="listDiv">
     <div class="list-com-name"><?php echo $_smarty_tpl->getVariable('agentname')->value;?>
报表</div>
<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
    <tr>
      <!--<th>日期</th>-->
       <th>子渠道号</th>      
      <th>公司名称</th>
      <th>新注册用户数</th>
      <th>登录用户数</th>
      <th>消费订单数</th> 
      <th>消费用户数</th>
      <th>消费金额</th>
      <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <th>爱贝的消费金额</th>
      <th>移动的消费金额</th>
      <?php }?>
      <th>人均充值次数</th>
      <th>付费率</th>
      <th>ARPPU值</th>
      <th>用户价值</th>
      
    </tr>
    <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
?>
    <tr>
      <!--<td><?php echo $_smarty_tpl->getVariable('thedates')->value;?>
 </td>-->
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientids'];?>
</td>      
      <td style="text-align:center"><a href="/index.php?ac=report_count&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&clientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
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
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney'];?>
</td>
      <!--<td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney']*$_smarty_tpl->tpl_vars['i']->value['intoratio']/10;?>
</td>-->
      <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyipay'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyyd'];?>
</td>
      <?php }?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgpaymenttime'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['paymentrate'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['ARPPU'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['registerusersvalue'];?>
</td>
    </tr>
    <?php }} ?>
  </table>
  </div>
  <table width="100%" border="0">
    <tr style="background:none;">
      <td width="300"><input type="button" name="button5"  class="btn" id="s" value="导出Excel" /> </td>
      <td></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php }?>
<?php }?>
 
<!-- 二级渠道的数据 --> 
<?php if ($_smarty_tpl->getVariable('levels')->value=="30"){?>
<form name="form1" id="form1" method="get" action=""><div class="form-div">
  子渠道号/公司
  <input name="keyword" type="text" id="keyword" value="<?php echo $_smarty_tpl->getVariable('keyword')->value;?>
">
  日期:从
  <!--<input name="dateStart" type="text" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateStart', '%Y-%m-%d', '24', false,'dateStart');" readonly />-->
  <input name="dateStart" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'dateEnd\')||\'2020-10-01\'}'})"/> 
  到
  <input name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'dateStart\')}',maxDate:'2020-10-01'})"/>
<!--  <input type="text" name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateEnd', '%Y-%m-%d', '24', false,'dateEnd');" readonly />
  <input type="hidden"  name="selectDate" value="<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
" id="selectDate" />-->
  <input type="submit" name="button" class="btn" id="button" value="查询">
  <input type="button" name="button2" class="btn" id="button2" value="昨天" onClick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=&selectDate=yesterday&keyword='+this.form.keyword.value">
  <input type="button" name="button3" class="btn" id="button3" value="最近7天"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=&selectDate=hebdomad&keyword='+this.form.keyword.value">
  <input type="button" name="button4" class="btn" id="button4" value="本月"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=&selectDate=month&keyword='+this.form.keyword.value">
    <input type="button" name="button10" class="btn" id="find-btn" value="<?php echo $_smarty_tpl->getVariable('show_graph')->value;?>
" onclick="location.href='/index.php?sac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&shape=1&ac=graph_show&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&dateStart='+this.form.dateStart.value+'&dateEnd='+this.form.dateEnd.value+'&selectDate=<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
&keyword='+this.form.keyword.value" >
  <input type="hidden" name="gameid" class="btn" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">
  <input type="hidden" name="ac" class="btn" value="<?php echo $_smarty_tpl->getVariable('ac')->value;?>
">	
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
  <div class="list-div" id="listDiv">
     <div class="list-com-name"><?php echo $_smarty_tpl->getVariable('agentname')->value;?>
报表</div>
<table width="100%" cellspacing="1" cellpadding="2" id="list-table2">
    <tr>
      <!--<th>日期</th>-->
      <th>渠道号</th>
      <th>公司名称</th>
      <th>新注册用户数</th>
      <th>登录用户数</th>
      <th>消费订单数</th>
      <th>消费用户数</th>
      <th>消费金额</th>
      <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <th>爱贝的消费金额</th>
      <th>移动的消费金额</th>
      <?php }?>
      <th>人均充值次数</th>
      <th>付费率</th>
      <th>ARPPU值</th>
      <th>用户价值</th>
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
      <td style="text-align:center"><a href="/index.php?ac=report_count&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&clientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
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
      <td><a href="javascript:;" onClick=" showpaydetail('<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
','<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
','<?php echo $_smarty_tpl->tpl_vars['i']->value['name'];?>
',<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
,'',<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
);"><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney'];?>
</a></td>
      <!--<td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoney']*$_smarty_tpl->tpl_vars['i']->value['intoratio']/10;?>
</td>-->
       <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyipay'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyyd'];?>
</td>
      <?php }?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgpaymenttime'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['paymentrate'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['ARPPU'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['registerusersvalue'];?>
</td>
    </tr>
    <?php }} ?>
  </table>
  </div>
</form>
<br>
<?php if ($_smarty_tpl->getVariable('totalrow')->value==0&&(($tmp = @$_smarty_tpl->getVariable('totalrows')->value)===null||$tmp==='' ? 1 : $tmp)==0){?>
子渠道没有数据
<?php }else{ ?>
<form id="form2" name="form2" method="post" action="/index.php/reportnewDown" onSubmit="return btnDisabled()">
<input type="hidden"  name="thedates" value="<?php echo $_smarty_tpl->getVariable('thedates')->value;?>
" />
<input type="hidden"  name="selectDate" value="<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
" id="selectDate" />
  <input type="hidden" name="gameid" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">	
   <div class="list-div" id="listDiv">
     <div class="list-com-name">子渠道列表</div>
<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
    <tr>
      <!--<th>日期</th-->
      <th>子渠道号</th>
      <th>公司名称</th>
      <th>新注册用户数</th>    
      <th>登录用户用户数</th>
      <th>消费订单数</th>
      <th>消费用户数</th>
      <th>消费金额</th>
      <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <th>爱贝的消费金额</th>
      <th>移动的消费金额</th>
      <?php }?>
      <th>人均充值次数</th>
      <th>付费率</th>
      <th>ARPPU值</th>
      <th>用户价值</th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
?>
    <tr>
      <!--<td><?php echo $_smarty_tpl->getVariable('thedates')->value;?>
</td>-->
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientids'];?>
</td>
      <td style="text-align:center"><a href="/index.php?ac=report_count&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&clientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
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
</td>-->
      <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyipay'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyyd'];?>
</td>
      <?php }?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgpaymenttime'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['paymentrate'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['ARPPU'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['registerusersvalue'];?>
</td>
    </tr>
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
      <td width="300"><input type="button" name="button5"  class="btn" id="s" value="导出Excel" /> </td>
      <td></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php }?>
<?php }?>
 
 
 <!-- 管理员级别的数据-->
  <?php if ($_smarty_tpl->getVariable('levels')->value=="200"){?>
<form name="form1" id="form1" method="get" action=""><div class="form-div">
<?php if ($_smarty_tpl->getVariable('table')->value==2||$_smarty_tpl->getVariable('table')->value==3){?>
二级渠道：<select name="companys" id="companys">
			<option>请选择</option>
			<?php  $_smarty_tpl->tpl_vars['citem'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['ckey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('companys')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['citem']->key => $_smarty_tpl->tpl_vars['citem']->value){
 $_smarty_tpl->tpl_vars['ckey']->value = $_smarty_tpl->tpl_vars['citem']->key;
?>
				<OPTGROUP LABEL="<?php echo $_smarty_tpl->tpl_vars['ckey']->value;?>
">
				<?php  $_smarty_tpl->tpl_vars['scitem'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['sckey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['citem']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['scitem']->key => $_smarty_tpl->tpl_vars['scitem']->value){
 $_smarty_tpl->tpl_vars['sckey']->value = $_smarty_tpl->tpl_vars['scitem']->key;
?>
					<option><?php echo $_smarty_tpl->tpl_vars['scitem']->value;?>
</option>;
				<?php }} ?>
			<?php }} ?>
		</select>
三级渠道：<span id="subcompanys"></span>
<?php }?>
  渠道号/公司
  <input name="keyword" type="text" id="keyword" value="<?php echo $_smarty_tpl->getVariable('keyword')->value;?>
">
  日期:从
  <!--<input name="dateStart" type="text" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateStart', '%Y-%m-%d', '24', false,'dateStart');" readonly />-->
  <input name="dateStart" id="dateStart" value="<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'dateEnd\')||\'2020-10-01\'}'})"/> 
  到
  <input name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" class="Wdate" type="text" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'dateStart\')}',maxDate:'2020-10-01'})"/>
<!--  <input type="text" name="dateEnd" id="dateEnd" value="<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
" size="16" maxlength="19" onClick="return showCalendar('dateEnd', '%Y-%m-%d', '24', false,'dateEnd');" readonly />
  <input type="hidden"  name="selectDate" value="<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
" id="selectDate" />-->
  <input type="submit" name="button" class="btn" id="button" value="查询">
  <input type="button" name="button2" class="btn" id="button2" value="昨天" onClick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&selectDate=yesterday&keyword='+this.form.keyword.value">
  <input type="button" name="button3" class="btn" id="button3" value="最近7天"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&selectDate=hebdomad&keyword='+this.form.keyword.value">
  <input type="button" name="button4" class="btn" id="button4" value="本月"  onclick="location.href='/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&selectDate=month&keyword='+this.form.keyword.value">
  <input type="button" name="button10" class="btn" id="find-btn" value="<?php echo $_smarty_tpl->getVariable('show_graph')->value;?>
" onclick="location.href='/index.php?sac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
&shape=1&ac=graph_show&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&dateStart='+this.form.dateStart.value+'&dateEnd='+this.form.dateEnd.value+'&selectDate=<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
&keyword='+this.form.keyword.value" >
  <input type="hidden" name="gameid" class="btn" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">	
  <input type="hidden" name="ac" class="btn" value="<?php echo $_smarty_tpl->getVariable('ac')->value;?>
">	
  <input type="hidden" name="channeltype" class="btn" value="<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
">	
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
<form id="form2" name="form2" method="post" action="/index.php/reportnewDown"  onSubmit="return btnDisabled()">
<input type="hidden"  name="thedates" value="<?php echo $_smarty_tpl->getVariable('thedates')->value;?>
" />
<input type="hidden"  name="selectDate" value="<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
" id="selectDate" />
  <input type="hidden" name="gameid" value="<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">	
  <input type="hidden" name="detail" id="detail" value="">	
  <div class="list-div" id="listDiv">
<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
    <tr>      <!--<th>日期</th>-->
      <!--<th>子渠道号</th>-->
      <th>渠道号</th>
      <th>公司名称</th>
      <th>新注册用户数</th>
      <th>登录用户数</th>
      <th>消费订单数</th>
      <th>消费用户数</th>
      <th>消费金额</th>
      <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <th>爱贝的消费金额</th>
      <th>移动的消费金额</th>
      <?php }?>
      <th>人均充值次数</th>
      <th>付费率</th>
      <th>ARPPU值</th>
      <th>用户价值</th>
      <!--<th>分成收益总金额</th>
      <?php if ((($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp)=='month'){?>
      <th>本月活跃登录用户数</th>
      <th>本月用户平均游戏时长</th>
      <?php }?>-->
      <th>操作</th>
    </tr>
    <?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
?>
    <tr>      <!--<td><?php echo $_smarty_tpl->getVariable('thedates')->value;?>
 </td>-->
      <!--<td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientids'];?>
</td>-->
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
</td>
      <td style="text-align:center"><a href="/index.php?ac=report_count&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&clientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
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
      <?php if ((($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp)=='month'){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['activeusermonth'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgplaygamemonth'];?>
</td>
      <?php }?>-->
       <?php if ($_smarty_tpl->getVariable('channeltype')->value!=2){?>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyipay'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['consumemoneyyd'];?>
</td>
      <?php }?>
       <td><?php echo $_smarty_tpl->tpl_vars['i']->value['avgpaymenttime'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['paymentrate'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['ARPPU'];?>
</td>
      <td><?php echo $_smarty_tpl->tpl_vars['i']->value['registerusersvalue'];?>
</td>
      <td>
      <?php if ($_smarty_tpl->tpl_vars['i']->value['clientids']==0){?>
      <a href="/index.php?ac=report_sub&channeltype=<?php echo $_smarty_tpl->getVariable('channeltype')->value;?>
&clientid=<?php echo $_smarty_tpl->tpl_vars['i']->value['clientid'];?>
&selectDate=<?php echo (($tmp = @$_REQUEST['selectDate'])===null||$tmp==='' ? '' : $tmp);?>
&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
">查看子渠道</a>
      <?php }?>
      </td>
    </tr>
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
      <td width="300" nowrap><input type="button" name="button5"  class="btn" id="s" value="导出Excel" /> 
        <input type="button" name="btn_excel"  class="btn" id="btn_excel" value="导出详细Excel" />
      </td>
      <td><?php echo $_smarty_tpl->getVariable('pagination')->value;?>
</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php }?>
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
$(document).ready(function(){
	 $("#list-table tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
	 $("#list-table2 tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");})
	 $("#companys").change(function (){
		$("#subcompanys").load('/index.php/reportnew/get_sub_company/'+(this.value?this.value:-1)+'/'+Math.random())
	 });
});


</script>
</body>
</html>