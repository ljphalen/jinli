<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class IndexController extends Channel_BaseController{
	
	public $actions = array(
		'listUrl' => '/channel/index/index',
		'detailUrl' => '/channel/index/detail/',
		'tjUrl' => '/channel/index/tj'
	);

	public $perpage = 100;
	
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$t_bi = $this->getInput('t_bi');
		
		$sp = $this->getInput('sp');
		$imei = end(explode('_',$sp));
		$tj = explode('_',$sp);
		Util_Cookie::set("imei", $imei, true, Common::getTime() + strtotime("+1 days"));
		Util_Cookie::set("version", $tj[1], true, Common::getTime() + strtotime("+1 days"));
		if (!$t_bi) $this->setSource();
		
		$this->assign('source', $this->getSource());
		$webroot = Common::getWebRoot();
		$this->assign('tjUrl', $webroot.$this->actions['tjUrl']);
		$this->assign('cache', Game_Service_Config::getValue('game_client_cache'));
	}
		
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
				Client_Service_Game::updateTJ($id);
				break;
			case AD:
				Client_Service_Ad::updateAdTJ($id);
				break;
			case SUBJECT:
				Client_Service_Subject::updateSubjectTJ($id);
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
		$intersrc = $this->getInput('intersrc');
		$app_version = Util_Cookie::get('version', true);
		if(!$app_version){
			$sp = $this->getInput('sp');
			$tj = explode('_',$sp);
			Util_Cookie::set("app_version", $tj[1], true, Common::getTime() + strtotime("+1 days"));
			$version = $tj[1];
		}
		$intersrc = $this->getInput('intersrc');
		$info = Resource_Service_Games::getGameAllInfo(array('id'=>$id));
		
		$game_ids = Client_Service_Recommend::getRecommendGames(array('GAMEC_RESOURCE_ID'=>$id));
		if($game_ids){
			foreach($game_ids as $key=>$value){
				$tmp = array();
				$tmp = Resource_Service_Games::getGameAllInfo(array('id'=>$value['GAMEC_RECOMEND_ID']));
				if($tmp['status']){
					$games[] = $tmp;
				}
			}
		}
		$this->assign('info', $info);
		$this->assign('games', $games);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('app_version', $app_version);
	}
}
