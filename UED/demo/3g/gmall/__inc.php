<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />
<?php include "_config.php";?>
<?php if($useICAT !== false):?>
<!-- s -->
<?php if($isLocal):?>
<link rel="stylesheet" href="<?php echo "$assets/$appRef/assets/css/$mainCss$source.css";?>" />
<script src="<?php echo "$assets/$sysRef/icat/1.1.5/icat$source.js";?>"></script>
<?php if($mainJs) { ?><script src="<?php echo "$assets/$appRef/assets/js/$mainJs$source.js";?>"></script><?php } ?>
<?php else :?>
<link rel="stylesheet" href="<?php echo "$assets/$appRef/assets/css/$mainCss$source.css";?>" />
<script src="<?php echo "$assets/??/$sysRef/icat/1.1.5/icat$source.js,/$appRef/assets/js/$mainJs$source.js";?>"></script>
<?php endif;?>
<!-- e -->
<?php else :?>
<!-- s -->
<?php if($isLocal):?>
<link rel="stylesheet" href="<?php echo "$assets/$appRef/assets/css/$mainCss$source.css";?>" />
<script src="<?php echo "$assets/$sysRef/lib/zepto/zepto$source.js";?>"></script>
<?php if($mainJs) { ?><script src="<?php echo "$assets/$appRef/assets/js/$mainJs$source.js";?>"></script><?php } ?>
<?php else :?>
<link rel="stylesheet" href="<?php echo "$assets/$appRef/assets/css/$mainCss$source.css";?>" />
<script src="<?php echo "$assets/??/$sysRef/lib/zepto/zepto$source.js,/$appRef/assets/js/$mainJs$source.js";?>"></script>
<?php endif;?>
<!-- e -->
<?php endif;?>
<script>var token = '132465564654';</script>
<!-- 多屏幕模拟测试专用 START -->
<!-- 使用方法：http://yourdomain/#?protoFluid=ready-->
<!--<script type="text/javascript" src="<?php echo $assets;?>/sys/jquery.min.js" async="true"></script> -->
<!--<script type="text/javascript" src="<?php echo $assets;?>/sys/protoFluid3.02.js" async="true"></script> -->
<!-- 多屏幕模拟测试专用 END -->