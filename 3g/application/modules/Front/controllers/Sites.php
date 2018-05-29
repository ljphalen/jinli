<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网址大全页
 */
class SitesController extends Front_BaseController {

	public function indexAction() {
		//统计
		Gionee_Service_Log::pvLog('sites_map');
		Gionee_Service_Log::uvLog('sites_map', $this->getSource());
		$data     = Gionee_Service_SiteContent::getSitesData();
		$hotwords = Common::getCache()->get("baidu_hotwords");
		$this->assign('hotwords', array_slice($hotwords, 0, 6));//百度热词
		$this->assign('ads', $this->_getAds());// 广告
		$this->assign('data', $data);
	}

	//获得广告信息
	private function _getAds() {
		$rs   = Common::getCache();
		$rcKey  = "SITE:MAP:ADS";
		$data = $rs->get($rcKey);
		if ($data === false) {
			$data = array();
			$posMsg = Gionee_Service_SiteCategory::getBy(array('style' => 'ads'));
			if ($posMsg) {
				$params               = array();
				$params['start_time'] = array('<=', time());
				$params['end_time']   = array('>=', time());
				$params['status']     = 1;
				$params['cat_id']     = $posMsg['id'];
				$orderBy              = array('sort' => 'DESC', 'id' => 'DESC');
				$data                 = Gionee_Service_SiteContent::getsBy($params, $orderBy);
			}
			$rs->set($rcKey, $data, 300);
		}
		return $data;
	}
}