<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ResController extends Front_BaseController {
	/**
	 * 2.0 资源下载详情页
	 */
	public function infoAction() {
		$cpId  = Widget_Service_Cp::unifyCpId($this->getInput('cp_id'));
		$ver         = $this->getInput('ver');
		$model       = $this->getInput('model');
		$info        = Widget_Service_Down::getByCpId($cpId);
		$info['pic'] = json_decode($info['pic'], true);
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_RES, $cpId.':'.$ver);
		$this->assign('ver', $ver);
		$this->assign('model', $model);
		$this->assign('info', $info);
	}

	/**
	 * 2.0 资源下载地址
	 */
	public function downAction() {
		$cpId  = Widget_Service_Cp::unifyCpId($this->getInput('cp_id'));
		$ver   = $this->getInput('ver');
		$model = $this->getInput('model');
		$info        = Widget_Service_Down::getByCpId($cpId);
		if (!empty($info['url'])) {
			$downUrl = html_entity_decode($info['url']);
			Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_DOWN, $cpId.':'.$ver);
			header('Location: ' . $downUrl);
			exit;
		}
		exit;
	}

	/**
	 * 3.0 资源下载详情页
	 */
	public function detailAction() {
		$cpId  = Widget_Service_Cp::unifyCpId($this->getInput('cp_id'));
		$ver   = $this->getInput('ver');
		$model = $this->getInput('model');
		Widget_Service_Log::incrBy(Widget_Service_Log::TYPE_RES, $cpId.':'.$ver);

		$info        = Widget_Service_Down::getByCpId($cpId);
		$info['pic'] = json_decode($info['pic'], true);
		$this->assign('info', $info);
	}

}
