<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Util_Array {

    /**
     * 二维数组排序;
     * @param type $arr
     * @param type $keys
     * @param type $type
     * @param type $key
     * @return type
     */
    public static function array_sort($arr, $keys, $type = 'asc', $key = true) {
        $keysvalue = $new_array = array();
        $type = ($type == "desc") ? $type : "asc";

        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v [$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }


        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            if ($key) $new_array[$k] = $arr[$k];
            else $new_array[] = $arr[$k];
        }
        return $new_array;
    }

}
