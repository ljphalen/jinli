<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_Service_Favorite{

    const FAV_TYPE_STORY = 1;
    const FAV_TYPE_GOODS = 2;
    const FAV_TYPE_SHOP  = 3;
    const FAV_TYPE_WEB   = 4;

    public static $type=array(
         1=>'知物',
         2=>'商品',
         3=>'店铺',
         4=>'网页'
    );

/**
 * 
 * @param unknown_type $uid
 * @param unknown_type $fid
 * @return multitype:unknown
 */
    public static function getStoryListByUid($uid, $fid){
        list($total,$list) = self::getsBy(array('uid'=>$uid, 'type'=>1, 'item_id'=>array('IN', $fid)), array());
        return Common::resetKey($list,'item_id');
    }

    public static function getImageUrl($image){
        $staticroot = Common::getAttachPath();
        if(strpos($image, 'http://')===false&&!empty($image))$image = $staticroot.$image;
        return $image;
    }
    /**
     * 
     * @param unknown_type $uid
     * @param unknown_type $item_id
     * @param unknown_type $type
     * @return boolean
     */
    public static function getOneByParams($uid,$item_id,$type=1){
        $ret = self::getBy(array('uid'=>$uid,'type'=>1,'item_id'=>$item_id));
        return !empty($ret);
    }


	public static function getCount($params=array()){
		return self::_getDao()->getCountByDay($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array('id'=>'DESC')) {
	    $params = self::_cookData($params);
	    if ($page < 1) $page = 1;
	    $start = ($page - 1) * $limit;
	    $ret = self::_getDao()->getList($start, $limit, $params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
	}

	public static function getField($page = 1, $limit = 10,$fields=array(),$params=array(),$orderBy=array()){
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getFields($start, $limit,$fields, $params, $orderBy);
//		$total = self::_getDao()->countByFields($fields, $params);
		return array(array(), $ret);
	}

	public static function getCountWithField($fields,$params){
		return self::_getDao()->countByFields($fields, $params);
	}
	/**
	 * update reduce && price
	 * @param int   $item_id goods id
	 * @param float $cur_price price got last
	 * @param bool  $is_reduce last price vs current price in db
	 * @return bool
	 */
	public static function updatePrice($item_id, $cur_price, $is_reduce = false){
		if($cur_price<=0) return false;
		self::_getDao()->updateBy(array('price' => $cur_price), array('item_id' => $item_id, 'price' => 0));
		if(!$is_reduce){
			return true;
		}
		$data['status'] = 1;
		$condition['item_id'] = $item_id;
		$condition['price'] = array('>', $cur_price);
		$condition['create_time'] = array("<", strtotime(date("Y-m-d", strtotime("-1Day"))));
		self::_getDao()->updateBy($data, $condition);
		$items = self::_getDao()->getsBy(array('item_id' => $item_id, 'price' => array('>',$cur_price), 'create_time'=>$condition['create_time']));
		foreach ($items as $item) {
			self::update(array('diff_price'=>$item['price']-$cur_price),$item['id']);
		}

		return true;
	}


	/**
	 *
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @param array $sort
	 * @return array
	 */
	public static function getUniqueList($page = 1, $limit = 10, $params = array(), $sort=array('id'=>'ASC')) {
	    $params = self::_cookData($params);
	    if ($page < 1) $page = 1;
	    $start = ($page - 1) * $limit;
	    $ret = self::_getDao()->getUniqueList($start, $limit, $params,$sort);
	    $total = self::_getDao()->uniqueCount($params);
	    return array($total, $ret);
	}


	/**
	 * @param $data
	 * @return bool|string
	 */
	public static function addFavorite($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
        if(empty($data['create_time']))$data['create_time'] = Common::getTime();
		$res = self::_getDao()->insert($data);
        if(!$res)return false;
        return self::_getDao()->getLastInsertId();
	}

    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

	public static function getBy($params = array(),$orderBy=array()) {
	    if(!is_array($params)) return false;
		$params = self::_cookData($params);
	    return self::_getDao()->getBy($params,$orderBy);
	}
	

	/**
	 * @param $params
	 * @param $sort
	 * @return array|bool
	 */
	public static function getsBy($params, $sort = array()) {
	    if (!is_array($params) || !is_array($sort)) return false;
	    $ret = self::_getDao()->getsBy($params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
	}
	

	/**
	 * @param $data
	 * @param $id
	 * @return bool|int
	 */
	public static function update($data, $id) {
	    if (!is_array($data)) return false;
	    $data = self::_cookData($data);
	    //用于cron spider update type
	    if($data['request_count'] >= 3) unset($data['type']);
	    return self::_getDao()->update($data, intval($id));
	}



	/**
	 * @param $data
	 * @param $params
	 * @return bool
	 */
	public static function updateBy($data, $params) {
	    if (!is_array($params)) return false;
	    $data = self::_cookData($data);
	    return self::_getDao()->updateBy($data,$params);
	}
	

	/**
	 * @param $id
	 * @return bool|int
	 */
	public static function deleteFavorite($id) {
	    return self::_getDao()->delete(intval($id));
	}

	/**
	 * 设置用户收藏商品列表的降价状态为0
	 * @param int $uid
	 * @return bool
	 */
	public static function removeReduce($uid = 0){
		return self::_getDao()->updateBy(array('status'=>0), array('uid'=>$uid, 'type'=>2));
	}


	/**
	 * @param $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
		if(isset($data['url'])) $tmp['url'] = $data['url'];
		if(isset($data['channel'])) $tmp['channel'] = $data['channel'];
		if(isset($data['channel_id'])) $tmp['channel_id'] = $data['channel_id'];
		if(isset($data['type'])) $tmp['type'] = $data['type'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['diff_price'])) $tmp['diff_price'] = $data['diff_price'];
		if(isset($data['item_id'])) $tmp['item_id'] = $data['item_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return User_Dao_Favorite
	 */
	private static function _getDao() {
		return Common::getDao("User_Dao_Favorite");
	}
}
