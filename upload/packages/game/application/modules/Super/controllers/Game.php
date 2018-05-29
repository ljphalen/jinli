<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class GameController extends Game_BaseController{
	
	public $actions = array(
		'listUrl' => '/super/game/index',
		'detailUrl' => '/super/game/detail/',
		'tjUrl' => '/super/game/tj'
	);
    
	
	/**
	 * get hits
	 * @return boolean
	 */
	public function tjAction(){
		$id = intval($this->getInput('id'));
		$type = $this->getInput('type');
		$url = html_entity_decode(urldecode($this->getInput('_url')));
		if (!id || !$type) return false;
		switch ($type)
		{
			case GAME:
				Game_Service_Game::updateGameTJ($id);
				break;
			case AD:
				Game_Service_Ad::updateAdTJ($id);
				break;
			case SUBJECT:
				Game_Service_Subject::updateSubjectTJ($id);
				break;
			default:
		}
		if (strpos($url, '?') === false) {
			$url = $url.'?t_bi='.$this->getSource();
		} else {
			$url = $url.'&t_bi='.$this->getSource();
		}
		$this->redirect($url);
	}
	
	/**
	 * 
	 * get game detail info
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$go = intval($this->getInput('go'));
		$info = Game_Service_Game::getGameInfo($id);
		$webroot = Common::getWebRoot();
		if(!$info){
			$this->redirect($webroot);
			exit;
		}
		$this->assign('info', $info);
		list(, $gimgs) = Game_Service_GameImg::getList(0,10, array('game_id'=>$id));
		$this->assign('gimgs', $gimgs);
		list($total, $categorys) = Game_Service_Category::getAllCategory();
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
		//game price
		list(, $prices) = Game_Service_GamePrice::getAllGamePrice();
		$prices = Common::resetKey($prices, 'id');
		$this->assign('prices', $prices);
		$this->assign('title', $info['name']);
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		unset($configs['game_react']);
		$this->assign('configs', $configs);
		$this->assign('go', $go);
		$this->assign('source', $this->getSource());
		if ($this->isAjax()) {
			$this->output(0, '', array('info'=>$info, 'gimgs'=>$gimgs));
		}
	}
}
