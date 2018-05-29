<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<script type="text/javascript">var token = '<?php echo $token;?>';</script>
<title>报名有礼</title>
<?php include "../../_inc.php" ?>
<style type="text/css">
	*{-webkit-tap-highlight-color:rgba(0,0,0,0);}
	html{font-size:20px; -webkit-text-size-adjust:none;}
	@media all and (min-width:359px){.uc-hack{font-size:22.5px;}}
	@media all and (min-width:359px) and (-webkit-min-device-pixel-ratio:1.5){html{font-size:22.5px;}}
	body,div,p,form,input,h1,h2,h3,h4,h5,ul,ol,li,dl,dt,dd,table,tbody,tr,td,textarea,img,iframe,figure{margin:0; padding:0; list-style:none; vertical-align:middle;}
	body{font:14px/1.5 "\5FAE\8F6F\96C5\9ED1",Helvetica,Arial; color:#000;-webkit-text-size-adjust:none;}
	img{border:0; -webkit-touch-callout:none;}
	a,*[onclick]{-webkit-tap-highlight-color:rgba(0,0,0,0); text-decoration:none; color:#000;}
	a:active,a:hover{outline:none;}
	h1,h2,h3,h4,h5,h6 {font-size:100%; font-weight:500;}
	body{background: #e8e8e8;}
	#events{width:16rem; margin:10px auto 20px;}
	.evt-banner{width:15rem; min-height: 8.1rem; position: relative; margin:0 auto; background: rgba(0,0,0,.5); border:1px solid #c6c6c6;}
	.evt-banner img{width:15rem;}
	.evt-info{color:#fff; position: absolute; top: 64px; padding: 0 15px;}
	.evt-info h1{font-size:18px; color:rgba(255,255,255,.8);}
	.evt-info .evt-date{margin-top:10px; text-shadow:0 0 1px #fff;}
	.evt-info .evt-desc span{color:#f06254;}
	.evt-info .evt-note{color:#f06254; text-shadow:1px 0 1px #fff;}
	.evt-block {width:15rem; margin:0 auto; color:#888;}
	.evt-block ul{margin: 10px 0 0 0; padding:0 5px;}
	.evt-block ul:first-child{border-bottom:1px dashed #c7c7c7; padding-bottom:10px;}
	.evt-block li strong{color:#f06254; font-weight: normal;}
	.evt-block .evt-title{text-align: center; border-top:1px dashed #c7c7c7; margin-top: .5rem;}
	.evt-block .evt-title h2{
		display: inline-block;
		background: url('<?php echo $staticResPath; ?>/3g/events/assets/img/events-e7-01.png') no-repeat 0 center;
		background-size: 20px auto;
		color:#f06254; font-size:18px; text-shadow: 1px 1px #fff; padding: 10px 0 10px 25px;
	}
	/*steps*/
	.evt-block .evt-steps{
		display: -webkit-box; margin:.2rem .5rem .5rem;
	}

	.evt-block .evt-steps p{
		position: relative;
		background-size: 34px auto; padding-left:40px; color:#7d7d7d;
		-webkit-box-flex:1;
		font-size:12px;
	}

	.evt-block .evt-steps span{
		font-size: .6rem;
		text-shadow: 0 1px rgba(255,255,255,.8);
	}

	.evt-block .evt-step1{
		background: url('<?php echo $staticResPath; ?>/3g/events/assets/img/events-e7-02.png') no-repeat 0 center;
		margin-right: 10px;
	}
	.evt-block .evt-step1::before,
	.evt-block .evt-step2::before{
		content: ''; display: block; position: absolute; right: 0; top: 40%;
		width: 4px; height: 4px; border: 1px solid #999; border-width:1px 1px 0 0;
		-webkit-transform:rotate(45deg);
	}
	.evt-block .evt-step2{
		background: url('<?php echo $staticResPath; ?>/3g/events/assets/img/events-e7-03.png') no-repeat 0 center;
		margin-right: 10px;
	}
	.evt-block .evt-step3{
		background: url('<?php echo $staticResPath; ?>/3g/events/assets/img/events-e7-04.png') no-repeat 0 center;
	}


	/*form*/
	.evt-block form{background: #e3e3e3; border-radius: 3px; border: 1px solid #d7d7d7;}
	.evt-block .inp-item{margin:.5rem auto; text-align: center;}
	.evt-block .inp-username{
		background:#fff url('<?php echo $staticResPath; ?>/3g/events/assets/img/events-e7-05.png') no-repeat 0 center;
	}
	.evt-block .inp-phone{
		background:#fff url('<?php echo $staticResPath; ?>/3g/events/assets/img/events-e7-06.png') no-repeat 0 center;
	}
	.evt-block .inp-text{
		width: 11.9rem; height: 1.9rem; padding:0 0 0 2.4rem; border:1px solid #c1c1c1; border-radius: 3px; outline: 0;
		background-size: 2.1rem 1.9rem;
	}

	.evt-block .inp-btn{ 
		background: #7fb12f; color:#fff; text-shadow:1px 0 #fff;
		border: none; box-shadow: inset 0 1px rgba(255,255,255,.5);
		width:100%; height: 100%; border-radius:18px; outline:0;
	}
	.evt-block .inp-btn:hover,.evt-block .inp-btn:active{
		opacity: 0.8;
	}
	.evt-block .inp-wrap{
		width:60%; margin:0 auto; border:1px solid #6d814c; border-color:#6d814c #7d9656 #84956b #7d9656;
		
		height: 36px; border-radius:18px;
		box-sizing:border-box;
	}

	.alert-box{
		width:15.6rem; margin:0 auto; position: fixed; left:.2rem; right: .2rem; bottom: .5rem;
		background:rgba(255,255,255,.9); box-shadow: 0 0 3px rgba(0,0,0,.5); border-radius: 3px;
	}
	.alert-box .alert-title{
		padding: .5rem; color:#fa8b00; border-bottom:1px solid #faa437;
	}

	.alert-box .alert-content{
		padding:.5rem; color:#222;
	}
	.alert-box .alert-button{
		padding:.5rem; border-top: 1px solid #ececec;
		text-align: center; cursor: pointer;
	}

	.alert-error{
		position: fixed; left:.2rem; right: .2rem; bottom: .5rem;
		text-align: center;
	}
	.alert-error span{
		padding: .5rem 1rem; color:#fff; border-radius: 3px;
		background: #3f3f3f; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,.5);
	}

	.none{
		display: none;
	}

</style>
</head>
<body>
<section id="events">
	<div class="evt-banner">
		<img src="<?php echo $staticResPath; ?>/3g/events/pic/2014010701.jpg" alt="大眼E7">
		<div class="evt-info">
			<h1 class="evt-h1">寻找大眼中的欢乐</h1>
			<p class="evt-date">活动时间：<br>2014年1月7日 至 2014年1月12日</p>
			<p class="evt-note">此活动仅限重庆地区.</p>
		</div>
	</div>
	<div class="evt-block">
		<div class="evt-title"><h2>轻松三步，奖品拿到你手软！欧耶</h2></div>
	</div>

	<div class="evt-block">
		<div class="evt-steps">
			<p class="evt-step1">Step1<br><span>报名</span></p>
			<p class="evt-step2">Step2<br><span>获得短信</span></p>
			<p class="evt-step3">Step3<br><span>奖品领取</span></p>
		</div>
	</div>
	
	<div class="evt-block">
		<form action="javascript:void(0)" data-action="<?php echo $PostUrl;?>" method="post">
			<div class="inp-item">
				<input class="inp-text inp-username" type="text" name="username" maxLength="16" placeholder="请输入姓名">
			</div>
			<div class="inp-item">
				<input class="inp-text inp-phone" type="text" name="mobile" maxLength="11" placeholder="请输入的11位有效手机号">
			</div>
			<div class="inp-item">
				<div class="inp-wrap"><input type="button" class="inp-btn" value="立即报名"></div>
			</div>
		</form>
	</div>

	<div class="evt-block">
		<ul>
			<li><strong>【报名规则】</strong>填写姓名和电话，点击“立即报名”按钮，可获得礼品领取短信，凭借短信可去现场，领取礼品（报名短信一旦删除则报名无效，请保留好短信信息）.<li>
			<li>报名时间：2014年1月7日 至 2014年1月10日</li>
		</ul>
		<ul>
			<li><strong>【活动规则】</strong>报名成功后，可参加实体店面“拍照互动分享”活动，更有机会赢得以下奖品：</li>
			<li>大眼欢乐奖（2名）：E7手机一部</li>
			<li>大眼幸运奖（1名）：2000元旅游基金</li>
			<li>实体店面活动时间：2014年1月11日 至 2014年1月12日</li>
		</ul>
	</div>
</section>

<div class="alert-box alert-success none">
	<div class="alert-title">温馨提示</div>
	<div class="alert-content">
		恭喜您，报名成功，请关注您的手机短信，我们即将为您下发奖品信息及领取方式.
	</div>
	<div class="alert-button">确定</div>
</div>

<div class="alert-error none"></div>

<script type="text/javascript" src="<?php echo $staticSysPath;?>/lib/zepto/zepto.js"></script>
<script type="text/javascript">
	$(function(){
		$('#events .inp-btn').on('click', function(){
			var that = $(this);
			var username = $('.inp-username').val(), phone = $('.inp-phone').val();
			if (username == '' || phone == '') {
				//alert('姓名和手机号不能为空！');
				$('.alert-success').hide();
				$('.alert-error').html('<span>姓名和手机号不能为空！</span>');
				$('.alert-error').show();
				setTimeout(function(){
					$('.alert-error').hide();
				},3000)
				return;
			}

			$.ajax({
				url: $('#events form').attr('data-action'),
				type: 'post',
				data: {name: username, mobile: phone, token: token},
				dataType: 'json',
				success: function(data){
					if (data.success) {
						// success
						$('.alert-success').show();
						$('.alert-error').hide();
					} else {
						// error
						$('.alert-success').hide();
						$('.alert-error').html('<span>'+data.msg+'</span>');
						$('.alert-error').show();
						setTimeout(function(){
							$('.alert-error').hide();
						},3000)
					}
				}
			});
		});

		$('.alert-button').on('click', function(){
			$('.alert-success').hide();
		});
	});
</script>
</body>
</html>