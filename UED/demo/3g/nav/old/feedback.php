<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>意见反馈</title>
<!-- 指定当前应用模块名称 START-->
<?php $moduleName = "pcenter"; $webTitle="意见反馈"; ?>
<!-- 指定当前应用模块名称 END-->
<?php include '../_inc.php';?>
</head>
<body>
	<div id="page">
		<div class="pfeedback" id="iscrollWrap">
			<div class="mainWrap">
				<!-- 内容区域 START-->
				<header class="hd">
					<div class="slide banner">
						<img src="<?php echo $appPic;?>/feedback_banner.jpg" alt="意见反馈" />
					</div>
				</header>
				<div class="module">
					<section class="ct-form-feedback">
						<h1>欢迎您给我们提出宝贵的意见，让我们一起把金立官网变得更好。</h1>
						<section>
							<form action="json.php" name="dataForm" id="J_checkForm">
								<div class="field">
									<label>反馈内容</label>
									<textarea cols="60" rows="10" placeholder="请输入您的反馈意见（字数500字以内）" name="react" id="J_react"></textarea>
								</div>
								<div class="field">
									<label>联系方式</label>
									<input type="text" name="uinfo" autocomplete="off" placeholder="请留下您的email或手机号码，惊喜不定期送出" value="" id="J_uinfo"/>
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
	<script type="text/javascript" src="<?php echo $sysRef;?>/icat/1.1.3/icat.js" corelib="//zepto.js" main="<?php echo $mainJs;?>"></script>
	<script type="text/javascript">var token = '132465564654';</script>
</body>
</html>