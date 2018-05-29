<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js "></script>
	<script type="text/javascript">
	$(function(){
		var moduleHtml='<table>\
			<tr>\
				<td></td>\
				<td>名称：<input type="text" class="input wc" name="name" value=""></td>\
				<td></td>\
			</tr>\
			<tr>\
				<td>排序：<input type="text" class="input" name="sort" value="0"></td>\
				<td>地址：<input type="text" class="input wc" name="link" value=""></td>\
				<td style="text-align:right"><a data-type="del" href="javascript:void(0);">删除</a></td>\
			</tr>\
		</table>';
		$("#addModule").click(function(){
			$("#container").append(moduleHtml);
		});
		$("#container").delegate('a[data-type=del]', 'click', function(event) {
			var parent=$(this).parent('td').parent('tr').parent('tbody').parent('table');
			if($("#container").children('table').length>1){
				parent.remove();
			}
		});
	})
	</script>
</head>
<body>
	<div id="container">
		<table>
			<tr>
				<td></td>
				<td>名称：<input type="text" class="input wc" name="name" value=""></td>
				<td></td>
			</tr>
			<tr>
				<td>排序：<input type="text" class="input" name="sort" value="0"></td>
				<td>地址：<input type="text" class="input wc" name="link" value=""></td>
				<td style="text-align:right"><a data-type="del" href="javascript:void(0);">删除</a></td>
			</tr>
		</table>
	</div>
	<button id="addModule" >添加</button>
</body>
</html>