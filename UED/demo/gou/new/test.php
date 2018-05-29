<!doctype html>
<?php include '_cfg.php';?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<?php include '_inc.php';?>
	<style>
	.color-box{height:20px; margin-top:10px;}
	.red{background:red;} .green{background:green;}
	@media all and (orientation:portrait){.color-box{background:red;}}
	@media all and (orientation:landscape){.color-box{background:green;}}
	/* @media all and (aspect-ratio:16/9){.color-box{background:blue;}} */
	@media all and (max-device-width:720px){.color-box{background:blue;}}

	.wbox{display:-webkit-flex; width:720px;}
	.wbox li, .wbox .item{flex:1; overflow:hidden;}
	.wbox span{display:block; padding:5px; background:#ccc; margin:1px;}
	</style>
	<script>
	//alert(window.devicePixelRatio);
		/*function savetxt(fileURL){
	        var fileURL=window.open (fileURL,"_blank","height=0,width=0,toolbar=no,menubar=no,scrollbars=no,resizable=on,location=no,status=no");
	        fileURL.document.execCommand("SaveAs");
	        fileURL.window.close();
	        fileURL.close();
	    }*/
		//ICAT.include('jQuery', function($){
			/*$('#loadbtn').click(function(){
				var url = this.getAttribute('data-loadurl'),
					node = document.createElement('iframe');
				node.style.display = 'none';
				document.body.appendChild(node);
				setTimeout(function(){node.src = url;}, 1);
				//$.ajax({url:url, dataType:'jsonp'});
			});*/
			/*$('.J_loadBtn').click(function(){
				alert('click once...')
			});*/
		//});
	</script>
</head>
<body>
	<div class="color-box" style="width:18rem;"></div>
	<div class="color-box" style="width:640px;"></div>
	<iframe name="test" src="" frameborder="0" style="display:none;"></iframe>
	<span id="loadbtn" data-loadurl="http://game.gionee.com//index/tj?id=117&type=GAME&_url=http%3A%2F%2Fgamedl.gionee.com%2Fapps%2Fgames%2Fnew%2FGN_GameHall1_1-N01005.apk&t_bi=_2869957670">安装</span>
	<a class="J_loadBtn" style="display:block; margin-top:50px;" target="test" href="http://game.gionee.com//index/tj?id=117&type=GAME&_url=http%3A%2F%2Fgamedl.gionee.com%2Fapps%2Fgames%2Fnew%2FGN_GameHall1_1-N01005.apk&t_bi=_2869957670">安装2</a>

	<ul class="wbox">
		<li><span>发大水的飞洒</span></li>
		<li><span>说的方法</span></li>
		<li><span>而为惹我撒旦法温热污染</span></li>
		<li><span>发生的</span></li>
		<li><span>额外热特特特热</span></li>
	</ul>
	<div class="wbox">
		<div class="item"><span>发大水的飞洒</span></div>
		<div class="item"><span>说的方法</span></div>
		<div class="item"><span>而为惹我撒旦法温热污染</span></div>
		<div class="item"><span>发生的</span></div>
		<div class="item"><span>额外热特特特热</span></div>
	</div>
	<script>
		//ICAT.include('jQuery', function($){
			/*$('body').on('click', '.J_loadBtn', function(){
				alert('click 111...');
			});*/
		//});
		document.querySelector('.J_loadBtn').addEventListener('click', function(){
			alert('000111...')
		}, false);
	</script>
</body>
</html>