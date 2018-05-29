<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Push {

	/**
	 * 获取机型列表
	 */
	public static function getAll($orderBy = array()) {
		$list = self::_getDao()->getAll($orderBy);
		return $list;
	}


	/**
	 *
	 * 下载资源列表
	 * @param int $page
	 * @param int $limit
	 * @param int $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params, array('created_at' => 'DESC', 'id' => 'DESC'));
		return $ret;
	}

	/**
	 * 下载资源数量
	 * @param array $params
	 * @return string
	 */
	public static function getTotal($params = array()) {
		$total = self::_getDao()->count($params);
		return $total;
	}


	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret   = self::_getDao()->getsBy($params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param array $data
	 * @param int $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param int $id
	 */
	public static function del($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter description here ...
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		self::_getDao()->insert($data);
		return self::_getDao()->getLastInsertId();

	}

	public static function all() {
		$list = self::_getDao()->getAll();
		return $list;
	}


	/**
	 *
	 * 字段过滤
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array('title', 'content', 'type', 'msg_body', 'created_at', 'response');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}


	public static function toPush($id, $body) {
		//type [setting,subscribe,update,site,cp]
		$appId = Widget_Service_Config::getValue('push_appid');;
		$appwd = Widget_Service_Config::getValue('push_appwd');

		$tokenUrl = "http://push.gionee.com:8001/sas/requestToken?applicationID={$appId}&passwd={$appwd}";

		//succ:{"result":"0","authToken":"51a8c937377a469082f38bc5255ba25e","expired":"1353565272"}
		//fail:{"errorCode":"50001"}
		$ret         = Widget_Service_Push::toReq($tokenUrl, array());
		$responseArr = json_decode($ret, true);
		if (!empty($responseArr['authToken'])) {
			$sendUrl = 'http://push.gionee.com:8001/push_service?v=1';
			$msg     = array(
				array(
					"type"    => "notification",
					"save"    => "true",
					"expired" => strval(time() + 7 * 24 * 3600),
					"msgid"   => strval($id),
					"msg"     => urlencode($body),
					"rids"    => array(),
				),
			);
			$params  = array(
				"appid" => $appId,
				"token" => $responseArr['authToken'],
				"msgs"  => $msg,
			);

			$toData = json_encode($params);
			$ret    = Widget_Service_Push::toReq($sendUrl, $toData);
		}
		return $ret;
	}

	public static function toReq($url, $params, $post = false) {
		//curl抓取图片过程
		$ch = curl_init();
		if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if ($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

		$content = curl_exec($ch);
		$info    = curl_getinfo($ch);
		curl_close($ch);
		if ($info['http_code'] != 200) {
			$content = null;
		}
		return $content;
	}

	public static function buildBody($id) {
		$row = Widget_Service_Source::get($id);
		if (empty($row)) {
			return array();
		}

		$sourceStatsName = isset(Widget_Service_Cp::$CpCate[$row['source']][2]) ? Widget_Service_Cp::$CpCate[$row['source']][2] : '';
		$webroot         = Yaf_Application::app()->getConfig()->webroot;

		$ret = array(
			'source_name'    => $sourceStatsName,
			'source_id'      => $row['out_link'],
			'column_id'      => $row['id'],
			'cp_id'          => $row['cp_id'],
			'url_download_0' => $webroot . '/front/res/info?cp_id=' . intval($row['source']),
		);

		return $ret;
	}

	/**
	 *
	 * @return Widget_Dao_Spec
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Push");
	}
}
