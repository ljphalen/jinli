<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 反馈消息
 */
class Gionee_Service_VodContent {

	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		return $ret;
	}

	public static function getTotal($params) {
		$total = self::_getDao()->count($params);
		return $total;
	}

	/**
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * @param array $data
	 * @param int   $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) {
			return false;
		}
		return self::_getDao()->update($data, intval($id));
	}

	public static function getBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getBy($params, $orderBy);
	}

	public static function getsBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($params, $orderBy);
	}

	/**
	 * @param array $ids
	 * @param array $data
	 */
	public static function sets($ids, $data) {
		if (!is_array($data) || !is_array($ids)) {
			return false;
		}
		return self::_getDao()->updates('id', $ids, $data);
	}

	/**
	 * @param int $id
	 */
	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) {
			return false;
		}
		$ret = self::_getDao()->insert($data);
		return $ret ? self::_getDao()->getLastInsertId() : 0;
	}

	/**
	 * @return Gionee_Dao_VodContent
	 */
	public static function _getDao() {
		return Common::getDao("Gionee_Dao_VodContent");
	}


	public static function run() {
		$d   = date('Ymd');
		$all = Gionee_Service_VodChannel::getsBy(array('up_date' => array('!=', $d)));
		$vod = new Vendor_Vod();
		foreach ($all as $v) {
			$i    = 0;
			$list = $vod->getListByChannelId($v['channel_id']);
			foreach ($list as $row) {
				$where = array(
					'channel_id' => $row['channel_id'],
					'cont_id'    => $row['cont_id'],
					'day'        => date('Y-m-d')
				);
				$t     = Gionee_Service_VodContent::getBy($where);
				if (empty($t['id'])) {
					Gionee_Service_VodContent::add($row);
					$i++;
				}
			}
			$total = count($list);
			Gionee_Service_VodChannel::set(array('up_date' => $d), $v['id']);
			echo "{$v['channel_id']}:{$total}:{$i}:{$v['id']}\n";
		}
	}

	static public function getListByChannelId($channelId) {
		$vod  = new Vendor_Vod();
		$list = $vod->getListByChannelId($channelId);
		foreach ($list as $row) {
			$t = Gionee_Service_VodContent::getBy(array(
				'channel_id' => $row['channel_id'],
				'cont_id'    => $row['cont_id'],
				'day'        => date('Y-m-d')
			));
			if (empty($t['id'])) {
				Gionee_Service_VodContent::add($row);
			}
		}
		return $list;

	}
}