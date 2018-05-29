<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title></title>
	<?php include '_inc.php';?>
</head>
<body>
<div id="page">
	<header>
		<div class="ui-toolbar">
			<div class="ui-toolbar-wrap">
				<div class="ui-toolbar-left">
					<a class="ui-toolbar-backbtn" href="step2.php">返回</a>
				</div>
				<div class="ui-toolbar-title"><h1>充值卡充值</h1></div>
			</div>
		</div>
	</header>

	<section class="content">
		<section class="ui-form">
			<form action="###" method="post">
				<div class="ui-form-field">
					<div class="ui-form-select">
						<div><label>充值卡类</label></div>
						<div>
							<select>
								<option value=" 请选择"> 请选择</option>
							</select>
						</div>
					</div>
				</div>
				<!-- 当选择某类卡时显示 START-->
				<div class="ui-form-field">
					<div class="ui-form-card">
						<div><label>点卡面额</label></div>
						<div><input type="text" name="number" autocomplete="off" /></div>
					</div>
				</div>
				<!-- 当选择某类卡时显示 END-->
				<div class="ui-form-field">
					<div class="ui-form-select">
						<div><label>点卡面额</label></div>
						<div>
							<select>
								<option value=" 请选择正确的卡面金额"> 请选择正确的卡面金额</option>
							</select>
						</div>
					</div>
				</div>
				<div class="ui-form-field" style="margin:1.5rem 0;">充值的金币：<em class="zt-red">9.5金币</em>（费率5%）</div>
				
				<div class="ui-msg-error"><p>冲值金额不正确或金额超过1000元，请重新输入</p></div>

				<div class="ui-form-textWrap">
					<div class="ui-form-field ui-form-text">
						<div class="ui-form-input">
							<div><label for="">卡号：</label></div>
							<div><input type="text" name="number" autocomplete="off" maxLength="17" placeholder="17位数字" /></div>
						</div>
					</div>
					<div class="ui-form-field ui-form-text">
						<div class="ui-form-input">
							<div><label for="">密码：</label></div>
							<div><input type="password" name="pass" autocomplete="off" maxLength="21" placeholder="8-21位数字" /></div>
						</div>
					</div>
				</div>
				<div class="ui-form-field">
					<div class="ui-tips-box">
						<h2>温馨提示：</h2>
						<p>
							1.请务必保持卡面金额填写正确，否则。。。<br />
							2.费率由易宝收取，充值问题请联系易宝客服<br />
							422-4454-252
						</p>
					</div>
				</div>
				<div class="button mrt20">
					<input type="submit" name="submit" class="btn gray" value="确定充值" />
				</div>
			</form>
		</section>
	</section>
	
	<footer class="ft-smallpage"><p><a href="tel:400-779-6666">客服热线：400-779-6666</a></p></footer>
</div>
</body>
</html>