<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Terry
 *
 */
class Craw_Service_Index{
    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @param $data
     * @return bool|string
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $res = self::_getDao()->insert($data);
        if(!$res)return false;
        return self::_getDao()->getLastInsertId();
    }

    /**
     * 获取记录
     * @param int $id
     * @return boolean|mixed
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     * get by
     */
    public static function getBy($params = array()) {
        if(!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    /**
     * @param $data
     * @return Ambigous|bool
     */
    public static function replace($data) {
        if(!$data["item_id"]) return false;
        $data = self::_cookData($data);
        return self::_getDao()->replace($data);
    }

    /**
     * @param array $params
     * @return array
     */
    public static function getsBy($params, $sort=array()) {
        if (!is_array($params) || !is_array($sort)) return false;
        $ret = self::_getDao()->getsBy($params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function update($data, $item_id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updateBy($data, array("item_id"=>intval($item_id)));
    }

    /**
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public function updateBy($data, $params) {
        if (!is_array($params)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updateBy($data,$params);
    }

    /**
     * 删除记录
     * @param int $id
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }


    /**
     * Enter desription here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['item_id'])) $tmp['item_id'] = $data['item_id'];
        if(isset($data['category_id'])) $tmp['category_id'] = $data['category_id'];
        if(isset($data['channel_id'])) $tmp['channel_id'] = $data['channel_id'];
        if(isset($data['price'])) $tmp['price'] = $data['price'];
        if(isset($data['price_pos'])) $tmp['price_pos'] = $data['price_pos'];
        if(isset($data['sale_num'])) $tmp['sale_num'] = $data['sale_num'];
        if(isset($data['status'])) $tmp['status'] = $data['status'];
        if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
        if(isset($data['zhe'])) $tmp['zhe'] = $data['zhe'];
        if(isset($data['request_count'])) $tmp['request_count'] = $data['request_count'];
        return $tmp;
    }

    /**
     * @return Craw_Dao_Index
     */
    private static function _getDao() {
        return Common::getDao("Craw_Dao_Index");
    }
}
