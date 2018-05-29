<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Resource_Service_Img{

    const SCREEN_240_400 = '240_400';
    const SCREEN_480_800 = '480_800';
    const SCREEN_720_1280 = '720_1280';
    const SCREEN_TYPE_720_1280 = 1;
    const SCREEN_TYPE_480_800 = 0;
    /**
     * 获取游戏不同尺寸的截图
     * @param $gameId
     * @return array
     */
    public static function getScreenImg($gameId){
        $imgData = self::getsBy(array('game_id'=>$gameId));
        if(!$imgData){
            return array();
        }
        $screenImg = $hdImg = $normalImg = $smallImg = array();
        foreach($imgData as $item){
           if($item['type'] == self::SCREEN_TYPE_720_1280){
               $hdImg[] = $item['img'];
            } else {
               $normalImg[] = $item['img'];
               $imgExtension = pathinfo($item['img'], PATHINFO_EXTENSION);
               $smallImg[] = $item['img'] .  '_240x400.' . $imgExtension;
           }
        }
        $screenImg = array(
            self::SCREEN_240_400 => $smallImg,
            self::SCREEN_480_800 => $normalImg,
            self::SCREEN_720_1280 => $hdImg,
        );
        return $screenImg;
    }

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGameImg() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'ASC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	
	public static function getsBy($params , $orderBy = array()){
		if (!is_array($params) || !is_array($orderBy)){
			return false;
		} 
		return self::_getDao()->getsBy($params);
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGameImg($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGameImg($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGameImg($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public static function deleteGameImgByGameId($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->deleteBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGameImg($data) {
		if (!is_array($data)) return false;
		$temp = array();
        foreach($data as $key=>$value) {
            $temp[] = array(
                'game_id' => $value['game_id'],
                'type' => $value['type'],
                'img' => $value['img']
            );
        }
		$ret = self::_getDao()->mutiFieldInsert($temp);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId(); 
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
        if(isset($data['type'])) $tmp['type'] = $data['type'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Resource_Dao_Img
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Img");
	}
}
