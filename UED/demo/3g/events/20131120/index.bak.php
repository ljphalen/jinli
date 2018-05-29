<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<title>游戏手柄活动</title>
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
	body{padding:10px; background: #000;}
	#events-box{padding:10px;  background:#fff; -webkit-border-radius:5px;}
	.success{padding:20px 0; text-align: center; color:#3bf808;}
	.form .error{color:red; margin-left:5px;}
	.form .item-field{display:-webkit-box; padding:15px 0;}
	.form .item-span{display:block; -webkit-box-flex:1;}
	.form .item-txt{margin:0 5px; font-style: normal; display: inline-block; width:60px; height: 30px;}
	.form .item-txt img{width:100%;}
	.form .item-blue, .item-orange, .item-black{text-indent: -9999px; vertical-align: top; overflow: hidden; -webkit-border-radius:3px;}
	.form .item-blue{background:#0084e8;}
	.form .item-orange{background:#ef6d21;}
	.form .item-black{background:#020202;}
	.form .item-field textarea{display:block;  width:90%; /*margin:0 auto;*/ height:100px; padding:10px; border:1px solid #666; -webkit-border-radius:3px;}
	.form .buttons input{
		border:none; margin: 0 auto; background:#fff; color:#f00; font-size: 16px; font-weight: bold; text-align:center; width: 150px; padding:12px 0; -webkit-border-radius:5px;
		-webkit-box-shadow: 3px 5px 0 #bf0000;
	}
	.form .buttons{margin:15px 0; text-align: center;}
	.form .item-input{padding: 10px 0;}
	.form .item-input label{display: inline-block; width:60px; }
	.form .item-input input{border:1px solid #666; padding:5px 2px;  -webkit-border-radius:3px;}
</style>
</head>
<body>
<div id="wrapper">
	<form class="form" action="" method="">
		<div id="events-box">
			<h3>1.我选择那种颜色<span id="item-tip1" class="error"><span></h3>
			<div class="item-field">
				<span class="item-span"><input id="lb-tp10" type="radio" name="topic1" value="0" /><label for="lb-tp10" class="item-txt item-blue">蓝色</label></span>
				<span class="item-span"><input id="lb-tp11" type="radio" name="topic1" value="1" /><label for="lb-tp11" class="item-txt item-orange">橙色</label></span>
				<span class="item-span"><input id="lb-tp12" type="radio" name="topic1" value="2" /><label for="lb-tp12" class="item-txt item-black">黑色</label></span>
			</div>
			<h3>2.我选择怎样的手感<span id="item-tip2" class="error"><span></h3>
			<div class="item-field">
				<span class="item-span"><input id="lb-tp20" type="radio" name="topic2" value="0" /><label for="lb-tp20" class="item-txt">平滑</label></span>
				<span class="item-span"><input id="lb-tp21" type="radio" name="topic2" value="1" /><label for="lb-tp21" class="item-txt">磨砂</label></span>
				<span class="item-span"><input id="lb-tp22" type="radio" name="topic2" value="2" /><label for="lb-tp22" class="item-txt">凹凸</label></span>
			</div>
			<h3>3.我选择那种外观<span id="item-tip3" class="error"><span></h3>
			<div class="item-field">
				<span class="item-span"><input id="lb-tp30" type="radio" name="topic3" value="0" /><label for="lb-tp30" class="item-txt"><img src="http://18.8.10.194:8899/apps/3g/events/pic/20131120_pic001.jpg" /></label></span>
				<span class="item-span"><input id="lb-tp31" type="radio" name="topic3" value="1" /><label for="lb-tp31" class="item-txt"><img src="http://18.8.10.194:8899/apps/3g/events/pic/20131120_pic002.jpg" /></label></span>
				<span class="item-span"><input id="lb-tp32" type="radio" name="topic3" value="2" /><label for="lb-tp32" class="item-txt"><img src="http://18.8.10.194:8899/apps/3g/events/pic/20131120_pic003.jpg" /></label></span>
			</div>
			<h3>4.我选择什么价位<span id="item-tip4" class="error"><span></h3>
			<div class="item-field">
				<span class="item-span"><input id="lb-tp40" type="radio" name="topic4" value="0" /><label for="lb-tp40" class="item-txt">129</label></span>
				<span class="item-span"><input id="lb-tp41" type="radio" name="topic4" value="1" /><label for="lb-tp41" class="item-txt">189</label></span> 
				<span class="item-span"><input id="lb-tp42" type="radio" name="topic4" value="2" /><label for="lb-tp42" class="item-txt">1299</label></span>
			</div>
			<div class="item-field">
				<textarea name="comment" id="comment" maxLength="255" placeholder="有更好的想法......"></textarea>
			</div>
			<p>(随机抽取20名幸运用户，手柄送你家!)</p>
			<!-- 用户信息 -->
			<div class="item-field item-input">
				<label>姓名</label>
				<span class="item-span"><input id="lb-tp50" type="text" name="name" value="" /><span id="item-tip8" class="error"><span></span>
			</div>
			<div class="item-field item-input">
				<label>手机号</label>
				<span class="item-span"><input id="lb-tp51" type="text" maxLength="11"; name="phone" value="" /><span id="item-tip9" class="error"><span></span>
			</div>
			<div class="item-field item-input">
				<label>地址</label>
				<span class="item-span"><input id="lb-tp52" type="text" name="address" value="" /><span id="item-tip10" class="error"><span></span>
			</div>
		</div>
		<div id="events-buttons" class="buttons">
			<input id="submit" type="button" name="submit" value="提交" />
		</div>
	</form>
</div>
<script type="text/javascript">
	window.onload = function(){
		function checked(name){
			var topic1 = document.getElementsByName(name);
			for (var i=0;i<topic1.length;i++) {
				if(topic1[i].checked == true) {
					return topic1[i].value;
				}
			}
		}

		function showTips(id, msg){
			$(id).innerHTML = msg;
			return msg ? false : true;
		}

		function $(id){
			return document.getElementById(id);
		}

		var dataAjax = new XMLHttpRequest();
		dataAjax.callbak = function(callback){
			dataAjax.onreadystatechange = function(){
				if (dataAjax.readyState == 4 && dataAjax.status == 200) {
					callback(dataAjax.responseText);
				}
			}
		};

		dataAjax.post = function(url, data, callback) {
			dataAjax.callbak(callback);
			dataAjax.open('post', url, true);
			dataAjax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			dataAjax.send(data);
		}

		$('submit').onclick = function(){
			var arr = [] , st = '', su = '';
			var smsg     = $('comment').value,
				name     = $('lb-tp50').value,
				phone    = $('lb-tp51').value,
				address  = $('lb-tp52').value;

			arr[0] = checked('topic1') === undefined ? showTips('item-tip1','(＊必填项)') : showTips('item-tip1','');
			arr[1] = checked('topic2') === undefined ? showTips('item-tip2','(＊必填项)') : showTips('item-tip2','');
			arr[2] = checked('topic3') === undefined ? showTips('item-tip3','(＊必填项)') : showTips('item-tip3','');
			arr[3] = checked('topic4') === undefined ? showTips('item-tip4','(＊必填项)') : showTips('item-tip4','');
			arr[4] = name == '' ? showTips('item-tip8','(＊必填项)') : showTips('item-tip8','');
			arr[5] = phone == '' ? showTips('item-tip9','(＊必填项)') : showTips('item-tip9','');
			arr[6] = address == '' ? showTips('item-tip10','(＊必填项)') : showTips('item-tip10','');

			if (arr.indexOf(false) > -1) return;
			//选题
			st = 't[1]=' + checked('topic1') + '&t[2]=' + checked('topic2') + '&t[3]=' + checked('topic3') + '&t[4]=' + checked('topic4') + '&t[5]=' + smsg;
			//用户信息
			su = '&name=' + name + '&phone=' + phone + '&address=' + address; 
			dataAjax.post('api.php', st + su, function(data){
				var res = JSON.parse(data);

				if(res.err === 0){
					$('events-box').innerHTML = '<div class="success">提交成功！谢谢您的参与。</div>';
					$('events-buttons').style.display = 'none';
					if (res.to_href){
						setTimeout(function(){
							location.href = res.to_href;
						},5000);
					}
				} else if (res.err === 1) {
					showTips('item-tip9','(手机号格式错误！)');
				} else if (res.err === 2) {
					showTips('item-tip9','(该手机号己存在！)');
				}
			});
		};

	}
</script>
</body>
</html>