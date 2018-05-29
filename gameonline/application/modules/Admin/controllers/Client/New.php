<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_NewController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_New/index',
		'editUrl'=>'/admin/Client_New/index',
		'editPostUrl'=>'/admin/Client_New/edit_post',

	);
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Game_Service_Config::getAllConfig();
	    $page = intval($this->getInput('page'));

	    list(, $client_games) = Resource_Service_Games::getGames(array('status'=>1));
	    if($client_games){
		    $client_games = Common::resetKey($client_games, 'game_id');
		    $this->assign('client_games', $client_games);
		    $resource_game_ids = array_keys($client_games);
		    $this->assign('resource_game_ids', $resource_game_ids);
		    $params['id'] = array("IN", $resource_game_ids);
		    list($total, $games) = Resource_Service_Games::getList(1, $configs['game_rank_newnum'], $params);
	    }
	    $this->assign('games', $games);
	    
	    list(, $categorys) =  Resource_Service_Attribute::getsortList(1, 150,array('at_type'=>1));
		//categorys list
		$categorys = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
       
		list(, $subjects) = Client_Service_Subject::getAllSubject();
		$subjects = Common::resetKey($subjects, 'id');


		$subject_ids = Client_Service_Game::getIdxGameClientSubjects();
		$subject_ids = Common::resetKey($subject_ids, 'id');
		
		$category_ids = Resource_Service_Games::getIdxGameResourceCategorys();
		$category_ids = Common::resetKey($category_ids, 'game_id');
		
		$client_games = Common::resetKey($client_games, 'id');
		$this->assign('client_games', $client_games);
		
		//游戏专题
		$game_subjects = array();
		foreach($subject_ids as $k=>$v){
			if (!is_array($game_subjects[$v['resource_game_id']])) $game_subjects[$v['resource_game_id']] = array();
			array_push($game_subjects[$v['resource_game_id']], $subjects[$v['subject_id']]['title']);
		}
		
		//游戏分类
		$game_categorys = array();
		foreach($category_ids as $k=>$v){
			if (!is_array($game_categorys[$v['game_id']])) $game_categorys[$v['game_id']] = array();
			array_push($game_categorys[$v['game_id']], $categorys[$v['category_id']]['title']);
		}
		
		$this->assign('game_subjects', $game_subjects);
		$this->assign('game_categorys', $game_categorys);
		$this->assign('category_ids', $category_ids);
		$url = $this->actions['listUrl'].'/?';
		$this->assign('pager', Common::getPages($total, $page, $configs['game_rank_newnum'], $url));
		
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$client_num = $this->getInput('client_num');
		Game_Service_Config::setValue('client_num', $client_num);
		$this->output(0, '操作成功.');
	}
}
