<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author wupeng
 *
 */
class Client_Ad_RecommendnewController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Recommendnew/index',
		'addUrl' => '/Admin/Client_Ad_Recommendnew/add',
		'editUrl' => '/Admin/Client_Ad_Recommendnew/edit',
		'addPostUrl' => '/Admin/Client_Ad_Recommendnew/addPost',
		'editPostUrl' => '/Admin/Client_Ad_Recommendnew/editPost',
		'deleteUrl' => '/Admin/Client_Ad_Recommendnew/delete',
		'batchUpdateUrl'=>'/Admin/Client_Ad_Recommendnew/batchUpdate',
		'dayUrl'=>'/Admin/Client_Ad_RecommendDay/index',
		'gamesUrl' => '/Admin/Client_Ad_Recommendnew/games',
		'gamesPostUrl' => '/Admin/Client_Ad_Recommendnew/gamesPost',
		'closeUrl' => '/Admin/Client_Ad_Recommendnew/close',
		'closePostUrl' => '/Admin/Client_Ad_Recommendnew/closePost',
		'queryGameUrl' => '/Admin/Client_Ad_Recommendnew/queryGame',
	    'gamesSortUrl' => '/Admin/Client_Ad_Recommendnew/gamesSort',
		'gamesSortPostUrl' => '/Admin/Client_Ad_Recommendnew/gamesSortPost',
	);
	
	public $perpage = 20;
	
	/**
	 * 列表
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$search = $this->getInput(array('title', 'start_time', 'end_time', 'status'));
		$params = $this->getSearchParams($search);
		list($total, $list) = Game_Service_H5RecommendNew::getList($page, $this->perpage, $params, array('sort'=>'desc', 'id'=>'desc'));
		
		$this->setGameName($list);
		$this->assignGroups();
		
		$this->assign('search', $search);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	/**查询参数*/
	private function getSearchParams($search) {
	    $params = array();
		if ($search['title']) $params['title'] = array('like', $search['title']);
		if ($search['start_time']) $params['start_time'] = array('>=', strtotime($search['start_time']));
		if ($search['end_time'] && $search['start_time']) $params['end_time'] = array('<=', strtotime($search['end_time']));
		if ($search['status']) $params['status'] =  intval($search['status']) -1;
	    return $params;
	}
	
	/**设置游戏名称*/
	private function setGameName(&$list) {
	    foreach ($list as $key => $value) {
	        $gameName = "";
	        $games = Game_Service_H5RecommendGames::getGames($value["id"]);
	        foreach ($games as $game) {
	            if($gameName) {
	                $gameName .= ", ";
	            }
	            $game = Resource_Service_Games::getGameAllInfo(array('id'=>$game['game_id']));
	            $gameName .= $game['name'];
	        }
	        $value["game_name"] = $gameName;
	        $list[$key] = $value;
	    }
	}
	
	/**
	 * 添加
	 */
	public function addAction() {
		$this->assignGroups();
	}
	
	/**
	 * 添加内容提交
	 */
	public function addPostAction() {
        $data = $this->getInput(array('title', 'content', 'start_time', 'end_time', 'pgroup', 'sort'));
        $info = $this->cookData($data);
        $info['create_time'] = Common::getTime();
        $info['status'] = Game_Service_H5RecommendNew::RECOMMEND_INVALID_STATUS;
        
        $result = Game_Service_H5RecommendNew::addRecommendnew($info);
        if (! $result)
            $this->output(- 1, '操作失败');
        $this->output(0, '操作成功', $result);
	}
	
	/**
	 * 编辑
	 */
	public function editAction() {
        $id = $this->getInput('id');
        $info = Game_Service_H5RecommendNew::getRecommendnew(intval($id));
		$this->assignGroups();
        $this->assign('info', $info);
	}
	
	/**
	 * 编辑内容提交
	 */
	public function editPostAction() {
		$id = intval($this->getInput('id'));
		$info = $this->getInput(array('title', 'content', 'start_time', 'end_time', 'pgroup', 'sort'));
		$postData = $this->cookData($info);
		$editInfo = Game_Service_H5RecommendNew::getRecommendnew($id);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');
		
		$updateParams = $this->getUpdateParams($postData, $editInfo);
		if (count($updateParams) >0) {
            $ret = Game_Service_H5RecommendNew::updateRecommendnew($updateParams, $id);
            if (! $ret) $this->output(-1, '操作失败');
        }
        Game_Service_H5RecommendNew::updateRecomendListCacheData();
        $this->output(0, '操作成功', $id);
	}
	
	/**取需要更新的数据*/
	private function getUpdateParams($postData, $dbData) {
	    $updateParams = array();
	    foreach ($postData as $key => $value) {
	        if ($value == $dbData[$key])
	            continue;
	        $updateParams[$key] = $value;
	    }
	    return $updateParams;
	}
	
	/**
	 * 编辑
	 */
	public function gamesAction() {
	    $id = $this->getInput('id');
	    $info = Game_Service_H5RecommendNew::getRecommendnew(intval($id));
	    $games = Game_Service_H5RecommendGames::getGames($id);
        foreach ($games as $key => $value) {
            $game = $this->getGameInfo($value['game_id']);
            $games[$key] = array_merge($game, $value);
        }
        $this->assign('info', $info);
        $this->assign('games', $games);
	}
	
	/**游戏相关信息*/
	private function getGameInfo($gameId) {
	    $info = array();
	    $game = Resource_Service_Games::getGameAllInfo(array( 'id' => $gameId ));
	    $info['gameName'] = $game['name'];
	    $info['gameCategory'] = $game['category_title'];
	    $info['gameIcon'] = $game['img'];
	    $info['gameSize'] = $game['size'];
	    $info['gameVersion'] = $game['version'];
	    return $info;
	}

    /**
     * 编辑
     */
    public function gamesPostAction () {
        $id = intval($this->getInput('id'));
        $data = $this->getInput('games');
        if(!$data) {
            $this->output('-1', '推荐游戏为空，不可以提交');
        }
        $games = array_unique($data);
        $gameSize = count($games);
        if ($gameSize != count($data)) {
            $this->output('-1', '有重复游戏，请删除后提交');
        }
        if ($gameSize <Game_Service_H5RecommendNew::SHOW_NUM) {
            $this->output('-1', '列表至少添加'.Game_Service_H5RecommendNew::SHOW_NUM.'个游戏');
        }
        $info = Game_Service_H5RecommendNew::getRecommendnew($id);
        if (! $info)
            $this->output(- 1, '编辑的内容不存在');
        $ret = Game_Service_H5RecommendGames::updateItemsList($id, $games);
        if (! $ret)
            $this->output('-1', '操作失败.');
        Game_Service_H5RecommendNew::updateRecomendListCacheData();
        $this->output('0', '操作成功.');
    }
	
	/**
	 * 推荐游戏排序
	 */
	public function gamesSortAction() {
	    $id = intval($this->getInput('id'));
	    $list = Game_Service_H5RecommendGames::getGames($id);
	    foreach($list as $key=>$value){
            $game = $this->getGameInfo($value['game_id']);
            $list[$key] = array_merge($game, $value);
	    }
	    $info = Game_Service_H5RecommendNew::getRecommendnew($id);
	    if($info['status'] == Game_Service_H5RecommendNew::RECOMMEND_INVALID_STATUS) {
	        $info['status'] = Game_Service_H5RecommendNew::RECOMMEND_OPEN_STATUS;
	    }
	    $this->assign('info', $info);
	    $this->assign('id', $id);
	    $this->assign('list', $list);
	}
	
	/**
	 * 推荐游戏排序保存
	 */
	public function gamesSortPostAction() {
	    $id = $this->getInput('id');
	    $sort = $this->getInput('sort');
	    $status = $this->getInput('status');
	    
	    $info = Game_Service_H5RecommendNew::getRecommendnew(intval($id));
	    if(!$info) {
	        $this->output('-1', '操作失败.');
	    }
	    $ret = Game_Service_H5RecommendGames::updateItemsSort($id, $sort);
	    if (!$ret) $this->output('-1', '操作失败.');
	    
	    if($status != $info['status']) {
	        $data['status'] = $status;
	        Game_Service_H5RecommendNew::updateRecommendnew($data, $id);
	    }
	    
        Game_Service_H5RecommendNew::updateRecomendListCacheData();
	    $this->output('0', '操作成功.');
	}
	
	/**
	 * 删除
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Game_Service_H5RecommendNew::getRecommendnew($id);
		if (!$info) $this->output(-1, '无法删除');
		$result = Game_Service_H5RecommendNew::deleteRecommendnew($id);
		if (!$result) $this->output(-1, '操作失败');
        Game_Service_H5RecommendNew::updateRecomendListCacheData();
		$this->output(0, '操作成功');
	}
	
	/**
	 * 批量操作
	 */
	public function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='delete'){
			 $ret = Game_Service_H5RecommendNew::deleteRecommendnewList($info['ids']);
		}elseif ($info['action'] == 'sort') {
		    $ret =Game_Service_H5RecommendNew::updateListSort($info['ids'], $info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
        Game_Service_H5RecommendNew::updateRecomendListCacheData();
		$this->output('0', '操作成功.');
	}

	/**
	 * 推荐开关
	 */
	public function closeAction() {
        $this->assign('status', Game_Service_H5RecommendNew::closed_1_5_7_recommend() ? 1 : 0);
	}

	/**
	 * 推荐开关提交
	 */
	public function closePostAction() {
	    $status = $this->getInput('status');
	    Game_Service_Config::setValue(Game_Service_RecommendNew::CONFIG_KEY, $status);
	}
	
	/**
	 * 查询游戏
	 */
	public function queryGameAction() {
	    $name = $this->getInput('name');
	    $selected = $this->getInput('selected');
	    
	    //游戏名称检索
	    $gameParams['status'] = 1;
	    $gameParams['name'] = array('LIKE', $name);
	    $gameData = Resource_Service_Games::getsBy($gameParams);
        
	    $list = array();
	    foreach($gameData as $value){
	        if($selected && in_array($value['id'], $selected)) continue;
	        $game = $this->getGameInfo($value['id']);
	        $game['id'] = $value['id'];
	        $list[] = $game;
	    }
	    $this->output('0', '查询成功.', $list);
	}
	
	private function assignGroups() {
        list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
        $groups = Common::resetKey($groups, 'id');
        $all[0] = array("id" => 0, "title" => "全部");
        $groups = array_merge($all, $groups);
        $this->assign('groups', $groups);
	}
	
	/**
	 * 检查数据有效性
	 */
	private function cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
		if(!isset($info['pgroup'])) $this->output(-1, '机组不能为空.');
		if(!isset($info['sort'])) $this->output(-1, '排序为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		
		$info['start_time'] = strtotime(date('Y-m-d H:00:00', strtotime($info['start_time'])));
		$info['end_time'] = strtotime(date('Y-m-d H:00:00', strtotime($info['end_time'])));
		if($info['start_time'] >= $info['end_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
        
		return $info;
	}

}
