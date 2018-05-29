<?php
/**
 * THINKPHP Cli 独立分组
 * 运行方法
 * /usr/php cli.php
 * 
 * 根据提示执行相关命令
 * 
 * @author shuhai
 */
if(PHP_SAPI !== 'cli')
{
	header('Location: index.php');
	exit;
}

//定义项目名称和路径
define('APP_NAME',				'Source');
define('APP_PATH',				'./Source/');

//全局变量
define('SITE_PATH',             dirname(__FILE__));
define('DATA_HOME',             SITE_PATH.'/Data/');
define('CONF_PATH',             DATA_HOME.'Config/');
define('ATTACH_PATH',           DATA_HOME.'Attachments/');
define('SESSION_PATH',          DATA_HOME.'Session/');
define('EVENT_LOG_PATH',        DATA_HOME.'Log/');

//获取当前运行的域名，Cli模式下模拟二级域名
define('APP_HOST_NAME',			"cli.gionee.com");
define('APP_HOST_FIX',			".gionee.com");
$_SERVER['HTTP_HOST'] =	APP_HOST_NAME;

//获取集群中的php服务器编号，生成不同的编译目录
define('SERVERID',				(isset($_SERVER['SERVERID']) && !empty($_SERVER['SERVERID'])) ? $_SERVER['SERVERID'] : '' );
define('RUNTIME_PATH',          DATA_HOME.'Runtime/Cli/');

//取得模块和操作名称
define('MODULE_NAME',			isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:"Index");
define('ACTION_NAME',			isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:"Index");
if($_SERVER['argc'] > 3)
{
	//解析剩余参数，模拟GET方式获取
	preg_replace('@(\w+),([^,\/]+)@e', '$_GET[\'\\1\']="\\2";', implode(',',array_slice($_SERVER['argv'],3)));
}

//定义调试模式
define('APP_DEBUG',				1);
define('THINK_PATH',            realpath('./Core').'/');
require(THINK_PATH."/ThinkPHP.php");