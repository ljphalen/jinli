<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TopicController extends Front_BaseController {
	/**
	 * 专题界面
	 */
	public function infoAction() {
		$id          = $this->getInput('id');
		$ver         = $this->getInput('ver');
		$model       = $this->getInput('model');
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_TOPIC, $id . ':' . $ver);
		$info = W3_Service_Topic::get($id);
		if (empty($info['id'])) {
			exit;
		}
		$this->assign('ver', $ver);
		$this->assign('model', $model);
		$this->assign('info', $info);
	}

}
