<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<title>锁屏详情页</title>
<?php
$isLocal = preg_match('/dev\./i', $_SERVER['HTTP_HOST']);
$webRoot = $isLocal>=1? 'http://dev.assets.gionee.com' : 'http://3gtest.gionee.com:100';

$sysRef = $webRoot.'/sys';
$appRef = $webRoot.'/apps/lock';
$appAssets = $appRef.'/assets';
$appPic = $appRef.'/pic';
?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no" />

<meta name="appRef" content="<?php echo $appRef;?>" />
<link rel="stylesheet" href="<?php echo $sysRef;?>/reset/mpcore.css" />
<style type="text/css">
.progress-bar {
  display: inline-block;
  vertical-align: middle;
  width: 11.5rem;
  height: .9rem;
  margin: 0 .7rem;
  background-color: #edf2f6;
  text-align: center;
  -webkit-border-radius: 8px;
  -moz-border-radius: 8px;
  -ms-border-radius: 8px;
  -o-border-radius: 8px;
  border-radius: 8px;
  position: relative;
}
.progress-bar em {
  font-size: .7rem;
  color: #999;
  position: relative;
  z-index: 10;
}
.progress-bar .done {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  background-color: #004a97;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #1f6fc5), color-stop(100%, #004a97));
  background-image: -webkit-linear-gradient(top, #1f6fc5, #004a97);
  background-image: -moz-linear-gradient(top, #1f6fc5, #004a97);
  background-image: -o-linear-gradient(top, #1f6fc5, #004a97);
  background-image: linear-gradient(top, #1f6fc5, #004a97);
  -webkit-border-radius: 7px;
  -moz-border-radius: 7px;
  -ms-border-radius: 7px;
  -o-border-radius: 7px;
  border-radius: 7px;
  width: 0%;
}

.load-status {
  /*padding:.6rem 0 .8rem;*/
  padding-top: .6rem;
  height: 2.6rem;
  background-color: #7dacce;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #90c2e7), color-stop(100%, #7dacce));
  background-image: -webkit-linear-gradient(top, #90c2e7, #7dacce);
  background-image: -moz-linear-gradient(top, #90c2e7, #7dacce);
  background-image: -o-linear-gradient(top, #90c2e7, #7dacce);
  background-image: linear-gradient(top, #90c2e7, #7dacce);
}
.load-status span {
  display: inline-block;
  width: 4.2rem;
  text-align: right;
  vertical-align: middle;
  color: #243b53;
  font-size: 1rem;
  background-repeat: no-repeat;
  background-size: contain;
}
.load-status span.download {
  height: 2rem;
  line-height: 2rem;
  background-image: url(<?php echo $appAssets;?>/img/status_download.png);
}
.load-status span.useit {
  height: 1.8rem;
  line-height: 1.8rem;
  background-image: url(<?php echo $appAssets;?>/img/status_useit.png);
}
.load-status span.cancel {
  height: 1.65rem;
  line-height: 1.65rem;
  background-image: url(<?php echo $appAssets;?>/img/status_cancel.png);
}
.load-status span.pause {
  height: 1.75rem;
  line-height: 1.75rem;
  background-image: url(<?php echo $appAssets;?>/img/status_pause.png);
}
.load-status span.continue {
  height: 1.6rem;
  line-height: 1.6rem;
  background-image: url(<?php echo $appAssets;?>/img/status_continue.png);
}
</style>
<script type="text/javascript">var FileData = {"id":"9","title":"sadsad","author":"qqtf","description":"wqeqrwqrqwetweargfeq","thumbUrl ":"http:\/\/3gtest.gionee.com:89\/attachs\/file\/201211\/aqn154125\/icon_aqn.png","gifUrl ":"http:\/\/3gtest.gionee.com:89\/attachs\/file\/201211\/aqn154125\/pre_web_aqn.gif","uxUrl ":"http:\/\/3gtest.gionee.com:89\/down\/201211\/aqn154125\/aqn.ux"};</script>
</head>
<body>
	<div id="page" class="detail">
		<footer class="ft">
			<div class="load-status J_loadStatus">
				<span class="download">下载</span>
			</div>
		</footer>
	</div>
	<?php include '_icat.php';?>
</body>
</html>