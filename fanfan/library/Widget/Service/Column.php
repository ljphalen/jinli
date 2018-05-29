<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Widget_Service_Column {

	static $types = array(
		1  => '娱乐',
		2  => '视觉',
		3  => '汽车',
		4  => '养生',
		5  => '旅游',
		6  => '体育',
		7  => '财经',
		8  => '笑话',
		9  => '科技',
		10 => '历史',
		11 => '家居',
		12 => '军事',
		10 => '历史',
		11 => '家居',
		12 => '军事',
		13 => '新闻',
		14 => '生活',
		15 => '人文',
		16 => '星座',
		17 => '情感',
	);

	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		return self::_getDao()->get(intval($id));
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

	public static function update($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * @param int $uid
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}


	/**
	 *
	 */
	public static function deleteByType($type) {
		if (!$type) return false;
		return self::_getDao()->deleteByType(intval($type));
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
	 * 批量修改显示状态
	 * @param array $ids
	 * @return boolean
	 */
	public static function updateStatusByIds($ids, $status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateStatusByIds($ids, $status);
	}


	/**
	 * 通过版本号获取栏目列表
	 * @author william.hu
	 * @param string $ver
	 * @return array
	 */
	public static function getListByVer($specId = 0, $urlIds = array()) {
		$rcKey   = Common::KN_WIDGET_COLUMN. crc32($specId . ':' . implode(',', $urlIds));
		$retData = Common::getCache()->get($rcKey);
		if (!empty($retData) && Common::TO_CACHE) {
			return $retData;
		}

		$search = array('status' => 1, 'spec_id' => $specId);
		if ($urlIds) {
			$search['url_id'] = array('IN', $urlIds);
		}
		$columns = self::_getDao()->getsBy($search, array('sort' => 'DESC'));
		$retData = array();
		foreach ($columns as $value) {

			$tName     = isset(Widget_Service_Column::$types[$value['type']]) ? Widget_Service_Column::$types[$value['type']] : $value['type'];
			$retData[] = array(
				'id'           => $value['id'],
				'url_id'       => $value['url_id'],
				'type'         => $tName,
				'title'        => $value['title'],
				'icon'         => Common::getAttachPath() . $value['icon'],
				'summary'      => $value['summary'],
				'sort'         => $value['sort'],
				'is_recommend' => $value['is_recommend'],
				'status'       => $value['status'],
			);
		}
		Common::getCache()->set($rcKey, $retData, Common::KT_COLUMN);

		return $retData;
	}

	public static function getListBySpecId($specId) {
		$param = array('spec_id' => $specId);
		return self::_getDao()->getsBy($param);
	}

	/**
	 * @param array $data
	 */
	private function _cookData($data) {
		$fields = array('title', 'url_id', 'type', 'icon', 'summary', 'sort', 'is_recommend', 'status', 'spec_id');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	/*
	 * 获取栏目对应的ID列表
	 */
	public static function getSourceIdsByColumnId($id, $sync = false) {
		$rcKey = 'WIDGET_COLUMN_IDS:' . $id;
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
	 * @return Widget_Dao_Column
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Column");
	}
}
