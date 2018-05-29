<?php

/*
 * 一些全局公共函数放置于此
 */



/*
 * 验证签名是否正确
 */

function _isSign($arg) {
    if (!isset($arg["encrypt_str"]) || empty($arg["encrypt_str"])) {//如果没有签名串的话
        return false;
    }
    $encrypt = $arg["encrypt_str"];
    unset($arg["encrypt_str"]); //剔除不需要用来加密的字段
    if (!isset($arg) || empty($arg)) {
        return false;
    }
    ksort($arg); //排序
    $signstr = Doo::conf()->APPSECRET . str_replace("&", "", http_build_query($arg)); //拼装加密串
    if (md5($signstr) != $encrypt) {
        return false;
    }
    return true;
}

function create_rand_array($product) {//产生数组
    if (empty($product) || !is_array($product)) {
        return false;
    }
    $product = array_map(create_function('$n', 'return $n*100;'), $product); //将数组中的概率乘以100
    asort($product); //对数组按照概率从升序排序
    $first = true;
    $tmp_array = array();
    $result = array();
    /*
     * array("1001"=>10,"1002"=>20,"1003"=>70)
     */
    foreach ($product as $key => $value) {
        if ($first) {
            $tmp_array = array_fill(0, $value, $key);
        } else {
            $tmp_array = array_fill($prenum, $value, $key);
        }
        $result = array_merge($result, $tmp_array);
        $first = false;
        $prenum = $value;
    }
    return $result;
}

/*
 * 根据概率返回
 * @param:array("1001"=>0.1,"1002"=>0.2,"1003"=>0.7)
 * @autor:Stephen.Feng
 */

function rate_array($arr) {
    $arr = create_rand_array($arr);
    //获得数组大小
    $arr_size = sizeof($arr);

    //初始化结果数组
    $tmp_arr = array();

    //开始乱序排列
    for ($i = 0; $i < $arr_size; $i++) {

        //随机配置种子，减少重复率
        mt_srand((double) microtime() * 1000000);

        //获得被配置的下标
        $rd = mt_rand(0, $arr_size - 1);

        //下标是否已配置
        if (!isset($tmp_arr[$rd])) {  //未配置
            //进行配置
            $tmp_arr[$rd] = $arr[$i];
        } else {  //已配置
            //返回
            $i = $i - 1;
        }
    }
    return $tmp_arr[rand(0, sizeof($tmp_arr))];
}

/**
 * 概率计算
 * @param array('a'=>0.5, 'b'=>0.2)
 * @return string (key of array, eg. 'a' or 'b')
 */
function random($ps) {
    $arr = array();
    $key = md5(serialize($ps));
    Doo::loadClass("Fredis/FRedis");
    $res = FRedis::getCacheSingleton();
    $arr_redis = $res->get($key);
    if (!empty($arr_redis)) {
        $arr_redis = object_array(json_decode($arr_redis));
        return $arr_redis[$key][mt_rand(0, count($arr_redis[$key]) - 1)];
    }
    if (!isset($arr[$key])) {
        $max = array_sum($ps);
        foreach ($ps as $k => $v) {
            $v = $v / $max * 100;
            for ($i = 0; $i < $v; $i++) {
                $arr[$key][] = $k;
            }
        }
        $res->set($key, json_encode($arr));
    }
    return $arr[$key][mt_rand(0, count($arr[$key]) - 1)];
}

//@param array('a'=>0.5, 'b'=>0.2)
function rate($arr) {//概率函数
    if (empty($arr)) {
        return;
    }
    asort($arr);
    $total = sizeof($arr);
    $max = 10000;
    $random = rand(1, $max);
    $i = 0;
    foreach ($arr as $key => $value) {
        $value = $value * $max;
        if ($i == 0) {//如果是第一个
            if ($random <= $value) {
                return $key;
            }
        }
        if ($i != 0) {//如果不是第一个
            $prevalue = $arr[$prekey] * $max;
            if ($prevalue < $random && $random <= $prevalue + $value) {
                return $key;
            }
        }
        if ($i == $total - 1) {//如果是最后一个
            if ($random >= $value) {
                return $key;
            }
        }
        $i++;
        $prekey = $key;
    }
}

//对象转数组 支持多维数组
function object_array($array) {
    if (is_object($array)) {
        $array = (array) $array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

function json2array($jsonstr) {
    if (empty($jsonstr)) {
        return false;
    }
    $jsonobj = json_decode($jsonstr);
    if (is_string($jsonobj)) {//如果转换完成后依然是字符串则表明此json格式非法
        return false;
    }
    $arr = object_array($jsonobj);
    return $arr;
}

//通过type,subtype获取广告的分类名称
function adtype($type, $subtype) {
    $ad_type_cate = Doo::conf()->AD_TYPE_CATE;
    if (empty($ad_type_cate)) {
        return "未知";
    }
    if ($type == "nembd") {
        $type = "0";
    }
    if ($type == "embd") {
        $type = "1";
    }
    return $ad_type_cate[$type]["subtype"][$subtype];
}

/*
 * @autor:Stephen.Feng
 * 生成appkey,出现重复的概率理论上千万分之一
 */

function createappkey() {
    $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()";
    $str = str_shuffle($str); //随机打乱字符
    $str = substr($str, 0, 32); //取打乱后的前32个字符作为基本字符
    $md5str = md5(strrev(sha1(md5($str))));
    $serialnum = str_replace(array('O', '0', 'I', '1'), '', strtoupper($md5str)); //去掉容易产生误会的字符
    $serialnum = substr($serialnum, 0, 20);
    if (empty($serialnum) || strlen($serialnum) > 20 || strpbrk($serialnum, "0OI1")) {//如果生成的序列号少于20个字符或者字符中存在0，O，I，1则重新生成
        createappkey();
    }
    return $serialnum;
}

function subtype(&$type) {//$myconfig["AD_SUBTYPE_NAME"]=array("nembd"=>0,"embd"=>1);
    if ($type == "nembd") {
        $type = 0;
        return;
    }
    if ($type == "embd") {
        $type = 1;
        return;
    }
}

//供模版使用的
function subtypetag($type) {//$myconfig["AD_SUBTYPE_NAME"]=array("nembd"=>0,"embd"=>1);
    if ($type == "nembd") {
        return 0;
    }
    if ($type == "embd") {
        return 1;
    }
}

function execSocketCmd($server, $port, $cmd) {
    $sock = @fsockopen($server, $port, $errno, $errstr, 30);
    if (!$sock) {
        //return "$errstr ($errno)";
        return false;
    }
    @fputs($sock, $cmd);
    $body = @fread($sock, 4096);
    @fclose($sock);
    $body = iconv('gbk', 'utf-8', $body);
    return $body;
}

//转换数组为'1','2'格式
function implodeSqlstr($array) {
    if (empty($array))
        return false;
    $str = is_array($array) ? $array : (array) $array;
    $str = implode("','", $array);
    $str = "'" . $str . "'";
    return $str;
}

//排列组合函数(无重复)
function getCombinationToString($arr, $m) {
    $result = array();
    if ($m == 1) {
        return $arr;
    }

    if ($m == count($arr)) {
        $result[] = implode(',', $arr);
        return $result;
    }

    $temp_firstelement = $arr[0];
    unset($arr[0]);
    $arr = array_values($arr);
    $temp_list1 = getCombinationToString($arr, ($m - 1));

    foreach ($temp_list1 as $s) {
        $s = $temp_firstelement . ',' . $s;
        $result[] = $s;
    }
    unset($temp_list1);

    $temp_list2 = getCombinationToString($arr, $m);
    foreach ($temp_list2 as $s) {
        $result[] = $s;
    }
    unset($temp_list2);

    return $result;
}

/*
 * 两个数组中的值排列组合(单向)
 * $array1 array 
 * $array2 array
 * $glue string
 */

function getArrayCom($array1, $array2, $glue = "") {
    if (empty($array1) || empty($array2)) {
        return false;
    }
    $array1 = is_array($array1) ? $array1 : (array) $array1;
    $array2 = is_array($array2) ? $array2 : (array) $array2;
    foreach ($array1 as $v1) {
        foreach ($array2 as $v2) {
            $arr[] = $v1 . "_" . $v2 . $glue;
        }
    }
    return $arr;
}

/*
 * 将数组格式Array ( [0] => Array ( [type_value] => 294BEED38BE3E7E68C7D ) )转换成Array ( [0] =>294BEED38BE3E7E68C7D )
 * 以为数组
 */

function array2one($result, $feild) {
    $res = array();
    if (empty($result) || empty($feild) || !is_array($result)) {
        return $res;
    }
    foreach ($result as $value) {
        if (!empty($value[$feild])) {
            array_push($res, $value[$feild]);
        }
    }
    return $res;
}

function remove_directory($dir) {
    if ($handle = opendir("$dir")) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir("$dir/$item")) {
                    remove_directory("$dir/$item");
                } else {
                    unlink("$dir/$item");
                    echo " removing $dir/$item<br>\n";
                }
            }
        }
        closedir($handle);
        rmdir($dir);
        echo "removing $dir<br>\n";
    }
}

/**
 *
 * 需要exec支持
 * @var $apk_file APK文件路径，可以是HTTP形式
 * @var $是否提取渠道信息 默认不提取
 * @var $get_icon 是否提取APK文件中的ICON图标,默认不提取
 * @author admclub.com
 */
function readApkInfoFromFile($apk_file, $channel = false, $get_icon = false) {
    $aapt = Doo::conf()->AAPT_BIN;
    if (empty($aapt)) {
        $aapt = '/usr/bin/aapt';
    }
    if (substr($apk_file, 0, 7) == 'http://') {
        $tmp_apk = "/tmp/" . md5(microtime()) . ".apk";

        exec('/usr/bin/wget ' . $apk_file . ' -O ' . $tmp_apk . " -t 1", $_out, $_return);
        $filesize = filesize($tmp_apk);
        if ($filesize > 0) {
            $apk_file = $tmp_apk;
        }
    }
    if(file_exists($tmp_apk)){
        $info["size"] = filesize($tmp_apk);
    }else{
        $info["size"] = filesize($apk_file);
    }
    exec("{$aapt} d badging {$apk_file}", $out, $return);
    if ($return == 0) {
        $str_out = implode("\n", $out);
        $out = null;
        #icon
        if ($get_icon) {
            $pattern_icon = "/icon='(.+)'/isU";
            preg_match($pattern_icon, $str_out, $m);
            $info['icon'] = $m[1];
            if ($info['icon']) {

                if ($tmp_apk) {
                    $command = "unzip {$tmp_apk} {$info['icon']} -d /tmp";
                } else {
                    $command = "unzip {$apk_file} {$info['icon']} -d /tmp";
                }

                mkdirs("/tmp/" . $info['icon'], true);
                exec($command);
            }
        }
        if ($channel) {
            if ($tmp_apk) {
                $command = "unzip {$tmp_apk} 'assets/skynet_config.txt' -d /tmp";
            } else {
                $command = "unzip {$apk_file} 'assets/skynet_config.txt' -d /tmp";
            }
            
            exec("mkdir -p /tmp/assets && " . $command);
            $pubkey=Doo::conf()->PUBKEY_NETGAME;
            $channel=decoding_skynet_config($pubkey);
            if(empty($channel)){
                $pubkey=Doo::conf()->PUBKEY_ONEPACKAGE;
                $channel=decoding_skynet_config($pubkey);
            }
            $info["channel"]=$channel;
        }
        #对外显示名称
        $pattern_name = "/application: label='(.*)'/isU";
        preg_match($pattern_name, $str_out, $m);
        $info['lable'] = $m[1];

        #内部名称,软件唯一的
        $pattern_sys_name = "/package: name='(.*)'/isU";
        preg_match($pattern_sys_name, $str_out, $m);
        $info['sys_name'] = $m[1];

        #内部版本名称,用于检查升级
        $pattern_version_code = "/versionCode='(.*)'/isU";
        preg_match($pattern_version_code, $str_out, $m);
        $info['version_code'] = $m[1];

        #对外显示的版本名称
        $pattern_version = "/versionName='(.*)'/isU";
        preg_match($pattern_version, $str_out, $m);
        $info['version'] = $m[1];

        #系统
        $pattern_sdk = "/sdkVersion:'(.*)'/isU";
        preg_match($pattern_sdk, $str_out, $m);
        $info['sdk_version'] = $m[1];
        if ($info['sdk_version']) {
            $sdk_names = array(3 => "1.5", 4 => "1.6", 7 => "2.1", 8 => "2.2", 10 => '2.3.3', 11 => "3.0", 12 => "3.1", 13 => "3.2", 14 => "4.0");
            if ($sdk_names[$info['sdk_version']]) {
                $info['os_req'] = "Android {$sdk_names[$info['sdk_version']]}";
            }
        }

        #权限
//        $pattern_perm = "/uses-permission:'(.*)'/isU";
//        preg_match_all($pattern_perm, $str_out, $m);
//        if ($m) {
//            $cnt = count($m[1]);
//            for ($i = 0; $i < $cnt; $i++) {
//                $info['permissions'] .= $info['permissions'] ? "\n" . $m[1][$i] : $m[1][$i];
//            }
//        }
        #需要的功能(硬件支持)
//        $pattern_features = "/uses-feature:'(.*)'/isU";
//        preg_match_all($pattern_features, $str_out, $m);
//        if ($m) {
//            $cnt = count($m[1]);
//            for ($i = 0; $i < $cnt; $i++) {
//                $info['features'] .= $info['features'] ? "\n" . $m[1][$i] : $m[1][$i];
//            }
//        }
        //$info['apk_info'] = $str_out;

        if ($tmp_apk) {
            unlink($tmp_apk);
        }
        return $info;
    }

    if ($tmp_apk) {
        unlink($tmp_apk);
    }
}
/**
 *
 * 需要exec支持
 * @var $ipa_file ipa文件路径，可以是HTTP形式
 * @var $是否提取渠道信息 默认不提取
 * @var $get_icon 是否提取APK文件中的ICON图标,默认不提取
 * @author admclub.com
 */
function readIPAInfoFromFile($ipa_file) {
    if (substr($ipa_file, 0, 7) == 'http://') {
        $tmp_file = "/tmp/" . md5(microtime()) . ".ipa";
        exec('/usr/bin/wget ' . $ipa_file . ' -O ' . $tmp_file . " -t 1", $_out, $_return);
        $filesize = filesize($tmp_file);
        if ($filesize > 0) {
            $ipa_file = $tmp_file;
        }
    }
    $info["size"] = filesize($ipa_file);
    $ipadir="/tmp/".md5(microtime());
    
    if ($ipa_file) {
        $command = "unzip {$ipa_file} -d $ipadir";
        exec($command,$_out,$_return);
        if($_return==0){
            $plistfile=exec("/bin/ls $ipadir/*.plist");
            if(file_exists($plistfile)){
                Doo::loadClass("plist/PropertyList");
                $plist = new PropertyList($plistfile);
                $m = $plist->toArray();
                #对外显示名称
                $info['lable'] = $m["bundleDisplayName"];

                #内部名称,软件唯一的
                $info['sys_name'] = $m["softwareVersionBundleId"];

                #内部版本名称,用于检查升级
                $info['version_code'] = $m["bundleVersion"];

                #对外显示的版本名称
                $info['version'] = $m["bundleShortVersionString"];
            }
            if (is_dir($ipadir)) {
                unlink($ipadir);
            }
            return $info;
        }
    }
    return false;
    //bundleDisplayName
    //bundleVersion
    //bundleShortVersionString
}
/**
 * 公钥解密
 */
function publickey_decoding($crypttext, $pubkey) {
    //$crypttextArr = json_decode(urldecode($crypttext), true);
    $prikeyid = openssl_get_publickey($pubkey);
    $ret=openssl_public_decrypt(base64_decode($crypttext), $sourcestr, $prikeyid);
//    foreach ($crypttextArr as $value) {
//        if (openssl_public_decrypt(base64_decode($value), $sourcestr, $prikeyid)) {
//            $ret .=$sourcestr;
//        }
//    }
    return $sourcestr;
}
/*
 *解析skynet_config.txt中的信息
 */
function decoding_skynet_config($pubkey) {
    if(!file_exists("/tmp/assets/skynet_config.txt")){
        return false;
    }
    $crypttext=  file_get_contents("/tmp/assets/skynet_config.txt");
    $crypttextArr = json_decode(urldecode($crypttext), true);
    foreach ($crypttextArr as $value) {
            $ret .=publickey_decoding($value,$pubkey);
    }
    if(empty($ret)){
        return false;
    }
    $ret=  json_decode($ret);
    if(isset($ret["channel_id"])){
        return $ret;
    }
    return false;
}

/**
 * 用户操作日志模拟登录
 * @return boolean
 */
function logger_login(){
    global $config;
    global $myconfig;
    $app_url = $config['APP_URL'];
    $url = $app_url.'login/login/login';//接收post数据地址
    $cookieFile    = './protected/cache/logger_cookie.txt';
    $post_field = array('username'=>$myconfig['logger']['username'], 'password'=>$myconfig['logger']['password']);
	
	$ch = curl_init(); //初始化curl
	curl_setopt($ch, CURLOPT_URL, $url);//设置链接
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
	curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);//POST数据
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$response = curl_exec($ch);//接收返回信息
	if(curl_errno($ch)){//出错则显示错误信息
//		print curl_error($ch);
        return false;
	}
	curl_close($ch); //关闭curl链接
    return true;
}

/**
 * 操作日志，保存管理员后台操作时候的保存页面
 * @global type $config
 * @param string $url
 * @param type $file_pre
 * @return boolean|string
 */
function save_referer_page($url, $file_pre){
    //session 24分钟后就超时，这里设置每20分钟重新登录一次获取新的cookie里面的sessionid    
    $cookieFile    = './protected/cache/logger_cookie.txt';
    $file_ctime = filectime($cookieFile);
    $reLogin = false;
    
    if(time() - $file_ctime > 60 * 20){
        $reLogin = true;
    }
    
    if($reLogin){
        if(!logger_login()){
//            echo "logger login error! ";
//            die;
        }
    }
    
    $timeout = 30;//超时设置
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0');
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $data = curl_exec($ch);
    curl_close($ch);
    if ($data)
    {
        $disableFormScript = '<script language="javascript"> $("input").attr("disabled", "disabled");$("select").attr("disabled", "disabled");</script>';
        $data = str_replace('</body>', $disableFormScript.'</body>', $data);
        global $config;
        $saveDir = $config['SITE_PATH'].'misc/ledou/history/';
        if(path_exists($saveDir)){
            $fileName = $file_pre.'_'.time().".html";
            $file = $saveDir.$fileName;
            Doo::loadCore('helper/DooFile');
            $dooFile = new DooFile();
            $result = $dooFile->create($file, $data, 'w');
            if($result){
                $url = $config['APP_URL'].'misc/ledou/history/'.$fileName;
                return $url;
            }else{
                return false;
            }
        }
    }
    else
    {
        return false;
    }
}


/**
 * 强建目录路径
 *
 * @param string $path
 * @return string || false
 */
function path_exists($path)
{
    $pathinfo = pathinfo($path . '/tmp.txt');
    if (!empty($pathinfo['dirname']))
    {
        if (file_exists($pathinfo['dirname']) === false)
        {
            if (mkdir($pathinfo['dirname'], 0777, true) === false)
            {
                return false;
            }
        }
    }
    return $path;
}