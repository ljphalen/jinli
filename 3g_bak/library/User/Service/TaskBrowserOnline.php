<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class User_Service_TaskBrowserOnline {
	/**
	 *
	 * @return User_Dao_TaskBrowserOnline
	 */
	public static function getDao() {
		return Common::getDao("User_Dao_TaskBrowserOnline");
	}


	static public function getStageTime() {
		$conf = Gionee_Service_Config::getValue('browser_online_time');
		$tmp = explode("\n",$conf);
		$ret = array();
		$i = 1;
		foreach($tmp as $val) {
			list($k,$v) = explode(',',trim($val));
			if (!empty($k) && !empty($v)) {
				$ret[$i] = array($k,trim($v));
				$i++;
			}
		}
		return $ret;
	}
}

