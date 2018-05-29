<?php

if (!defined('BASE_PATH')) exit('Access Denied!');


class Gionee_Service_VoIP {

	//分页请求数据
	public static function getListByPage($page, $pageSize, $where, $orderBy) {
		$page = max($page, 1);
		return array(self::_getDao()->count($where), self::_getDao()->getList(($page - 1) * $pageSize, $pageSize, $where, $orderBy));
	}

	//添加数据
	public static function insert($params) {
		if (!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}

	//编辑数据 
	public static function update($id, $params) {
		if (!is_array($params)) return false;
		$data = self::_checkData($params);
		return self::_getDao()->update($data, $id);
	}

	//请求单条数据
	public static function get($id) {
		if (!is_numeric($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	//请求多条数据
	public static function getsBy($params = array(), $order = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $order);
	}

	public static function getBy($params, $order) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $order);
	}

	public static function delete($id) {
		if (!is_numeric($id)) return fasle;
		return self::_getDao()->delete($id);
	}

	private function _checkData($params) {
		$temp = array();
		if (isset($params['start_time'])) $temp['start_time'] = strtotime($params['start_time']);
		if (isset($params['end_time'])) $temp['end_time'] = strtotime($params['end_time']);
		if (isset($params['valid_time'])) $temp['valid_time'] = strtotime($params['valid_time']);
		if (isset($params['add_time'])) $temp['add_time'] = $params['add_time'];
		if (isset($params['sort'])) $temp['sort'] = $params['sort'];
		if (isset($params['number'])) $temp['number'] = $params['number'];
		if (isset($params['status'])) $temp['sta'] = $params['status'];
		return $temp;
	}

	//得到地区编码
	public static function getAreaCode($phone) {
		if (!$phone) return false;
		$areaCode = $segment = '';  //需要返回的地区信息
		$first    = substr($phone, 0, 1);
		switch ($first) {
			case '0': { //座机
				$twoNumber = substr($phone, 0, 2);
				if ($twoNumber == '00') {
					$segment = substr($phone, 0, 5);//港澳台地区
				} elseif (preg_match('/^0(1|2)$/', $twoNumber)) { //三位数区号的城市
					$segment = substr($phone, 0, 3);
				} else {
					$segment = substr($phone, 0, 4);
				}
				$areaCode = Common::getCache()->get('AREA_CODE:TEL:' . $segment);
				if (!$areaCode) {
					$data = Gionee_Service_AreaCode::getBy(array('area_code' => $segment), array());
					if ($data) {
						$areaCode = $data['province'] . ' ' . $data['city'];
						Common::getCache()->set('AREA_CODE:TEL:' . $segment, $areaCode, 24 * 3600);
					}
				}
				break;
			}

			case '1': {//手机
				$segment  = substr($phone, 0, 7);
				$areaCode = Common::getCache()->get('AREA_CODE:PHONE:' . $segment);
				if (!$areaCode) {
					$data = Gionee_Service_MobileCode::getBy(array('mobile_segment' => intval($segment)), array());
					if ($data) {
						$areaCode = $data['province'] . ' ' . $data['city'];
					} else {
						$matches = self::_getMobileAreaByNumber($phone);
						if ($matches) {
							$res = explode("&nbsp;", $matches[1][1]);
							Gionee_Service_MobileCode::add(array('mobile_segment' => intval(substr($phone, 0, 8)), 'province' => $res[0], 'city' => $res[1], 'servicer' => $matches[1][2]));
							$areaCode = $matches[1][1];
						}
					}

					Common::getCache()->set('AREA_CODE:PHONE:' . $segment, $areaCode, 24 * 3600);
				}
				break;
			}
			default:
				break;
		}
		return $areaCode;
	}

	//查询手机归属地
	private function _getMobileAreaByNumber($phone) {
		if (!Common::checkIllPhone($phone, 'phone')) return false;
		$searchURL = 'http://www.ip138.com:8080/search.asp?action=mobile&mobile=' . $phone;
		$model     = new Util_Http_Curl($searchURL);
		$info      = $model->get(array());
		$string    = '/tdc2>(.*)<\/TD>/';
		preg_match_all($string, $info, $matches);
		return $matches;
	}

	/**
	 * 日志记录
	 *
	 * @author william.hu
	 * @param array $arr
	 */
	static public function log($args) {
		if (ENV == 'develop' || ENV == 'test') {
			//array_push($args, $_SERVER['HTTP_USER_AGENT']);
			array_push($args, $_SERVER['REMOTE_ADDR']);

            $path = '/data/3g_log/tel/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
			$logFile = $path . date('Ymd');
			$logText = date('Y-m-d H:i:s') . ' ' . json_encode($args) . "\n";
			error_log($logText, 3, $logFile);
		}
	}

	private function _getDao() {
		return Common::getDao('Gionee_Dao_VoIP');
	}


	/**
	 * @return Gionee_Dao_VoIPGiveLog
	 */
	static public function getGiveLogDao() {
		return Common::getDao('Gionee_Dao_VoIPGiveLog');

	}
}