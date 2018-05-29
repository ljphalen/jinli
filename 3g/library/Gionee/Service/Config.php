<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author rainkid
 *
 */
class Gionee_Service_Config {

	/**
	 *
	 * get all configs
	 */
	public static function getAllConfig() {
		$ret  = self::_getDao()->getAll();
		$temp = array();
		foreach ($ret as $key => $value) {
			$temp[$value['3g_key']] = $value['3g_value'];
		}
		return $temp;
	}

	/**
	 *
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$rcKey = '3G_CONFIG_KEY:' . $key;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret === false) {
			$info = self::_getDao()->getBy(array('3g_key' => $key));
			$ret = $info['3g_value'];
			Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
		}
		return $ret;

	}

	/**
	 *
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	public static function getsBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params);
	}

	/**
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function setValue($key, $value) {
		if (!$key) return false;
		$rcKey = '3G_CONFIG_KEY:' . $key;
		Common::getCache()->delete($rcKey);
		return self::_getDao()->updateByKey($key, $value);
	}

	/**
	 *
	 * @return Gionee_Dao_Config
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Config");
	}

	public static function version_static($fid) {
		$key  = 'VERSION_STATIC:' . $fid;
		$data = Common::getCache()->get($key);
		if (!empty($data)) {
			return json_decode($data, true);
		}

		$readers = Common::getConfig('readerConfig');
		$file    = $readers[$fid];
		$finfo   = pathinfo($file);
		$content = file_get_contents($file);
		$ret     = array($content, $finfo);
		Common::getCache()->set($key, json_encode($ret), Common::T_ONE_DAY);
		return $ret;
	}
}
