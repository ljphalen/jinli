<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class NewsController extends Front_BaseController {

	/**
	 * 初始化数据接口
	 */
	public function initAction() {
		$id          = $this->getInput('id');
		$initNewsStr = Widget_Service_Config::getValue('w3_init_news_id');
		$idArr       = explode(',', $initNewsStr);
		$k           = max(intval($id - 1), 0);
		$id          = $idArr[$k];

		$info = Widget_Service_Source::getDetail($id);
		$this->assign('info', $info);
	}

	public function detailAction() {
		$id  = $this->getInput('id');
		$ver = $this->getInput('app_ver');
		$row = Widget_Service_Source::getDetail($id);
		if (!empty($row['id'])) {
			Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_NEWS_DETAIL, $id . ':' . $ver);
		}

		$cpId = intval($row['cp_id']);

		//外部链接通过浏览器调用
		$cpInfo = W3_Service_Cp::get($cpId);
		if ($cpInfo['to_url'] || $cpId == 1) {
			$toUrl = !empty($row['url']) ? $row['url'] : $row['out_link'];
			header('Location: ' . $toUrl);
			exit;
		}
		$this->assign('info', $row);
	}


}


