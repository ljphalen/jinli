<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 内置书签页
 */
class  Gionee_Service_Inbuilt {
	const KEY_INFO = 'INBUILT_INFO:';

	public static function  add($params) {
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}

	public static function getCate() {
		return self::_getDao()->getCate();
	}

	public static function getList($page, $pageSize, $where, $order) {
		$page = max($page, 1);
		return array(self::_getDao()->count($where), self::_getDao()->getList(($page - 1) * $pageSize, $pageSize, $where, $order));
	}

	public static function get($id) {
		return self::_getDao()->get($id);
	}

	public static function edit($params, $id) {
		$data = self::_checkData($params);
		$rcKey = Gionee_Service_Inbuilt::KEY_INFO . $data['key'];
		Common::getCache()->delete($rcKey);
		return self::_getDao()->update($data, $id);
	}

	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	public static function getsBy($params, $order = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $order);
	}

	public static function delete($id) {
		return self::_getDao()->delete($id);
	}

	private static function _checkData($params) {
		$temp = array();
		if (isset($params['id'])) $temp['id'] = $params['id'];
		if (isset($params['name'])) $temp['name'] = $params['name'];
		if (isset($params['cate'])) $temp['cate'] = $params['cate'];
		if (isset($params['key'])) $temp['key'] = $params['key'];
		if (isset($params['url'])) $temp['url'] = $params['url'];
		if (isset($params['status'])) $temp['status'] = $params['status'];
		if (isset($params['sort'])) $temp['sort'] = $params['sort'];
		if (isset($params['image'])) $temp['image'] = $params['image'];
		if (isset($params['model'])) $temp['model'] = $params['model'];
		if (isset($params['version'])) $temp['version'] = $params['version'];
		if (isset($params['operator'])) $temp['operator'] = $params['operator'];
		if (isset($params['usage'])) $temp['usage'] = $params['usage'];
		if (isset($params['start_time'])) $temp['start_time'] = strtotime($params['start_time']);
		if (isset($params['end_time'])) $temp['end_time'] = strtotime($params['end_time']);
		if (isset($params['add_time'])) $temp['add_time'] = $params['add_time'];
		return $temp;
	}

	public static function getByKey($key) {
		$rcKey = Gionee_Service_Inbuilt::KEY_INFO . $key;
		$info  = Common::getCache()->get($rcKey);
		if (empty($info['id'])) {
			$info = Gionee_Service_Inbuilt::getBy(array('key' => $key));
			if (!empty($info['url'])) {
				$info['tourl'] = Common::clickUrl($info['id'], 'INBUILT', $info['url']);
			}
			Common::getCache()->set($rcKey, $info, 86400*7);
		}
		return $info;

	}

	/**
	 * @return Gionee_Dao_Inbuilt
	 */
	private static function _getDao() {
		return Common::getDao('Gionee_Dao_Inbuilt');
	}
}