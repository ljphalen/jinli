<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" >
		<!-- search header -->
		<header class="hd search_bg ">
			<h1>搜索</h1>
			<div class="back-home"><a href="index.php">&lt;</a></div>
			<div class="search-bar">
				<form action="" method="post">
					<input class='special' maxlength=12  name="keyword" type="text" rqd="true" data-error="" data-null="请输入关键字" placeholder="请输入关键字"  value=""  onfocus="if(this.value==''){this.placeholder='';} " oninput="if(this.value=='')this.placeholder='';"
					onblur="if(this.value==''){this.placeholder='';}"
					 />
					<button type="button"  class="search"></button>
				</form>
			</div>
		</header>
		<!-- without search -->
		<article class="ac search_bg" >
			
			<div class="tips-num">大家都在搜：</div>
			<div class="result-list">
				<ul>
					<?php for($i=1; $i<11; $i++){?>
					<li>
						<a class='search' href="detail.php">
							<div class="num<?php if($i<=3){echo ' yellow';}?>"><?php echo $i ?></div>							
							<span class="desc">捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人捕鱼达人</span>
						</a>
					</li>
					<?php }?>
				</ul>
			</div>
		</article>

				<!-- result-search nothing -->
		 <article class="ac search_bg">
			<div class="search_nothing">
				<!-- <p>对不起，没有找到你想要的结果！</p> -->
				<img src="<?php echo $appPic;?>/pic_error.png" alt="">
			</div>
		</article>  

		<!-- result-search from Baidu Application-->
		<article class="ac search_bg">
			
			<div class="tips-num">共5条结果来自百度应用：</div>
			<div class="baiduSearch-tips">百度应用的游戏可能不适配您的手机，请谨慎下载。</div>
			<div class="item-list J_resultItem">
				<ul>
					<?php for($i=1; $i<6; $i++){?>
					<li>
						<a class="detail" href="detail.php">
							<div class="pic">
								<img class="pic" alt="" data-src="<?php echo $appPic;?>/icon_game.jpg" src="<?php echo $appPic;?>/blank.gif">
							</div>
							<div class="desc">
								<h3>捕鱼达人</h3>
								<p>捕鱼达人是一款深海捕鱼的游戏，玩家在</p>
							</div>
						</a>
						<div class="download"><a class="btn" href="">安装</a></div>
					</li>
					<!-- <li>
						<img class="pic" alt="" data-src="<?php echo $appPic;?>/icon_game.jpg" src="<?php echo $appPic;?>/blank.gif">
						<div class="info">
							<p class="title">捕鱼达人</p>
							<p class='content'>是一款深海捕鱼游戏</p>
						</div>
						<div class="detail_info"><span>2.3M</span><a href="detail.php"></a></div>
					</li> -->
					<?php }?>
				</ul>
			</div>
			<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="true" data-curpage="1">
				<span>加载更多</span>
			</div>
		</article>

		<!-- result-search from Local Application-->
		<article class="ac search_bg" >
			
			<div class="tips-num">共搜索到5条结果</div>
			<div class="item-list J_resultItem">
				<ul>
					<?php for($i=1; $i<6; $i++){?>
						<li>
						<a class="detail" href="detail.php">
							<div class="pic">
								<img class="pic" alt="" data-src="<?php echo $appPic;?>/icon_game.jpg" src="<?php echo $appPic;?>/blank.gif">
							</div>
							<div class="desc">
								<h3>捕鱼达人</h3>
								<p>捕鱼达人是一款深海捕鱼的游戏，玩家在</p>
							</div>
						</a>
						<div class="download"><a class="btn" href="">安装</a></div>
					</li>
					<?php }?>
				</ul>
			</div>
			<div class="load-more J_loadMore" data-ajaxurl="json.php" data-hasnext="true" data-curpage="1">
				<span>加载更多</span>
			</div>
		</article>
		<div class="goTop J_gotoTop">
			<a ></a>
		</div>
	</div>
	<div class="J_tipBox tip-box invisible">
		<p></p>
		<div class="mask"></div>
	</div>
	<?php include '_icat.php';?>
</body>
</html>