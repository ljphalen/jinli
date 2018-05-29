<!DOCTYPE HTML>
<!-- manifest="dpl.appcache" -->
<html>
<head>
	<meta charset="utf-8">
	<title>Gionee H5 R&D DPL Module</title>
	<!-- 指定当前应用模块名称 START-->
	<?php $moduleName = "dpl"; $webTitle="DPL 模块";?>
	<!-- 指定当前应用模块名称 END-->
	<?php include '_inc.php';?>
</head>
<body>
<section id="wrapper">
	<header>
		<div class="lookit-toolbar">
			<div class="lookit-toolbar-wrap">
				<div class="lookit-toolbar-left">
					<a class="lookit-toolbar-backbtn lookit-toolbar-backbtn-home" href="../index.php">首页</a>
				</div>
				<!-- <div class="lookit-toolbar-title">前端通用样式库</div> -->
				<div class="lookit-toolbar-right"></div>
			</div>
		</div>
	</header>
	<section id="content">
		<section class="module">
			<ul class="lookit-demo-list">
				<li>
					<a href="media.php">
						<div class="pic"><i class="i_slider">S</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Media Queries</h3>
						<p>设备信息检测</p>
					</a>
				</li>
				<li>
					<a href="widget/slider/slider.php">
						<div class="pic"><i class="i_slider">S</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Slider</h3>
						<p>图片轮播</p>
					</a>
				</li>
				<li>
					<a href="widget/box/box.php">
						<div class="pic"><i class="i_form">B</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Box</h3>
						<p>区块</p>
					</a>
				</li>
				<li>
					<a href="widget/button/button.php">
						<div class="pic"><i class="i_button">B</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Button</h3>
						<p>按钮</p>
					</a>
				</li>
				<!--<li>
					<a href="widget/Button-dropdown/dropdown.php">
						<div class="pic"><i class="i_form">B</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Button-dropdown</h3>
						<p>按钮菜单</p>
					</a>
				</li>-->
				<!--<li>
					<a href="widget/dropmenu/dropmenu.php">
						<div class="pic"><i class="i_form">D</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Dropmenu</h3>
						<p>下拉菜单</p>
					</a>
				</li>-->
				<li>
					<a href="widget/form/form.php">
						<div class="pic"><i class="i_form">F</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Form</h3>
						<p>HTML5 表单</p>
					</a>
				</li>
				<li>
					<a href="widget/gotop/gotop.php">
						<div class="pic"><i class="i_gotop">G</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Gotop</h3>
						<p>返回顶部</p>
					</a>
				</li>
				<li>
					<a href="widget/list/list.php">
						<div class="pic"><i class="i_form">L</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>List</h3>
						<p>列表</p>
					</a>
				</li>
				<li>
					<a href="widget/loading/loading.php">
						<div class="pic"><i class="i_loading">L</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Loading</h3>
						<p>加载...</p>
					</a>
				</li>
				<li>
					<a href="widget/navigator/navigator.php">
						<div class="pic"><i class="i_nav">N</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>navigator</h3>
						<p>导航栏</p>
					</a>
				</li>
				<!--<li>
					<a href="widget/poptips/poptips.php">
						<div class="pic"><i class="i_pop">P</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Poptips</h3>
						<p>弹窗提示</p>
					</a>
				</li>-->
				<li>
					<a href="widget/refresh/refresh.php">
						<div class="pic"><i class="i_refresh">R</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Refresh</h3>
						<p>加载更多</p>
					</a>
				</li>
				<li>
					<a href="widget/toolbar/toolbar.php">
						<div class="pic"><i class="i_toolbar">T</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>ToolBar</h3>
						<p>工具栏</p>
					</a>
				</li>
				<li>
					<a href="widget/tabs/tabs.php">
						<div class="pic"><i class="i_tab">T</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Tabs</h3>
						<p>选项卡</p>
					</a>
				</li>
				<!--<li>
					<a href="widget/tipbox/tipbox.php">
						<div class="pic"><i class="i_tipbox">T</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Tipbox</h3>
						<p>提示框</p>
					</a>
				</li>-->
				<!--<li>
					<a href="widget/tiptext/tiptext.php">
						<div class="pic"><i class="i_tiptext">T</i><img src="<?php echo $appPic;?>/blank.gif" /></div>
						<h3>Tiptext</h3>
						<p>提示文本</p>
					</a>
				</li>-->
			</ul>
		</section>
	</section>
</section>
</body>
</html>