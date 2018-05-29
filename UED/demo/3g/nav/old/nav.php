<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>导航</title>
<!--指定当前应用模块名称 START-->
	<?php $moduleName = "nav2";?>
<!--指定当前应用模块名称 END-->
<?php include '../__inc.php';?>
</head>
<?php
	$arr = Array("常用","资讯","阅读","娱乐","生活","软件");
?>
<body class="home" data-pagerole="body">
<div class="wrapper">
	<section class="module">
		<!--<section class="ui-nav-wrap">
		<?php for($i = 0; $i < 6; $i++):?>
			<div class="block">
				<div class="ui-nav-title J_nav_title" <?php if($i == 5) echo 'data-last="true"';?>><h2><?php echo $arr[$i];?></h2></div>
				<div class="ui-nav-content J_nav_content <?php if($i != 0) echo 'ishide';?>">
					<ul class="ui-nav-ul">
					<?php for($j = 0; $j < 6; $j++):?>
						<li>
							<a href="###">
								<div>
									<img src="<?php echo $appPic;?>/baidu.png" />
									<span>购物</span>
								</div>
							</a>
						</li>
					<?php endfor;?>
					</ul>
				</div>
				</div>
		<?php endfor;?>
		</section>-->
	</section>
</div>
</body>
</html>
