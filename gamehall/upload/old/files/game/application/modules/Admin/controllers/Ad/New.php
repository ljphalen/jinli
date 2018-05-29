<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Ad_NewController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ad_New/index',

	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('game_num', $configs['game_num']);
	    $page = intval($this->getInput('page'));

	    $tmp = $games = array();
	    //游戏列表 
	    //list($total, $resource_games) = Resource_Service_Games::getList(1, $configs['game_num'], array('status'=>1), array('online_time'=>'DESC'));
	    list($total, $resource_games) = Client_Service_Taste::getList(1, $configs['game_num'], array('game_status'=>1), array('start_time'=>'DESC','game_id'=>'DESC'));
	    foreach($resource_games as $key=>$value) {
	    	$info = array();
	    	$info = Resource_Service_Games::getResourceGames($value['game_id']);
	    	if($info) {
	    		$games[] = $info;
	    		$tmp[] = $value['game_id'];
	    	}
	    }
	    $this->assign('games', $games);
	    
	    //专题
		list(, $subjects) = Client_Service_Subject::getAllSubject();
		$subjects = Common::resetKey($subjects, 'id');

		//专题索引
		$subject_ids = Client_Service_Game::getIdxGameClientSubjects();
		$subject_ids = Common::resetKey($subject_ids, 'id');

		//游戏专题
		$game_subjects = array();
		foreach($subject_ids as $k=>$v){
			if (!is_array($game_subjects[$v['resource_game_id']])) $game_subjects[$v['resource_game_id']] = array();
			array_push($game_subjects[$v['resource_game_id']], $subjects[$v['subject_id']]['title']);
		}
	
		//在线游戏版本
		$params['game_id'] = array('IN',$tmp);
		$params['status'] = 1;
		$oline_versions = Resource_Service_Games::getIdxVersionByResourceGameId($params);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		$this->assign('oline_versions', $oline_versions);
		$this->assign('game_subjects', $game_subjects);
	
	}
}
