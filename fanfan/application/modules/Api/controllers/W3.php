<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 翻翻3.0 API接口
 */
class W3Controller extends Api_BaseController {

	/**
	 * 新闻id列表接口
	 *
	 * @version 3.0
	 */
	public function listAction() {
		$this->_log(array(__METHOD__, $_REQUEST));
		$params = $this->getInput(array('column_ids', 'data_ver', 'imei', 'app_ver'));

		list($urlIds, $topList, $idsArr) = W3_Service_Source::filterIds($params['column_ids']);
		$adPosArr = W3_Service_Source::getUserAdPos($params['imei']);
		$idsVal   = Widget_Service_Source::filterAdIds($topList, $idsArr, $adPosArr);
		if ($adPosArr) {
			W3_Service_Source::setUserAdPos($params['imei'], $adPosArr);
		}
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'w3list:' . $params['app_ver']);
		foreach ($urlIds as $urlId) {
			Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CPURL, $urlId . ':' . $params['app_ver']);
		}

		$params['url_ids'] = implode(',', $urlIds);
		W3_Service_User::initLog($params);

		$dataVer = crc32($idsVal);
		if ($params['data_ver'] == $dataVer) {
			$idsVal = '';
		}

		$data = array(
			'data_ver' => $dataVer,
			'list_ids' => $idsVal,
		);
		//输出结果
		$this->output(0, '', $data);

	}

	/**
	 * 数据提供商接口
	 *
	 * @version 3.0
	 */
	public function initAction() {
		$this->_log(array(__METHOD__, $_REQUEST));
		$params = $this->getInput(array('imei', 'net', 'gps', 'app_ver', 'cp_ver', 'column_ver', 'model'));
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'w3init:' . $params['app_ver']);
		$t1   = $this->_genRandTime();
		$t2   = $this->_genRandTime();
		$data = array(
			'auto_time' => "{$t1},{$t2}",
		);

		W3_Service_User::initLog($params);

		$cpVer     = Widget_Service_Config::getValue('w3_cp_ver');
		$columnVer = Widget_Service_Config::getValue('w3_column_ver');
		if (strlen($params['cp_ver']) > 0 && $params['cp_ver'] != $cpVer) {
			$data['cp_ver']  = $cpVer;
			$data['cp_list'] = Widget_Service_CpClient::toApiOfW3($params);
		}

		if (strlen($params['column_ver']) > 0 && $params['column_ver'] != $columnVer) {
			$specId             = Widget_Service_Spec::getIdByName(strtolower($params['model']));
			$data['column_ver'] = $columnVer;
			$cl                 = W3_Service_Column::getListBySpec($specId);
			if (count($cl) == 0) {
				$cl = W3_Service_Column::getListBySpec(0);
			}
			$data['column_list'] = $cl;
		}

		//输出结果
		$this->output(0, '', $data);
	}

	/**
	 * 多个时间分割
	 * @return array
	 */
	private function _genRandTime() {
		$connection  = Widget_Service_Config::getValue('w3_auto_time');
		$connTimeArr = explode(',', $connection);
		$tmpT        = '';
		if (!empty($connTimeArr[0])) {
			list($h, $m) = explode(':', $connTimeArr[0]);
			$newMin = $m + rand(1, 50);
			if ($newMin > 59) {
				$h += 1;
				$m = $newMin - 59;
			} else {
				$m = $newMin;
			}
			$tmpT = str_pad($h, 2, '0', STR_PAD_LEFT) . ':' . str_pad($m, 2, '0', STR_PAD_LEFT);
		}

		return !empty($tmpT) ? $tmpT : '05:00';
	}

	/**
	 * 新闻内容接口
	 *
	 * @version 3.0
	 */
	public function newsAction() {
		$this->_log(array(__METHOD__, $_REQUEST));
		$params  = $this->getInput(array('ids', 'app_ver'));
		$idArr   = explode(',', $params['ids']);
		$data    = array();
		$webroot = Yaf_Application::app()->getConfig()->webroot;

		$isInit = false;
		if (empty($idArr[0])) {
			$initNewsStr = Widget_Service_Config::getValue('w3_init_news_id');
			$idArr       = explode(',', $initNewsStr);
			$isInit      = true;
		}

		$n = 1;
		foreach ($idArr as $id) {
			$row = Widget_Service_Source::getInfoV3($id);
			if ($row['id']) {
				if ($isInit) {
					$row['id'] = $n;
				}
				if ($isInit) {
					$row['detail_url'] = "http://fanfan.gionee.com/front/news/init?id={$row['id']}";
				} else {
					$row['detail_url'] = $webroot . "/front/news/detail?id={$row['id']}&ver={$params['app_ver']}";
				}

				$n++;
				$data[] = $row;
			}
		}

		//输出结果
		$this->output(0, '', $data);
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


	/**
	 * 给BI统计查询新闻数据
	 */
	public function infoAction() {
		$ret = array();
		$id  = intval($this->getInput("id"));
		$row = Widget_Service_Source::get($id);
		if ($row) {
			$sourceTitle = isset(Widget_Service_Cp::$CpCate[$row['cp_id']][1]) ? Widget_Service_Cp::$CpCate[$row['cp_id']][1] : $row['source'];

			$cpUrls  = Widget_Service_Cp::getAll();
			$urlName = isset($cpUrls[$row['url_id']]['title']) ? $cpUrls[$row['url_id']]['title'] : '';
			$ret     = array(
				'id'     => intval($row['id']),
				'title'  => $row['title'],
				'resume' => !empty($row['subtitle']) ? $row['subtitle'] : $cpUrls[$row['url_id']]['resume'],
				'source' => $sourceTitle,
				'cp_id'  => intval($row['cp_id']),
				'type'   => $urlName,
				'is_ad'  => 0,
			);

			if ($row['cp_id'] == Widget_Service_Cp::CP_GIONEE) {
				$ret['is_ad'] = 1;
			}
		}

		echo json_encode($ret);
		exit;
	}

	/**
	 * 给BI统计查询分类名称接口
	 */
	public function columnlistAction() {
		$columns = W3_Service_Column::getsBy();

		$retData = array();
		foreach ($columns as $value) {
			if (!empty($value['id'])) {
				$retData[] = array(
					'id'    => $value['id'],
					'title' => $value['title'],
				);
			}
		}
		$this->output(0, '', $retData);
	}

}