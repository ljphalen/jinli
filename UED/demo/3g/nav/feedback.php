<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>网址导航</title>
<?php include "../_inc.php"; ?>
<meta name="appRef" content="<?php echo $staticPath;?>/apps/3g" />
<link rel="stylesheet" href="<?php echo $staticPath;?>/sys/reset/mpcore.css" />
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/assets/css/3g.pcenter.source.css">
</head>
<body>
<div id="page">
	<div class="pfeedback" id="iscrollWrap">
		<div class="mainWrap">
			<!-- 内容区域 START-->
			<header class="hd">
				<div class="slide banner">
					<img src="<?php echo $staticPath;?>/apps/3g/pic/feedback_banner.jpg" alt="意见反馈" />
				</div>
			</header>
			<div class="module">
				<section class="ct-form-feedback">
					<h1>欢迎您给我们提出宝贵的意见，让我们一起把金立官网变得更好。</h1>
					<section>
						<form action="javascript:void(0)" data-ajaxUrl="json.php" method="post" name="dataForm" id="J_checkForm">
							<div class="field">
								<label>反馈内容</label>
								<textarea placeholder="请输入您的反馈意见（字数500字以内）" name="react" id="J_react"></textarea>
							</div>
							<div class="field">
								<label>联系方式</label>
								<input type="text" name="contact" autocomplete="off" placeholder="请留下您的email或手机号码，惊喜不定期送出" value="" id="J_uinfo"/>
							</div>
							<div class="field button">
								<input type="submit" name="submit" value="提交" class="btn" />
							</div>
						</form>
					</section>
				</section>
			</div>

			<!-- 内容区域 END-->
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo $staticPath;?>/sys/icat/1.1.3/icat.js" corelib="//zepto.js"></script>
<script type="text/javascript" src="<?php echo $staticPath;?>/apps/3g/assets/js/3g.pcenter.source.js?v=20131228"></script>
<script type="text/javascript">var token = '132465564654';</script>
</body>
</html>