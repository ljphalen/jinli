<?php
return array(
		/* 分组控制 */
		'APP_AUTOLOAD_PATH'					=>'#/Source/Modules/Dev/Model,#/Source/Modules/Admin/Model',
		'APP_GROUP_MODE'					=>1,
		'APP_GROUP_LIST'					=>'Dev,Admin',
		'DEFAULT_GROUP'						=>'Dev',
		'APP_SUB_DOMAIN_DEPLOY'				=>1,
		'APP_SUB_DOMAIN_SUFFIX'				=>'game.3gtest.gionee.com',
		'APP_SUB_DOMAIN_RULES'				=>array(
				'dev'			=>array('Dev/'),
				'admin'			=>array('Admin/'),
				'cli'			=>array('Cli/'),
		),
);
