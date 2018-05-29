<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 分表Dao工厂
 * @author liyf
 *
 */
class Common_Dao_SplitFactory {
    static $instances;

    /**
     * 分表Dao工厂 单例
     *
     * @param string $daoName
     * @return object
     */
    static public function getDao($daoName) {
        $key = md5($daoName);
        if (isset(self::$instances[$key]) && self::$instances[$key] !== null) {
            self::$instances[$key]->initAdapter();
            return self::$instances[$key];
        }
        self::$instances[$key] = new $daoName();
        self::$instances[$key]->initAdapter();
        return self::$instances[$key];
    }

}
