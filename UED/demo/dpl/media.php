<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>Gionee H5 R&D DPL Module</title>
	<?php $moduleName = "dpl"; include '_inc.php';?>
</head>
<style type="text/css">
.ui-media{
	padding:10px;
}
.ui-media, .ui-media > span, .ui-media ul{
	display:none;
}

.ui-media i {
	color:red;
}

/* Desk computer 1024 * 768 */
@media only screen and (min-width:1024px) {
	.ui-media-pc, .ui-media-pc ul{display:block; background:yellow;}
}

/* ================================================================================================================================ */

/* 
Android 480 * 800 portrait
@media only screen and (min-width:241px) and (max-width:320px) and (-webkit-min-device-pixel-ratio:1) {
	.ui-media-android-small{display:block; background:yellow;}
	.ui-media-android-small .portrait{display:inline-block;}
}

Android 480 * 800 landscape
@media only screen and (min-width:241px) and (max-width:320px) and (-webkit-min-device-pixel-ratio:1) {
	.ui-media-android-small{display:block; background:lightblue;}
	.ui-media-android-small .landscape{display:inline-block;}
} */

/* Android 480 * 800 */
@media only screen and (min-width:241px) and (max-width:360px) and (-webkit-device-pixel-ratio:1.5) {
	.ui-media-android-middle{display:block; background:yellow;}
	.ui-media-android-middle .portrait{display:inline-block;}
}

@media only screen and (min-width:361px) and (max-width:640px) and (-webkit-device-pixel-ratio:1.5){
	.ui-media-android-middle{display:block; background:lightblue;}
	.ui-media-android-middle .landscape{display:inline-block;}
}

/* Android 720 * 1280  */
@media only screen and (min-width:321px) and (max-width:360px) and (-webkit-min-device-pixel-ratio:2) {
	.ui-media-android-hight{display:block; background:yellow;}
	.ui-media-android-hight .portrait{display:inline-block;}
}

@media only screen and (min-width:361px) and (max-width:1024px) and (-webkit-min-device-pixel-ratio:2) {
	.ui-media-android-hight{display:block; background:lightblue;}
	.ui-media-android-hight .landscape{display:inline-block;}
}

/* ================================================================================================================================ */

/* iPhone Media Queries (All generations) */

/* iPhone 5 Media Queries */
@media only screen and (min-device-width:320px) and (max-device-width:568px) and (orientation:portrait) and (device-aspect-ratio: 40/71){
	.ui-media-iphone-5th{display:block; background:yellow;}
	.ui-media-iphone-5th .portrait{display:inline-block;}
}

@media only screen and (min-device-width:320px) and (max-device-width:568px) and (orientation:landscape) and (device-aspect-ratio: 40/71){
	.ui-media-iphone-5th{display:block; background:lightblue;}
	.ui-media-iphone-5th .landscape{display:inline-block;}
}

/* iPhone 4/4S Meida Queries */
@media only screen and (min-device-width:320px) and (max-device-width:480px) and (orientation:portrait) and (device-aspect-ratio: 2/3) and (-webkit-min-device-pixel-ratio:2){
	.ui-media-iphone-4th{display:block; background:yellow;}
	.ui-media-iphone-4th .portrait{display:inline-block;}
}

@media only screen and (min-device-width:320px) and (max-device-width:480px) and (orientation:landscape) and (device-aspect-ratio: 2/3) and (-webkit-min-device-pixel-ratio:2){
	.ui-media-iphone-4th{display:block; background:lightblue;}
	.ui-media-iphone-4th .landscape{display:inline-block;}
	.ui-media-android-hight{display:none;}
}

/* iPhone 2G & 3G & 3GS Media Queries */
@media only screen and (min-device-width:320px) and (max-device-width:480px) and (orientation:portrait) and (device-aspect-ratio: 2/3) and (-webkit-device-pixel-ratio:1){
	.ui-media-iphone{display:block; background:yellow;}
	.ui-media-iphone .portrait{display:inline-block;}
}

@media only screen and (min-device-width:320px) and (max-device-width:480px) and (orientation:landscape) and (device-aspect-ratio: 2/3) and (-webkit-device-pixel-ratio:1){
	.ui-media-iphone{display:block; background:lightblue;}
	.ui-media-iphone .landscape{display:inline-block;}
}


/* iPad Media Queries (All generations - including iPad mini) (device-aspect-ratio: 3/4) */

/* iPad 1th & 2th */
@media only screen and (min-width:768px) and (max-width:1024px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio:1){
	.ui-media-ipad{display:block; background:yellow;}
	.ui-media-ipad .portrait{display:inline-block;}
}

@media only screen and (min-width:768px) and (max-width:1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio:1){
	.ui-media-ipad{display:block; background:lightblue;}
	.ui-media-ipad .landscape{display:inline-block;}
}

/*iPad3 & iPad4 Media Queries */
@media only screen and (min-width:768px) and (max-width:1024px) and (orientation:portrait) and (-webkit-min-device-pixel-ratio:2){
	.ui-media-ipad-retina{display:block; background:yellow;}
	.ui-media-ipad-retina .portrait{display:inline-block;}
}

@media only screen and (min-width:768px) and (max-width:1024px) and (orientation:landscape) and (-webkit-min-device-pixel-ratio:2){
	.ui-media-ipad-retina{display:block; background:lightblue;}
	.ui-media-ipad-retina .landscape{display:inline-block;}
}
</style>
<script type="text/javascript">
	window.onload = window.onresize = function(){
		var screenWidth = window.screen.width, screenHeight = window.screen.height,
			outputWidth = window.document.width, outputHeight = window.document.height,
			output = document.querySelectorAll('.ui-media .output'),
			resolution = document.querySelectorAll('.ui-media .resolution');
		for (var i = 0; i < resolution.length; i++) {
			resolution[i].innerHTML = screenWidth + '*' + screenHeight;
		}

		for (var i = 0; i < output.length; i++) {
			output[i].innerHTML = outputWidth;
		}
		
		//alert(window.devicePixelRatio);
		//alert(window.document.width);
	}
</script>
<body>
<section id="wrapper">
	<header>
		<div class="lookit-toolbar">
			<div class="lookit-toolbar-wrap">
				<div class="lookit-toolbar-left">
					<a class="lookit-toolbar-backbtn lookit-toolbar-backbtn-blue" href="../../index.php">返回</a>
				</div>
				<div class="lookit-toolbar-title">Meida Queries 检测</div>
				<div class="lookit-toolbar-right"></div>
			</div>
		</div>
	</header>
	<section id="content">
		<div class="lookit-modules">
			<div class="lookit-module">
				<div class="lookit-module-head">
					<!-- <div class="pic"><i class="i_form">B</i></div>
					<div class="txt">
						<h3>Box version 0.1</h3>
						<p>带边框和标题的标准区块。</p>
					</div> -->
				</div>
				<div class="lookit-module-demo">
					<div class="lookit-module-dom">
						<!-- box -->
						<div class="ui-box">
							<div class="ui-box-head">
								<div class="ui-box-head-border">
									<h3 class="ui-box-head-title">当前设备信息</h3>
									<span class="ui-box-head-desc">Media Queries</span>
								</div>
							</div>
							<div class="ui-box-container">
								<div class="ui-box-content">
									<!-- PC -->
									<div class="ui-media ui-media-pc">
										<ul>
											<li><span class="label">当前设备：</span> PC</li>
											<li><span class="label">屏幕状态：</span> 横屏（正常）</li>
											<li><span class="label">DPI等级：</span> <i class="rank">1.0</i></li>
											<li><span class="label">分辨率：</span> <i class="resolution">1024 * 768</i></li>
											<li><span class="label">渲染尺寸（宽）：</span> <i class="output"></i><li>
										</ul>
									</div>
									<!-- iPhone Media Queries -->
									<div class="ui-media ui-media-iphone">
										<span class="landscape">iPhone 2G & 3G & 3GS <i>320 * 480</i> 横屏</span>
										<span class="portrait">iPhone 2G & 3G & 3GS <i>320 * 480</i> 竖屏</span>
									</div>
									<div class="ui-media ui-media-iphone-4th">
										<span class="landscape">iPhone 4/4S <i>640 * 960</i> 横屏</span>
										<span class="portrait">iPhone 4/4S <i>640 * 960</i> 竖屏</span>
									</div>
									<div class="ui-media ui-media-iphone-5th">
										<span class="landscape">iPhone 5 Retina <i>640 * 1136</i> 横屏</span>
										<span class="portrait">iPhone 5 Retina <i>640 * 1136</i> 竖屏</span>
									</div>

									<!-- iPad Media Queries -->
									<div class="ui-media ui-media-ipad">
										<span class="landscape">ipad 1 & 2 & mini 横屏</span>
										<span class="portrait">ipad 1 & 2 & mini 竖屏</span>
									</div>
									<div class="ui-media ui-media-ipad-retina">
										<span class="landscape">ipad 3 & 4 横屏</span>
										<span class="portrait">ipad 3 & 4 竖屏</span>
									</div>
									<!-- android -->
									<!-- lower DPI -->
									<div class="ui-media ui-media-android ui-media-android-small">
										<ul class="landscape">
											<li><span class="label">当前系统：</span> Android</li>
											<li><span class="label">屏幕状态：</span> 横屏</li>
											<li><span class="label">DPI等级：</span> <i class="rank">中 (1.0 ~ 1.5)</i></li>
											<li><span class="label">分辨率：</span><i class="resolution">320 * 480</i></li>
											<li><span class="label">渲染尺寸(宽)：</span> <i class="output"></i><li>
										</ul>
										<ul class="portrait">
											<li><span class="label">当前系统：</span> Android</li>
											<li><span class="label">屏幕状态：</span> 竖屏</li>
											<li><span class="label">DPI等级：</span> <i class="rank">中 (1.0 ~ 1.5)</i></li>
											<li><span class="label">分辨率：</span> <i class="resolution">320 * 480</i></li>
											<li><span class="label">渲染尺寸（宽）：</span> <i class="output"></i><li>
										</ul>
									</div>
									<!-- middle DPI -->
									<div class="ui-media ui-media-android ui-media-android-middle">
										<ul class="landscape">
											<li><span class="label">当前系统：</span> Android</li>
											<li><span class="label">屏幕状态：</span> 横屏</li>
											<li><span class="label">DPI等级：</span> <i class="rank">中 (1.5 ~ 2)</i></li>
											<li><span class="label">分辨率：</span><i class="resolution">480 * 800</i></li>
											<li><span class="label">渲染尺寸(宽)：</span> <i class="output"></i><li>
										</ul>
										<ul class="portrait">
											<li><span class="label">当前系统：</span> Android</li>
											<li><span class="label">屏幕状态：</span> 竖屏</li>
											<li><span class="label">DPI等级：</span> <i class="rank">中 (1.5 ~ 2)</i></li>
											<li><span class="label">分辨率：</span> <i class="resolution">480 * 800</i></li>
											<li><span class="label">渲染尺寸（宽）：</span> <i class="output"></i><li>
										</ul>
									</div>
									<!-- hight DPI -->
									<div class="ui-media ui-media-android ui-media-android-hight">
										<ul class="landscape">
											<li><span class="label">当前系统：</span> Android</li>
											<li><span class="label">屏幕状态：</span> 横屏</li>
											<li><span class="label">DPI等级：</span> <i class="rank">高 (2+)</i></li>
											<li><span class="label">分辨率：</span><i class="resolution">720 * 1280</i></li>
											<li><span class="label">渲染尺寸(宽)：</span> <i class="output"></i><li>
										</ul>
										<ul class="portrait">
											<li><span class="label">当前系统：</span> Android</li>
											<li><span class="label">屏幕状态：</span> 竖屏</li>
											<li><span class="label">DPI等级：</span> <i class="rank">高 (2+)</i></li>
											<li><span class="label">分辨率：</span> <i class="resolution">720 * 1280</i></li>
											<li><span class="label">渲染尺寸（宽）：</span> <i class="output"></i><li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</section>
</body>
</html>