<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Visit {


	/**
	 *
	 * @param string $data
	 */
	public static function get($date) {
		return self::_getDao()->get($date);
	}

	/**
	 *
	 * @param array $data
	 * @param string $date
	 */
	public static function set($data, $date) {
		if (!is_array($data)) return false;
		return self::_getDao()->update($data, $date);
	}

	/**
	 *
	 *
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * @return Widget_Dao_Vist
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Visit");
	}
}
