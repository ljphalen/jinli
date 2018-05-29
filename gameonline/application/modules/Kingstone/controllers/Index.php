<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class IndexController extends Kingstone_BaseController{
	
	public $actions = array(
		'listUrl' => '/kingstone/index/index',
		'detailUrl' => '/kingstone/index/detail/',
		'tjUrl' => '/kingstone/index/tj'
	);

	public $perpage = 10;
	private $_recoGames = array();
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
		if (!$t_bi) $this->setSource();
		
 		$this->assign('source', $this->getSource());
		$webroot = Common::getWebRoot();
		$this->assign('tjUrl', $webroot.$this->actions['tjUrl']);
		$this->assign('apk_version', $tj[1]);
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
		
		//获取系统模版目录
		$tplPath = $this->getViewpath() ;
		//游戏大厅版本判断
		$checkVer = $this->checkAppVersion();
		//1.4.8之前版本
		$tpl= 'detail/v0';
		$this->_detailv0($id);
		if($checkVer >= 2){
			//1.4.8之后版本
			$tpl= 'detail/v1';
			$this->_detailv1($id);
		}
		
        $this->display($tpl);
        exit;
	}
	
	private function _detailv0($gameid){
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) {
			$this->setSource();
			$t_bi = $this->getSource();
		}
		$intersrc = $this->getInput('intersrc');
		$preview = $this->getInput('preview');
		$app_version = Util_Cookie::get('version', true);
		if(!$app_version){
			$sp = $this->getInput('sp');
			$tj = explode('_',$sp);
			Util_Cookie::set("app_version", $tj[1], true, Common::getTime() + strtotime("+1 days"));
			$version = $tj[1];
		}
		
		$info = Resource_Service_Games::getGameAllInfo(array('id'=>$gameid), $preview);
		
		$game_ids = Client_Service_Recommend::getRecommendGames(array('GAMEC_RESOURCE_ID'=>$gameid));
		if($game_ids){
			foreach($game_ids as $key=>$value){
				$tmp = array();
				$tmp = Resource_Service_Games::getGameAllInfo(array('id'=>$value['GAMEC_RECOMEND_ID']));
				if($tmp['status']){
					$games[] = $tmp;
				}
			}
		}
		
		//推荐游戏
		$this->_recoGames = $games;
		
		$this->assign('info', $info);
		$this->assign('games', $games);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('app_version', $app_version);
	}
	
	private function _detailv1($gameid){
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) {
			$this->setSource();
			$t_bi = $this->getSource();
		}
		$intersrc = $this->getInput('intersrc');
		
		//加入礼包
		$params = array(
				'game_id' => intval($gameid),
				'status'=>1,
				'effect_start_time' => array('<=', Common::getTime()),
				'effect_end_time' => array('>', Common::getTime())
		);
		$gifts = Client_Service_Gift::getsBy($params);
		$this->assign('gifts', $gifts);
		//加入评测
		$params = array(
				'ntype' =>2,
				'status' => 1,
				'game_id' => intval($gameid),
				'create_time'=> array('<=', Common::getTime()),
		);
		list(,$evaluation) = Client_Service_News::getList(1, 1, $params);
		$this->assign('evaluation', $evaluation);
		
		//加入攻略
		$params = array(
				'ntype' =>4,
				'status' => 1,
				'game_id' => intval($gameid),
				'create_time'=> array('<=', Common::getTime()),
		);
		list(,$strategy) = Client_Service_News::getList(1, 1, $params);
		$this->assign('strategy', $strategy);
		
		//相关推荐
		$games = $this->_recoGames;
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach ($games as $key => $value) {
			//加入评测链接
			$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($value['id']);
			$evaluationUrl = '';
			if ($evaluationId) {
				$evaluationUrl = ',评测,' . $webroot . '/kingstone/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $intersrc . '&t_bi=' . $t_bi;
			}

			$href = $webroot . '/kingstone/index/detail/?id=' . $value['id'] . '&pc=1&intersrc=gltj' . $gameid.'_GID'.$value['id'] . '&t_bi=' .$t_bi ;
			$tempStr = sprintf("%s,%s,%s",'游戏详情',$href,$value['id']);
			if($evaluationId) $tempStr .= $evaluationUrl;
			 $value['infpage-v2'] = $tempStr;
			$temp[] = $value;
		}
		
		$this->assign('games', $temp);
	}
	
	
}
