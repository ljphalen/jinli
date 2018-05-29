#!/usr/bin/php
<?php
php_sapi_name()=='cli' or die();

printf("%s".PHP_EOL, str_repeat("-", 60));

$ini = array(
	'memory_limit'			=>'前台建议2G以上，后台500M以上',
	'post_max_size'			=>'200M以上，Nginx client_max_body_size 200M以上',
	'upload_max_filesize'	=>'200M以上'
);

foreach($ini as $i=>$v)
	printf("php configture %s:%s, 推荐:%s".PHP_EOL, $i, ini_get($i), $v);

#----------------------------------------
printf("%s".PHP_EOL, str_repeat("-", 60));

$cmd = array(
	'awk',
	'aapt',
	'zip',
	'nginx',
	'php-fpm',
	'memcached',
	'mongod',
	'mv',
	'php',
	'java',
	'convert',	//imagemagick
);

foreach($cmd as $i)
	shell_check($i);

#----------------------------------------
printf("%s".PHP_EOL, str_repeat("-", 60));

$php_module = array(
	'memcache', 'mongo', 'redis', 'mysqli', 'session',
	'mcrypt', 'curl', 'iconv', 'json', 'mbstring', 'pcntl', 'readline', 'zip'
);
$php_m = `php -m`;
foreach($php_module as $i)
	printf("php module %s:%s".PHP_EOL, $i, false === strpos($php_m, $i) ? 'not exists' : 'ok' );

#----------------------------------------
printf("%s".PHP_EOL, str_repeat("-", 60));

$thread = array(
	'xs-searchd', 'xs-indexd', 'nginx', 'php-fpm', 'memcached', 'mysqld', 'mysqld_safe', 'mongod'
);
$_thread = `ps ax`;
foreach($thread as $i)
	printf("thread %s:%s".PHP_EOL, $i, false === strpos($_thread, $i) ? 'not exists' : 'ok' );


#----------------------------------------
printf("%s".PHP_EOL, str_repeat("-", 60));
if(class_exists('mongoclient'))
{
	$msg = 'exists';

	try {
		new mongoclient('mongomaster');
		$msg .= " and mongomaster connected ";
	} catch (Exception $e) {
		$msg .= " but " . $e->getMessage();
	}
}else{
	$msg = 'not exists';
}
printf("php mongoclient %s".PHP_EOL, $msg);

function shell_check($name)
{
	$shell = (`type $name >/dev/null 2>&1||echo 'fail'` != 'fail') ? 'ok' : 'not start';
	printf("command %s:%s".PHP_EOL, $name, $shell);
}