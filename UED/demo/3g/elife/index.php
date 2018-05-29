<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>Elife-产品服务页</title>
<?php include "../_inc.php"; ?>
<link id="linkMain" rel="stylesheet" type="text/css" href="<?php echo $staticPath;?>/apps/3g/elife/assets/css/elife.3g.source.css">
<script src="<?php echo $staticPath;?>/sys/icat/1.1.5/icat.js"></script>
<script src="<?php echo $staticPath;?>/apps/3g/elife/assets/js/elife_3g.source.js"></script>
</head>

<body>
<div id="wrapper">
	<header>
		<div class="menu-panel">
            <h2 class="menu-panel-logo"><img src="<?php echo $staticPath;?>/apps/3g/elife/pic/top.jpg" /></h2>
            <div class="menu-panel-drapbox">
                <div class="drapdown J_drapdown"><span class="label">更多产品</span><i class="icon"></i></span></div>
                <ul>
                    <li><a href="#">E3</a></li>
                    <li><a href="#">E5</a></li>
                    <li><a href="#">E6</a></li>
                </ul>
            </div>
		</div>
	</header>
	<div class="container fullscreen">
		<section class="content">
			<!-- Banner轮播区域 -->
			<div class="module">
				<div class="ui-slider showpic" id="J_full_slider">
					<div class="ui-slider-wrap">
						<div class="ui-slider-content">
							<ul class="ui-slider-pic">
								<li>
									<a href="###">
										<span>自然精彩</span>
										<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/banner1.jpg" >
									</a>
								</li>
								<li>
									<a href="###">
										<span>自然精彩</span>
										<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/banner2.jpg" >
									</a>
								</li>
							</ul>
						</div>
						<div class="ui-slider-handle">
							<?php for($i = 0; $i < 2; $i++): ?>
							<span class="<?php if($i == 0) echo 'on';?>"></span>
							<?php endfor; ?>
						</div>
					</div>
				</div>
			</div>
			<!-- 内容展示区域 -->
			<div class="module ui-tab-wrap">
				<div class="ui-tab-title">
					<ul>
						<li><a href="#param" class="sel">参数</a></li>
						<li><a href="#show">外观</a></li>
					</ul>
				</div>
				<div class="ui-tab-content">
					<div class="ui-tb-list">
						<div class="ui-tb-title" style="border:0; text-align:center;">全新E3性能怪兽</div>
						<ul class="ui-tb-ul">
							<li>
								<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/icon-cpu.png" />
								<h3>极速四核</h3>
								释放强劲动能<br />
								多重任务自如切换
							</li>
							<li>
								<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/icon-3g.png" />
								<h3>高速3G网络</h3>
								支持联通WCDMA网络<br />
								畅享极速3G
							</li>
							<li>
								<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/icon-hdr.png" />
								<h3>高动态光照渲染</h3>
								逆光环境下<br />
								亦可拍出好画质
							</li>
							<li>
								<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/icon-dts.png" />
								<h3>DTS专业音效</h3>
								增强型重低音<br />
								3D立体环绕声
							</li>
							<li>
								<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/icon-size.png" />
								<h3>4.7英寸显示屏</h3>
								还原真实色彩<br />
								画面细腻生动
							</li>
							<li>
								<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/icon-camera.png" />
								<h3>高清摄像头</h3>
								200万前置<br />
								800万后置
							</li>
						</ul>
					</div>

					<div class="ui-tb-list">
						<div class="ui-tb-title">规格参数</div>
						<ul class="ui-tb-tr">
							<li><span class="ui-tb-th">操作系统</span><span>Android 4.2</span></li>
							<li><span class="ui-tb-th">支持网络</span><span>GSM 850/900/1800/1900MHz WCDMA 900/2100MHz</span></li>
							<li><span class="ui-tb-th">网络模式</span><span>W+G双卡双待</span></li>
							<li><span class="ui-tb-th">尺 寸</span><span>137.5mm×68.4mm×7.9mm</span></li>
							<li><span class="ui-tb-th">显示屏</span><span>4.7英寸HD(720×1280），电容式触摸屏</span></li>
							<li><span class="ui-tb-th">摄像头</span><span>前置：200万像素/后置：800万像素</span></li>
							<li><span class="ui-tb-th">传感器</span><span>重力传感器、光线感应器、距离传感器、指南针</span></li>
							<li><span class="ui-tb-th">扩展内存</span><span>支持Micro SD卡，最高支持32GB</span></li>
							<li><span class="ui-tb-th">电 池</span><span>1800毫安时</span></li>
							<li><span class="ui-tb-th">配 件</span><span>原装电池、旅行充电器、耳机、数据线、保护膜</span></li>
							<li><span class="ui-tb-th">其 他</span><span>淘宝*、微博*、百度地图*、购物大厅*等</span></li>
							<li><span class="ui-tb-th"></span><span>* 需网络支持</span></li>
						</ul>
					</div>
				</div>
				<!-- 外观 -->
				<div class="ui-show-pic">
					<div class="ui-show-part1">
						<div class="pos"><h2>高清大屏</h2>4.7英寸超大高清显示屏</div>
						<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/show-pic-part1.jpg" />
					</div>
					<div class="ui-show-part2">
						<div class="pos"><h2>多彩超薄</h2>7.9mm超薄机身，采用OGS+全贴合<br />工艺技术，炫彩外壳随心换</div>
						<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/show-pic-part2.jpg" />
					</div>
					<div class="ui-show-part3">
						<div class="pos"><h2>灵动自拍</h2>前置200万像素<br />FHD全高清摄像头<br />内置实时自然美肤特效</div>
						<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/show-pic-part3.jpg" />
					</div>
				</div>
			</div>
		</section>
	</div>
	<footer>
		<p>
			<img src="<?php echo $staticPath ?>/apps/3g/elife/pic/icon-sina.png" width="22" height="19" />
			<a href="http://e.weibo.com/gioneelife">@智能手机e-life</a>
		</p>
	</footer>
</div>
</body>
</html>