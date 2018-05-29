<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * yhd goods api
 * @author ryan
 *
 */
class Client_Service_Source{


    /**
     * @return array
     */
    public static function getAll() {
        return array(self::_getDao()->count(), self::_getDao()->getAll());
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array(),$sort=array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params,$sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function update($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }
    public static function deleteBy($params=array()) {
        if(empty($params))return false;
        return self::_getDao()->deleteBy($params);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $ret = self::_getDao()->insert($data);
        if (!$ret) return $ret;
        return self::_getDao()->getLastInsertId();
    }


    public static function insert(array $data){
        if (!is_array($data)) return false;
        $ret = self::_getDao()->mutiInsertByKeys($data);
        if (!$ret) return $ret;
        return self::_getDao()->getLastInsertId();
    }
    public static function dropAll() {
        $ret = self::_getDao()->dropAll();
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function getBy($params) {
        if (!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getBy($data);
    }

    /**
     * @param $params
     * @param array $sort
     * @return bool|mixed
     */
    public static function getsBy($params,$sort=array()) {
        if (!is_array($params)) return false;
        $total = self::_getDao()->count($params);
        $ret = self::_getDao()->getsBy($params,$sort);
        return array($total,$ret);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function updateShopTJ($id) {
        if (!$id) return false;
        Gou_Service_ClickStat::increment(15, $id);
        return self::_getDao()->increment('hits', array('id'=>intval($id)));
    }

    /**
     * @description 收藏量统计
     * @param $id
     * @return bool|int
     */
    public static function updateFavCount($item_id,$step=1) {
        if (!$item_id||!in_array($step,array(1,-1))) return false;
        return self::_getDao()->increment('favorite_count', array('shop_id'=>intval($item_id),'channel_id'=>2),$step);
    }


    /**
     * @param $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['id'])) $tmp['id'] = intval($data['id']);
        if(isset($data['goods_id'])) $tmp['goods_id'] = intval($data['goods_id']);
        if(isset($data['title'])) $tmp['title'] = $data['title'];
        if(isset($data['supplier'])) $tmp['supplier'] = $data['supplier'];
        if(isset($data['img'])) $tmp['img'] = $data['img'];
        if(isset($data['market_price_min'])) $tmp['market_price_min'] = $data['market_price_min'];
        if(isset($data['sale_price'])) $tmp['sale_price'] = $data['sale_price'];
        if(isset($data['sale_price_min'])) $tmp['sale_price_min'] = $data['sale_price_min'];
        if(isset($data['storage'])) $tmp['storage'] = $data['storage'];
        if(isset($data['link'])) $tmp['link'] = $data['link'];
        if(isset($data['market_price'])) $tmp['market_price'] = $data['market_price'];
        if(isset($data['category_name'])) $tmp['category_name'] = $data['category_name'];
        return $tmp;
    }

    /**
     *
     * @return Client_Dao_Source
     */
    private static function _getDao() {
        return Common::getDao("Client_Dao_Source");
    }
}