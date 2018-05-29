<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * Festival_Service_TouchGame
 * @author fanch
 *
 */
class Festival_Service_TouchGame {
    const OPEN = 1 ;
    const CLOSE = 0;

    /**
     * @param $params
     * @param array $orderBy
     * @return bool
     */
    public static function getByInfo($params,  $orderBy = array()){
        if (!is_array($params)) return false;
        return self::getInfoDao()->getBy($params, $orderBy);
    }

    /**
     * @param $params
     * @param array $orderBy
     * @return bool
     */
    public static function getsByInfo($params, $orderBy = array()){
        if (!is_array($params)) return false;
        return self::getInfoDao()->getsBy($params, $orderBy);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @param array $orderBy
     * @return array
     */
    public static function getInfoList($page = 1, $limit = 10, $params = array(), $orderBy = array('create_time'=>'DESC', 'id' => 'DESC')) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::getInfoDao()->getList($start, $limit, $params, $orderBy);
        $total = self::getInfoDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @param $data
     * @return bool
     */
    public static function insertInfo($data){
        if (!is_array($data)) return false;
        $data = self::cookInfoData($data);
        return  self::getInfoDao()->insert($data);
    }

    /**
     * @param $data
     * @return bool
     */
    public static function insertTotal($data){
        if (!is_array($data)) return false;
        $data = self::cookTotalData($data);
        return  self::getTotalDao()->insert($data);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @param array $orderBy
     * @return array
     */
    public static function getTotalList($page = 1, $limit = 10, $params = array(), $orderBy = array('create_time'=>'DESC', 'id' => 'DESC')) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::getTotalDao()->getList($start, $limit, $params, $orderBy);
        $total = self::getTotalDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @param $data
     * @return bool
     */
    public static function insertLog($data){
        if (!is_array($data)) return false;
        $data = self::cookLogsData($data);
        return  self::getLogsDao()->insert($data);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @param array $orderBy
     * @return array
     */
    public static function getLogsList($page = 1, $limit = 10, $params = array(), $orderBy = array('create_time'=>'DESC', 'id' => 'DESC')) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::getLogsDao()->getList($start, $limit, $params, $orderBy);
        $total = self::getLogsDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @param $data
     * @param $params
     * @return bool
     */
    public static function updateByInfo($data, $params) {
        if (!is_array($data)) return false;
        $data = self::cookInfoData($data);
        return self::getInfoDao()->updateBy($data, $params);
    }

    /**
     * @param $params
     * @param array $orderBy
     * @return bool
     */
    public static function getByTotal($params){
        if (!is_array($params)) return false;
        return self::getTotalDao()->getBy($params);
    }

    /**
     * @param $data
     * @param $params
     * @return bool
     */
    public static function updateByTotal($data, $params) {
        if (!is_array($data)) return false;
        $data = self::cookTotalData($data);
        return self::getTotalDao()->updateBy($data, $params);
    }

    /**
     * @param $data
     * @return array
     */
    private static function cookInfoData($data) {
        $tmp = array();
        if(isset($data['id'])) $tmp['id'] = $data['id'];
        if(isset($data['name'])) $tmp['name'] = $data['name'];
        if(isset($data['preheat_time'])) $tmp['preheat_time'] = $data['preheat_time'];
        if(isset($data['preheat_config'])) $tmp['preheat_config'] = $data['preheat_config'];
        if(isset($data['game_start_time'])) $tmp['game_start_time'] = $data['game_start_time'];
        if(isset($data['game_end_time'])) $tmp['game_end_time'] = $data['game_end_time'];
        if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
        if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
        if(isset($data['login_desc'])) $tmp['login_desc'] = $data['login_desc'];
        if(isset($data['descript'])) $tmp['descript'] = $data['descript'];
        if(isset($data['img_config'])) $tmp['img_config'] = $data['img_config'];
        if(isset($data['props_config'])) $tmp['props_config'] = $data['props_config'];
        if(isset($data['waring_config'])) $tmp['waring_config'] = $data['waring_config'];
        if(isset($data['join'])) $tmp['join'] = $data['join'];
        if(isset($data['times'])) $tmp['times'] = $data['times'];
        if(isset($data['points'])) $tmp['points'] = $data['points'];
        if(isset($data['status'])) $tmp['status'] = $data['status'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        return $tmp;
    }

    /**
     * @param $data
     * @return array
     */
    private static function cookTotalData($data) {
        $tmp = array();
        if(isset($data['id'])) $tmp['id'] = $data['id'];
        if(isset($data['info_id'])) $tmp['info_id'] = $data['info_id'];
        if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
        if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
        if(isset($data['times'])) $tmp['times'] = $data['times'];
        if(isset($data['score'])) $tmp['score'] = $data['score'];
        if(isset($data['points'])) $tmp['points'] = $data['points'];
        if(isset($data['status'])) $tmp['status'] = $data['status'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        return $tmp;
    }

    /**
     * @param $data
     * @return array
     */
    private static function cookLogsData($data) {
        $tmp = array();
        if(isset($data['id'])) $tmp['id'] = $data['id'];
        if(isset($data['info_id'])) $tmp['info_id'] = $data['info_id'];
        if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
        if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
        if(isset($data['score'])) $tmp['score'] = $data['score'];
        if(isset($data['points'])) $tmp['points'] = $data['points'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        return $tmp;
    }

    /**
     * @return object
     */
    private static function getTotalDao() {
        return Common::getDao("Festival_Dao_TouchGameTotal");
    }

    /**
     * @return object
     */
    private static function getLogsDao() {
        return Common::getDao("Festival_Dao_TouchGameLogs");
    }

    /**
     * @return object
     */
    private static function getInfoDao() {
        return Common::getDao("Festival_Dao_TouchGameInfo");
    }
}