<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$staticroot = Yaf_Application::app()->getConfig()->staticroot;
$v = Common::getConfig('siteConfig', 'version');

$config = array(
	'Client_Index_index'=>array(
			'CACHE'=>array(
					$staticroot . '/sys/reset/phonecore.css?v=' . $v,
					$staticroot . '/apps/game/apk/assets/css/game.css?v=' . $v,
					$staticroot . '/??/apps/game/apk/assets/js/cordova-2.0.0.js,/sys/icat/1.1.3/icat.js,/sys/lib/zepto/zepto.js,/sys/lib/zepto/touchSwipe.js,/apps/game/apk/assets/js/slidePic.js,/apps/game/apk/assets/js/game.js?v=' . $v,
					$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
					$staticroot . '/sys/lib/zepto/touchSwipe.js?v=' . $v,
					$staticroot . '/apps/game/apk/assets/js/slidePic.js?v=' . $v,
					$staticroot . '/apps/game/apk/pic/blank.gif',
					$staticroot . '/apps/game/apk/assets/img/default_bg.png'
				),
			'NETWORK'=>array('*'),
	),
);
return $config;
