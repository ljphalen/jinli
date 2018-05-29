<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<?php include "_config.php";?>
<?php if($isLocal):?>
<link rel="stylesheet" href="<?php echo "$webroot/$sysRef/reset/mpcore.css";?>" />
<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/$mainCss$source.css";?>" />
<script src="<?php echo "$webroot/$sysRef/icat/1.1.5/icat$source.js";?>"></script>
<!--<script src="<?php echo "$webroot/$appRef/assets/js/dist/zepto.js";?>"></script>-->
<!--<script src="<?php echo "$webroot/$appRef/assets/js/core/gmu.js";?>"></script>-->
<!--<script src="<?php echo "$webroot/$appRef/assets/js/core/event.js";?>"></script>-->
<!--<script src="<?php echo "$webroot/$appRef/assets/js/core/widget.js";?>"></script>-->
<!--<script src="<?php echo "$webroot/$appRef/assets/js/widget/refresh/refresh.js";?>"></script>-->
<!--<script src="<?php echo "$webroot/$appRef/assets/js/widget/refresh/lite.js";?>"></script>-->
<!--<script src="<?php echo "$webroot/$appRef/assets/js/tempcore.js";?>"></script>-->
<script src="<?php echo "$webroot/$appRef/assets/js/$mainJs$source.js";?>"></script>
<?php else :?>
<link rel="stylesheet" href="<?php echo "$webroot/$sysRef/reset/mpcore.css";?>" />
<link rel="stylesheet" href="<?php echo "$webroot/$appRef/assets/css/$mainCss$source.css";?>" />
<script src="<?php echo "$webroot/??/$sysRef/icat/1.1.5/icat$source.js,/$appRef/assets/js/$mainJs$source.js";?>"></script>
<?php endif;?>
<script>var token = '132465564654';</script>
<!-- 多屏幕模拟测试专用 START -->
<!-- 使用方法：http://yourdomain/#?protoFluid=ready-->
<!--<script type="text/javascript" src="<?php echo $webroot;?>/sys/jquery.min.js" async="true"></script>-->
<!--<script type="text/javascript" src="<?php echo $webroot;?>/sys/protoFluid3.02.js" async="true"></script>-->
<!-- <script src="http://18.8.2.55:8080/target/target-script-min.js#anonymous"></script> -->
<!-- 多屏幕模拟测试专用 END -->