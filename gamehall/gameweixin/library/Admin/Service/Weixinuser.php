<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Admin_Service_Weixinuser
 * @author wupeng
 *
 */
class Admin_Service_Weixinuser {
	
	/**
	 * 
	 * @author wupeng
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $perpage = 20, $params = array()) {
		if ($page < 1) {
		    $page = 1;
		}
		$start = ($page -1) * $perpage;
		$list = self::getDao()->getList(intval($start), intval($perpage), $params, array('subscribe_time' => 'DESC'));
		$total = self::getDao()->count($params);
		return array($total, $list);
	}
	
	/**
	 * 
	 * @author wupeng
	 * @param unknown $params
	 */
	public static function getTotal($params = array()) {
	    return self::getDao()->count($params);
	}
	
	/**
	 * 
	 * @author wupeng
	 * @param int $id
	 */
	public static function delete($id) {
		return self::getDao()->delete(intval($id));
	}
	
	public static function deleteByOpenId($openId) {
	    $params = array(
	                    'open_id' => $openId
	    );
	    return self::getDao()->deleteBy($params);
	}
	
	/**
	 *
	 * @author wupeng
	 * @param 数组 $params
	 */
	public static function add($params) {
		return self::getDao()->insert($params);
	}

	public static function update($data, $params) {
	    return self::getDao()->updateBy($data, $params);
	}
	
	public static function updateById($data, $id) {
	    return self::getDao()->update($data, $id);
	}
	
	/**
	 *
	 * @author wupeng
	 * @param 数组 $params
	 */
	public static function edit($id, $params) {
		return self::getDao()->update($params, $id);
	}
	
	/**
	 * @author wupeng
	 * @param unknown $id
	 */
	public static function getById($id) {
		$news = self::getDao()->get(intval($id));
		return $news;
	}
	
	/**
	 * @author wupeng
	 * @param unknown $id
	 */
	public static function getByOpenId($openId) {
		$params = array("open_id"=>$openId);
		$user = self::getDao()->getBy($params);
		return $user;
	}
    
	/**
	 * 获取绑定的数量
	 * @param unknown $params
	 * @return string
	 */
	public static function getBindedTotal() {
	    $params = array('is_binded' => 1);
	    return self::getDao()->count($params);
	}
	
	public static function getLoginUrl($openId) {
	    $webroot = Yaf_Application::app()->getConfig()->webroot;
	    return $webroot.'/front/login/login?token='.$openId;
	}
	
	/***
	 * 获取游戏相关数据
	* @param unknown $uuid
	* @return mixed
	*/
	public static function getBindInfo($uuid) {
	    $gameRoot = Common::getConfig('apiConfig', 'gameApiRoot');
	    $gameSecreKey = Common::getConfig('siteConfig', 'gameSecreKey');
	    $data = array (
	                    'puuid' => $uuid,
	                    'token' => md5($gameSecreKey.$uuid),
	    );
	    $curl = new Util_Http_Curl($gameRoot.'/api/weixin_account/mybalance');
	    $curl->setData($data);
	    $result = $curl->send('GET');
	    $resultData = json_decode($result, true);
	    return $resultData['data'];
	}

	/**
	 * 获取还未加载头像的list
	 */
	public static function getUnloadImgList($limit = 20) {
	    return self::getDao()->getUnloadImgList(0, $limit, array('subscribe_time' => 'DESC'));
	}
	
	/**
	 *
	 * @author wupeng
	 * @return Admin_Dao_WeixinUser
	 */
	private static function getDao() {
	    return Common::getDao("Admin_Dao_Weixinuser");
	}
}