<?php
if (!defined('BASE_PATH')) exit ('Access Denied!');

$staticroot = Yaf_Application::app()->getConfig()->staticroot;
$v          = Gionee_Service_Config::getValue('styles_version');

$config = array(
	'Front_Nav_index'        => array(
		'CACHE'   => array(
			// $staticroot . '/sys/reset/mpcore.css?v=' . $v,
			$staticroot . '/apps/3g/nav/css/navigator.min.css?v=' . $v,
			// $staticroot . '/sys/icat/1.1.4/icat.js?v=' . $v,
			// $staticroot . '/sys/lib/jquery/jquery.js?v=' . $v,
			$staticroot . '/apps/3g/nav/js/navigator.min.js?v=' . $v,
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_Nav_test2'        => array(
		'CACHE'   => array(
			// $staticroot . '/sys/reset/mpcore.css?v=' . $v,
			$staticroot . '/apps/3g/assets/css/3g.navigator.css?v=' . $v,
			// $staticroot . '/sys/icat/1.1.4/icat.js?v=' . $v,
			// $staticroot . '/sys/lib/jquery/jquery.js?v=' . $v,
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/apps/3g/assets/js/navigator.js?v=' . $v
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_Nav_test3'        => array(
		'CACHE'   => array(
			// $staticroot . '/sys/reset/mpcore.css?v=' . $v,
			$staticroot . '/apps/3g/assets/css/3g.navigator.css?v=' . $v,
			// $staticroot . '/sys/icat/1.1.4/icat.js?v=' . $v,
			// $staticroot . '/sys/lib/jquery/jquery.js?v=' . $v,
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/apps/3g/assets/js/navigator.js?v=' . $v
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_News_index'       => array(
		'CACHE'   => array(
			'http://jsdev1.m.sohu.com/h5/css/h5portal/v4/tags/1.1.1/c/jinli.css?v=' . $v,
			'http://jsdev1.m.sohu.com/h5/js/tags/v4/h5portal/1.1.1/jinli.js?v=' . $v
			// $staticroot . '/??/sys/icat/1.1.5/icat.js,/apps/3g/news/assets/js/news.js?v=' . $v,
			// $staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			// $staticroot . '/apps/3g/news/assets/css/3g.news.css?v=' . $v,
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_News_test'        => array(
		'CACHE'   => array(
			$staticroot . '/??/sys/icat/1.1.5/icat.js,/apps/3g/news/assets/js/news.js?v=' . $v,
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/apps/3g/news/assets/css/3g.news.css?v=' . $v
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_Index_index'      => array(
		'CACHE'   => array(
			$staticroot . '/sys/reset/mpcore.css?v=' . $v,
			$staticroot . '/apps/3g/assets/css/3g.browser.css?v=' . $v,
			$staticroot . '/sys/icat/1.1.3/icat.js?v=' . $v,
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/apps/3g/assets/js/3g.browser.js?v=' . $v,
			$staticroot . '/apps/3g/assets/js/tempcore.js?v=' . $v
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_Nav2_index'       => array(
		'CACHE'   => array(
			$staticroot . '/apps/3g/nav/assets/css/3g.nav.css?v=' . $v,
			$staticroot . '/??/sys/icat/1.1.4/icat.js,/apps/3g/nav/assets/js/main.js?v=' . $v,
			$staticroot . '/sys/lib/jquery/jquery.js?v=' . $v,
			$staticroot . '/??/apps/3g/nav/assets/js/mvc/template.js,/apps/3g/nav/assets/js/mvc/initdata.js,/apps/3g/nav/assets/js/mvc/view.js,/apps/3g/nav/assets/js/mvc/model.js,/apps/3g/nav/assets/js/mvc/controller.js?v=' . $v
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_Elife_nav'        => array(
		'CACHE'   => array(
			$staticroot . '/??/sys/icat/1.1.4/icat.js,/apps/3g/elife/assets/js/e3.nav.js?v=' . $v,
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/apps/3g/elife/assets/css/e3.nav.css?v=' . $v
		),
		'NETWORK' => array(
			'*'
		)
	),

	'Front_Webapp_index'     => array(
		'CACHE'   => array(
			$staticroot . '/apps/3g/app/v1/assets/css/gnapp.css?v=' . $v,
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/sys/lib/underscore/underscore.js?v=' . $v,
			$staticroot . '/sys/lib/backbone/backbone.js?v=' . $v,
			$staticroot . '/sys/icat/1.1.5/icat.js?v=' . $v,
			$staticroot . '/apps/3g/app/v1/assets/js/gnapp.js?v=' . $v,
			$staticroot . '/apps/3g/app/v1/assets/js/cacheprovider.js?v=' . $v,
			$staticroot . '/apps/3g/app/v1/assets/img/no-img.png',
			$staticroot . '/apps/3g/app/v1/assets/img/blank.gif'
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_Browser_localnav' => array(
		'CACHE'   => array(
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/apps/3g/localnav/js/main.min.js?v=' . $v,
			$staticroot . '/apps/3g/localnav/css/main.min.css?v=' . $v,
		),
		'NETWORK' => array(
			'*'
		)
	),
	'Front_Browser_overseas' => array(
		'CACHE'   => array(
			$staticroot . '/apps/3g/outnav/css/style.source.css?v=' . $v,
			$staticroot . '/sys/lib/zepto/zepto.js?v=' . $v,
			$staticroot . '/apps/3g/outnav/js/zepto.scroll.js?v=' . $v,
			$staticroot . '/apps/3g/outnav/js/tempcore.js?v=' . $v,
			$staticroot . '/apps/3g/outnav/js/main.source.js?v=' . $v,
		),
		'NETWORK' => array(
			'*'
		)
	)
);
return $config;