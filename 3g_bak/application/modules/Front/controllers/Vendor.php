<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class VendorController extends Front_BaseController {

	/**
	 * 对外合作页面
	 */
	public function indexAction() {
		Gionee_Service_Log::pvLog('3g_vendor');
		$t_bi = $this->getSource();
		Gionee_Service_Log::uvLog('3g_vendor', $t_bi);
		//跳转页
		$redirectUrl = Gionee_Service_Config::getValue('sohu_redirect_url');
		if (!empty($redirectUrl) && preg_match('/(https?:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-]*)*\/?/', $redirectUrl)) {
			Common::redirect($redirectUrl);
		}
		//检查对外渠道号
		$ch     = $this->getInput('ch');
		$rcKey  = 'Vendor:' . $ch;
		$result = Common::getCache()->get($rcKey);
		if ($result === false) {
			$result = Gionee_Service_Vendor::getBy($ch);
			Common::getCache()->set($rcKey, $result, 600);
		}

		if ($result) {
			//统计导航pv
			Gionee_Service_Log::pvLog($ch);
			$this->assign('ch', $ch);
		}

		$pageData = Gionee_Service_Ng::getIndexData();

		$this->assign('pageData', $pageData['content']);

		//顶部广告
		$time      = time();
		$rcKey     = 'NG_COLUMN:img1:1';
		$column_ad = Common::getCache()->get($rcKey);
		if ($column_ad === false) {
			$where     = array('type_id' => 1, 'style' => 'img1', 'status' => 1);
			$column_ad = Gionee_Service_NgColumn::getBy($where, array('sort' => 'ASC', 'id' => 'DESC'));
			Common::getCache()->set($rcKey, $column_ad, 600);
		}

		$rcKey  = 'NG:' . $column_ad['id'];
		$top_ad = Common::getCache()->get($rcKey);
		if ($top_ad === false) {
			$where  = array(
				'column_id'  => $column_ad['id'],
				'status'     => 1,
				'start_time' => array('<', $time),
				'end_time'   => array('>', $time)
			);
			$top_ad = Gionee_Service_Ng::getBy($where, array('sort' => 'ASC', 'id' => 'ASC'));
			Common::getCache()->set($rcKey, $column_ad, 600);
		}

		$this->assign('top_ad', $top_ad);
		//百度热词
		$words = Gionee_Service_Baidu::getNavIndexWrods();
		$this->assign('baidu_hotword', $words);
		//百度渠道号
		$baidu_num = Gionee_Service_Baidu::getFromNo();
		$this->assign('baidu_num', $baidu_num);
		//更多新闻
		$more = 'http://info.3g.qq.com';
		$this->assign('more', $more);
	}
}