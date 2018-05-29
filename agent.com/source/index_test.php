<?php
include 'source/inc/check.php';//登陆检查
// smarty
require 'plugin/smarty.php';

// 显示模板的内容
$smarty->display('index_'.$ac.'.html');