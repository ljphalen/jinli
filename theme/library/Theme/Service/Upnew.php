<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 主题上新;
 * @author Lyd
 *
 */
class Theme_Service_Upnew {

    private static $upnewKey = "theme:upnews";

    public static function insert($num) {
        $redis = Common::getQueue();
        $date = time();
        $data = $date . '_' . $num;
        return $redis->lpush(self::$upnewKey, $data);
    }

    public static function getNum() {
        $redis = Common::getQueue();
        $t = $redis->lrange(self::$upnewKey, 0, 0);
        $res = self::mk_data($t)[0];
        return $res;
    }

    public static function getAllNum($page = 1, $num = 15) {
        $redis = Common::getQueue();
        $start = ($page - 1) * $num;
        $end = ($page * $num) - 1;
        $res = $redis->lrange(self::$upnewKey, $start, $end);
        return $res;
    }

    public static function getCountNum() {
        $redis = Common::getQueue();
        $count = $redis->llen(self::$upnewKey);

        return $count;
    }

    private static function mk_data($data) {
        if (!$data) return null;

        foreach ($data as $key => $v) {
            $tem[$key]["uptime"] = explode("_", $v)[0];
            $tem[$key]["num"] = explode("_", $v)[1];
        }

        return $tem;
    }

}
