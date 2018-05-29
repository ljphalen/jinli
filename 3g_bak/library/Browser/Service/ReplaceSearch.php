<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 浏览器搜索关键字过滤
 * @author huwei
 *
 */
class Browser_Service_ReplaceSearch {

    /**
     * @return Browser_Dao_ReplaceSearch
     */
    static public function getDao() {
        return Common::getDao("Browser_Dao_ReplaceSearch");
    }

}