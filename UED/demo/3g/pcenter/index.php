<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>个人中心</title>
<?php
	/** 指定当前应用模块名称 START */
	$moduleName = "pcenter";
	$webTitle = "个人中心";
	/** 指定当前应用模块名称 END */
?>
<?php include '../_inc.php';?>
</head>

<body>
	<div id="page">
		<div class="pindex" id="iscrollWrap">
			<div class="mainWrap">
				<!-- 内容区域 START-->
				<header class="hdWrap">
					<div class="hd-l"><a href="../index.php" class="l-arrow">back</a></div>
					<div class="hd-r"><h1><?php echo $webTitle; ?></h1></div>	
				</header>
				<div class="module">
					<section class="pcenter">
						<ul class="mod-text-list">
							<li><h3>基本信息</h3></li>
							<li>
								<a href="paccount.php" class="r-arrow">
									<dl class="mod-dl-list">
										<dd class="user-img"><img src="<?php echo $appPic;?>/user-icon01.gif" /></dd>
										<dd>账号： 18600440212</dd>
										<dd>昵称：邹晓丽</dd>
									</dl>
								</a>
							</li>
						</ul>

						<ul class="mod-text-list">
							<li><h3>详细资料</h3></li>
							<li>
								<a href="pinfo.php" class="r-arrow">
									<dl class="mod-dl-list">
										<dd>QQ：580011646</dd>
										<dd>Email：zoulili@gionee.com</dd>
										<dd>生日：1990年40月4日</dd>
										<dd>性别：女</dd>
									</dl>
								</a>
							</li>
						</ul>

						<p style="margin-top:10px;"><a href="#"><img src="<?php echo $appPic;?>/pcenter-link-ad.jpg" alt="" /></a></p>
					</section>
				</div>
			</div>
		</div>
	</div>
</body>
</html>