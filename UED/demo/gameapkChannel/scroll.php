<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>上下轮播并且带图片控制</title>
<meta name="description" content="web前端，杭州小白的个人博客，小白的个人博客" />
<meta name="keywords" content="web前端，杭州小白，小白"  />
</head>
<style type="text/css">
body, h1, h2, h3, h4, h5, h6, p,
dl, dt, dd, ul, ol, li, th, td, img, input{border:medium none;margin: 0;padding: 0;}
body{ font-size:12px; font-family:Arial, Helvetica, sans-serif;}
img{ border:0 none;}
h1, h2, h3, h4, h5, h6 { font-size: 100%; }
p,ul,li{list-style:none;}
a{ text-decoration:none; outline:none;}
a:hover{text-decoration:none;}
.clearFix:after{ content:" "; display:block; font-size:0; line-height:0; height:0; visibility:hidden; clear:both;}
.clearFix{ zoom:1;}
.clearLeft{ clear:both; height:1px; line-height:1px; overflow:hidden;}
.fl{ float:left;}
.fr{ float:right;}
.h10{ height:10px; line-height:10px;}

/*---img_ad---*/
.img_ad{ height:100px;min-width:990px;margin:0 auto;position:relative;overflow:hidden;}
.img_wrap{position:absolute;left:0; top:0;}
.img_wrap ul{}
.img_wrap ul li{height:100px;min-width:990px;}
.imgFlashIco{width:990px;overflow:hidden; margin:0 auto; margin-top:10px;}
.imgFlashIco li{ float:left; border:1px solid #CCC; color:#000; width:20px;margin-left:6px; height:26px; cursor:pointer; line-height:26px; text-align:center;}
.imgFlashIco li.on{ border:1px solid #F00; color:#F00;}
</style>

<body>
<div class="wrapper">
    <div class="module">
    	<div class="img_ad js_img">
        	<div class="img_wrap">
                <ul>
                    <li><a href="#"><img src="images/1.jpg" /></a></li>
                    <li><a href="#"><img src="images/2.jpg" /></a></li>
                    <li><a href="#"><img src="images/3.jpg" /></a></li>
                </ul>
            </div>
        </div>
        <div class="imgFlashIco">
            <ul class="js_imgFlashIco">
                <li class="on">1</li>
                <li >2</li>
                <li >3</li>
            </ul>
        </div>
    </div> 
</div>
<script type="text/javascript" src="http://18.8.2.98:8899/sys/lib/jquery/jquery.source.js?t=176"></script>

<script type="text/javascript" src="main.js"></script>
<script type="text/javascript">

var scrollstart=new IndexScrollAll({goods_event:".js_img li",scroll_cont:".js_img ul",imgFlashIco:".js_imgFlashIco li"});
	scrollstart.Start();

</script>
</body>

</html>