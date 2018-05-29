<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Gionee_Service_ShortUrl {

	/**
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return array [总数,列表]
	 */
	public static function getList($page, $limit, $params = array()) {
		$start = (max(1, $page) - 1) * $limit;
		$list  = self::_getDao()->getList(intval($start), intval($limit), $params, array('id' => 'DESC'));
		return $list;
	}
	
	public static function getUserIndexIndex($page,$limit,$params = array()) {
		$url   = $params['url'];
		$start = (max(1, $page) - 1) * $limit;
		$list  = self::_getDao()->getUserIndexIndex(intval($start), intval($limit), $url);
		return $list;		
	}

	public static function getTotal($params = array()) {
		$total = self::_getDao()->count($params);
		return $total;
	}

	/**
	 * @param string $data
	 */
	public static function get($date) {
		return self::_getDao()->get($date);
	}

	public static function getBy($param) {
		if (!is_array($param)) return false;
		return self::_getDao()->getBy($param);
	}

	public static function getsBy($param, $orderBy = array()) {
		if (!is_array($param)) return false;
		return self::_getDao()->getsBy($param, $orderBy);
	}

	/**
	 * @param array $data
	 * @param string $date
	 */
	public static function set($data, $id) {
		if (!is_array($data)) return false;
		return self::_getDao()->update($data, $id);
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$ret =  self::_getDao()->insert($data);
		return self::_getDao()->getLastInsertId();
	}

	public static function del($id) {
		if (!$id) return false;
		return self::_getDao()->delete($id);
	}

	public static function check($t) {
		$rc      = Common::getCache(1);
		$k       = 'ToUrl:' . $t;
		$tmpInfo = $rc->get($k);
		if ($tmpInfo == 1) {//防止攻击
			return false;
		}
		if ($tmpInfo) {
			$info = json_decode($tmpInfo, true);
			$id   = $info['id'];
			$type = $info['type'];
			$url  = html_entity_decode($info['_url']);
		} else {
			$val = Gionee_Service_ShortUrl::getBy(array('key' => $t));
			if (!empty($val['mark'])) {//如果缓存失效从数据库拉数据
				$info = json_decode($val['mark'], true);
				$id   = $info['id'];
				$type = $info['type'];
				$url  = html_entity_decode($info['_url']);
				$rc->set($k, $val['mark']);
			} else {//数据库不存在数据 防止数据库被攻击
				$rc->set($k, 1, 60);
			}
		}
		$words = '';
		if (strstr($url, 'word=')) {
			$temp  = explode('word=', $url);
			$words = urldecode($temp[1]);
		}elseif(strpos($url, 'q=')){
			$temp  = explode('q=', $url);
			$words = urldecode($temp[1]);
		}
		return array($id, $type, $url, $words);
	}


	public static function make($t, $redirect, $mark) {
		$val = Gionee_Service_ShortUrl::getBy(array('key' => $t));
		if (empty($val['id'])) {
			$shortUrlData = array(
				'key'        => $t,
				'url'        => $redirect,
				'created_at' => time(),
				'mark'       => $mark
			);
			Gionee_Service_ShortUrl::add($shortUrlData);
		}
	}

	public static function genTVal($url,$len=6) {
		$base32    = array(
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
			'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
			'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
			'y', 'z', '0', '2', '4', '6', '8', '9'
		);
		$hex       = md5($url);
		$hexLen    = strlen($hex);
		$subHexLen = $hexLen / 8;
		$output    = array();
		for ($i = 0; $i < $subHexLen; $i++) {
			$subHex = substr($hex, $i * 8, 8);
			$int    = 0x3FFFFFFF & (1 * ('0x' . $subHex));
			$out    = '';
			for ($j = 0; $j < $len; $j++) {
				$val = 0x0000001F & $int;
				$out .= $base32[$val];
				$int = $int >> 5;
			}
			$output[] = $out;
		}
		//$k = rand(0,3);
		return $output[0];
	}

	/**
	 * @return Gionee_Dao_ShortUrl
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_ShortUrl");
	}
}
