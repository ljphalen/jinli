<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 翻翻API接口
 */
class WidgetController extends Api_BaseController {

	/**
	 * 信息接口
	 *
	 * @version 1.0
	 */
	public function indexAction() {
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'index');
		$this->_log(array(__METHOD__, $_REQUEST));
		$id   = $this->getInput('id');
		$data = Widget_Service_Source::getIdsInfoV1($id);
		//输出结果
		$this->output(0, '', $data);
	}

	/**
	 * 版本号和联网时间接口
	 *
	 * @version 1.0
	 */
	public function versionAction() {
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'version');
		$this->_log(array(__METHOD__, $_REQUEST));
		$nowDate = date('Ymd');

		$tmpId = Widget_Service_Source::getIdsListV1();

		$lastId = isset($tmpId[0]) ? $tmpId[0] : 0;
		$sortId = implode(",", $tmpId);

		$data = array(
			'widget_version'  => $nowDate . $lastId,
			'widget_connect1' => '03:00', //1.0版本严重问题 切勿修改
			'widget_connect2' => '04:00',
			'sort_id'         => $sortId
		);

		$this->output(0, '', $data);

	}

	/**
	 * 新闻排序接口
	 *
	 * @version 2.0.1+
	 */
	public function sortAction() {
		$imei        = $this->getInput("imei");
		$columnIdStr = $this->getInput("column_id");
		$ver         = $this->getInput("ver"); //2.0.2
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'sort:' . $ver);
		//广告所在索引位置
		$adIdx = $this->_getCooike();
		$this->_log(array(__METHOD__, $_REQUEST, $adIdx));

		$retCode = -1;
		$retMsg  = '';
		$retData = array();

		if ($columnIdStr) {
			$lt2 = Widget_Service_Cp::isVer1($ver);

			list($urlIds, $topList, $idsArr) = Widget_Service_Source::filterIds($columnIdStr, $lt2);
			$data = Widget_Service_Source::filterAdIds($topList, $idsArr, $adIdx);

			$this->_toUrlIdLog($urlIds, $ver);
			$this->_toUserLog($imei, $columnIdStr, $urlIds, $ver);
			$rndTime1 = $this->_genRandTime();
			$rndTime2 = $this->_genRandTime();
			$retCode = 0;
			$retData = array(
				'widget_connect1' => $rndTime1,
				'widget_connect2' => $rndTime2,
				'widget_connect'  => array($rndTime1, $rndTime2),
				'ids'             => $data,
				'cookie'          => Widget_Service_Source::setCooike($adIdx) //需要返回给服务器的数据
			);
		}

		$this->output($retCode, $retMsg, $retData);
	}


	/**
	 *
	 * $cookie
	 * {
	 *  date => 20130416
	 * idx1=> adid1, adid2
	 *  idx2=> adid3, adid4,
	 * }
	 */
	private function _getCooike() {
		$val = $this->getInput("cookie");
		if (empty($val)) {
			return array();
		}

		$arr = json_decode(urldecode($val));
		if (empty($arr) || $arr['d'] != date('md')) {
			return array();
		}

		unset($arr['d']);
		return $arr;
	}

	private function _toUserLog($imei, $columnIdStr, $urlIds, $ver) {
		$params = array(
			'imei'       => $imei,
			'app_ver'    => $ver,
			'column_ids' => $columnIdStr,
			'url_ids'    => implode(',', $urlIds),
		);
		Widget_Service_User::initLog($params);
	}


	private function _toUrlIdLog($urlIds, $ver) {
		foreach ($urlIds as $urlId) { //统计
			Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CPURL, $urlId . ':' . $ver);
		}
	}

	/**
	 * 多个时间分割
	 * @return array
	 */
	private function _genRandTime() {
		$connection  = Widget_Service_Config::getValue('widget_connect1');
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

		return !empty($tmpT) ? $tmpT : '04:00';
	}


	/**
	 * 获取详细信息
	 *
	 * @version 2.0.1+
	 */
	public function newsAction() {
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'news');
		$this->_log(array(__METHOD__, $_REQUEST));

		$retCode = -1;
		$retMsg  = '';
		$retData = array();

		$ids = $this->getInput("ids");
		//$ver = $this->getInput("ver");//2.0.2

		if ($ids) {
			$ids = explode(",", $ids);
			if (count($ids) < 9) { //防止一次请求多个 导致服务器压力
				$tmp = array();
				foreach ($ids as $id) {
					Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_NEWS, $id);
					$info = Widget_Service_Source::getInfo($id);
					if (!empty($info)) {
						$tmp[] = $info;
					}
				}

				$retCode = 0;
				$retData = $tmp;
			}
		}

		$this->output($retCode, $retMsg, $retData);
	}

	/**
	 * 栏目列表接口
	 *
	 * @version 2.0.1+
	 */
	public function columnAction() {
		$this->_log(array(__METHOD__, $_REQUEST));
		$ver      = $this->getInput("ver"); //2.0.2
		$specName = $this->getInput("model");
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'column:' . $ver);
		$specId = Widget_Service_Spec::getIdByName(strtolower($specName));

		if (mb_stristr($specName, '预置')) { //预制数据开始版本号
			$ver = '2.0.5';
		}

		$urlIds = array();
		$lt2    = Widget_Service_Cp::isVer1($ver);
		if ($lt2) {
			$cpUrls = Widget_Service_Cp::getAll();
			foreach ($cpUrls as $urlId => $urlVal) {
				if (in_array($urlVal['cp_id'], Widget_Service_Source::$CpVer201)) {
					$urlIds[] = $urlVal['id'];
				}
			}
		}

		$retData = Widget_Service_Column::getListByVer($specId, $urlIds);
		if (count($retData) == 0) {
			$retData = Widget_Service_Column::getListByVer(0, $urlIds);
		}

		$this->output(0, '', $retData);
	}

	/**
	 * 栏目版本号接口
	 *
	 * @author william.hu
	 * @version 2.0.1+
	 */
	public function version2Action() {
		$this->_log(array(__METHOD__, $_REQUEST));
		$version = Widget_Service_Config::getValue('column_version');
		$ver     = $this->getInput("ver");
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_CALL, 'version2:' . $ver);
		$data = array(
			'column_version' => $version,
			'cp_client'      => Widget_Service_CpClient::toApi(),
		);
		$this->output(0, '', $data);
	}

	/**
	 * 跳转接口,通过php来跳转
	 * 1统计,2动态防止写死导致老版本无法使用
	 *
	 * @author william.hu
	 * @version 2.0.3+
	 */
	public function tourlAction() {
		$this->_log(array(__METHOD__, $_REQUEST));
		$cpId    = intval($this->getInput('cp_id'));
		$ver     = $this->getInput('ver');
		$model   = $this->getInput('model');
		$param   = array('app_ver' => $ver, 'model' => $model);
		$downUrl = Widget_Service_Cp::buildDownUrl($cpId, $param);
		if ($downUrl) {
			header('Location: ' . $downUrl);
		}
		exit;
	}

	/**
	 * 给BI统计查询新闻数据
	 */
	public function infoAction() {
		$ret = array();
		$id  = intval($this->getInput("id"));
		$row = Widget_Service_Source::get($id);
		if ($row) {
			$sourceTitle     = isset(Widget_Service_Cp::$CpCate[$row['cp_id']][1]) ? Widget_Service_Cp::$CpCate[$row['cp_id']][1] : $row['source'];
			$sourceStatsName = isset(Widget_Service_Cp::$CpCate[$row['cp_id']][2]) ? Widget_Service_Cp::$CpCate[$row['cp_id']][2] : '';

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
		$columns = Widget_Service_Column::getsBy();

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

			$logFile = '/tmp/fanfan_access_' . date('Ymd');
			$logText = date('Y-m-d H:i:s') . ' ' . json_encode($args) . "\n";
			error_log($logText, 3, $logFile);
		}
	}


}