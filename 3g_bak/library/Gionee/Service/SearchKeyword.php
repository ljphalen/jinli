<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


class Gionee_Service_SearchKeyword {


	/**
	 * @return Gionee_Dao_SearchKeyword
	 */
	static public function getDao() {
		return Common::getDao('Gionee_Dao_SearchKeyword');
	}


	static public function all($sync = false) {
		$rcKey = 'SEARCH_KEYWORD_LIST';
		$ret   = Common::getCache(1)->get($rcKey);
		if (empty($ret) || $sync) {
			$list = self::getDao()->getsBy();
			$ret  = array();
			foreach ($list as $val) {
				$ret[$val['from']] = $val['url'];
			}
			Common::getCache(1)->set($rcKey, $ret, Common::T_ONE_DAY);
		}
		return $ret;
	}

}