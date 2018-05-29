<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Enter description here ...
 * the last known user to change this file in the repository  <$LastChangedBy: Nash.xiong $>
 * @author Nash.xiong <nash.xiong@gmail.com>
 * @copyright Copyright &copy; 2003-2011 phpwind.com
 * @license 
 */
$config = array(
	'test' => array(
		'host' => '10.249.198.235',
        'port' => '11300',
		'timeout' => 0.25
	),
	'product' => array(
		'host' => '172.18.120.165',
        'port' => '11301',
		'timeout' => 0.25
	),
	'develop' => array(
		'host' => '10.249.198.235',
        'port' => '11300',
		'timeout' => 0.25
	)
);
return defined('ENV') ? $config[ENV] : $config['product'];