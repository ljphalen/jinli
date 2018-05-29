<?php
if(PHP_SAPI !== 'cli')
{
	header('Location: index.php');
	exit;
}else{
	$_SERVER['HTTP_HOST'] = 'Cli';
}

define('REPL_MODE', true);
require "index.php";

require "Source/vendor/autoload.php";
use Guzzle\Http\Client;

$boris  = new \Boris\Boris(">>> ");

$config = new \Boris\Config();
$config->apply($boris, true);

$options = new \Boris\CLIOptionsHandler();
$options->handle($boris);

$boris->onStart(sprintf("echo 'REPL FOR THINKPHP by vus520@github\nTHINKPHP_VERSION: %s, PHP_VERSION: %s\n';", THINK_VERSION, PHP_VERSION));

$boris->start();
