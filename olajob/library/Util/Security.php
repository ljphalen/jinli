<?php
if (! defined ( 'BASE_PATH' )) exit ( 'Access Denied!' );
/**
 * Basic Security Filter Service
 * User: sam
 */
class Util_Security {
    /**
     * 整型数过滤
     * @param $param
     * @return int
     */
    public static function int($param) {
        return intval($param);
    }
    /**
     * 字符过滤
     * @param $param
     * @return string
     */
    public static function str($param) {
        return trim($param);
    }
    /**
     * 是否对象
     * @param $param
     * @return boolean
     */
    public static function isObj($param) {
        return is_object($param) ? true : false;
    }
    /**
     * 是否数组
     * @param $params
     * @return boolean
     */
    public static function isArray($params) {
        return (!is_array($params) || !count($params)) ? false : true;
    }
    /**
     * 变量是否在数组中存在
     * @param $param
     * @param $params
     * @return boolean
     */
    public static function inArray($param, $params) {
        return (!in_array((string)$param, (array)$params)) ? false : true;
    }
    /**
     * 是否是布尔型
     * @param $param
     * @return boolean
     */
    public static function isBool($param) {
        return is_bool($param) ? true : false;
    }
    /**
     * 是否是数字型
     * @param $param
     * @return boolean
     */
    public static function isNum($param) {
        return is_numeric($param) ? true : false;
    }

    /**
     * 是否是有值 主要包含  0 '0' 为真
     * Enter description here ...
     * @param unknown_type $param
     */
    public static function isNatualValue($param){
        switch (gettype($param)) {
            case 'string':
            case 'integer':
            case 'double':
                return strlen($param) > 0;
                break;
            case 'array':
                return Util_Security::isArray($param);
                break;
            default:
                return $param ? true : false;
                break;
        }
    }
    /**
     * 加载类/函数文件
     * @param $file
     */
    public static function import($file) {
        if (!is_file($file)) return false;
        require_once $file;
    }
    /**
     * html转换输出
     * @param $param
     * @return string
     */
    public static function htmlEscape($param) {
        return trim(htmlspecialchars($param, ENT_QUOTES));
    }
    /**
     * 过滤标签
     * @param $param
     * @return string
     */
    public static function stripTags($param) {
        return trim(strip_tags($param));
    }

    /**
     * @param $keys
     * @param GET/POST $method
     * @param bool $is_int
     * @param bool $istrim
     * @return array
     */
    public static function getInput($ori_keys, $method = null, $is_int = false, $is_trim = true) {
        $result = $keys = array();
        if(is_string($ori_keys)) {
            $keys = array($ori_keys);
        } else {
            $keys = $ori_keys;
        }

        foreach ($keys as $key) {
            if ($method == 'GET' && isset($_GET[$key])) {
                $result[$key] = $_GET[$key];
            } elseif ($method == 'POST' && isset($_POST[$key])) {
                $result[$key] = $_POST[$key];
            } else {
                if (isset($_GET[$key])) $result[$key] = $_GET[$key];
                if (isset($_POST[$key])) $result[$key] = $_POST[$key];
            }
            if (isset($result[$key])) {
                $result[$key] = Util_Security::escapeChar($result[$key], $is_int, $is_trim);
            }
        }
        if(is_string($ori_keys)) return $result[$ori_keys];
        return $result;
    }

    /**
     * 全局变量过滤
     */
    public static function filter() {
        if (!get_magic_quotes_gpc()) {
            Util_Security::slashes($_POST);
            Util_Security::slashes($_GET);
            Util_Security::slashes($_COOKIE);
        }
        Util_Security::slashes($_FILES);
    }

    /**
     * 路径转换
     * @param $fileName
     * @param $ifCheck
     * @return string
     */
    public static function escapePath($fileName, $ifCheck = true) {
        if (!Util_Security::_escapePath($fileName, $ifCheck)) {
            exit('Forbidden');
        }
        return $fileName;
    }
    /**
     * 私用路径转换
     * @param $fileName
     * @param $ifCheck
     * @return boolean
     */
    public static function _escapePath($fileName, $ifCheck = true) {
        $tmpname = strtolower($fileName);
        $tmparray = array('://',"\0");
        $ifCheck && $tmparray[] = '..';
        if (str_replace($tmparray, '', $tmpname) != $tmpname) {
            return false;
        }
        return true;
    }
    /**
     * 目录转换
     * @param unknown_type $dir
     * @return string
     */
    public static function escapeDir($dir) {
        $dir = str_replace(array("'",'#','=','`','$','%','&',';'), '', $dir);
        return rtrim(preg_replace('/(\/){2,}|(\\\){1,}/', '/', $dir), '/');
    }
    /**
     * 通用多类型转换
     * @param $mixed
     * @param $isint
     * @param $istrim
     * @return mixture
     */
    public static function escapeChar($mixed, $isint = false, $istrim = false) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = Util_Security::escapeChar($value, $isint, $istrim);
            }
        } elseif ($isint) {
            $mixed = (int) $mixed;
        } elseif (!is_numeric($mixed) && ($istrim ? $mixed = trim($mixed) : $mixed) && $mixed) {
            $mixed = Util_Security::escapeStr($mixed);
        }
        return $mixed;
    }
    /**
     * 字符转换
     * @param $string
     * @return string
     */
    public static function escapeStr($string) {
        $string = str_replace(array("\0","%00","\r"), '', $string);
        $string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
        $string = str_replace(array("%3C",'<'), '&lt;', $string);
        $string = str_replace(array("%3E",'>'), '&gt;', $string);
        $string = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $string);
        return $string;
    }
    /**
     * 变量检查
     * @param $var
     */
    public static function checkVar(&$var) {
        if (is_array($var)) {
            foreach ($var as $key => $value) {
                Util_Security::checkVar($var[$key]);
            }
        }  else {
            str_replace(array('<iframe','<meta','<script'), '', $var);
        }
    }

    /**
     * 变量转义
     * @param $array
     */
    public static function slashes(&$array) {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    Util_Security::slashes($array[$key]);
                } else {
                    $array[$key] = addslashes($value);
                }
            }
        }
    }

    /**
     * 获取服务器变量
     * @param $keys
     * @return string
     */
    public static function getServer($keys) {
        $server = array();
        $array = (array) $keys;
        foreach ($array as $key) {
            $server[$key] = NULL;
            if (isset($_SERVER[$key])) {
                $server[$key] = str_replace(array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e'), '', $_SERVER[$key]);
            }
        }
        return is_array($keys) ? $server : $server[$keys];
    }
}