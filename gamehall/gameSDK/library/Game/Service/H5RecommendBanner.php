<?php

if (!defined('BASE_PATH'))
    exit('Access Denied!');

/**
 * 推荐banner图
 * Game_Service_RecommendBanner
 * @author wupeng
 */
class Game_Service_H5RecommendBanner {

    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    const BANNER_TYPE_GAME = 1;
    const BANNER_TYPE_CATEGORY = 2;
    const BANNER_TYPE_SUBJECT = 3;
    const BANNER_TYPE_URL = 4;
    const BANNER_TYPE_ACTIVITY = 5;

    /**
     * 返回所有记录
     * @return array
     */
    public static function getAllRecommendBanner() {
        return array(self::getDao()->count(), self::getDao()->getAll());
    }

    /**
     * 查询记录
     * @return array
     */
    public static function getRecommendBannersBy($searchParams, $sortParams = array('sort' => 'desc', 'id' => 'asc')) {
        return self::getDao()->getsBy($searchParams, $sortParams);
    }

    /**
     * 分页查询
     * @param int $page
     * @param int $limit
     * @param array $searchParams
     * @param array $sortParams
     * @return array
     */
    public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
        if ($page < 1)
            $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
        $total = self::getDao()->count($searchParams);
        return array($total, $ret);
    }

    /**
     * 根据主键查询一条记录
     * @param int $id
     * @return array
     */
    public static function getRecommendBanner($id) {
        if (!$id)
            return null;
        $keyParams = array('id' => $id);
        return self::getDao()->getBy($keyParams);
    }

    /**
     * 根据主键更新一条记录
     * Enter description here ...
     * @param array $data
     * @param int $id
     * @return boolean
     */
    public static function updateRecommendBanner($data, $id) {
        if (!$id)
            return false;
        $dbData = self::checkField($data);
        if (!is_array($dbData))
            return false;
        $keyParams = array('id' => $id);
        return self::getDao()->updateBy($dbData, $keyParams);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateListSort($idList, $sortList) {
        if (!is_array($idList) || !is_array($sortList))
            return false;
        Common_Service_Base::beginTransaction();
        foreach ($idList as $id) {
            $ret = self::getDao()->update(array('sort' => $sortList[$id]), $id);
            if (!$ret) {
                Common_Service_Base::rollBack();
                return false;
            }
        }
        Common_Service_Base::commit();
        return true;
    }

    /**
     * 根据主键删除一条记录
     * @param int $id
     * @return boolean
     */
    public static function deleteRecommendBanner($id) {
        if (!$id)
            return false;
        $keyParams = array('id' => $id);
        Game_Service_H5RecommendDelete::deleteOneRowById(self::getDao()->getTableName(), $id);
        return self::getDao()->deleteBy($keyParams);
    }

    /**
     * 根据主键删除多条记录
     * @param array $keyList
     * @return boolean
     */
    public static function deleteRecommendBannerList($keyList) {
        if (!is_array($keyList))
            return false;
        Game_Service_H5RecommendDelete::deleteAllRowByIdList(self::getDao()->getTableName(), $keyList);
        return self::getDao()->deletes('id', $keyList);
    }

    public static function deleteRecommendBannerByDayId($day_id) {
        $keyParams = array('day_id' => $day_id);
        Game_Service_H5RecommendDelete::deleteOneRowByDayId(self::getDao()->getTableName(), $day_id);
        return self::getDao()->deleteBy($keyParams);
    }

    /**
     * 添加一条记录
     * @param array $data
     * @return boolean
     */
    public static function addRecommendBanner($data) {
        if (!is_array($data))
            return false;
        $dbData = self::checkField($data);
        return self::getDao()->insert($dbData);
    }

    public static function addMutiRecommendBanner($list) {
        if (!$list)
            return false;
        return self::getDao()->mutiFieldInsert($list);
    }

    public static function updateRecommendBannerBy($data, $searchParams) {
        $dbData = self::checkField($data);
        if (!is_array($dbData))
            return false;
        return self::getDao()->updateBy($dbData, $searchParams);
    }

    /**
     * 检查数据库字段
     * @param array $data
     * @return array
     */
    private static function checkField($data) {
        $dbData = array();
        if (isset($data['id']))
            $dbData['id'] = $data['id'];
        if (isset($data['sort']))
            $dbData['sort'] = $data['sort'];
        if (isset($data['title']))
            $dbData['title'] = $data['title'];
        if (isset($data['link_type']))
            $dbData['link_type'] = $data['link_type'];
        if (isset($data['link']))
            $dbData['link'] = $data['link'];
        if (isset($data['img']))
            $dbData['img'] = $data['img'];
        if (isset($data['day_id']))
            $dbData['day_id'] = $data['day_id'];
        if (isset($data['status']))
            $dbData['status'] = $data['status'];
        if (isset($data['create_time']))
            $dbData['create_time'] = $data['create_time'];
        $dbData['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
        return $dbData;
    }

    /**
     * @return Game_Dao_RecommendBanner
     */
    private static function getDao() {
        return Common::getDao("Game_Dao_H5RecommendBanner");
    }

}
