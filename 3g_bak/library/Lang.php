<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 语言包
 *
 */
class Lang {

    static public function _($key) {
        $p = APP_VER;
        if (empty($p)) {
            $p = '3g';
        }
        return isset(self::$l[$p][$key]) ? self::$l[$p][$key] : '';
    }

    static $l = array(
        '3g'       => array(
            'USER_CENTER'       => '个人中心',
            'PRODUCT_TEAM_NAME' => '金立浏览器',
        ),
        'overseas' => array(
            'USER_CENTER'       => 'center',
            'PRODUCT_TEAM_NAME' => '金立浏览器'
        ),
        'sige'     => array(
            'USER_CENTER'       => '个人中心',
            'PRODUCT_TEAM_NAME' => '四格浏览器'
        ),
    );
}