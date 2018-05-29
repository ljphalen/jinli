<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html<?php echo ($ucClass? ' class="'.$ucClass.'"' : '');?>>
<head>
	<meta charset="UTF-8">
	<title>订单查询列表</title>
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
					<h1>订单查询列表</h1>
					<span class="voice"></span>
				</div>
			</div>
		</header>

		<section id="iScroll">
			<div class="select-order J_proDetail" data-ajaxurl="amigo_olapi.php">
				<div class="wrap"></div>
				<script id="J_dtemplate" type="text/icat-template">
					<ul>
						<%for(var i=0, len=data.list.length; i<len; i++){%>
						<li>
							<a href="<%=data.list[i].link%>">
								<time>下单时间：<%=data.list[i].time%></time>
								<figure class="item-pictext">
									<div class="pic">
										<span><img src="<%=data.list[i].img%>" alt="<%=data.list[i].title%>"></span>
									</div>
									<div class="desc">
										<h3><%=data.list[i].title%></h3>
										<p class="price">￥<%=data.list[i].price%></p>
										<p class="text">收货地址：<%=data.list[i].address%></p>
									</div>
								</figure>
							</a>
						</li>
						<%}%>
					</ul>
				</script>
			</div>
		</section>
	</div>
</body>
</html>