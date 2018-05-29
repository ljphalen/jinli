<?php
return array(
		/* 链接配置,1,pathinfo;2,rewrite;3,兼容模式 */
		'URL_MODEL'							=>2,
		'URL_HTML_SUFFIX'					=>'.html',
		'URL_CASE_INSENSITIVE'				=>true,
		'DEFAULT_TIMEZONE'					=>'Asia/Shanghai',
		
		'SHOW_PAGE_TRACE'					=>false,
		'TOKEN_ON'							=>false,
		'APP_DEBUG'							=>false,
		'VAR_PAGE'							=>'pageNum',			//分页变量
		'PAGE_LISTROWS'						=>10,					//分页显示数量
		'VAR_FILTERS'						=>'_call_var_filters',
		'DEFAULT_FILTER'					=>'dhtmlspecialchars',
		'VAR_URL_PARAMS'					=>'_URL_',
		
		/* Cookie设置 */
		'COOKIE_EXPIRE'						=>3600,					// Coodie有效期
		'COOKIE_DOMAIN'						=>APP_HOST_FIX,		// Cookie有效域名
		'COOKIE_PATH'						=>'/',					// Cookie路径
		'COOKIE_PREFIX'						=>'sso_',				// Cookie前缀 避免冲突
		
		/* 模板设置 */
		'DEFAULT_THEME'						=>'default',
		'URL_PARAMS_BIND'					=>false,
		
		/* SESSION */
		'SESSION_AUTO_START'				=>true,
		'SESSION_OPTIONS'					=>array('path'=>SESSION_PATH),
		
		/*日志记录*/
		'OUTPUT_ENCODE'						=>false,
		'SHOW_PAGE_TRACE'					=>APP_DEBUG,
		'SHOW_ERROR_MSG'					=>APP_DEBUG,
		'LOG_RECORD'						=>true,
		'LOG_LEVEL'							=>'EMERG,ALERT,CRIT,ERR',
		'JS_VERSION'						=>date("Ymd"),
		
		'COMMENT_GLOBAL'					=>array('close'=>false,'interval'=>15),
		
		/* 项目扩展配置 */
		'SITE_DEV_DOMAIN'					=>'dev.game.3gtest.gionee.com',
		'SITENAME'							=>'金立游戏开发者中心',
		'CONTACT'							=>'dev.game@gionee.com',
		'SERVICE_QQ'						=>'123456',
		'COMPANY'							=>'金立手机',
		'OFFLINE'							=>false,
		'OFFLINEMESSAGE'					=>'本站正在维护中，暂不能访问。<br /> 请稍后再访问本站。',
		'OFFLINEALLOWIP'                    =>'127.0.0.1,1.202.254.38',
		'COPYRIGHT'							=>'www.gionee.com',
		'DEFAULT_TIMEZONE'					=>'Asia/Shanghai',
		'SERVICE_QQ'						=>'123456',
		
		/* 关闭注册开关 */
		'CLOSE_REG'					=>FALSE,
);
