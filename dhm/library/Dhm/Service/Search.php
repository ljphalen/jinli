<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author terry
 *
 */
class Dhm_Service_Search extends Common_Service_Base{

    /**
     * 分页取搜索关键词列表
     * @param int $table
     * @param int $page
     * @param int $limit
     * @param array $params
     * @param array $orderBy
     * @return array]
     */
    public static function getList($table = 0, $page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        if(!$table) return false;
        $params = self::_cookData($params);
        unset($params['s']);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao(0, '', $table)->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao(0, '', $table)->count($params);
        return array($total, $ret);
    }

    /**
     * 读取一条搜索关键词信息
     * @param float $id
     * @return bool|mixed
     */
    public static function getKey($id) {
        return self::_getDao($id)->get(floatval($id));
    }

    /**
     * 通过key获取搜索关键词
     * @param $key
     * @return bool|mixed
     */
    public static function getByKey($s){
        $s = trim($s);
        if(empty($s)) return false;
        $id = self::_hash_crc32($s);
        return self::getKey($id);
    }


    /**
     * get key info by key
     * @param array $params
     * @param int $table 1-10表
     * @return bool|mixed
     */
    public static function getBy($params, $table = 0) {
        if(!is_array($params)) return false;
        $data = self::_cookData($params);
        if($table){
            return self::_getDao(0, '', $table)->getBy($data);
        }else{
            unset($data['s']);
            if(!isset($data['id']) || !$data['id']) return false;
            return self::_getDao($data['id'])->getBy($data);
        }
    }

    /**
     * get keys by params
     * @param array $params
     * @param int $table
     * @param array $sort
     * @return array|bool
     */
    public static function getsBy($params, $table = 0, $sort = array()) {
        if(!is_array($params)) return false;
        if($table){
            if(!is_array($sort)) return false;
            return self::_getDao(0, '', $table)->getsBy($params, $sort);
        }else{
            if(!isset($params['s'])) return false;
            $skeys = array();
            if(is_array($params['s'])) {
                $skeys = $params['s'][1];
            }else{
                $skeys[] = $params['s'];
            }
            unset($params['s']);
            $skey_r = array();
            foreach($skeys as $key){
                $params['s'] = $key;
                $skey = self::getBy($params);
                if($skey) array_push($skey_r, $skey);
            }
            return $skey_r;
        }
    }

    /**
     * 更新搜索关键词信息
     * @param array $data
     * @param float $id
     * @return bool|int
     */
    public static function updateKey($data, $id) {
        if (!is_array($data) || !$id) return false;
        $data = self::_cookData($data);
        return self::_getDao($id)->update($data, $id);
    }

    /**
     * 更新搜索关键词信息
     * @param array $data
     * @param array $params
     * @return bool
     */
    public static function updateKeyBy($data, $params) {
        if (!is_array($data) || !is_array($params) || !isset($params['s'])) return false;
        $params['s'] = trim($params['s']);
        if(empty($params['s'])) return false;

        $data = self::_cookData($data);
        $params = self::_cookData($params);
        unset($params['s']);
        if(!isset($params['id']) || !$params['id']) return false;
        return self::_getDao($params['id'])->updateBy($data, $params);
    }

    /**
     * increment count by key
     * @param $key
     * @return bool|int
     */
    public static function incrementCountByKey($s){
        $s = trim($s);
        if(empty($s)) return false;
        $id = self::_hash_crc32($s);
        return self::_getDao($id)->increment('count', array('id' => $id), 1);
    }

    /**
     * del key
     * @param float $id
     * @return bool|int
     */
    public static function deleteKey($id) {
        return self::_getDao($id)->delete(floatval($id));
    }

    /**
     * del key by key
     * @param string $s
     * @return bool|int
     */
    public static function deleteKeyByKey($s) {
        $s = trim($s);
        if(empty($s)) return false;
        $id = self::_hash_crc32($s);
        self::deleteKey($id);
    }

    /**
     * add key
     * @param search key
     * @return bool|string
     */
    public static function addKey($key) {
        if (empty($key)) return false;
        $key_ext = self::getByKey($key);
//        Common::log($key_ext, 'search.log');
        if($key_ext) return self::incrementCountByKey($key);

        $data = self::_cookData(array('s' => trim($key)));
        $data['count'] = 1;
        $data['create_time'] = Common::getTime();
        $ret = self::_getDao($data['id'])->insert($data);
        if (!$ret) return false;
        return $data['id'];
    }

    /**
     * @param $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['id'])) $tmp['id'] = floatval($data['id']);
        if (isset($data['s'])) {
            $data['s'] = trim($data['s']);
            $tmp['id'] = self::_hash_crc32($data['s']);
            $tmp['s'] = $data['s'];
        }
        if (isset($data['count'])) $tmp['count'] = intval($data['count']);
        if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
//        Common::log($tmp, 'search.log');
        return $tmp;
    }

    /**
     * @param $key string
     * @param $id int
     * @param $table int
     * @return Dhm_Dao_Search
     */
    private static function _getDao($id = 0, $s = '', $table = 0) {
        if($table > 0 && $table <= 10){
            $hash = $table-1;
        }else{
            if(empty($s) && empty($id)) return false;
            $hash = $id ? $id : self::_hash_crc32($s);
        }
        $dao = new Dhm_Dao_Search();
        $dao->hash = $hash;
        return $dao;
    }

    /**
     * 通过key进行hash返回对应的ID
     * @param $key
     * @return float|bool
     */
    private static function _hash_crc32($s){
        $s = trim($s);
        if($s) return floatval(sprintf('%u', crc32(md5($s))));
        return false;
    }
}
