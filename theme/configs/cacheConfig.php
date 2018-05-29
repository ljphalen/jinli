<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$staticroot = Yaf_Application::app()->getConfig()->staticroot;
$v = Common::getConfig('siteConfig', 'version');

$config = array(
	'Front_Index_index' => array(
			'CACHE'=>array(
					$staticroot . '/apps/theme/assets/css/theme.css?v=' . $v,
					$staticroot . '/??/sys/icat/1.1.5/icat.js,/apps/theme/assets/js/cordova-2.0.0.js,/apps/theme/assets/js/theme.js?v=' . $v,
					$staticroot . '/??/apps/theme/assets/js/core/zepto.js,/apps/theme/assets/js/core/zepto.extend.js,/apps/theme/assets/js/core/zepto.ui.js,/apps/theme/assets/js/widget/refresh.js,/apps/theme/assets/js/widget/refresh.lite.js,/apps/theme/assets/js/tempcore.js?v=' . $v,
					$staticroot . '/sys/lib/zepto/touchSwipe.js?v=' . $v,
					$staticroot . '/sys/reset/mpcore.css?v=' . $v,
					$staticroot . '/apps/theme/assets/js/slidePic.js?v=' . $v,
					$staticroot . '/apps/theme/pic/pic_nopreview.jpg',
					$staticroot . '/apps/theme/assets/img/bg_title.png',
					$staticroot . '/apps/theme//pic/banner_theme.png',
			),
			'NETWORK'=>array('*'),
	),
	'Front_List_index' => array(
			'CACHE'=>array(
					$staticroot . '/apps/theme/assets/css/theme.css?v=' . $v,
					$staticroot . '/??/sys/icat/1.1.5/icat.js,/apps/theme/assets/js/cordova-2.0.0.js,/apps/theme/assets/js/theme.js?v=' . $v,
					$staticroot . '/??/apps/theme/assets/js/core/zepto.js,/apps/theme/assets/js/core/zepto.extend.js,/apps/theme/assets/js/core/zepto.ui.js,/apps/theme/assets/js/widget/refresh.js,/apps/theme/assets/js/widget/refresh.lite.js,/apps/theme/assets/js/tempcore.js?v=' . $v,
					$staticroot . '/sys/lib/zepto/touchSwipe.js?v=' . $v,
					$staticroot . '/sys/reset/mpcore.css?v=' . $v,
					$staticroot . '/apps/theme/assets/js/slidePic.js?v=' . $v,
					$staticroot . '/apps/theme/pic/pic_nopreview.jpg',
					$staticroot . '/apps/theme/assets/img/bg_title.png',
					$staticroot . '/apps/theme//pic/banner_theme.png',
			),
			'NETWORK'=>array('*'),
	)
);
return $config;
