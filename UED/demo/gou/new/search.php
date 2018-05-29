<!DOCTYPE HTML>
<?php include '_cfg.php';?>
<html class="<?php echo $ucClass;?>">
<head>
	<meta charset="UTF-8">
	<title></title>
	<script>var webPage = true;</script>
	<?php include '_inc.php';?>
</head>

<body>
	<div class="module">
		<header id="iHeader" class="hd">
			<div class="top-wrap">
				<div class="title">
					<a href="" class="back"></a>
					<h1>搜索</h1>
				</div>
			</div>
		</header>
		<section id="iScroll" class="search-wrap">
			<div class="panel">
				<div class="search-bar">
					<form action=""><input type="text" value="aaabbbccc" placeholder="连衣裙"><button></button></form>
				</div>
				<div class="result-list">
					<ul>
						<?php for($i=1; $i<11; $i++){?>
						<li><a href=""><span<?php if($i<4){echo " class=\"num-top\"";}?>><?php echo $i;?></span>高跟鞋</a></li>
						<?php }?>
					</ul>
				</div>
			</div>
		</section>
	</div>
</body>
</html>