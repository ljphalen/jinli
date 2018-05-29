<?php

//register global/PHP functions to be used with your template files
//You can move this to common.conf.php   $config['TEMPLATE_GLOBAL_TAGS'] = array('isset', 'empty');
//Every public static methods in TemplateTag class (or tag classes from modules) are available in templates without the need to define in TEMPLATE_GLOBAL_TAGS
Doo::conf()->TEMPLATE_GLOBAL_TAGS = array('time','stripslashes','count','date', 'upper', 'tofloat', 'sample_with_args', 'debug', 'url', 'url2', 'function_deny', 'isset', 'empty','checkPermission','adtype','jsonEncode','substr_tg','pos_key_name','format_time');

/**
Define as class (optional)

class TemplateTag {
    public static test(){}
    public static test2(){}
}
**/

function upper($str){
    return strtoupper($str);
}

function tofloat($str){
    return sprintf("%.4f", $str);
}

function sample_with_args($str, $prefix){
    return $str .' with args: '. $prefix;
}

function debug($var){
    if(!empty($var)){
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

//This will be called when a function NOT Registered is used in IF or ElseIF statment
function function_deny($var=null){
   echo '<span style="color:#ff0000;">Function denied in IF or ElseIF statement!</span>';
   exit;
}


//Build URL based on route id
function url($id, $param=null, $addRootUrl=false){
    Doo::loadHelper('DooUrlBuilder');
    // param pass in as string with format
    // 'param1=>this_is_my_value, param2=>something_here'

    if($param!=null){
        $param = explode(', ', $param);
        $param2 = null;
        foreach($param as $p){
            $splited = explode('=>', $p);
            $param2[$splited[0]] = $splited[1];
        }
        return DooUrlBuilder::url($id, $param2, $addRootUrl);
    }

    return DooUrlBuilder::url($id, null, $addRootUrl);
}


//Build URL based on controller and method name
function url2($controller, $method, $param=null, $addRootUrl=false){
    Doo::loadHelper('DooUrlBuilder');
    // param pass in as string with format
    // 'param1=>this_is_my_value, param2=>something_here'

    if($param!=null){
        $param = explode(', ', $param);
        $param2 = null;
        foreach($param as $p){
            $splited = explode('=>', $p);
            $param2[$splited[0]] = $splited[1];
        }
        return DooUrlBuilder::url2($controller, $method, $param2, $addRootUrl);
    }

    return DooUrlBuilder::url2($controller, $method, null, $addRootUrl);
}

function checkPermission($group_id, $permission_id){
    Doo::loadClass("permission/class.permission");
    $permiss=new permission();
    return $permiss->check_permission($group_id, $permission_id);
}

function jsonEncode($array){
    return json_encode($array);
}
/**
 * 截取字符长度
 */
function substr_tg($string,$start=0,$end=1000){
    if(strlen($string)<=$end){
        return $string;
    }
    $string=  substr($string,$start,$end);
    return $string;
}
/*
 * 获取广告位类型名称
 */
function pos_key_name($key){
    $ad_pos_type=Doo::conf()->AD_POS_TYPE;
    $pos_name=$ad_pos_type[$key];
    if(empty($pos_name)){
        return "未知类型";
    }
    return $pos_name;
}
/*
 * 格式化时间
 */
function format_time($time, $format = "Y-m-d H:i:s"){
    if(empty($time)){
        return "0000-00-00 00:00:00";
    }
    return date($format,$time);
}
?>