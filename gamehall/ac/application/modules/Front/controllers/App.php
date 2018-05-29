<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class AppController extends Front_BaseController{
	
	public $actions = array(
		"infoUrl"=>"/front/app/info",
		"moreUrl"=>"/api/app/more"
	);

	public $perpage = 10;
	
	public function listAction() {
	}

	public function infoAction() {
		$id = $this->getInput('id');
		$info = Resource_Service_Apps::get($id);

		$imgs = Resource_Service_Img::getsBy(array('app_id'=>$info['id']));
		$this->assign('imgs', $imgs);
		$this->assign('info', $info);
	}

	/**
	 * 访问统计跳转
	 */
	public function visitAction(){
		$url = html_entity_decode($this->getInput('_url'));
		$this->redirect($url);
		exit;
	}
	
	/**
	 * 下载统计跳转
	 */
	public function dlAction(){
		$url = html_entity_decode($this->getInput('_url'));
		$this->redirect($url);
		exit;
	}
}
