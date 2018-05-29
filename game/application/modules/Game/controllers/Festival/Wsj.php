<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 万圣节
 * @author luojiapeng
 *
 */
class Festival_WsjController extends Game_BaseController{
	
	public function indexAction(){
		$servicePhone = Game_Service_Config::getValue('game_service_tel');
		$serviceQq = Game_Service_Config::getValue('game_service_qq');
		$this->assign('servicePhone', $servicePhone);
		$this->assign('serviceQq', $serviceQq);		
		$this->assign('source', $this->getSource());
	}
	
	public function tjAction(){
	 $url = html_entity_decode(html_entity_decode($this->getInput('_url')));
	 if (strpos($url, '?') === false) {
	 	$url = $url.'?t_bi='.$this->getSource();
	 } else {
	 	$url = $url.'&t_bi='.$this->getSource();
	 }
	 $this->redirect($url);
	}
	
}