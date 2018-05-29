<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author Terry
 *
 */
class Craw_Service_Goods{

    public static $channel=array(
        1=>'天猫',
        2=>'京东',
    );

    public static $status = array(
        0=>'未抓取',
        1=>'抓取中',
        2=>'已抓取',
        4=>'已下架',
    );

    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;

        list($total,$goods) = Craw_Service_Index::getList($page  , $limit , $params, $orderBy);
        foreach ($goods as $v) {
            $tmp  =static::get($v['item_id']);
            $tmp["data"] = json_decode($tmp["data"], true);
            if(!empty($tmp))$rows[] = $tmp;
        }

        return array($total, $rows);
    }

    /**
     * @param $data
     * @return bool|string
     */
    public static function addGoods($data) {
        if (!is_array($data)) return false;

        //过滤数据
        $data = self::_cookData($data);
        $data['update_time'] = Common::getTime();

        //如果已经存在，直接更新数据
        $ret = self::_getDao($data["item_id"])->getBy(array("item_id" => $data["item_id"]));

        //更新索引表
        Craw_Service_Index::replace($data);
        if ($ret) {
            self::_getDao($data["item_id"])->updateBy($data, array("item_id" => $data["item_id"]));
            return $ret["id"];
        }

        //新插入的数据 ,插入索引表
        $res = self::_getDao($data["item_id"])->insert($data);
        if (!$res) {
            return false;
        }
        return self::_getDao($data["item_id"])->getLastInsertId();
    }

    /**
     * @param $data
     * @return bool
     */
    public static function insert($data){
        if (!is_array($data)) return false;
        foreach ($data as $key => $value) {
            self::addGoods($value);
        }
        return true;
    }

    /**
     * @param $item_id
     * @return bool|mixed
     */
    public static function get($item_id) {
        if(!$item_id) return false;
        return self::_getDao($item_id)->getBy(array("item_id"=>$item_id));
    }

    /**
     * @param $data
     * @param $item_id
     * @return bool
     */
    public static function update($data, $item_id) {
        if (!$item_id) return false;
        $data = self::_cookData($data);
        Craw_Service_Index::update($data, $item_id);
        return self::_getDao($item_id)->updateBy($data, array("item_id"=>$item_id));
    }

    /**
     * @param $item_id
     * @return bool
     */
    public static function delete($item_id) {
        return self::_getDao($item_id)->deleteBy(array("item_id"=>$item_id));
    }


    /**
     * @param $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['id']))              $tmp['id']               = $data['id'];
        if(isset($data['sort']))      $tmp['sort']       = $data['sort'];
        if(isset($data['channel_id']))      $tmp['channel_id']       = $data['channel_id'];
        if(isset($data['category_id']))     $tmp['category_id']      = $data['category_id'];
        if(isset($data['item_id']))         $tmp['item_id']          = $data['item_id'];
        if(isset($data['title']))           $tmp['title']            = $data['title'];
        if(isset($data['price']))           $tmp['price']            = Common::money($data['price']);
        if(isset($data['price_pos']))       $tmp['price_pos']            = $data['price_pos'];

        if(isset($data['origi_price']))     $tmp['origi_price']      = Common::money($data['origi_price']);
        if(isset($data['img']))             $tmp['img']              = $data['img'];
        if(isset($data['data']))            $tmp['data']             = json_encode($data['data']);
        if(isset($data['request_count']))   $tmp['request_count']    = $data['request_count'];
        if(isset($data['status']))          $tmp['status']           = $data['status'];
        if(isset($data['sale_num']))     $tmp['sale_num']      = $data['sale_num'];
        if(isset($data['update_time']))     $tmp['update_time']      = $data['update_time'];
        if(isset($data['sort']))            $tmp['sort']             = $data['sort'];
        if(isset($data['zhe']))            $tmp['zhe']             = $data['zhe'];

        if(isset($data['price']) && isset($data['origi_price'])) {
            $tmp['zhe'] = sprintf("%.1f", ($data["price"]/$data["origi_price"]) * 10);
        }
        if (isset($data['price'])) {
            if($data["price"] < 10) $tmp["price_pos"] = 1;
            if($data["price"] >10 && $data["price"] < 20) $tmp["price_pos"] = 2;

        }

        return $tmp;
    }

    /**
     *
     * @return Craw_Dao_Goods
     */
    private static function _getDao($item_id) {
        $dao = new Craw_Dao_Goods();
        $dao->item_id = $item_id;
        return $dao;
    }
}
