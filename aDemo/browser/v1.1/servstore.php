<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立世界-售后服务</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<?php $title = '服务网点'; include '_sheader.php';?>
		
		<article class="ac">
			<div class="form-box">
				<form action="">
					<h3>选择你所在的：</h3>
					<ul>
						<li>
							<label for="">省份：</label>
							<select name="" id="">
								<option value="">广东</option>
							</select>
						</li>
						<li>
							<label for="">市区：</label>
							<select name="" id="">
								<option value="">深圳</option>
							</select>
						</li>
						<li>
							<select name="" id="">
								<option value="">客服中心</option>
							</select>
						</li>
						<li><button class="btn">搜索</button></li>
					</ul>
				</form>
			</div>
			
			<div class="radius-table">
				<table>
					<tr>
						<th>服务网点</th>
						<th>店面地址</th>
						<th>咨询电话</th>
					</tr>
					<?php for($i=0; $i<3; $i++){?>
					<tr>
						<td>广东中域电讯连锁股份有限公司</td>
						<td>东莞市万江区新和社区新华南路49号中域集团员工生活区B栋4楼资源部</td>
						<td>0769<br/>23661618/8835</td>
					</tr>
					<?php }?>
				</table>
			</div>
		</article>
	</div>
</body>
</html>