<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js "></script>
	<script type="text/javascript" src="script/main.js"></script>
</head>
<body>
	<input type="text" value="游戏名称1" id='txtSearch'/>
	<input type="button" id="btnSearch" value="搜索" />
	<div id="container" style="position:relative;">
		<div id="container_1" style="position:absolute;left:0px;">
			<div id="tb_1" style="margin-top:10px;">
				<table cellpadding="0" cellspacing="0" style="width:500px;border:1px solid #797979;" >
					<tr >
					<td colspan="5" style="text-align:right;color:#fff;background:#999999;width:498px;border:1px solid #797979;">共<span id="result"></span>条结果</td>	
					</tr>
					<tr style="background:#c9c9c9;">
					<td style="width: 40px;text-align: center;" ><input id="ck_1" type="checkbox"/></td>
					<td style="width: 101px;text-align: center;">图标</td>
					<td style="width: 146px;text-align: center;">标题</td>
					<td style="width: 129px;text-align: center;">分类</td>
					<td style="text-align: center;">大小</td>
				</tr>
				</table>
			</div>
			<div id="content_1" style="height:420px;overflow:hidden; overflow-y:auto;width:515px;" >
				<table  cellpadding="0" cellspacing="0" style="width:500px;border:1px solid #797979;border-top:0px;border-bottom:0px;" >
				<tbody data-role="tbody"></tbody>
			</table>
			</div>
		</div>
		<div id="panel" style="height:400px;margin-left:20px;padding-top:150px;position:absolute;left:500px;">
			<input type="button" id="add" value="添加" /><br/>
			<input type="button" id="delete" value="删除" />
		</div>
		<div id="container_2" style="margin-left:30px;position:absolute;left:560px;">
			<div id="tb_2" style="margin-top:10px;">
				<table cellpadding="0" cellspacing="0" style="width:500px;border:1px solid #797979;" >
					<tr >
					<td colspan="5" style="text-align:right;color:#fff;background:#999999;width:498px;border:1px solid #797979;">共<span id="result"></span>条结果</td>	
					</tr>
					<tr style="background:#c9c9c9;">
					<td style="width: 40px;text-align: center;" ><input id="ck_2" type="checkbox"/></td>
					<td style="width: 101px;text-align: center;">图标</td>
					<td style="width: 146px;text-align: center;">标题</td>
					<td style="width: 129px;text-align: center;">分类</td>
					<td style="text-align: center;">大小</td>
				</tr>
				</table>
			</div>
			<div id="content_2" style="height:420px;overflow:hidden; overflow-y:auto;width:515px;" >
				<table  cellpadding="0" cellspacing="0" style="width:500px;border:1px solid #797979;border-top:0px;border-bottom:0px;" >
				<tbody data-role="tbody"></tbody>
			</table>
			</div>
		</div>
	</div>
</body>
</html>