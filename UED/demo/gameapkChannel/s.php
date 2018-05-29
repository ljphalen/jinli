<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>向上无缝滚动的图片效果</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<link rel="stylesheet" type="text/css" href="http://18.8.2.98:8899/sys/reset/phonecore.source.css?t=176">
<link rel="stylesheet" href="http://18.8.2.98:8899/apps/game/apk/assets/css/game.source.css?t=176">
<script type="text/javascript" src="http://18.8.2.98:8899/sys/lib/jquery/jquery.source.js"></script>
<style type="text/css">
#ad{
	position:absolute;
	width:120px;
	height:89px;
	border:1px solid #000;
	overflow:hidden;
}
#ad ul{
	position:absolute;
	list-style-type:none;
	margin:0;
	padding:0;
}
#ad img{
	width:120px;height: 90px;
}
</style>
<script language="javascript">
var ad = {
	o:null,      // 存放滚动的UL
	cloneImg:null,  //克隆UL的第一个图片
	adY:0, //滚动值
	distan:0, //每个图片的高度
	init:function(obj){
		if(!obj.style.top){
		obj.style.top = '0px';
		}
		this.cloneImg = obj.firstChild.cloneNode(true); //克隆第一个节点
		if(this.cloneImg.nodeType == 3) this.cloneImg = obj.firstChild.nextSibling.cloneNode(true);	//除IE外第一个节点为文本节点，让克隆节点还是指向第一个元素
		obj.appendChild(this.cloneImg); //让克隆的节点放入最后
		this.adY = parseInt(obj.style.top);
		this.o = obj;
		this.distan = this.cloneImg.offsetHeight;
		this.moveCtrl();
	},
	moveCtrl:function(){
		if(Math.abs(this.adY) == this.o.offsetHeight - this.distan) this.adY = 0;//到达底部滚动跳回最上面
		if(Math.abs(this.adY)%this.distan==0){
			setTimeout('ad.moveCtrl()',8000);//图片停留延迟
		} else {
			setTimeout('ad.moveCtrl()',10);//运动循环
		}
		--this.adY;
		ad.o.style.top = this.adY + 'px';
	}
}
window.onload = function(){
	var obj = document.getElementById('adul');
	ad.init(obj);	
}
</script>
</head>
<body>
<div id="ad">
  <ul id="adul">
    <li><img src="images/1.jpg"></li>
    <li><img src="images/2.jpg"></li>
  </ul>
</div>


<div class="channel-list">
	<ul>
		<li>
			<a data-infpage="最新,http://game.3gtest.gionee.com/client/rank/index?flag=1">
				<div class="img-box">
					<div class="panel">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/092030.png" alt="">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/193805.png" alt="">
					</div>
				</div>
			</a>
		</li>
		<li>
			<a data-infpage="最新,http://game.3gtest.gionee.com/client/rank/index?flag=1">
				<div class="img-box">
					<div class="panel">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/092030.png" alt="">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/193805.png" alt="">
					</div>
				</div>
			</a>
		</li>
		<li>
			<a data-infpage="最新,http://game.3gtest.gionee.com/client/rank/index?flag=1">
				<div class="img-box">
					<div class="panel">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/092030.png" alt="">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/193805.png" alt="">
					</div>
				</div>
			</a>
		</li>
		<li>
			<a data-infpage="最新,http://game.3gtest.gionee.com/client/rank/index?flag=1">
				<div class="img-box">
					<div class="panel">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/092030.png" alt="">
						<img src="http://game.3gtest.gionee.com/attachs/ad/201309/193805.png" alt="">
					</div>
				</div>
			</a>
		</li>
		<!-- <li><a data-infpage="网游,http://game.3gtest.gionee.com/client/web/index?intersrc=ad22_web&amp;t_bi=_2286939825"><div class="img-box"><div class="panel"><img src="http://game.3gtest.gionee.com/attachs/ad/201309/152224.png" alt=""></div></div><div class="layer">网游</div></a></li><li><a data-infpage="必备,http://game.3gtest.gionee.com/client/installe/index?intersrc=ad23_ness&amp;t_bi=_2286939825"><div class="img-box"><div class="panel"><img src="http://game.3gtest.gionee.com/attachs/ad/201309/150023.png" alt=""></div></div><div class="layer">必备</div></a></li><li><a data-infpage="单机,http://game.3gtest.gionee.com/client/single/index?intersrc=ad24_single&amp;t_bi=_2286939825"><div class="img-box"><div class="panel"><img src="http://game.3gtest.gionee.com/attachs/ad/201309/152312.png" alt=""></div></div><div class="layer">单机</div></a></li></ul> -->
	</ul>
</div>
</body>
</html>