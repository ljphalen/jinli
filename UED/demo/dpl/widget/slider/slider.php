<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>Gionee H5 R&D DPL Module</title>
	<?php $moduleName = "dpl"; include '../../_inc.php';?>
</head>
<body>
<section id="wrapper">
	<header>
		<div class="lookit-toolbar">
			<div class="lookit-toolbar-wrap">
				<div class="lookit-toolbar-left"><a class="lookit-toolbar-backbtn" href="../../index.php">返回</a></div>
				<div class="lookit-toolbar-title">幻灯片轮播</div>
				<div class="lookit-toolbar-right"></div>
			</div>
		</div>
	</header>
	<section id="content">
		<div class="lookit-modules">
			<div class="lookit-module">
				<div class="lookit-module-head">
					<div class="pic"><i class="i_form">S</i></div>
					<div class="txt">
						<h3>Slider version 0.1</h3>
						<p>幻灯片轮播组件。</p>
					</div>
				</div>
				<div class="lookit-module-demo">
					<div class="lookit-module-dom lookit-module-padding">
						<!-- slider -->
						<div class="ui-slider showpic" id="J_full_slider2">
							<div class="ui-slider-wrap">
								<div class="ui-slider-content">
									<ul class="ui-slider-pic">
										<li><a href="###"><img src="<?php echo $appPic;?>/011.jpg" alt=""></a></li>
										<li><a href="###"><img src="<?php echo $appPic;?>/012.jpg" alt=""></a></li>
										<li><a href="###"><img src="<?php echo $appPic;?>/013.jpg" alt=""></a></li>
									</ul>
								</div>
								<div class="ui-slider-handle ui-slider-handle-square">
									<span class="on"></span>
									<span></span>
									<span></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="lookit-module-demo">
					<div class="lookit-module-dom lookit-module-padding">
						<!-- slider -->
						<div class="ui-slider showpic" id="J_full_slider1">
							<div class="ui-slider-wrap">
								<div class="ui-slider-content">
									<ul class="ui-slider-pic">
										<li><a href="###"><img src="<?php echo $appPic;?>/1.jpg" alt=""></a></li>
										<li><a href="###"><img src="<?php echo $appPic;?>/2.jpg" alt=""></a></li>
										<li><a href="###"><img src="<?php echo $appPic;?>/3.jpg" alt=""></a></li>
									</ul>
								</div>
								<div class="ui-slider-handle ui-slider-handle-line">
									<span class="on"></span>
									<span></span>
									<span></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- -->
				<div class="lookit-module-demo">
					<div class="lookit-module-dom lookit-module-padding">
						<div class="ui-slider" id="J_full_slider3">
							<div class="ui-slider-wrap">
								<div class="ui-slider-content">
									<ul class="ui-slider-pic ui-slider-text">
										<li>
											<a href="###"><img src="<?php echo $appPic;?>/1.jpg" ></a>
											<a href="###"><span>习近平欢迎阿巴斯到访</span></a>
										</li>
										<li>
											<a href="###"><img src="<?php echo $appPic;?>/2.jpg" ></a>
											<a href="###"><span>习近平欢迎阿巴斯到访</span></a>
										</li>
										<li>
											<a href="###"><img src="<?php echo $appPic;?>/3.jpg" ></a>
											<a href="###"><span>习近平欢迎阿巴斯到访</span></a>
										</li>
									</ul>
								</div>
								<div class="ui-slider-handle ui-slider-handle-circle">
									<span class="on"></span>
									<span></span>
									<span></span>
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