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
					<a class="ui-toolbar-backbtn" href="index.php">返回</a>
				</div>
				<div class="ui-toolbar-title"><h1>手机充值卡充值</h1></div>
			</div>
		</div>
	</header>
	<section>
		<form action="###" method="post">	
		<div class="phone-in">
			<div class="type">
				<ul onclick="var e=event||window.event;
				var target=e.target||e.srcElement;
				for(var i=0;i<this.children.length;i++)
					{
						this.children[i].className='';
						if(target==this.children[i])
							{
								this.children[i].className='selected';
							}
					}">
					<li>移动</li>
					<li class="selected">联通</li>
					<li>电信</li>
				</ul>
			</div>
			<div class="in-list">
				<ul>
					<li class="selected">10元</li>
					<li>20元</li>
					<li>50元</li>
					<li >100元</li>
					<li>200元</li>
					<li>500元</li>
				</ul>
			</div>
			<div class="tip">充值<span >20元=19金币</span>(费率5%)</div>
			<div class="info-input">
				<p>
				<span>卡号：</span>
				<input type="text" placeholder="17位数字" />
				</p>
				<p>
				<span>密码：</span>
				<input type="text" placeholder="8-12位数字" />
			</p>
			</div>
			<div class="tip_1"><span >1.卡面金额务必正确，否则会导致资金损失。</span>
				<a href="index.php">支持卡类型详细</a>
			</div>
			<div class="back">
				<input type="submit" name="submit" class="btn fixed" value="立即充值" onclick="var tip=document.getElementsByClassName('J_tipBox')[0];tip.style.display='inline';setTimeout(function(){tip.style.display='none';},3000);return false;" />
		</div>
		</div>
	</form>
	</section>
	<div class="J_tipBox tip-box">
		<p>请输入正确的卡号或密码</p>
	</div>
</div>
</body>
</html>