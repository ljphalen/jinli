<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Acoupon{

    const TICKET_TYPE_ACOUPON = 1;
    const TICKET_TYPE_GAMEVOUCHER = 2;
    const TICKET_OBJECT_GAMEHALL = 1;
    const TICKET_OBJECT_GAME = 2;
    const PAYMENT_ALL_GAME = 1;
    const PAYMENT_SINGLE_GAME = 2;

    /**
     * 8.20-改造
     * @param  $page
     * @param  $limit
     * @param  $params
     * @return
     */
    public static function getACouponActivities($page, $limit, $params = array()) {
        if(intval($page) < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('id'=>'DESC'));
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    public static function getACouponInfoActivity($id) {
        $ret = self::_getDao()->get($id);
        return $ret;
    }

    public static function getsByAcouponActivities($params){
        if (!is_array($params)) return false;
        return self::_getDao()->getsBy($params);
    }

    /**
     *
     * Enter description here ...
     * @param  $data
     */
    public static function addActivity($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->insert($data);
    }

    public static function editACouponActivity($id, $data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, $id);
    }

    public static function delete($id) {
        return self::_getDao()->delete($id);
    }

    /**
     *
     * Enter description here ...
     * @param  $data
     */
    private static function _cookData($data) {
        $tmp = array();
        $fields = array(
            'htype', 'ticket_type', 'ticket_object', 'title', 'hd_start_time', 'hd_end_time', 'status', 'hd_object',
            'condition_type', 'condition_value', 'rule_type', 'game_version', 'game_object',
            'denomination','deadline','rule_content','rule_content_percent','create_time','subject_id',
            'hd_object_addition_info', 'game_object_addition_info'
        );
        foreach ($fields as $field) {
            if(isset($data[$field])) {
                $tmp[$field] = $data[$field];
            }
        }
        return $tmp;
    }

    /**
     *
     * @return Client_Dao_Acoupon
     */
    private static function _getDao() {
        return Common::getDao("Client_Dao_Acoupon");
    }
}
