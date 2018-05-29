<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class IndexController extends Game_BaseController{
	
	public $actions = array(
		'listUrl' => '/index/index',
		'detailUrl' => '/index/detail/',
		'tjUrl' => '/index/tj'
	);

	public $perpage = 100;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		//首页bannel
		list(, $bannel) = Game_Service_Ad::getCanUseNormalAds(1, 1, array('ad_ltype'=>11));
		$this->assign('bannel', $bannel[0]);
		
		//首页轮转广告
		list($ad_total, $ads) = Game_Service_Ad::getCanUseNormalAds(1, 4, array('ad_type'=>1));
		$this->assign('ads', $ads);
		
		//客户端
		$configs = Game_Service_Config::getAllConfig();
        unset($configs['game_react']);
		$this->assign('configs', $configs);
		
		//最新游戏
		$params['status'] = 1;
	     $params = array('status'=> 1,'game_status'=>1,'start_time'=>array('<=',Common::getTime()));	 
	    list($total, $new_game_list) = Client_Service_Taste::getList(1, $configs['game_num'], $params, array('start_time' => 'DESC','game_id' => 'DESC'));
	    $newgames = array();
	    foreach($new_game_list as $key=>$value) {
	    	$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
	    	if ($info) $newgames[] = $info;
	    }
		
		if($total < 4) $newgames = '';
		$this->assign('newgames', $newgames);
		
		//评测中心 疯玩网评测
		list(,$new_ids) = Client_Service_Tuijian::getCanUseTuijian(1, 10,array('ntype' => 2, 'status'=>1));
		$this->assign('evaluation', $new_ids);
		
		$new_ids = Common::resetKey($new_ids, 'n_id');
		$new_ids = array_unique(array_keys($new_ids));
		if($new_ids){
		  list(, $items) = Client_Service_News::getAdEvaluation(1, 10, array('id' => array('IN',$new_ids), 'status'=>1));
		}
		$items = Common::resetKey($items, 'id');
		$this->assign('tjnews', $items);
		
		
		//新闻中心 资讯+活动
		list(, $news) = Client_Service_News::getList(1, 4, array('ntype' => array('IN',array(1, 3)), 'create_time' => array('<=', Common::getTime()), 'status' => 1));
		$this->assign('type', array(1 => '资讯', 3 => '活动'));
		$this->assign('news', $news);
		
        //推荐专题
		list(, $subjects) = Game_Service_Ad::getCanUseNormalAds(1, 2, array('ad_type'=>3, 'status'=>1));
		if(count($subjects) % 2 != 0 && $subjects){
			array_pop($subjects);
		} 
		//推荐游戏
		list(, $games) = Game_Service_Ad::getCanUseNormalAds(1, 16, array('ad_type'=>2, 'status'=>1));
		$tmp = array();
		$n =1;
		foreach($games as $key=>$value){
			if(count($games) >= 4){
				if(($key +1) % 4 == 0 && $key <= count($games)){
					for($i=0;$i<4;$i++){
						$tmp[$n][] = $games[$key - 3 + $i];
					}
					$n++;
				}
			}
		}
		$games = $tmp;
		$this->assign('subjects', $subjects);
		$this->assign('newgames', $newgames);
		$this->assign('games', $games);
		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
		$webroot = Common::getWebRoot();
		$this->assign('tjUrl', $webroot.$this->actions['tjUrl']);
	}
	
	/**
	 * get hits
	 * @return boolean
	 */
	public function tjAction(){
		$id = intval($this->getInput('id'));
		$type = $this->getInput('type');
		$url = html_entity_decode(html_entity_decode($this->getInput('_url')));
		if ($id) {
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
		}
		if (strpos($url, "gionee.com")) {
			if (strpos($url, '?') === false) {
				$url = $url.'?t_bi='.$this->getSource();
			} else {
				$url = $url.'&t_bi='.$this->getSource();
			}
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
		$sc = $this->getInput('source');
		$shareto = $this->getInput('shareto');
		$qudao = $this->getInput('qudao');
		$isShare = $this->getInput('isShare');		
	    $checkAppVersion = $this->checkAppVersion();
	
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		
		$info = Resource_Service_Games::getGameAllInfo(array('id'=>$id));
		if($id != 117){
			$webroot = Common::getWebRoot();
			if(!$info['status']){
				$this->redirect($webroot);
				exit;
			}
		} 
		// 1.5.5分享的赠送
		$clientVersion = $this->getInput('clientVersion');
		$uuid = $this->getInput('uuid');
		$this->shareSendAtIcket ( $id, $uuid, $clientVersion );

		
		$this->assign('info', $info);
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		
		//资讯评测
		list(, $news) = Client_Service_News::getGameNews(1, 10, array('hot'=>'1','game_id'=>$id,'status'=>1));
		
		unset($configs['game_react']);
		$this->assign('isShare', $isShare);
		$this->assign('configs', $configs);
		$this->assign('shareto', $shareto);
		$this->assign('qudao', $qudao);
		$this->assign('intersrc', $intersrc);
		$this->assign('source', $this->getSource());
		$this->assign('news', $news);
		$this->assign('sc', $sc);
		if ($this->isAjax()) {
			$this->output(0, '', array('info'=>$info, 'gimgs'=>$info['img']));
		}
	}
	
	 private function shareSendAtIcket($id, $uuid, $clientVersion) {
		// 1.5.5分享的赠送
		if(strnatcmp($clientVersion, '1.5.5') >= 0){
			if($uuid){
				$uuid =Common::encrypt(rawurldecode($uuid), 'DECODE');
				$configArr = array('uuid'=>$uuid,
						           'content_type'=>Util_Activity_Context::CONTENT_TYPE_SHARE_GAME,
								   'game_id'=>$id,
						           'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
						           'task_id'=>Util_Activity_Context::DAILY_TASK_SHARE_TASK_ID);
				$shareObject = new Util_Activity_Context(new Util_Activity_Share($configArr));
				$shareObject ->sendTictket();
			}
		}
	}

}
