<?php
require 'check.php';
require 'smartys/Smarty.class.php';

$smarty = new Smarty();
$smarty->debug = DEBUG;
//$smarty->left_delimiter = '<!--{';
//$smarty->right_delimiter = '}-->';
$smarty->left_delimiter = '<{';
$smarty->right_delimiter = '}>';

$smarty->template_dir = SROOT.'/template/'.$ini_template.'/';
$smarty->compile_dir = SROOT.'/data/smarty/compile/';
$smarty->config_dir = SROOT.'/data/smarty/configs/';
$smarty->cache_dir = SROOT.'/data/smarty/cache/';
$smarty->assign('template', $ini_template);
?>