<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_Livewallpapersubject {

    private static function _getDao() {
        return Common::getDao('Theme_Dao_Livewallpapersubject');
    }

    /**
     * 获取动态壁纸专题列表
     */
    public static function getAll($wheres = '', $limits = 15, $pages = 1) {
        return self::_getDao()->getAll($wheres, $limits, $pages);
    }

    /**
     * 添加专题
     * @param null
     */
    public static function add($data){
    	return self::_getDao()->insert($data);
    }

    /**
     * 更新专题
     * @param null
     */
    public static function update($data, $value){
    	return self::_getDao()->update($data, $value);
    }

    /**
     *  删除专题
     */
    public static function delete($value){
    	return self::_getDao()->delete($value);
    }

    /**
     * 获取专题
     */
    public static function get($value){
    	return self::_getDao()->get($value);
    }

    /**
     * 通过条件获取专题
     */
    public static function getsBy($params, $order = array()){
        $result = self::_getDao()->getsBy($params, $order);
        return $result;
    }

}