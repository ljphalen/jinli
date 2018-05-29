<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_QaAus
 * @author terry
 *
 */
class Gou_Service_QaAus{

    public static $status = array(
        0 => '所有',
        1 => '待审核',
        2 => '审核通过',
        3 => '审核未通过',
    );

    //回帖审核不通过的原因
    public static $reason = array(
        1 => '没有提到具体问题',
        2 => '问题表述不清',
        3 => '涉嫌广告',
        4 => '不文明内容',
        5 => '不合法内容',
        6 => '无关内容',
        7 => '涉嫌抄袭',
        8 => '其他原因',
        8 => '重复提交',
    );

    /**
     * @description 列表
     * @param int $page current page
     * @param int $limit page size
     * @param array $params conditions
     * @param array $orderBy order by index string
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
     * @function get
     * @description 根据id获取单条记录
     *
     * @param integer $id input id
     * @return array
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     * @description 添加记录
     *
     * @param array $data data will created
     * @return boolean
     */
    public static function add($data){
        if(!is_array($data)) return false;
        $data['create_time'] = Common::getTime();
        $data = self::_cookData($data);
        $ret = self::_getDao()->insert($data);
        if (!$ret) return false;
        return self::_getDao()->getLastInsertId();
    }

    /**
     * get by
     */
    public static function getBy($params = array()) {
        if(!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    /**
     * @param $params
     * @param $sort
     * @return array
     */
    public static function getsBy($params, $sort = array()) {
        if (!is_array($params) || !is_array($sort)) return false;
        return self::_getDao()->getsBy($params, $sort);
    }

    /**
     * @description 删除记录
     * @param integer $id
     * @return bool|int
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     * 删多条
     * @param string $field
     * @param array $values
     * @return bool|int
     */
    public static function deletes($field,$values) {
        return self::_getDao()->deletes($field,$values);
    }

    /**
     * @description 更新记录
     *
     * @param string $data new data
     * @param integer $id record modified
     * @return bool|int
     */
    public static function update($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     * 批量更新
     * @param $field
     * @param $values
     * @param $data
     * @return bool|int
     */
    public static function updates($field, $values, $data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updates($field, $values, $data);
    }

    /**
     * 获取记录数
     * @param $params
     * @return bool|string
     */
    public static function getCount($params){
        if(!is_array($params)) return false;
        return self::_getDao()->count($params);
    }

    /**
     * @description 统计更新
     * @param $id
     * @return bool|int
     */
//    public static function updateTJ($id) {
//        if (!$id) return false;
//        Gou_Service_ClickStat::increment(28, $id);
//        return self::_getDao()->increment('hits', array('id'=>intval($id)));
//    }


    /**
     * @description 点赞
     *
     * @param integer $step +1 or -1
     * @param integer $id record modified
     * @return bool|int
     */
    public static function praise($id, $step) {
        if (!$id) return false;
        if ($step < 0 && $row = self::get($id)) {
            if($row['praise'] <1) return true;
        }
        return self::_getDao()->increment('praise', array('id' => intval($id)), $step);
    }

    /**
     * 获取最新一条回帖列表
     * @param array $items  问贴的ID列表
     * @return array|bool
     */
    public static function getLastOneAus($items = array()){
        return self::_getDao()->getLastOneAus($items);
    }


    /**
     * 参数过滤
     *
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['content'])) $tmp['content'] = $data['content'];
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['item_id'])) $tmp['item_id'] = intval($data['item_id']);
        if(isset($data['status'])){
            if(!is_array($data['status'])){
                $tmp['status'] = intval($data['status']);
            }else{
                $tmp['status'] = $data['status'];
            }
        }
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['praise'])) $tmp['praise'] = intval($data['praise']);
        if(isset($data['parent_id'])) $tmp['parent_id'] = intval($data['parent_id']);
        if(isset($data['relate_item_id'])) $tmp['relate_item_id'] = intval($data['relate_item_id']);
        if(isset($data['reason'])) $tmp['reason'] = intval($data['reason']);
        if(isset($data['is_admin'])) $tmp['is_admin'] = intval($data['is_admin']);
        return $tmp;
    }

    /**
     * 获取每条问贴的有效回帖数
     * @param array $items  问贴的ID列表
     * @return array|bool
     */
    public static function getAusCount($items = array()){
        return self::_getDao()->getAusCount($items);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealCusUvStat($sDate, $eDate){
        return self::_getDao()->getRealCusUvStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getVirAusStat($sDate, $eDate){
        return self::_getDao()->getVirAusStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealAusStat($sDate, $eDate){
        return self::_getDao()->getRealAusStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealAusCusUvStat($sDate, $eDate){
        return self::_getDao()->getRealAusCusUvStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealPassAusStat($sDate, $eDate){
        return self::_getDao()->getRealPassAusStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealPassAusCusUvStat($sDate, $eDate){
        return self::_getDao()->getRealPassAusCusUvStat($sDate, $eDate);
    }

    /**
     *
     *
     * @return Gou_Dao_QaAus
     */
    private static function _getDao() {
        return Common::getDao("Gou_Dao_QaAus");
    }

}