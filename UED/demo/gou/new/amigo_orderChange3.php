<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>退/换货</title>
	<script>var webPage = true;</script>
	<?php include '_inc.php';?>
	<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/web$source.css$timestamp";?>">
</head>

<body data-pagerole="body">
	<div class="module">
		<header id="iHeader" class="hd">
			<div class="top-wrap">
				<div class="title">
					<a href="" class="back"></a>
					<h1>退/换货</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="edit-order">
				<form action="amigo_orderChange4.php">
					<ul>
						<li class="J_selectType">
							<em>*选择退/换货类型：</em>
							<input type="radio" name="type" value="0" checked="true"> <label for="">我要退货</label>
							<input type="radio" name="type" value="1"> <label for="">我要换货</label>
						</li>
						<li>
							<em>*请选择退换货的原因：</em>
							<span class="select J_selectReason">
								<select name="reason">
									<option value="1">商品是坏的</option>
									<option value="2">商品是差的</option>
								</select>
								<select>
									<option value="11">不想要了</option>
									<option value="22">太贵了</option>
								</select>
							</span>
						</li>
						<li>
							<em>留言：</em>
							<textarea name="gbook" placeholder="您有其他对我们说的吗？请在这里留言。"></textarea>
						</li>
						<li><input type="checkbox" checked="true" id="J_mustRead"> 已阅读<a href="amigo_changeRule.php">《退/换货规则》</a></li>
					</ul>
					<div class="web-btn"><button type="button" class="J_formSubmit">确认</button></div>
				</form>
			</div>
		</section>
	</div>
</body>
</html>