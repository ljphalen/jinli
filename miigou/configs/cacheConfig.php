<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

$staticroot = Yaf_Application::app()->getConfig()->staticroot;
$v = Common::getConfig('siteConfig', 'version');

$config = array(
	'Front_Index_index'=>array(
			'CACHE'=>array(
					$staticroot . '/apps/gou/h5/pic/blank.gif',
					$staticroot . '??/sys/reset/phonecore.css,/apps/gou/h5/assets/css/gou.css?v=' . $v,
					$staticroot . '??/sys/icat/1.1.5/icat.js,/apps/gou/h5/assets/js/main.js?v=' . $v,
					$staticroot . '??/apps/gou/h5/assets/js/mvc/view.js,/apps/gou/h5/assets/js/mvc/model.js,/apps/gou/h5/assets/js/mvc/controller.js?v=' . $v,
					$staticroot . '??/sys/lib/jquery/jquery.js,/apps/gou/h5/assets/js/plugin/gngou.new.js?v='.$v
						
			),
			'NETWORK'=>array('*'),
	),
);
return $config;
