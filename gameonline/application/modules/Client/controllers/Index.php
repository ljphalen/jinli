<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class IndexController extends Client_BaseController{
	
	public $actions = array(
		'listUrl' => '/index/index',
		'detailUrl' => '/index/detail/',
		'tjUrl' => '/index/tj'
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
		if (!id || !$type || !strpos($url, 'game.3gtest.gionee.com') || !strpos($url, 'ala.game.3gtest.gionee.com')) return false;
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
		$sp = $this->getInput('sp');
		
		//获取系统模版目录
		$tplPath = $this->getViewpath() ;
		//游戏大厅版本判断
		$checkVer = $this->checkAppVersion();
		$sp = explode('_', $sp);
		$version = $sp[1];
		
		//1.4.8之前版本
		$tpl= 'detail/v0';
		$this->_detailv0($id);
		if($checkVer >= 2 ){
			//1.4.8之后版本
			$tpl= 'detail/v1';
			$this->_detailv1($id);
		} 
		if($checkVer >= 3 ){
			//1.5.1之后版本
			$tpl= 'detail/v2';
			$this->_detailv2($id);
		}
		if($checkVer >= 4 ){
			//1.5.2之后版本
			$tpl= 'detail/v3';
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
		
		//推荐游戏
		$games = $this->_besttjData($gameid);
		
		$this->assign('info', $info);
		$this->assign('games', $games);
		$this->assign('gameid', $gameid);
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
		$games = $this->_besttjData($gameid);
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach ($games as $key => $value) {
			//加入评测链接
			$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($value['id']);
			$evaluationUrl = '';
			if ($evaluationId) {
				$evaluationUrl = ',评测,' . $webroot . '/client/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $intersrc . '&t_bi=' . $t_bi;
			}

			$href = $webroot . '/client/index/detail/?id=' . $value['id'] . '&pc=1&intersrc=gltj' . $gameid.'_GID'.$value['id'] . '&t_bi=' .$t_bi ;
			$tempStr = sprintf("%s,%s,%s",'游戏详情',$href,$value['id']);
			if($evaluationId) $tempStr .= $evaluationUrl;
			 $value['infpage-v2'] = $tempStr;
			$temp[] = $value;
		}
		
		$this->assign('gameid', $gameid);
		$this->assign('games', $temp);
	}
	
	
	private function _detailv2($gameid){
		//增加活动
		$params['game_id'] = $gameid;
		$params['start_time'] = array('<=',Common::getTime());
		$params['status'] = 1;
		$orderBy = array('sort'=>'DESC','start_time'=>'DESC');
		list(,$hd) = Client_Service_Hd::getList(1, 1, $params, $orderBy);
		$this->assign('hd', $hd);
		$this->assign('gameid', $gameid);
	}
	
	/**
	 * 推荐
	 * @param unknown_type $gameid
	 * @return array
	 */
	private function _besttjData($gameid){
		if(!$gameid) return '';
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
		return $games;
	}
	
}
