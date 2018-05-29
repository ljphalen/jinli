<!DOCTYPE HTML>
<html class="navi">
<head>
	<meta charset="utf-8">
	<title>右页导航</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<article class="ac">
			<div class="radius-table">
				<table>
					<tr>
						<th colspan="5">
							<h2>分类导航</h2>
							<div class="JS-handle"><span><img src="<?php echo $appPic;?>/ico_tArrow.jpg" alt="" /></span></div>
						</th>
					</tr>
					<tbody class="J_mainWrap">
						<?php for($i=0; $i<6; $i++){?>
						<tr>
							<td><a href="" class="title">[新闻]</a></td>
							<td><a href="">新浪</a></td>
							<td><a href="">搜狐</a></td>
							<td><a href="">网易</a></td>
							<td><a href="">腾讯</a></td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div>
		</article>
	</div>
</body>
</html>