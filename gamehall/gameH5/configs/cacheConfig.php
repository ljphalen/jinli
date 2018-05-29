<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$staticroot = Yaf_Application::app()->getConfig()->staticroot;
$v = Common::getConfig('siteConfig', 'version');
$apk_version = Yaf_Registry::get("apk_version");
$type = (stristr($staticroot,'ala') ? 'channel':'apkv1');
$gameOld_css = (',/apps/game/'.$type.'/assets/css/gameOld.css');
$old_css = (strcmp($apk_version, '1.4.7') >= 0 ? "" : $gameOld_css);
$icat = "1.1.6";
$config = array(
	'Client_Index_index'=>array(
			'CACHE'=>array(
					$staticroot . '/??/sys/reset/phonecore.css,/apps/game/apkv1/assets/css/game.css'.$old_css.'?v=' . $v,
					$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/main.js?v=' . $v,
					$staticroot . '/??/apps/game/apkv1/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/apkv1/assets/js/mvc/view.js,/apps/game/apkv1/assets/js/mvc/model.js,/apps/game/apkv1/assets/js/mvc/controller.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/pic/blank.gif',
					$staticroot . '/apps/game/apkv1/assets/img/default_bg.png'
				),
			'NETWORK'=>array('*'),
	),
	'Client_Category_index'=>array(
			'CACHE'=>array(
					$staticroot . '/??/sys/reset/phonecore.css,/apps/game/apkv1/assets/css/game.css'.$old_css.'?v=' . $v,
					$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/main.js?v=' . $v,
					$staticroot . '/??/apps/game/apkv1/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/apkv1/assets/js/mvc/view.js,/apps/game/apkv1/assets/js/mvc/model.js,/apps/game/apkv1/assets/js/mvc/controller.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/pic/blank.gif',
					$staticroot . '/apps/game/apkv1/assets/img/default_bg.png'
			),
			'NETWORK'=>array('*'),
	),
	'Client_Subject_index'=>array(
			'CACHE'=>array(
					$staticroot . '/??/sys/reset/phonecore.css,/apps/game/apkv1/assets/css/game.css'.$old_css.'?v=' . $v,
					$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/main.js?v=' . $v,
					$staticroot . '/??/apps/game/apkv1/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/apkv1/assets/js/mvc/view.js,/apps/game/apkv1/assets/js/mvc/model.js,/apps/game/apkv1/assets/js/mvc/controller.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/pic/blank.gif',
					$staticroot . '/apps/game/apkv1/assets/img/default_bg.png'
			),
			'NETWORK'=>array('*'),
	),
	//ala
	'Channel_Index_index'=>array(
			'CACHE'=>array(
					$staticroot . '/??/sys/reset/phonecore.css,/apps/game/apkv1/assets/css/game.css'.$old_css.'?v=' . $v,
					$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/main.js?v=' . $v,
					$staticroot . '/??/apps/game/apkv1/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/apkv1/assets/js/mvc/view.js,/apps/game/apkv1/assets/js/mvc/model.js,/apps/game/apkv1/assets/js/mvc/controller.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/pic/blank.gif',
					$staticroot . '/apps/game/apkv1/assets/img/default_bg.png'
			),
			'NETWORK'=>array('*'),
	),
	'Channel_Category_index'=>array(
			'CACHE'=>array(
					$staticroot . '/??/sys/reset/phonecore.css,/apps/game/apkv1/assets/css/game.css'.$old_css.'?v=' . $v,
					$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/main.js?v=' . $v,
					$staticroot . '/??/apps/game/apkv1/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/apkv1/assets/js/mvc/view.js,/apps/game/apkv1/assets/js/mvc/model.js,/apps/game/apkv1/assets/js/mvc/controller.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/pic/blank.gif',
					$staticroot . '/apps/game/apkv1/assets/img/default_bg.png'
			),
			'NETWORK'=>array('*'),
	),
	'Channel_Subject_index'=>array(
			'CACHE'=>array(
					$staticroot . '/??/sys/reset/phonecore.css,/apps/game/apkv1/assets/css/game.css'.$old_css.'?v=' . $v,
					$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/main.js?v=' . $v,
					$staticroot . '/??/apps/game/apkv1/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/apkv1/assets/js/mvc/view.js,/apps/game/apkv1/assets/js/mvc/model.js,/apps/game/apkv1/assets/js/mvc/controller.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/assets/js/game.js?v=' . $v,
					$staticroot . '/apps/game/apkv1/pic/blank.gif',
					$staticroot . '/apps/game/apkv1/assets/img/default_bg.png'
			),
			'NETWORK'=>array('*'),
	),
	//kingstone
	'Kingstone_Index_index'=>array(
				'CACHE'=>array(
						$staticroot . '/??/sys/reset/phonecore.css,/apps/game/kingStone/assets/css/game.css'.$old_css.'?v=' . $v,
						$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/assets/js/main.js?v=' . $v,
						$staticroot . '/??/apps/game/kingStone/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/kingStone/assets/js/game.js?v=' . $v,
						$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/kingStone/assets/js/mvc/view.js,/apps/game/kingStone/assets/js/mvc/model.js,/apps/game/kingStone/assets/js/mvc/controller.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/pic/blank.gif',
						$staticroot . '/apps/game/kingStone/assets/img/default_bg.png'
				),
				'NETWORK'=>array('*'),
	),
	'Kingstone_Category_index'=>array(
				'CACHE'=>array(
						$staticroot . '/??/sys/reset/phonecore.css,/apps/game/kingStone/assets/css/game.css'.$old_css.'?v=' . $v,
						$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/assets/js/main.js?v=' . $v,
						$staticroot . '/??/apps/game/kingStone/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/kingStone/assets/js/game.js?v=' . $v,
						$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/kingStone/assets/js/mvc/view.js,/apps/game/kingStone/assets/js/mvc/model.js,/apps/game/kingStone/assets/js/mvc/controller.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/assets/js/game.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/pic/blank.gif',
						$staticroot . '/apps/game/kingStone/assets/img/default_bg.png'
				),
				'NETWORK'=>array('*'),
	),
	'Kingstone_Subject_index'=>array(
				'CACHE'=>array(
						$staticroot . '/??/sys/reset/phonecore.css,/apps/game/kingStone/assets/css/game.css'.$old_css.'?v=' . $v,
						$staticroot . '/sys/icat/'.$icat.'/icat.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/assets/js/main.js?v=' . $v,
						$staticroot . '/??/apps/game/kingStone/assets/js/plugin/cordova-2.0.0.js,/sys/lib/jquery/jquery.js,/apps/game/kingStone/assets/js/game.js?v=' . $v,
						$staticroot . '/??/sys/icat/'.$icat.'/mvc.js,apps/game/kingStone/assets/js/mvc/view.js,/apps/game/kingStone/assets/js/mvc/model.js,/apps/game/kingStone/assets/js/mvc/controller.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/assets/js/game.js?v=' . $v,
						$staticroot . '/apps/game/kingStone/pic/blank.gif',
						$staticroot . '/apps/game/kingStone/assets/img/default_bg.png'
				),
				'NETWORK'=>array('*'),
	),
);
return $config;
