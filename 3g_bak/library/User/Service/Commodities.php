<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_Commodities {

	public static function add($params) {
		if (empty($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}


	public static function getList($page, $pageSize, $where = array(), $orderBy = array()) {
		if (!is_array($where)) return flase;
		$page = max($page, 1);
		return array(self::_getDao()->count($where), self::_getDao()->getList($pageSize * ($page - 1), $pageSize, $where, $orderBy));
	}

	public static function get($id) {
		if (!is_numeric($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getGoodsList($sync=false){
		$key = "USER:ALL:GOODS:LIST:";
		$data = Common::getCache()->get($key);
		if(empty($data) || $sync == true){
			$params = array();
			$params['start_time'] = array('<=', time());
			$params['end_time']   = array('>=', time());
			$params['status']     = 1;
			$params['event_flag'] = 0;
			$data = self::getsBy($params,array('sort'=>'DESC','id'=>'DESC'));
			Common::getCache()->set($key,$data,3600);
		}
		return $data;
	}
	
	public static function getsBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function getBy($params = array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	public static function update($params, $id) {
		$data = self::_checkData($params);
		return self::_getDao()->update($data, $id);
	}

	public static function delete($id) {
		return self::_getDao()->delete($id);
	}

	private static function _checkData($params) {
		$temp = array();
		if (isset($params['cat_id'])) $temp['cat_id'] = $params['cat_id'];
		if (isset($params['name'])) $temp['name'] = $params['name'];
		if (isset($params['link'])) $temp['link'] = $params['link'];
		if (isset($params['number'])) $temp['number'] = $params['number'];
		if (isset($params['goods_type'])) $temp['goods_type'] = $params['goods_type'];
		if (isset($params['scores'])) $temp['scores'] = $params['scores'];
		if (isset($params['start_time'])) $temp['start_time'] = strtotime($params['start_time']);
		if (isset($params['end_time'])) $temp['end_time'] = strtotime($params['end_time']);
		if (isset($params['add_time'])) $temp['add_time'] = $params['add_time'];
		if (isset($params['add_user'])) $temp['add_user'] = $params['add_user'];
		if (isset($params['edit_time'])) $temp['edit_time'] = $params['edit_time'];
		if (isset($params['image'])) $temp['image'] = $params['image'];
		if (isset($params['description'])) $temp['description'] = $params['description'];
		if (isset($params['status'])) $temp['status'] = $params['status'];
		if (isset($params['sort'])) $temp['sort'] = $params['sort'];
		if (isset($params['price'])) $temp['price'] = $params['price'];
		if (isset($params['is_special'])) $temp['is_special'] = $params['is_special'];
		if (isset($params['card_info_id'])) $temp['card_info_id'] = $params['card_info_id'];
		if(isset($params['virtual_type_id'])) $temp['virtual_type_id'] = $params['virtual_type_id'];
		if(isset($params['title']))				$temp['title'] = $params['title'];
		if(isset($params['event_flag']))		$temp['event_flag'] = $params['event_flag'];
		if(isset($params['show_number']))		$temp['show_number'] = $params['show_number'];
		if(isset($params['num_ratio']))		$temp['num_ratio'] = $params['num_ratio'];
		return $temp;
	}

	private static function _getDao() {
		return Common::getDao('User_Dao_Commodities');
	}
}