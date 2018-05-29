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
				<div class="lookit-toolbar-title">返回顶部</div>
				<div class="lookit-toolbar-right"></div>
			</div>
		</div>
	</header>
	<section id="content">
		<div class="lookit-modules">
			<div class="lookit-module">
				<div class="lookit-module-head">
					<div class="pic"><i class="i_form">G</i></div>
					<div class="txt">
						<h3>Gotop version 0.1</h3>
						<p>通用的返回顶部组件。</p>
					</div>
				</div>
				<div class="lookit-module-demo">
					<div class="loolit-module-dom">
						<!-- Gotop -->
						<ul class="ui-list">
							<?php for($i = 0; $i < 40; $i++):?>
							<li class="ui-list-item">row <?php echo $i+1;?></li>
							<?php endfor;?>
						</ul>
						<div class="ui-gotop">&#430;</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</section>
</body>
</html>