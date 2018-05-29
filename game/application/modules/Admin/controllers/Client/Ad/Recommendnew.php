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
		'listEditUrl' => '/Admin/Client_Ad_Recommendlist/edit',
	    
	    'recommendAddUrl' => '/Admin/Client_Ad_Recommendnew/recommendAdd',
	    'recommendEditUrl' => '/Admin/Client_Ad_Recommendnew/recommendEdit',
	    'recommendDeleteUrl' => '/Admin/Client_Ad_Recommendnew/recommendDelete',
	    'recommendAddPostUrl' => '/Admin/Client_Ad_Recommendnew/recommendAddPost',
	    'recommendEditPostUrl' => '/Admin/Client_Ad_Recommendnew/recommendEditPost',
	    'recommendGamesUrl' => '/Admin/Client_Ad_Recommendnew/recommendGames',
	    'recommendGamesPostUrl' => '/Admin/Client_Ad_Recommendnew/recommendGamesPost',
	    'recommendGamesSortUrl' => '/Admin/Client_Ad_Recommendnew/recommendGamesSort',
	    'recommendGamesSortPostUrl' => '/Admin/Client_Ad_Recommendnew/recommendGamesSortPost',
	    'recommendImgAddPostUrl' => '/Admin/Client_Ad_Recommendnew/recommendImgAddPost',
	    'recommendImgEditPostUrl' => '/Admin/Client_Ad_Recommendnew/recommendImgEditPost',
	    
	    'uploadUrl' => '/Admin/Client_Ad_Recommendnew/upload',
	    'uploadPostUrl' => '/Admin/Client_Ad_Recommendnew/upload_post',
	);
	

	public function recommendAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('rec_type', Game_Service_RecommendNew::$rec_type);
	    $this->assign('day_id', $dayId);
	    $this->assignGroups();
	}
	
	public function recommendAddPostAction() {
	    $data = $this->getInput(array('title', 'content', 'pgroup', 'rec_type', 'day_id'));
		$dayId = $data['day_id'];
	    $userId = $this->userInfo['uid'];
	    $info = $this->cookRecommendData($data);
	    if(! isset($info['rec_type'])) $this->output(-1, '推荐方式不能为空.');
	    $info['create_time'] = Common::getTime();
	    $info['status'] = Game_Service_RecommendNew::RECOMMEND_INVALID_STATUS;
	    
	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommendList[] = array(Game_Manager_RecommendList::RECOMMEND_INFO => $info);
	    Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    
	    $this->output(0, '操作成功', count($recommendList));
	}
	
	public function recommendEditAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $id = $this->getInput('id');
	    
	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    
	    $this->assignGroups();
	    $this->assign('info', $info);
	    $this->assign('id', $id);
        $this->assign('day_id', $dayId);
	    $this->assign('rec_type', Game_Service_RecommendNew::$rec_type);
	}
	
	public function recommendEditPostAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $info = $this->getInput(array('title', 'content', 'pgroup'));
	    $postData = $this->cookRecommendData($info);
	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    $oldData = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    if (! $oldData) $this->output(-1, '编辑的内容不存在');

	    $editInfo = Game_Manager_RecommendList::getNewData($postData, $oldData);
	    $recommend[Game_Manager_RecommendList::RECOMMEND_INFO] = $editInfo;
	    $recommendList[$id - 1] = $recommend;
	    Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    
	    $this->output(0, '操作成功');
	}
	
	public function recommendGamesAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    if (! $info) $this->output(-1, '编辑的内容不存在');
	    if($info['rec_type'] == Game_Service_RecommendNew::REC_TYPE_LIST) {
	        $this->initList($recommend, $dayId, $id, $userId);
	    }else if($info['rec_type'] == Game_Service_RecommendNew::REC_TYPE_IMAGE) {
	        $this->initImage($recommend, $dayId, $id, $userId);
	    }
	}
	
	private function initList($recommend, $dayId, $id, $userId) {
	    $games = $recommend[Game_Manager_RecommendList::RECOMMEND_GAMES];
	    foreach ($games as $key => $value) {
	        $game = Game_Manager_RecommendList::getGameInfo($value['game_id']);
	        $games[$key] = array_merge($game, $value);
	    }
	    
	    $this->assign('hiddens', array('id' => $id, 'day_id' => $dayId));
	    $this->assign('games', $games);
	    
	    $this->assign('postUrl', $this->actions['recommendGamesPostUrl']);
	    $this->assign('nextStepUrl', $this->actions['recommendGamesSortUrl'] . "?day_id={$dayId}&id=" . $id);
	    $this->assign('preStepUrl', $this->actions['recommendEditUrl'] . "?day_id={$dayId}&id=" . $id);
	    Yaf_Dispatcher::getInstance()->disableView();
	    $this->getView()->display("common/games.phtml");
	}
	
	private function initImage($recommend, $dayId, $id, $userId) {
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    $img = $recommend[Game_Manager_RecommendList::RECOMMEND_IMAGE];
	    if($info['status'] == Game_Service_RecommendNew::RECOMMEND_INVALID_STATUS) {
	        $info['status'] = Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS;
	    }
	    $this->assign('img', $img);
	    $this->assign('info', $info);
	    $this->assign('day_id', $dayId);
	    $this->assign('id', $id);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	    Yaf_Dispatcher::getInstance()->disableView();
	    if(! $img) {
	        $this->getView()->display("client/ad/recommendnew/recommendimgadd.phtml");
	    }else{
	        $this->getView()->display("client/ad/recommendnew/recommendimgedit.phtml");
	    }
	}
	
	public function recommendGamesPostAction () {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $data = $this->getInput('games');
	    if(! $data) {
	        $this->output('-1', '推荐游戏为空，不可以提交');
	    }
	    $gameList = array_unique($data);
	    $gameSize = count($gameList);
	    if ($gameSize != count($data)) {
	        $this->output('-1', '有重复游戏，请删除后提交');
	    }
	    if ($gameSize <Game_Service_RecommendNew::SHOW_NUM) {
	        $this->output('-1', '列表至少添加'.Game_Service_RecommendNew::SHOW_NUM.'个游戏');
	    }

	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    if (! $recommend){
	        $this->output(- 1, '编辑的内容不存在');
	    }
	    $oldGamesList = array();
	    $oldGames = $recommend[Game_Manager_RecommendList::RECOMMEND_GAMES];
	    foreach ($oldGames as $game) {
	        $oldGamesList[$game['game_id']] = $game;
	    }
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    $games = array();
	    for ($i = 0; $i < $gameSize; $i++) {
	        $gameId = $gameList[$i];
	        if($oldGamesList[$gameId]) {
	            $game = $oldGamesList[$gameId];
	        }else{
	            $game = array('game_id' => $gameId, 'sort' => 0, 'game_status' => Game_Service_RecommendNew::GAME_OPEN_STATUS);
	        }
	        if(! $game['recommend_id'] && $info['id']) {
	            $game['recommend_id'] = $info['id'];
	        }
            $games[] = $game;
	    }
	    $recommend[Game_Manager_RecommendList::RECOMMEND_GAMES] = $games;
	    
	    $recommendList[$id - 1] = $recommend;
	    Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function recommendGamesSortAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];

	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    if($info['status'] == Game_Service_RecommendNew::RECOMMEND_INVALID_STATUS) {
	        $info['status'] = Game_Service_RecommendNew::RECOMMEND_OPEN_STATUS;
	    }
	    $list = $recommend[Game_Manager_RecommendList::RECOMMEND_GAMES];
	    foreach($list as $key=>$value){
	        $game = Game_Manager_RecommendList::getGameInfo($value['game_id']);
	        $list[$key] = array_merge($game, $value);
	    }
	    $this->assign('info', $info);
	    $this->assign('id', $id);
	    $this->assign('day_id', $dayId);
	    $this->assign('list', $list);
	}
	
	public function recommendGamesSortPostAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $sorts = $this->getInput('sort');
	    $status = $this->getInput('status');

	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    if(! $info) {
	        $this->output('-1', '操作失败.');
	    }
	    $games = $recommend[Game_Manager_RecommendList::RECOMMEND_GAMES];
	    foreach ($games as $key => $game) {
	        $game["sort"] = $sorts[$game["game_id"]];
	        $games[$key] = $game;
	    }
	    $sortCriteria = array(
	        'sort' => array(SORT_DESC, SORT_NUMERIC),
	        'game_id' => array(SORT_ASC, SORT_NUMERIC)
	    );
	    $games = Util_ArraySort::multiSort($games, $sortCriteria);
	    
	    $info['status'] = $status;
	    $recommend[Game_Manager_RecommendList::RECOMMEND_INFO] = $info;
	    $recommend[Game_Manager_RecommendList::RECOMMEND_GAMES] = $games;
	    $recommendList[$id - 1] = $recommend;
	    Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
	
	    $this->output('0', '操作成功.');
	}
	
	public function recommendImgAddPostAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $status = $this->getInput('status');
	    $data = $this->getInput(array('link_type', 'link', 'img'));

	    $postData = $this->cookRecommendImgData($data);
	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    if(! $info) {
	        $this->output('-1', '操作失败.');
	    }
	    $info['status'] = $status;
	    
	    $recommend[Game_Manager_RecommendList::RECOMMEND_INFO] = $info;
	    $recommend[Game_Manager_RecommendList::RECOMMEND_IMAGE] = $postData;
	    $recommendList[$id - 1] = $recommend;
	    Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function recommendImgEditPostAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $status = $this->getInput('status');
	    $requestData = $this->getInput(array('link_type', 'link', 'img'));
	    $postData = $this->cookRecommendImgData($requestData);
	
	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id - 1];
	    $info = $recommend[Game_Manager_RecommendList::RECOMMEND_INFO];
	    if (! $info) $this->output(-1, '编辑的内容不存在');
	    $info['status'] = $status;
	    $oldData = $recommend[Game_Manager_RecommendList::RECOMMEND_IMAGE];
	    $editInfo = Game_Manager_RecommendList::getNewData($postData, $oldData);
	    $recommend[Game_Manager_RecommendList::RECOMMEND_INFO] = $info;
	    $recommend[Game_Manager_RecommendList::RECOMMEND_IMAGE] = $editInfo;
	    $recommendList[$id - 1] = $recommend;
	    Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function recommendDeleteAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
	    unset($recommendList[$id - 1]);
	    Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功', array('href'=>$this->actions['editUrl'].'?dayId='.$dayId));
	}
	
	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function upload_postAction() {
	    $ret = Common::upload('img', 'ad');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	private function assignGroups() {
        list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
        $groups = Common::resetKey($groups, 'id');
        $all[0] = array("id" => 0, "title" => "全部");
        $groups = array_merge($all, $groups);
        $this->assign('groups', $groups);
	}
	
	private function cookRecommendData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($info['title']) > 15) {
            $this->output(- 1, '标题不能超过15个字.');
        }
		if(!isset($info['pgroup'])) $this->output(-1, '机组不能为空.');
		return $info;
	}
	
	private function cookRecommendImgData($requestData) {
		if(!isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
		if(!isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
		$result = Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link']);
		if($result) {
		    $this->output(-1, $result);
		}
		if(!$requestData['img']) $this->output(-1, '图片不能为空.');
		return $requestData;
	}
	
}
