<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class ActivityController extends Client_BaseController{
	
	public $actions = array(
		'detailUrl' => '/client/activity/detail/',
		'tjUrl' => '/client/index/tj'
	);

	public $perpage = 10;
	
	public function indexAction() {
		Common::addSEO($this,'活动');
	}
	
	
	/**
	 *
	 * 抽奖活动的明细
	 */
	public function detailAction() {
		$app_version = $this->checkAppVersion();
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$sc = $this->getInput('source');
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		
		$activity = Client_Service_Activity::get($id);
		$this->assign('source', $this->getSource());
		$this->assign('activity', $activity);
		
		$this->assign('app_version', $app_version);
		Common::addSEO($this,'活动规则');
	}
	
	/**
	 * 客户端活动详情的介绍页
	 */
	public function hdetailAction() {
		$id = $this->getInput('id');
		$params['id'] = $id;
		$info = Client_Service_Hd::getHd($id); 
		$this->assign('info', $info);
		Common::addSEO($this,'活动详情');
	}
	
	/**
	 * 客户端活动详情的中奖公告
	 */
	public function announceAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Hd::getHd($id); 
		$this->assign('info', $info);
		Common::addSEO($this,'活动公告');
	}
	
	/**
	 * 1.5.3客户端活动详情的改变
	 */
	public function addetailAction() {
		$app_version = $this->checkAppVersion();
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$sc = $this->getInput('source');
		$t_bi = $this->getInput('t_bi');
		$isShare = $this->getInput('isShare');
		$shareto = $this->getInput('shareto');
		$qudao = $this->getInput('qudao');
		if (!$app_version && !$intersrc&& !$sc&& !$t_bi&& !$isShare&& !$shareto&& !$qudao) {
			$isShare = 1;
		}
		$checkAppVersion = $this->checkAppVersion();
		if (!$t_bi) $this->setSource();		
		if(!$id){
			$this->redirect('/Client/Error/index/');
			exit;
		}
		$params['id'] = $id;
		$info = Client_Service_Hd::getHd($id);
		$game = Resource_Service_Games::getGameAllInfo(array('id'=>$info['game_id']));
	    
		$clientVersion = $this->getInput('clientVersion');
		$uuid = $this->getInput('uuid');
		$this->shareSendAticket ( $id, $clientVersion, $uuid);

			
		//配置文件
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
		$this->assign('info', $info);
		$this->assign('isShare', $isShare);
		$this->assign('shareto', $shareto);
		$this->assign('qudao', $qudao);
		$this->assign('intersrc', $intersrc);
		$this->assign('source', $this->getSource());
		$this->assign('game', $game);
		
	}
	private function shareSendAticket($id, $clientVersion, $uuid) {
		// 1.5.5分享的赠送
		if(strnatcmp($clientVersion, '1.5.5') >= 0){
			if($uuid){
				$uuid = Common::encrypt(rawurldecode($uuid), 'DECODE');
				$configArr = array('uuid'=>$uuid,
						'content_type'=>Util_Activity_Context::CONTENT_TYPE_SHARE_ACTIVITY,
						'game_id'=>$id,
						'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
						'task_id'=>Util_Activity_Context::DAILY_TASK_SHARE_TASK_ID);
				$activity = new Util_Activity_Context(new Util_Activity_Share($configArr));
				$activity ->sendTictket();
			}
		}}

}