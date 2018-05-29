<!DOCTYPE HTML>
<!-- manifest="dpl.appcache" -->
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
				<div class="lookit-toolbar-left">
					<a class="lookit-toolbar-backbtn" href="../../index.php">返回</a>
				</div>
				<div class="lookit-toolbar-title">Form表单</div>
				<div class="lookit-toolbar-right"></div>
			</div>
		</div>
	</header>
	<section id="content">
		<div class="lookit-modules">
			<div class="lookit-module">
				<div class="lookit-module-head">
					<div class="pic"><i class="i_form">F</i></div>
					<div class="txt">
						<h3>Form version 0.1</h3>
						<p>移动端表单独立样式。</p>
					</div>
				</div>
				<div class="lookit-module-demo">
					<div class="lookit-module-dom">
						<div class="module ui-form-wrap">
							<form class="ui-form" name="" action="###" method="post" id="J_xxx_form">
								<!-- input text -->
								<div class="ui-form-item">
									<label for="" class="ui-label">文本框：</label>
									<input class="ui-input ui-input-text" type="text" placeholder="请输入内容" autocomplete="off" />
								</div>
								<!-- input text disable -->
								<div class="ui-form-item">
									<label for="" class="ui-label">文本框：</label>
									<input class="ui-input ui-input-disable" type="text" placeholder="文本框不可用" autocomplete="off" disabled />
								</div>
								<!-- input password -->
								<div class="ui-form-item">
									<label for="" class="ui-label">密码框：</label>
									<input class="ui-input ui-input-pass" type="text" placeholder="请输入密码" />
								</div>
								<!-- input password -->
								<div class="ui-form-item ui-form-item-check">
									<label for="" class="ui-label">验证码：</label>
									<input class="ui-input ui-input-code" type="text" />
									<img src="<?php echo $appPic;?>/checkcode.png" width="40" height="40" />
									<a href="###">看不清，换一张</a>
								</div>
								<!-- input password -->
								<div class="ui-form-item">
									<label for="" class="ui-label">内容框：</label>
									<textarea class="ui-textarea" rows="10" placeholder="默认内容文本"></textarea>
								</div>
								<!-- input select -->
								<div class="ui-form-item">
									<label for="" class="ui-label">下拉框：</label>
									<p class="ui-select">
										<select name="model">
											<option value="0">选择机型</option>
											<option value="GN305">GN305</option>
										</select>
									</p>
								</div>
								<!-- input button -->
								<div class="ui-button-wrap">
									<input class="ui-button ui-button-mlight" type="button" name="submit" value="提交" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</section>
</body>
</html>