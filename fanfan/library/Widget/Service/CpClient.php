<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_CpClient {

	static $defaultCpParams = array(
		'detail_id'    => 'data',
		'channel_name' => 'standard',
		'down_url'     => '',
		'data'         => array(
			array(
				'key'   => 'action',
				'value' => 'android.intent.action.VIEW',
				'type'  => 'string',
			)

		),
	);

	/**
	 *
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}


	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	public static function getAllByCpId() {
		$rcKey = Common::KN_WIDGET_CP_CLIENT;
		$ret   = Common::getCache()->get($rcKey);
		if (!empty($ret)) {
			return $ret;
		}
		$ret  = array();
		$list = self::_getDao()->getAll();
		foreach ($list as $val) {
			$ret[$val['cp_id']][] = $val;
		}
		Common::getCache()->set($rcKey, $ret, Common::KT_CP_INFO);
		return $ret;
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
		$ret = self::_getDao()->getsBy($params, $orderBy);
		return $ret;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function insert($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		Widget_Service_CpClient::cleanAllCache();
		return self::_getDao()->insert($data);
	}

	/**
	 *
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		Widget_Service_CpClient::cleanAllCache();
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * @param int $id
	 */
	public static function delete($id) {
		Widget_Service_CpClient::cleanAllCache();
		return self::_getDao()->delete(intval($id));
	}

	public static function cleanAllCache() {
		$rcKey = Common::KN_WIDGET_CP_CLIENT;
		Common::getCache()->delete($rcKey);
	}

	public static function all() {
		$list = self::_getDao()->getAll();
		return $list;
	}

	public static function toApi() {
		$cpList = Widget_Service_CpClient::getAllByCpId();
		$ret    = array();
		foreach ($cpList as $key => $vList) {
			$chver = array();
			foreach ($vList as $v) {
				$tmpD = json_decode($v['data'], true);
				$dd   = array();
				foreach ($tmpD as $d) {
					if ($d['key']) {
						$dd[] = $d;
					}
				}

				$chver[] = array(
					'detail_id'    => $v['detail_id'],
					'channel_name' => $v['channel_name'],
					'data'         => $dd,
				);
			}

			$sourceName = isset(Widget_Service_Cp::$CpCate[$key][2]) ? Widget_Service_Cp::$CpCate[$key][2] : $key;

			$ret[] = array(
				'cp_id'       => $key,
				'source_name' => $sourceName,
				'ch_ver'      => $chver,
			);
		}

		return $ret;
	}

	public static function toApiOfW3($param = array()) {
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$list    = Widget_Service_Cp::$CpCate;
		$ret     = array();
		$cpList  = Widget_Service_CpClient::getAllByCpId();
		foreach ($list as $cpId => $cpAttr) {
			$cpInfo  = W3_Service_Cp::get($cpId);
			$downUrl = Widget_Service_Cp::buildDownUrl($cpId, $param);
			$chVer   = array();
			if (!empty($cpInfo['to_url'])) {
				$tmpDef                 = self::$defaultCpParams;
				$tmpDef['down_url']     = $downUrl;
				$chVer[]                = $tmpDef;
				$tmpDef['channel_name'] = 'gionee';
				$chVer[]                = $tmpDef;
			} else {
				$vList = isset($cpList[$cpId]) ? $cpList[$cpId] : array();
				if (!empty($vList)) {
					foreach ($vList as $v) {
						$tmpD = json_decode($v['data'], true);
						$dd   = array();
						foreach ($tmpD as $d) {
							if ($d['key']) {
								$dd[] = $d;
							}
						}
						$chVer[] = array(
							'detail_id'    => $v['detail_id'],
							'channel_name' => $v['channel_name'],
							'down_url'     => $downUrl,
							'data'         => $dd,
						);
					}
				}
			}

			$sourceName = isset($cpAttr[2]) ? $cpAttr[2] : $cpId;
			$jmpFlag    = isset($cpAttr[3]) ? $cpAttr[3] : 1;

			$ret[] = array(
				'cp_id'       => $cpId,
				'source_name' => $sourceName,
				'info_url'    => $webroot . '/front/res/detail?cp_id=' . Widget_Service_Cp::unifyCpId($cpId),
				'jmp_text'    => isset($cpInfo['jmp_text']) ? $cpInfo['jmp_text'] : '',
				'jmp_flag'    => $jmpFlag,
				'ch_ver'      => $chVer,
			);
		}
		return $ret;
	}


	/**
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array('id','cp_id','channel_name','detail_id','down_url','data');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	/**
	 *
	 * @return Widget_Dao_Cp
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_CpClient");
	}
}
