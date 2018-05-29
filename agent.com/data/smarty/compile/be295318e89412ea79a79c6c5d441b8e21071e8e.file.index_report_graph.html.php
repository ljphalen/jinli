<?php /* Smarty version Smarty-3.0.6, created on 2013-08-12 16:41:09
         compiled from "/work/website/agent.com/template/default/index_report_graph.html" */ ?>
<?php /*%%SmartyHeaderCode:204365723152089fa59354b5-06032485%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be295318e89412ea79a79c6c5d441b8e21071e8e' => 
    array (
      0 => '/work/website/agent.com/template/default/index_report_graph.html',
      1 => 1375838017,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '204365723152089fa59354b5-06032485',
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
		window.location.href = "/index.php?ac=<?php echo $_smarty_tpl->getVariable('ac')->value;?>
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
  <input type="button" name="button" class="btn" id="button" value="返回列表" onClick="javascript :history.back(-1);">
</div>
</form>

<div id="graph" align="center">
    <img src="/index.php?<?php echo $_smarty_tpl->getVariable('url')->value;?>
">
    <img src="/index.php?<?php echo $_smarty_tpl->getVariable('url2')->value;?>
">
</div>
    
</body>
</html>