<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$staticroot = Yaf_Application::app()->getConfig()->staticroot;
$v = Common::getConfig('siteConfig', 'version');

$config = array(
	'Front_Index_index' => array(
			'CACHE'=>array(
					$staticroot . '/apps/lock/assets/css/lock.css?v=' . $v,
					$staticroot . '/??/sys/icat/1.1.4/icat.js,/apps/lock/assets/js/cordova-2.0.0.js,/apps/lock/assets/js/lock.js?v=' . $v,
					$staticroot . '/sys/reset/mpcore.css?v=' . $v,
					$staticroot . '/apps/lock/assets/js/tempcore.js?v=' . $v,
					$staticroot . '/apps/lock/assets/js/core/zepto.js?v=' . $v,
					$staticroot . '/apps/lock/assets/js/core/zepto.extend.js?v=' . $v,
					$staticroot . '/apps/lock/assets/js/core/zepto.ui.js?v=' . $v,
					$staticroot . '/apps/lock/assets/js/widget/refresh.js?v=' . $v,
					$staticroot . '/apps/lock/assets/js/widget/refresh.lite.js?v=' . $v,
					$staticroot . '/apps/lock/pic/pic_nopreview.jpg',
					$staticroot . '/apps/lock/assets/img/bg_title.png',
					$staticroot . '/apps/lock//pic/banner_theme.png',
			),
			'NETWORK'=>array('*'),
	),
);
return $config;
