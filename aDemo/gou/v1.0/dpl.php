<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>金立购—触屏版</title>
<?php include '_inc.php';?>
</head>

<body>
<div id="page">
	<?php include '_header.php';?>
	<!-- 主体内容 -->
	<div id="content" class="dpl">
		<h3>搜索框：首页、内页搜索框</h3>

		<div class="mod-search">
			<div class="short">
				<form action="#" method="get" name="form-search" id="J_form_search">
					<input type="text" name="keyword" autocomplete="off" placeholder="请输入商品或分类名" class="inp" maxlength="16" />
					<input type="submit" name="search-btn" value="" />
				</form>
			</div>
		</div>
		<br />
		<div class="">
			<div class="mod-search">
				<div class="long">
					<form action="#" method="get" name="form-search" id="J_form_search">
						<input type="text" name="keyword" autocomplete="off" placeholder="请输入商品或分类名" class="inp" />
						<button type="submit"></button>
					</form>
				</div>
			</div>
		</div>
		<div id="dpl-form">
			<h3>表单样式（文字+输入框、单输入框）</h3>
			<section class="mod-form">
				<form action="###" method="post" name="form">
					<div class="field">
						<input type="text" name="username" value="" autocomplete="off" placeholder="会员名" />
					</div>
					<div class="field">
						<input type="text" name="username" value="" autocomplete="off" placeholder="密码" />
					</div>
					<div class="field">
						<label>帐号：</label><input type="text" name="username" value="" autocomplete="off" />
					</div>
					<div class="field">
						<label>单选：</label>
						<input type="radio" name="sex" value="0" /> 男
						<input type="radio" name="sex" value="1" /> 女
					</div>
					<div class="field">
						<label>下拉：</label>
						<select>
							<option>下拉菜单一</option>
							<option>下拉菜单二</option>
							<option>下拉菜单三</option>
						</select>
					</div>
					<div class="field">
						<textarea cols="60" rows="10" placeholder="请输入您的反馈意见（字数500字以内）"></textarea>
					</div>
				</form>
			</section>
		</div>
		<h3>图文详情</h3>
		<h3>橱窗：首页分类、精品导航等</h3>
		<div id="dpl-tags">
			<h3>标签云（随机摆放、横向滑动）：搜索页面</h3>
			<ul class="mod-tags">
				<li><a href="">标签一</a></li>
				<li><a href="">标签二</a></li>
				<li><a href="">标签三</a></li>
				<li><a href="">标签四</a></li>
				<li><a href="">标签五</a></li>
			</ul>
		</div>

		<div id="auto-tips">
			<h3>提示框（自动消失型）</h3>
			<div class="mod-tips J_auto_tips">
				<span>登陆成功！</span>
			</div>
		</div>

		<h3>文字列表-无链接（单行、多行）</h3>
		<ul class="mod-text-list">
			<li><em>我的帐号：18600440212</em></li>
			<li><em>我的积分：</em>
				<dl>
					<dd>可用银币：30元（冻结银币70元）</dd>
					<dd>可用金币：0元 （冻结金币0元）</dd>
				<dl>
			</li>
		</ul>
		<ul class="mod-text-list">
			<li><a href="###" class="r-arrow">积分订单</a></li>
			<li><a href="###" class="r-arrow">积分详情</a></li>
			<li><a href="###" class="r-arrow">心愿清单</a></li>
			<li><a href="###" class="r-arrow">账号设置</a></li>
		</ul>
		
		<div id="dpl-button">
			<h3>button样式</h3>
			<span class="button btn-r-arrow"><a href="###" class="btn orange">黄色按钮</a></span><br />

			<span class="button"><a href="###" class="btn orange">默认按钮</a></span><br />

			<span class="button btn-gr-arrow"><a href="###" class="btn gray">灰色按钮</a></span><br />

			<span class="button"><a href="###" class="btn disable">禁用点击</a></span><br />
		</div>
		
		<h3>瀑布流</h3>
		<div><a href="waterfall.php">waterfall</a></div>
		
		<h3>banner轮播</h3>
		<div class="mod-slide">
			<div class="wrap">
				<div class="pic">
					<?php for($i=0; $i<5; $i++){?>
					<a href="###"><img src="<?php echo $appPic;?>/index-slide-pic.jpg" alt="" /></a>
					<?php }?>
				</div>
			</div>
			<div class="mask"></div>
			<div class="handle">
				<span class="on"></span>
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
	<!--  /主体内容 -->
	<?php include '_footer.php';?>
</div>
</body>
</html>