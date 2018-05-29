<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_QaSkey
 * @author terry
 *
 */
class Gou_Service_QaSkey{
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
        $data['hash'] = trim($data['skey']);
        $data['count'] = 1;
        $data['dateline'] = date('Y-m-d', Common::getTime());
        $data = self::_cookData($data);
        $ret = self::_getDao()->insert($data);
        if (!$ret) return false;
        return self::_getDao()->getLastInsertId();
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
     * 添加和更新搜索关键词
     * @param string $skey
     * @return bool|string
     */
    public static function updataSkey($skey, $has){
        if(!$skey) return false;
        $data['skey'] = trim($skey);
        $data['hash'] = md5($data['skey']);
        $data['count'] = 1;
        $data = self::_cookData($data);
        $dataline = date('Y-m-d', Common::getTime());
        $data['dateline'] = $dataline;
        $e_skey = self::getBy(array('dateline' => $dataline, 'hash' => $data['hash']));
        if($e_skey){
            $data['count'] = $e_skey['count']+1;
            if($has){
                $data['s_has_count'] = $e_skey['s_has_count']+1;
            }else{
                $data['s_empty_count'] = $e_skey['s_empty_count']+1;
            }
            if(self::_getDao()->update($data, $e_skey['id'])){
                return $e_skey['id'];
            }else{
                return false;
            }
        }else{
            if($has){
                $data['s_has_count'] = 1;
            }else{
                $data['s_empty_count'] = 1;
            }
            if(self::_getDao()->insert($data)){
                return self::_getDao()->getLastInsertId();
            }else{
                return false;
            }
        }
    }

    /**
     * 参数过滤
     *
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['skey'])) $tmp['skey'] = $data['skey'];
        if(isset($data['count'])) $tmp['count'] = intval($data['count']);
        if(isset($data['hash'])) $tmp['hash'] = $data['hash'];
        if(isset($data['s_empty_count'])) $tmp['s_empty_count'] = intval($data['s_empty_count']);
        if(isset($data['s_has_count'])) $tmp['s_has_count'] = intval($data['s_has_count']);
        if(isset($data['dateline'])) $tmp['dateline'] = $data['dateline'];
        return $tmp;
    }

    public static function getEmptyStat($sDate, $eDate){
        return self::_getDao()->getEmptyStat($sDate, $eDate);
    }

    public static function getHasStat($sDate, $eDate){
        return self::_getDao()->getHasStat($sDate, $eDate);
    }

    public static function getCountStat($sDate, $eDate){
        return self::_getDao()->getCountStat($sDate, $eDate);
    }


    /**
     *
     * @return Gou_Dao_QaQus
     */
    private static function _getDao() {
        return Common::getDao("Gou_Dao_QaSkey");
    }

}