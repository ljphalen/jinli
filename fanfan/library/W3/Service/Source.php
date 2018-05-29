<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class W3_Service_Source {

	static public function getUserAdPos($imei) {
		$nowD = date('Ymd');
		$key  = 'ADPOS:' . crc32($nowD . $imei);
		return Common::getCache()->get($key);
	}

	static public function setUserAdPos($imei, $adPosArr = array()) {
		$nowD = date('Ymd');
		$key  = 'ADPOS:' . crc32($nowD . $imei);
		return Common::getCache()->set($key, $adPosArr, 86400);
	}

	static public function filterIds($columnIdStr) {
		$columnIdArr = explode(',', $columnIdStr);
		sort($columnIdArr);
		$columnIdStr = implode(',', $columnIdArr);
		$rcKey = Common::KN_W3_IDS . crc32($columnIdStr);
		$ret   = Common::getCache()->get($rcKey);
		if (!empty($ret) && Common::TO_CACHE) {
			return $ret;
		}

		$urlIds = $tmpIds = array();
		foreach ($columnIdArr as $columnId) {
			$row      = W3_Service_Column::getSourceIdsByColumnId($columnId);
			if (!empty($row['ids'])) {
				$tmpIds   = array_merge($tmpIds, $row['ids']);
				$urlIds[] = $row['url_id'];
			}
		}
		rsort($tmpIds);
		$idsArr = array_slice($tmpIds, 0, Widget_Service_Source::RET_SORT_BASE_NUM);
		//置顶的人工数据
		$topList = Widget_Service_Source::getTopIds();
		$ret     = array($urlIds, $topList, $idsArr);
		Common::getCache()->set($rcKey, $ret, Common::KT_IDS);
		return $ret;
	}

}