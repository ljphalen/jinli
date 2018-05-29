<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 翻翻3.0 收藏接口
 */
class FavController extends Api_BaseController {


	/**
	 * 同步数据
	 */
	public function syncAction() {

		$args = array(
			'data_ver' => FILTER_VALIDATE_INT,
			'imei'     => FILTER_SANITIZE_STRING,
			'ids_del'  => FILTER_SANITIZE_STRING,
			'ids_add'  => FILTER_SANITIZE_STRING,
		);

		$inputArr = filter_var_array($_REQUEST, $args);

		$idsArr = $this->_change($inputArr);
		$idsStr = !empty($idsArr) ? implode(',', $idsArr) : '';
		$newVer = crc32($idsStr);

		$data['data_ver'] = $newVer;
		$data['flag']     = ($newVer != $inputArr['data_ver']) ? 1 : 0;
		$data['ids']      = ($newVer != $inputArr['data_ver']) ? $idsStr : '';
		$this->output(0, '', $data);
	}

	private function _change($inputArr) {
		$imei      = $inputArr['imei'];
		$idsDelStr = $inputArr['ids_del'];
		$idsAddStr = $inputArr['ids_add'];

		$this->_log(array(__METHOD__, $_REQUEST));

		$key = 'FAV_LIST:' . $imei;

		$userInfo = W3_Service_User::getBy(array('imei' => $imei));
		if (empty($userInfo['id'])) {
			return array();
		}

		$idsArr = Common::getCache()->get($key);
		$idsArr = !empty($idsArr) ? $idsArr : array();
		if (empty($idsDelStr) && empty($idsAddStr)) {
			return $idsArr;
		}

		$i = 0;
		if (!empty($idsDelStr)) {
			$idsArr = array_flip($idsArr);
			unset($idsArr[0]);
			$idsDelArr = explode(',', $idsDelStr);
			foreach ($idsDelArr as $id) {
				$id = intval($id);
				unset($idsArr[$id]);
				$i++;
			}
			$idsArr = array_flip($idsArr);
			$idsArr = array_values($idsArr);
		}

		if (!empty($idsAddStr)) {
			$idsAddArr = explode(',', $idsAddStr);
			foreach ($idsAddArr as $id) {
				$id = intval($id);
				if (!empty($id)) {
					array_unshift($idsArr, $id);
					$i++;
				}
			}
			$idsArr = array_values(array_flip(array_flip($idsArr)));
		}

		if ($i > 0) {
			Common::getCache()->set($key, $idsArr);

			W3_Service_User::set(array('fav_ids' => implode(',', $idsArr)), $userInfo['id']);
		}

		return $idsArr;

	}

	/**
	 * 日志记录
	 *
	 * @author william.hu
	 * @param array $arr
	 */
	private function _log($args) {
		if (ENV == 'develop' || ENV == 'test') {
			array_push($args, $_SERVER['HTTP_USER_AGENT']);
			array_push($args, $_SERVER['REMOTE_ADDR']);

			$logFile = '/tmp/fanfan_w3_access_' . date('Ymd');
			$logText = date('Y-m-d H:i:s') . ' ' . json_encode($args) . "\n";
			error_log($logText, 3, $logFile);
		}
	}

}