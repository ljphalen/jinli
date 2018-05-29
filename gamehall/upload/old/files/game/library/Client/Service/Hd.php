<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Hd{
    
    const STATUS_CLOSE = 0;
    const STATUS_OPEN = 1;
    const HD_TYPE_DEFAULT = 0;
    const HD_TYPE_COMMENT = 1;
    
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllHd() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC','update_time'=>'DESC'), $offset) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		if($offset) $start = $offset + ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getCount($params = array()) {
		return self::_getDao()->count($params);
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getHdByIds($params=array()){
		return self::_getDao()->getsBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getHd($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateHdStatus($data,$statu) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$ret = self::_getDao()->update(array('status'=>$statu), $value);
		}
		return $ret;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateHdSort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getsByHd($params,$orderBy = array('id' => 'ASC')) {
		$total = self::_getDao()->count($params);
		$ret =  self::_getDao()->getsBy($params,$orderBy);
		return array($total, $ret);
	}
	
	
	public static function getOnlineActivityInfo() {
		$params = array('status' => 1);
		$params['start_time'] = array('<=', Common::getTime());
		$params['end_time'] = array('>=', Common::getTime());
		return self::_getDao()->getBy($params,array('id'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateHd($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteHd($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addHd($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$data['create_time'] = Common::getTime();
		return self::_getDao()->insert($data);
	}
	
	public static function updateBy($data, $params) {
	    if (!is_array($data)) return false;
	    return self::_getDao()->updateBy($data, $params);
	}
	
	public static function updateByGameStatus($gameId, $status) {
	    $hdList = array();
	    if($status == Resource_Service_Games::STATE_OFFLINE) {
	        $params = array('game_id' => $gameId, 'status' => self::STATUS_OPEN);
	        $hdList = self::getsBy($params);
	    }
	    self::updateBy(array('status'=>$status), array('game_id'=>$gameId));
        foreach ($hdList as $hd) {
            self::hdClosed($hd['id']);
        }
	}

	public static function getsBy($params) {
	    return self::_getDao()->getsBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['hd_type'])) $tmp['hd_type'] = intval($data['hd_type']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['placard'])) $tmp['placard'] = $data['placard'];
		if(isset($data['award'])) $tmp['award'] = $data['award'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Hd
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Hd");
	}
	
	/**活动被关闭*/
	public static function hdClosed($hdId) {
	    try {
	        Game_Service_RecommendList::updateHDStatus($hdId, self::STATUS_CLOSE);
	        Game_Service_GameWebRecommend::updateHDStatus($hdId, self::STATUS_CLOSE);
	        Game_Service_SingleRecommend::updateHDStatus($hdId, self::STATUS_CLOSE);
	    } catch (Exception $e) {
	    }
	}
	
}
