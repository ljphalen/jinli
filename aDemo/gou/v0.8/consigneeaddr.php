<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>金立购—触屏版</title>
	<?php include '_inc.php';?>
</head>
<body>
	<div id="page">
		<header class="hd">
			<h2>收货人信息</h2>
			<div class="back-page">
				<a href="index.php"><img src="<?php echo $appPic;?>/btn_backPage.png" alt="" /></a>
			</div>
		</header>
		
		<article class="ac">
			<div class="form-box long-label">
				<form action="">
					<ul>
						<li>
							<label for="">收货人：</label>
							<input type="text" name="" value="" />
						</li>
						<li>
							<label for="">手机号码：</label>
							<input type="text" name="" value="" />
						</li>
                        <li>
							<label for="">固定电话：</label>
							<input type="text" name="" value="" />
						</li>
                        <li class="J_areaWrap">
							<label for="">所在地区：</label>
                            省 <span class="select">
								<select name="" id="J_province"></select>
	                            <span></span>
                            </span><br/>
							市  <span class="select">
								<select name="" id="J_city"></select>
	                            <span></span>
                            </span><br/>
                            区  <span class="select">
								<select name="" id="J_county"></select>
	                            <span></span>
                            </span>
						</li>
                        <li>
							<label for="">详细地址：</label>
							<input type="text" name="" value="" />
						</li>
                        <li>
							<label for="">邮编：</label>
							<input type="text" name="" value="" />
						</li>
						<li class="btn"><button>保存</button></li>
					</ul>
				</form>
			</div>
		</article>
	</div>
</body>
</html>