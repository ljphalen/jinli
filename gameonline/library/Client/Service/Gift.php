<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_Service_Gift extends Common_Service_Base{
	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllGift() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 
	 * @param int $page
	 * @param int $limit
	 * @param params $params
	 * @return multitype:unknown
	 */
	public static function getList($page, $limit, $params = array()) {
		$params = self::_cookData($params);
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort'=>'DESC', 'id' => 'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getGiftByGameIds($params) {
		if (!isset($params)) return false;
		return self::_getDao()->getGiftByGameIds($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getOnlineGifts() {
		$params = array('status' => 1, 'game_status'=>1);
		$params['effect_start_time'] = array('<=', Common::getTime());
		$params['effect_end_time'] = array('>=', Common::getTime());
		return self::_getDao()->getsBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getBy($params = array()) {
		return self::_getDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getsBy($params = array()) {
		return self::_getDao()->getsBy($params);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGift($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 通过游戏id 统计可用礼包数量
	 * @param unknown $gameId
	 * @return boolean|string
	 */
	public static function countCanUseGiftByGameId($gameId) {
		if (!intval($gameId)) return false;
		$params = array(
			'game_id' => intval($gameId),
			'status' => 1,
			'effect_start_time' => array('<=', Common::getTime()),
			'effect_end_time' => array('>', Common::getTime())				
		);
		return self::_getDao()->count($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftByGift($gift_id) {
		if (!intval($gift_id)) return false;
		return self::_getDao()->getBy(array('gift_id'=>$gift_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGiftByGameId($game_id) {
		if (!intval($game_id)) return false;
		return self::_getDao()->getBy(array('game_id'=>intval($game_id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGift($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $code
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateActivationCode($code, $id) {
		if (!$code && !$id) return false;
		return self::_getDao()->update(array('activation_code'=>$code),$id);
	}
	
	/**
	 * 
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateGiftGameId($status,$game_id) {
		if (!$game_id) return false;
		return self::_getDao()->updateBy(array('game_status'=>intval($status)), array('game_id'=>intval($game_id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateGiftStatus($sorts, $status) {
		if (!is_array($sorts)) return false;
	    foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('status'=>$status), $value);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function batchSortByGift($sorts) {
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
	public static function replaceGift($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->replace($data);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteGift($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $code
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteGiftActivationCode($code,$id) {
		return self::_getDao()->deleteBy(array('activation_code'=>$code),$id);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGift($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret  =  self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addGiftGame($data,$codes) {
		if (!is_array($data)) return false;
	    //开始事务
		$trans = parent::beginTransaction();
		try {
			//添加礼包
			$gift_id = self::addGift($data);
			if (!$gift_id) throw new Exception("Add Gift fail.", -202);
			
			//添加礼包领取记录
			if($codes){
				$tmp = array();
				foreach($codes as $key=>$value){
					$game_info = array();
					if($value){
						$tmp[] = array(
								'id'=>'',
								'gift_id'=>$gift_id,
								'game_id'=>$data['game_id'],
								'uname' => '',
								'imei'=>'',
								'imeicrc'=>'',
								'activation_code'=>$value,
								'create_time'=>'',
								'status'=>0
						);
					}
				}
				$ret = self::_getGiftlogDao()->mutiInsert($tmp);
				if (!$ret) throw new Exception('Add Giftlog fail.', -205);
			}
							
			//事务提交
			if($trans) {
				parent::commit();
				return true;
			}
		} catch (Exception $e) {
			parent::rollBack();
			print_r($e->getMessage());
			return false;
		}
		return true;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function updateGiftGame($data, $id, $game_id, $codes, $logs) {
		if (!is_array($data)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新礼包
			$ret = self::updateGift($data,$id);
			if (!$ret) throw new Exception("Update Gift fail.", -201);
			
			if($logs){
				foreach($logs as $key=>$value){
					if(!in_array($value,$codes)){                       //判断是否为新的激活码
						$log = Client_Service_Giftlog::getByActivationCode($value);
						if($log){
							$ret = Client_Service_Giftlog::deleteGiftlogByActivationCcode($value); //不存在的激活码删除
							if (!$ret) throw new Exception("Delete Giftlog fail.", -201);
						} 
						
					}
				}
			}
			
			//更更新领取记录游戏id
			$ret_game = Client_Service_Giftlog::updateGiftLogGameId($game_id,$data['game_id'],$id);
			if (!$ret_game) throw new Exception("Update Giftlog fail.", -201);
			
			$tmp = array();
			foreach($codes as $k=>$v){
				$game_info = array();
				if(!in_array($v,$logs)){                              //判断是否为新的激活码
					if($v){
						$tmp[] = array(
									'id'=>'',
									'gift_id'=>$id,
								    'game_id'=>$data['game_id'],
									'uname' =>'',
									'imei'=>'',
									'imeicrc'=>'',
									'activation_code'=>$v,
									'create_time'=>'',
									'status'=>0,
						);
					}
				}
			}
			if($tmp){
				$ret = self::_getGiftlogDao()->mutiInsert($tmp);       //新的激活码就添加
				if (!$ret) throw new Exception('Add Giftlog fail.', -205);
			}
			
			//事务提交
			if($trans) {
				parent::commit();
				return true;
			}
		} catch (Exception $e) {
			parent::rollBack();
			print_r($e->getMessage());
			return false;
		}
		return true;
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
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['activation_code'])) $tmp['activation_code'] = $data['activation_code'];
		if(isset($data['method'])) $tmp['method'] = $data['method'];
		if(isset($data['use_start_time'])) $tmp['use_start_time'] = $data['use_start_time'];
		if(isset($data['use_end_time'])) $tmp['use_end_time'] = $data['use_end_time'];
		if(isset($data['effect_start_time'])) $tmp['effect_start_time'] = $data['effect_start_time'];
		if(isset($data['effect_end_time'])) $tmp['effect_end_time'] = $data['effect_end_time'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Gift
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Gift");
	}
	
	/**
	 *
	 * @return Client_Dao_Giftlog
	 */
	private static function _getGiftlogDao() {
		return Common::getDao("Client_Dao_Giftlog");
	}
}
