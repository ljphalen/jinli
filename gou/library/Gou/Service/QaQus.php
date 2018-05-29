<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_QaQus
 * @author terry
 *
 */
class Gou_Service_QaQus{

    public static $status = array(
        0 => '所有',
        1 => '待审核',
        2 => '审核通过',
        3 => '审核未通过',
    );

    //问贴审核不通过的原因
    public static $reason = array(
        1 => '没有提到具体问题',
        2 => '问题表述不清',
        3 => '涉嫌广告',
        4 => '不文明内容',
        5 => '不合法内容',
        6 => '无关内容',
        7 => '涉嫌抄袭',
        8 => '其他原因',
        9 => '重复提交',
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
        if(self::_getDao()->insert($data))
        {
            return self::_getDao()->getLastInsertId();
        }else{
            return false;
        }
    }

    /**
     * 更新问贴的回帖数
     * @param $id
     * @param int $type
     * @return bool|int
     */
    public static function updateTotal($id, $type=1) {
        if (!$id) return false;
        if(is_array($id)){
            return self::_getDao()->increment('total', array('id'=>array('IN', $id)), $type);
        }
        if ($type < 0 && $row = self::get($id)) {
            if($row['total'] <1) return true;
        }
        return self::_getDao()->increment('total', array('id'=>intval($id)), $type);
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
     * get by
     */
    public static function getBy($params = array()) {
        if(!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    /**
     * @param $params
     * @param $sort
     * @return array|bool
     */
    public static function getsBy($params, $sort=array()) {
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
    public static function deletes($field, $values) {
        return self::_getDao()->deletes($field, $values);
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
     * 问贴搜索
     * @param $page
     * @param $limit
     * @param $search
     * @return bool|array
     */
    public static function searchQus($page, $limit, $search){
        if(empty($search)) return false;
        return self::getList(
            $page,
            $limit,
            array('status'=>2, 'title'=>array('LIKE', $search)),
            array('recommend'=>'DESC', 'total'=>'DESC', 'create_time'=>'DESC')
        );
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
    public static function getVirQusStat($sDate, $eDate){
        return self::_getDao()->getVirQusStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealQusStat($sDate, $eDate){
        return self::_getDao()->getRealQusStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealQusCusUvStat($sDate, $eDate){
        return self::_getDao()->getRealQusCusUvStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealPassQusStat($sDate, $eDate){
        return self::_getDao()->getRealPassQusStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealPassQusCusUvStat($sDate, $eDate){
        return self::_getDao()->getRealPassQusCusUvStat($sDate, $eDate);
    }

    /**
     * 参数过滤
     *
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['title'])) $tmp['title'] = $data['title'];
        if(isset($data['content'])) $tmp['content'] = $data['content'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['images'])) $tmp['images'] = $data['images'];
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['recommend'])) $tmp['recommend'] = intval($data['recommend']);
        if(isset($data['status'])) $tmp['status'] = $data['status'];
        if(isset($data['category_id'])) $tmp['category_id'] = intval($data['category_id']);
        if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
        if(isset($data['total'])) $tmp['total'] = intval($data['total']);
        if(isset($data['reason'])) $tmp['reason'] = intval($data['reason']);
        if(isset($data['is_hidden'])) $tmp['is_hidden'] = intval($data['is_hidden']);
        if(isset($data['is_admin'])) $tmp['is_admin'] = intval($data['is_admin']);
        return $tmp;
    }

    /**
     *
     *
     * @return Gou_Dao_QaQus
     */
    private static function _getDao() {
        return Common::getDao("Gou_Dao_QaQus");
    }

}