<?php
$sysRef = 'http://a.gionee.com/sys';

$appRef = 'http://a.gionee.com/apps/browser/v1.05';
$appAssets = $appRef.'/assets';
$appPic = $appRef.'/pic';
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0" />
	<meta charset="utf-8">
	<title></title>
	<link rel="stylesheet" href="<?=$sysRef?>/core.css" />
	<style type="text/css">
		html{background:-webkit-gradient(linear, 0 0, 0 80%, from(#77C7FC), to(#FEFFFF)); background-repeat:no-repeat;}
		@media screen and (min-width:721px){body{font-size:15px;}}
		@media screen and (max-width:480px){body{font-size:10px;}}
		@media screen and (max-width:320px){body{font-size:6.6px;}}
		@media screen and (max-width:240px){body{font-size:5px;}}
		a:link, a:visited{text-decoration:none; color:#0066B5;}
		
		.wrap{margin:8px;}
		.nav-list{border-radius:6px; background:#E7F1FA; overflow:hidden;}
		.nav-list dt{padding:9px 0px 9px 6px; font-size:2em; border:solid #B0C5D8; border-width:0 1px; border-bottom:solid 1px #B0C5D8; position:relative;}
		.nav-list dt img{right:9px; top:9px; height:18px; width:18px; position:absolute;}
		.nav-list dd{background:-webkit-gradient(linear, 0 0, 0 100%, from(#daeefe), to(#feffff));}
		.nav-list table{width:100%; border-collapse:collapse; font-size:1.6em;}
		.nav-list table th, .nav-list table td{padding:12px 0; text-align:center; border:solid #B0C5D8; border-width:0 1px; border-bottom:dotted 1px #B0C5D8;}
		.nav-list table th{color:#4CAB13;}
	</style>
	<script type="text/javascript" src="<?=$sysRef?>/jquery.js"></script>
	<script type="text/javascript">
		$(function(){
			$('dt').click(function(){
				$(this).next('dd').toggle();
			});
		});
	</script>
</head>
<body>
	<div data-role="page" id="page" class="wrap">
		<div class="nav-list">
			<dl>
				<dt>分类导航<img src="<?=$appPic?>/up.png" alt="" /></dt>
				<dd>
					<table>
						<?for($i=0; $i<15; $i++){?>
						<tr>
							<th>[新闻]</th>
							<td><a href="">新浪</a></td>
							<td><a href="">搜狐</a></td>
							<td><a href="">网易</a></td>
							<td><a href="">腾讯</a></td>
						</tr>
						<?}?>
					</table>
				</dd>
				
				<dt>精品导航<img src="<?=$appPic?>/up.png" alt="" /></dt>
				<dd>
					<table>
						<?for($i=0; $i<3; $i++){?>
						<tr>
							<th>[新闻]</th>
							<td><a href="">新浪</a></td>
							<td><a href="">搜狐</a></td>
							<td><a href="">网易</a></td>
							<td><a href="">腾讯</a></td>
						</tr>
						<?}?>
					</table>
				</dd>
			</dl>
		</div>
	</div>
</body>
</html>