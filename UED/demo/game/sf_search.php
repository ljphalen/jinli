<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page" class="superfile">
		<article class="ac">
			<div class="search-bar">
				<form action="" method="post">
					<input name="keyword" type="text" placeholder="请输入关键字" value="" />
					<button type="submit" class="btn" onclick="event.preventDefault();var p=this.parentNode,input=p.querySelector('input');if(input.value!=''){p.submit();}else{input.focus();}">搜索</button>
				</form>
			</div>
			<div class="tips-num">20条关于X的搜索结果</div>
			<div class="item-list J_gameItem">
				<ul>
					<?php for($i=0; $i<6; $i++){?>
					<li>
						<a class="detail" href="detail.php">
							<div class="pic"><img src="<?php echo $appPic;?>/icon_game.jpg" alt=""></div>
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
			<div class="load-more J_loadMore" data-ajaxUrl="json.php" data-hasnext="true" data-curpage="1">
				<span>加载更多</span>
			</div>
		</article>
		<div class="goTop J_gotoTop">
			<a ></a>
		</div>
		<footer class="ft">
			<div class="quick-links">
				<a href="contact.php">联系我们</a>
				<a href="">意见反馈</a>
				<a href="">客户端下载</a>
			</div>
		</footer>

		<article class="ac">
			<div class="sear-nothing">
				<img src="<?php echo $appPic;?>/pic_error.jpg" alt="">
				<p>找到0条符合的结果</p>
			</div>
		</article>
	</div>
	<?php include '_icat.php';?>
</body>
</html>