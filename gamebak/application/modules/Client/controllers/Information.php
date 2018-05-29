<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class InformationController extends Client_BaseController {
	
	public $actions =array(
			    'detailUrl' => '/client/information/detail/',
			);	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		
		$info = Client_Service_News::getNews(intval($id));
		//游戏数据
		$gameData = Resource_Service_GameData::getBasicInfo($info['game_id']);
		$title = $gameData ? html_entity_decode($gameData['name'], ENT_QUOTES) . '-资讯' : '';
		
		$this->assign('info', $info);
		Common::addSEO($this, $title);
	}
}
