<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class W3_Service_Column {

	/**
	 * @param int $id
	 */

	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}


	/*
	 * 获取栏目对应的ID列表
	 */
	public static function getSourceIdsByColumnId($id, $sync = false) {
		$rcKey = 'W3_COLUMN_IDS:' . $id;
		$ret   = Common::getCache()->get($rcKey);
		if (empty($ret) || $sync) {
			$info = self::_getDao()->get($id);
			if (!empty($info['id'])) {
				$ids = Widget_Service_Source::getLimitIdsByUrlId($info['url_id']);
				$ret = array('url_id' => $info['url_id'], 'ids' => $ids);
				Common::getCache()->set($rcKey, $ret, Common::KT_IDS);
			}
		}
		return $ret;
	}

	/*
	 * 同步所有栏目对应的ID列表
	 */
	public static function syncSourceIdsByColumn() {
		$list = self::_getDao()->getsBy();
		foreach($list as $val) {
			self::getSourceIdsByColumnId($val['id'], true);
		}
	}

	/**
	 *
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array(), $orderby = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, $orderby);
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params = array(), $orderBy = array()) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret = self::_getDao()->getsBy($params, $orderBy);
		return $ret;
	}

	/**
	 *
	 * @param array $data
	 */
	public static function add($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * @param array $data
	 */

	public static function set($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * @param int $uid
	 */
	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}


	/**
	 *
	 * @param array $data
	 * @param int $id
	 */
	public static function updateBy($data, $params) {
		if (!is_array($data) || !is_array($params)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}

	/**
	 * 获取过滤的url_id
	 * @author huwei
	 * @param array $columnIdArr 栏目ID数组
	 * @return array
	 */
	public static function getUrlId($columnIdArr) {
		$urlIds  = array();
		$columns = W3_Service_Column::getsBy(array('id' => array('IN', $columnIdArr)));
		foreach ($columns as $val) {
			$urlIds[] = $val['url_id'];
		}
		sort($urlIds);
		return $urlIds;
	}

	public static function getListBySpec($specId = 0) {
		$rcKey   = Common::KN_W3_COLUMN . $specId;
		$retData = Common::getCache()->get($rcKey);
		if (!empty($retData) && Common::TO_CACHE) {
			return $retData;
		}

		$search  = array('status' => 1, 'spec_id' => $specId);
		$columns = self::_getDao()->getsBy($search, array('sort' => 'ASC'));
		$retData = array();
		foreach ($columns as $value) {
			$retData[] = array(
				'id'           => intval($value['id']),
				'url_id'       => intval($value['url_id']),
				'type'         => '',
				'title'        => trim($value['title']),
				'icon'         => Common::getAttachPath() . $value['icon'],
				'summary'      => trim($value['summary']),
				'sort'         => intval($value['sort']),
				'is_recommend' => intval($value['is_recommend']),
				'status'       => intval($value['status']),
			);
		}
		Common::getCache()->set($rcKey, $retData, Common::KT_COLUMN);

		return $retData;
	}

	/**
	 * @param array $data
	 */
	private function _cookData($data) {
		$fields = array('id', 'title', 'url_id', 'type', 'icon', 'summary', 'sort', 'is_recommend', 'status', 'spec_id');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	/**
	 *
	 * @return W3_Dao_Column
	 */
	private static function _getDao() {
		return Common::getDao("W3_Dao_Column");
	}
}
