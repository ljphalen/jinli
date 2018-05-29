<?php /* Smarty version Smarty-3.0.6, created on 2013-05-16 13:35:15
         compiled from "D:\www\trunk\agent.com/template/default/index_report_paydetail.html" */ ?>
<?php /*%%SmartyHeaderCode:14001519470139e9074-89798411%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '08eabb8fb48afde5859b65b12da606a9b6abc3ed' => 
    array (
      0 => 'D:\\www\\trunk\\agent.com/template/default/index_report_paydetail.html',
      1 => 1364548236,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14001519470139e9074-89798411',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
 <!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>消费金额明细</title>
<link rel="stylesheet" type="text/css" href="/template/default/css/general.css" />
<link rel="stylesheet" type="text/css" href="/template/default/css/main.css" />
<script src="/public/js/common.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript" src="/template/default/js/common.js"></script>
<script type="text/javascript" src="/template/default/js/jquery-1.4.2-min.js"></script>

</head>

<body>
<script type="text/javascript">

$(function(){
	$("#btnexcel").click(function(){
		document.location.href='/index.php?ac=report_paydetail&dateStart=<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
&dateEnd=<?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
&clientid=<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
&subclientid=<?php echo $_smarty_tpl->getVariable('subclientid')->value;?>
&gameid=<?php echo $_smarty_tpl->getVariable('gameid')->value;?>
&name=<?php echo $_smarty_tpl->getVariable('name')->value;?>
&toexcel=1';
	});
});

function show_ipaydetail(){
	if($("#ipay_detail").is(':visible')){
		$("#ipay_detail").hide();	
	}else{
		$("#ipay_detail").show();	
	}
}

</script>

<style>
#listDiv,#list-table{
	width:400px;	
}
#list-table td{
	text-align:center;
	width:50%;
}
#list-table th{
	background-color:#BBDDE5;
}

#list-table td{
	background-color:#FFF;
}
</style>

<div>日期：<?php echo $_smarty_tpl->getVariable('dateStart')->value;?>
 — <?php echo $_smarty_tpl->getVariable('dateEnd')->value;?>
 &nbsp;&nbsp;&nbsp;&nbsp;<br>
渠道号：<?php echo $_smarty_tpl->getVariable('clientid')->value;?>
 &nbsp;&nbsp;&nbsp;&nbsp;公司名称：<?php echo $_smarty_tpl->getVariable('name')->value;?>
</div>
<div class="list-div" id="listDiv">
<?php echo $_smarty_tpl->getVariable('paytable')->value;?>


<div style="text-align:right"><input type="button" name="button" id="btnexcel" value="导出Excel" /></div>
</div>

</body>
</html>