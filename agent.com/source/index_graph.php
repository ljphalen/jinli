<?php
// smarty
require 'plugin/smarty.php';

if(post('smt')){
    $tmparr = array('pie','pie_w','pie_h','bar','bar_w','bar_h');
    $param = array();
    foreach($_POST as $key=>$var){
        if(in_array($key,$tmparr)){
            $param[$key] = $var;
            if(!is_numeric($var)){
                //echo $key.'-'.$var.'<br>';
                jump_url('字符：'.$var.'  输入参数必须为数字');
            }
        }
    }
    $str = '<?php return '.var_export($param,true);
    $str .= ';';
    file_put_contents(GRAPHCONFIG, $str);
    jump_url('更新配置成功');
}

if(file_exists(GRAPHCONFIG)){
        $tmpConfig = include  GRAPHCONFIG;
        //var_dump($tmpConfig);
        $smarty->assign('mypie',$tmpConfig['pie']);
        $smarty->assign('pie_w',$tmpConfig['pie_w']);
        $smarty->assign('pie_h',$tmpConfig['pie_h']);
        $smarty->assign('mybar',$tmpConfig['bar']);
        $smarty->assign('bar_w',$tmpConfig['bar_w']);
        $smarty->assign('bar_h',$tmpConfig['bar_h']);
    }


//$mypie

$smarty->assign('actionText','图形设置');
$smarty->assign('ac',$ac);
$smarty->display('index_'.$ac.'.html');