<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立门户</title>
	<?include '_inc.php'?>
</head>
<body>
	<div data-role="page" id="page" class="news" data-theme="no">
		<header class="hd">
			<div class="logo">
				<h1 class="inner-title"><strong>企业荣誉</strong></h1>
			</div>
		</header>
		<div class="ct">
			<div class="list news-title">
				<ul>
					<?for($i=0; $i<3; $i++){?>
					<li>
						<a href="">
							<i class="ico"><img src="<?=$appPic?>/1.gif" alt="" /></i>
							<em>“GiONEE金立”被评为“2011年度中国手机行业十大品牌”<br /><span>2012年3月23日</span></em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<li>
						<a href="">
							<i class="ico"><img src="<?=$appPic?>/2.gif" alt="" /></i>
							<em>“GiONEE金立手机”被评为“全国市场放心消费品牌”<br /><span>2012年3月23日</span></em>
							<s><img src="<?=$appPic?>/jt.png" alt="" /></s>
						</a>
					</li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>