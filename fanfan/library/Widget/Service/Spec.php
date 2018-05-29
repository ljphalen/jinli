<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Spec {

	/**
	 * 获取机型列表
	 */
	public static function all($orderBy = array()) {
		static $list = null;

		if ($list == null) {
			$tmpList = self::_getDao()->getAll($orderBy);
			$list    = array(
				0 => '默认'
			);
			foreach ($tmpList as $val) {
				$list[$val['id']] = $val['name'];
			}
		}

		return $list;
	}


	public static function getTypes() {
		$ret = self::_getDao()->getTypes();
		return $ret;
	}

	public static function getTotal() {
		$total = self::_getDao()->count();
		return $total;
	}


	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $orderBy=array()) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret   = self::_getDao()->getsBy($params, $orderBy);
		return $ret;
	}

	/**
	 * @param array $data
	 * @param int $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	public static function getAll() {
		$list = array(array('id'=>0,'name'=>'默认','type'=>'默认'));
		$tmp = self::_getDao()->getAll();
		foreach($tmp as $val) {
			$list[] = $val;
		}
		return $list;
	}

	public static function getIdByName($name = '') {
		$ret = 0;
		if (!empty($name)) {
			$info = self::_getDao()->getBy(array('name' => $name));
			if (!empty($info['id'])) {
				$ret = $info['id'];
			}
		}
		return $ret;
	}

	public static function getUrlByName($name) {
		$ret = array();
		if (!empty($name)) {
			$info = self::_getDao()->getBy(array('name' => $name));
			if (!empty($info['url'])) {
				$ret = json_decode($info['url'], true);
			}
		}
		return $ret;
	}

	/**
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array('name','type','url');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	/**
	 * @return Widget_Dao_Spec
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Spec");
	}
}
