<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 后台公共查询链接地址
 * @author wupeng
 */
class CommonController extends Admin_BaseController {
	
	public $actions = array(
	);
	
    /**查询游戏*/
	public function queryGameListAction() {
	    $name = $this->getInput('name');
	    $page = $this->getInput('page');
	    $selected = $this->getInput('selected');
	    $name = html_entity_decode($name);
	    
	    //游戏名称检索
	    $gameParams['status'] = 1;
	    $gameParams['name'] = array('LIKE', $name);
	    if($selected) {
	        $gameParams['id'] = array('NOT IN', $selected);
	    }
        
	    if(! $page || $page <0) {
	        $page=1;
	    }
	    list($total, $gameData) = Resource_Service_Games::getList($page, 10, $gameParams);
	    
	    $data = array();
	    $list = array();
	    foreach($gameData as $value){
	        if($selected && in_array($value['id'], $selected)) continue;
	        $game = $this->getGameInfo($value['id']);
	        $game['id'] = $value['id'];
	        $list[] = $game;
	    }
	    $data['list'] = $list;
	    $data['total'] = $total;
	    $data['page'] = $page;
	    $data['pageSize'] = ceil($total/10);
	    $this->output('0', '查询成功.', $data);
	}
	
	/**查询游戏名称*/
	public function queryGameNameAction() {
	    $id = $this->getInput('id');
	    $game = Resource_Service_GameData::getGameAllInfo($id);
	    if(!$game) {
	        $this->output(-1, '游戏不存在.');
	    }
	    $this->output('0', $game['name']);
	}
	
	public function getGameListAction(){
	    $gameIds = $this->getInput('gameIds');
	    $gameIdsArr = explode(',', html_entity_decode($gameIds));
	    $gameParams['status'] = 1;
	    $gameParams['id'] = array('IN', $gameIdsArr);
	    $gameData = Resource_Service_Games::getsBy($gameParams);
	    $data = array();
	    $list = array();
	    foreach($gameData as $value){
	        $game = $this->getGameInfo($value['id']);
	        $game['id'] = $value['id'];
	        $list[] = $game;
	    }
	    $data['list'] = $list;
	    $data['total'] = count($list); 
	    $this->output('0', '初始化选中的游戏完成.', $data);
	}
	
	/**游戏相关信息*/
	private function getGameInfo($gameId) {
	    $info = array();
	    $game = Resource_Service_GameData::getGameAllInfo($gameId);
	    $info['gameId'] = $gameId;
	    $info['gameName'] = $game['name'];
	    $info['gameCategory'] = $game['category_title'];
	    $info['gameIcon'] = $game['img'];
	    $info['gameSize'] = $game['size'];
	    $info['gameVersion'] = $game['version'];
	    return $info;
	}
	
}
