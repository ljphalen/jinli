<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author huyuke
 *
 */
class Util_TimeConvert {

    const RADIX_DAY = 1;
    const RADIX_HOUR = 2;
    const RADIX_MINUTE = 3;

    const SECOND_OF_DAY = 86400;
    const SECOND_OF_HOUR = 3600;
    const SECOND_OF_MINUTE = 60;

    private static $sRadixTimeFormat = array(
                                        self::RADIX_DAY => 'Y-m-d 00:00:00',
                                        self::RADIX_HOUR => 'Y-m-d H:00:00',
                                        self::RADIX_MINUTE => 'Y-m-d H:i:00',
                                      );


    public static function floor($originalTime, $radix) {
        if (!array_key_exists($radix, self::$sRadixTimeFormat)) {
            return -1;
        }

        $format = self::$sRadixTimeFormat[$radix];

        $timeFormat = date($format, $originalTime);

        return strtotime($timeFormat);
    }
}
