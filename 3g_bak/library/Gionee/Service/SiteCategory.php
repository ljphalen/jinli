<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *网址大全 分类
 */
class Gionee_Service_SiteCategory {

	static $styles = array(
		'hotnav' => '热门站点样式',
		'image'  => '图文样式',
		'word4'  => '四列内容样式',
		'word5'  => '五列内容样式',
		'ads'    => '广告图样式',
	);

	public static function add($params) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->insert($params);
	}

	public static function update($id, $params = array()) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->update($params, $id);
	}

	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}

	public static function getsBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function getAll() {
		return self::_getDao()->getAll();
	}

	public static function getList($page, $pageSize, $params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		$count  = self::_getDao()->count($params);
		$data   = self::_getDao()->getList($pageSize * ($page - 1), $pageSize, $params, $orderBy);
		return array($count, $data);
	}

	public static function delete($id) {
		if (!intval($id)) return false;
		return self::_getDao()->delete($id);
	}

	public static function getAllIds($params) {
		if (!is_array($params)) return false;
		$data = self::getsBy($params);
		$temp = array();
		foreach ($data as $v) {
			$temp[] = $v['id'];
		}
		return $temp;
	}


	public static function getCategoryData($parentId = 0) {
		$rcKey = 'SiteCategory:' . $parentId;
		$list  = Common::getCache()->get($rcKey);
		if ($list == false) {
			$where              = array();
			$where['parent_id'] = $parentId;
			$where['status']    = 1;
			$list               = Gionee_Service_SiteCategory::getsBy($where, array('sort' => 'ASC', 'id' => 'ASC'));
			Common::getCache()->set($rcKey, $list, 600);
		}
		return $list;
	}

	private static function _checkData($data = array()) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['parent_id'])) $tmp['parent_id'] = $data['parent_id'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['is_show'])) $tmp['is_show'] = $data['is_show'];
		if (isset($data['add_time'])) $tmp['add_time'] = $data['add_time'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['image'])) $tmp['image'] = $data['image'];
		if (isset($data['style'])) $tmp['style'] = $data['style'];
		return $tmp;
	}

	private static function _getDao() {
		return Common::getDao("Gionee_Dao_SiteCategory");
	}
}