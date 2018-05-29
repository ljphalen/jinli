<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游频道
 * Game_SinglegameController
 * @author wupeng
 */
class Game_SinglegameController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Game_Singlegame/index',

		'editUrl' => '/Admin/Game_Singlegame/edit',
		'editPostUrl' => '/Admin/Game_Singlegame/editPost',
		'copyUrl' => '/Admin/Game_Singlegame/copy',
	    'editlogUrl' => '/Admin/Game_Singlegame/editlog',

	    'bannerAddUrl' => '/Admin/Game_Singlegame/bannerAdd',
	    'bannerEditUrl' => '/Admin/Game_Singlegame/bannerEdit',
	    'bannerDeleteUrl' => '/Admin/Game_Singlegame/bannerDelete',
	    'bannerAddPostUrl' => '/Admin/Game_Singlegame/bannerAddPost',
	    'bannerEditPostUrl' => '/Admin/Game_Singlegame/bannerEditPost',

	    'listGameAddUrl' => '/Admin/Game_Singlegame/listGameAdd',
	    'listGameEditUrl' => '/Admin/Game_Singlegame/listGameEdit',
	    'listGameAddPostUrl' => '/Admin/Game_Singlegame/listGameAddPost',
	    'listGameEditPostUrl' => '/Admin/Game_Singlegame/listGameEditPost',
	    'listGameSelectUrl' => '/Admin/Game_Singlegame/listGameSelect',
	    'listGameSelectPostUrl' => '/Admin/Game_Singlegame/listGameSelectPost',
	    'listGameSortUrl' => '/Admin/Game_Singlegame/listGameSort',
	    'listGameSortPostUrl' => '/Admin/Game_Singlegame/listGameSortPost',

	    'listImgAddUrl' => '/Admin/Game_Singlegame/listImgAdd',
	    'listImgEditUrl' => '/Admin/Game_Singlegame/listImgEdit',
	    'listImgAddPostUrl' => '/Admin/Game_Singlegame/listImgAddPost',
	    'listImgEditPostUrl' => '/Admin/Game_Singlegame/listImgEditPost',

	    'aloneAddUrl' => '/Admin/Game_Singlegame/aloneAdd',
	    'aloneAddPostUrl' => '/Admin/Game_Singlegame/aloneAddPost',
	    'aloneEditUrl' => '/Admin/Game_Singlegame/aloneEdit',
	    'aloneEditPostUrl' => '/Admin/Game_Singlegame/aloneEditPost',

	    'giftAddUrl' => '/Admin/Game_Singlegame/giftAdd',
	    'giftEditUrl' => '/Admin/Game_Singlegame/giftEdit',
	    'giftAddPostUrl' => '/Admin/Game_Singlegame/giftAddPost',
	    'giftEditPostUrl' => '/Admin/Game_Singlegame/giftEditPost',
	    'queryGiftNameUrl' => '/Admin/Game_Singlegame/queryGiftName',

	    'uploadUrl' => '/Admin/Game_Singlegame/upload',
	    'uploadPostUrl' => '/Admin/Game_Singlegame/upload_post',

	    'listDeleteUrl' => '/Admin/Game_Singlegame/listDelete',
	    'sortPostUrl' => '/Admin/Game_Singlegame/sortPost',
	);
	
	public function indexAction() {
	    $month = $this->getInput('date');
	    if(! $month) $month = date("Y-m");
	    $month = strtotime($month);
	    $list = $this->getMonthData($month);
	    $lastMonthDay = strtotime("-1 day", $month);
	    $lastList = Game_Service_SingleRecommend::getSingleRecommend($lastMonthDay);
	    $this->assign('month', $month);
	    $this->assign('before', $lastList != 0);
	    $this->assign('list', $list);
	}

	private function getMonthData($month) {
	    $searchParams = array();
	    $nextMonth = strtotime("+1 months", $month);
	    $searchParams['day_id'][] = array(">=", $month);
	    $searchParams['day_id'][] = array("<", $nextMonth);
	    $sortParams = array('day_id' => 'DESC');
	    $list = Game_Service_SingleRecommend::getSingleRecommendListBy($searchParams, $sortParams);
	    $list = Game_Manager_SingleGameRecommend::initMonthList($list, $month);
	    return $list;
	}

	public function editAction() {
	    $from = 0;//链接来源::0:编辑,1:编辑回调
	    $keys = $this->getInput(array('day_id', 'dayId'));
	    if($keys['day_id']) {
	        $dayId = strtotime($keys['day_id']);
	    }elseif ($keys['dayId']) {
	        $dayId = $keys['dayId'];
	        $from = 1;
	    }else{
	        exit();
	    }
	    $userId = $this->userInfo['uid'];
	    if($from == 0) {
	        Game_Manager_SingleGameRecommend::loadRecommendList($dayId, $userId);
	    }
	    $data = $this->initPageData($dayId, $userId);
	    
	    $this->assign('data', $data);
	    $log = $this->getEditLog($dayId);
	    $this->assign('log', $log);
	    $this->assign('day_id', $dayId);
	    $this->assign('from', $from);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function editPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    if(! $userId) {
	        exit();
	    }
	    $banner = html_entity_decode($this->getInput('banner'));
	    $recommend = html_entity_decode($this->getInput('recommend'));
	    $bannerSort = json_decode($banner);
	    $recommendSort = json_decode($recommend);
	    $bannerList = Game_Manager_SingleGameRecommend::getRecommendBanner($dayId, $userId);
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	
	    if(! $bannerList && ! $recommendList) {
	        $this->output(-1, '没有添加数据');
	    }
	    $bannerCounts = count($bannerSort);
	    $listCounts = count($recommendSort);
	    if(count($bannerList) != $bannerCounts || count($recommendList) != $listCounts) {
	        $this->output(-1, '没有添加数据');
	    }
	    foreach ($bannerSort as $index) {
	        $banner = $bannerList[$index];
	        $banner['sort'] = $bannerCounts--;
	        $bannerList[$index] = $banner;
	    }
	    foreach ($recommendSort as $index) {
	        $rec = $recommendList[$index][Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	        $rec['sort'] = $listCounts--;
	        $recommendList[$index][Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $rec;
	    }
	    
	    $flg = Game_Service_SingleRecommend::saveRecommend($dayId, $bannerList, $recommendList);
	    if(! $flg) {
	        $this->output(-1, '保存失败.');
	    }
        
	    $editInfo = Game_Service_SingleRecommend::getSingleRecommend($dayId);
	    if (! $editInfo) {
	        $recList = array();
	        $recList['day_id'] = $dayId;
	        Game_Service_SingleRecommend::addSingleRecommend($recList);
	    }
	
	    if(strtotime(date("Y-m-d")) == $dayId) {
	        Async_Task::execute('Async_Task_Adapter_UpdateSingleRecCache', 'update');
	    }
	    Game_Manager_SingleGameRecommend::deleteRecommend($dayId, $userId);
	    Game_Manager_SingleGameRecommend::addLog($dayId, $userId);
	    $this->output(0, '操作成功.');
	}
	
	public function bannerAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('day_id', $dayId);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function bannerAddPostAction() {
	    $requestData = $this->getInput(array('title', 'link_type', 'link', 'img', 'img_high', 'day_id', 'status'));
	    if(! ($requestData['day_id'])) $this->output(-1, '生效日期不能为空.');
	    $postData = $this->checkBannerData($requestData);
	    $dayId = $requestData['day_id'];
	    $userId = $this->userInfo['uid'];
	    $bannerList = Game_Manager_SingleGameRecommend::getRecommendBanner($dayId, $userId);
	    if(count($bannerList) >=4) {
	        $this->output(-1, 'Banner图不能多于4个.');
	    }
	    $bannerList[] = $postData;
	    Game_Manager_SingleGameRecommend::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功');
	}
	
	public function bannerEditAction() {
	    $dayId = $this->getInput('day_id');
	    $index = $this->getInput('id');
	    $userId = $this->userInfo['uid'];
	
	    $bannerList = Game_Manager_SingleGameRecommend::getRecommendBanner($dayId, $userId);
	    $info = $bannerList[$index];
	
	    $this->assign('id', $index);
	    $this->assign('day_id', $dayId);
	    $this->assign('info', $info);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function bannerEditPostAction() {
	    $dayId = $this->getInput('day_id');
	    $index = $this->getInput('id');
	    $requestData = $this->getInput(array('title', 'link_type', 'link', 'img', 'img_high', 'status'));
	    if(! $dayId) $this->output(-1, '生效日期不能为空.');
	    $postData = $this->checkBannerData($requestData);
	    $userId = $this->userInfo['uid'];
	
	    $bannerList = Game_Manager_SingleGameRecommend::getRecommendBanner($dayId, $userId);
	    $editInfo = $bannerList[$index];
	    if (! $editInfo) $this->output(-1, '编辑的内容不存在');
	
	    $editInfo = Game_Manager_SingleGameRecommend::getNewData($postData, $editInfo);
	    $bannerList[$index] = $editInfo;
	    Game_Manager_SingleGameRecommend::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功.');
	}
	
	public function bannerDeleteAction() {
	    $dayId = $this->getInput('day_id');
	    $index = $this->getInput('id');
	    $userId = $this->userInfo['uid'];
	    $bannerList = Game_Manager_SingleGameRecommend::getRecommendBanner($dayId, $userId);
	    array_splice($bannerList, $index, 1);
	    Game_Manager_SingleGameRecommend::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功');
	}
	
	public function listImgAddAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $this->assign('day_id', $dayId);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	    $this->assign('status_list', Game_Service_SingleList::$status_list);
	}
	
	public function listImgEditAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    $img = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_IMAGE];
	    $this->assign('img', $img);
	    $this->assign('info', $info);
	    $this->assign('day_id', $dayId);
	    $this->assign('id', $index);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	    $this->assign('status_list', Game_Service_SingleList::$status_list);
	}
	
	public function listImgAddPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $data = $this->getInput(array('link_type', 'link', 'img'));
	    $info = $this->getInput(array('title', 'status', 'day_id'));
		if(! $info['title']) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($info['title']) > 15) {
            $this->output(- 1, '标题不能超过15个字.');
        }
	    $info['rec_type'] = Game_Service_SingleList::REC_TYPE_IMAGE;
	    $img = $this->cookRecommendImgData($data);
	    
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $info;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_IMAGE] = $img;
	    $recommendList[] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listImgEditPostAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $mainInputData = $this->getInput(array('title', 'status'));
		if(! isset($mainInputData['title'])) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($mainInputData['title']) > 15) {
            $this->output(- 1, '标题不能超过15个字.');
        }
	    
	    $imgInputData = $this->getInput(array('link_type', 'link', 'img'));
	    $imgData = $this->cookRecommendImgData($imgInputData);
	
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    if (! $info) $this->output(-1, '编辑的内容不存在');
	    $info['title'] = $mainInputData['title'];
	    $info['status'] = $mainInputData['status'];
	    $oldImgData = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_IMAGE];
	    $editInfo = Game_Manager_SingleGameRecommend::getNewData($imgData, $oldImgData);
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $info;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_IMAGE] = $editInfo;
	    $recommendList[$index] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listGameAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('list_template', Game_Service_SingleList::$list_template);
	    $this->assign('day_id', $dayId);
	    $this->assignGroups();
	}
	
	public function listGameAddPostAction() {
	    $data = $this->getInput(array('title', 'content', 'day_id', 'pgroup', 'template'));
	    $dayId = $data['day_id'];
	    $userId = $this->userInfo['uid'];
	    $info = $this->cookListData($data);
	    $info['rec_type'] = Game_Service_SingleList::REC_TYPE_LIST;
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommendList[] = array(Game_Manager_SingleGameRecommend::SINGLEGAME_INFO => $info);
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功', count($recommendList)-1);
	}
	
	public function listGameEditAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $index = $this->getInput('id');
	    
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    
	    $this->assignGroups();
	    $this->assign('info', $info);
	    $this->assign('id', $index);
	    $this->assign('day_id', $dayId);
	    $this->assign('list_template', Game_Service_SingleList::$list_template);
	}
	
	public function listGameEditPostAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $listData = $this->getInput(array('title', 'content', 'pgroup', 'template'));
	    $postData = $this->cookListData($listData);
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $oldData = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    if (! $oldData) $this->output(-1, '编辑的内容不存在');
	    
	    $editInfo = Game_Manager_SingleGameRecommend::getNewData($postData, $oldData);
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $editInfo;
	    $recommendList[$index] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功');
	}
	
	public function listGameSelectAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	     
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    if (! $info) $this->output(-1, '编辑的内容不存在');
	    
	    $games = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GAMES];
	    foreach ($games as $key => $value) {
	        $game = Game_Manager_SingleGameRecommend::getGameInfo($value['game_id']);
	        $games[$key] = array_merge($game, $value);
	    }
	    
	    $this->assign('hiddens', array('id' => $index, 'day_id' => $dayId));
	    $this->assign('games', $games);
	    
	    $this->assign('postUrl', $this->actions['listGameSelectPostUrl']);
	    $this->assign('nextStepUrl', $this->actions['listGameSortUrl'] . "?day_id={$dayId}&id=" . $index);
	    $this->assign('preStepUrl', $this->actions['listGameEditUrl'] . "?day_id={$dayId}&id=" . $index);
	    Yaf_Dispatcher::getInstance()->disableView();
	    $this->getView()->display("common/games.phtml");
	}
	
	public function listGameSelectPostAction () {
	    $index = intval($this->getInput('id'));
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
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    if (! $recommend){
	        $this->output(- 1, '编辑的内容不存在');
	    }
	    /**单机不需要限制游戏数量
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    $needGameCounts = Game_Service_SingleList::$list_template_counts[$info['template']];
	    if ($gameSize < $needGameCounts) {
	        $this->output('-1', '列表至少添加'.$needGameCounts.'个游戏');
	    }
	    */
	    $oldGamesList = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GAMES];
	    $oldGamesList = Common::resetKey($oldGamesList, 'game_id');
	    $newGamesList = array();
	    for ($i = 0; $i < $gameSize; $i++) {
	        $gameId = $gameList[$i];
	        if($oldGamesList[$gameId]) {
	            $game = $oldGamesList[$gameId];
	        }else{
	            $game = array('game_id' => $gameId, 'sort' => 0);
	        }
	        $newGamesList[] = $game;
	    }
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GAMES] = $newGamesList;
	    $recommendList[$index] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listGameSortAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];

	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $gameList = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GAMES];
	    $sort = count($gameList);
	    foreach($gameList as $key=>$value){
	        $game = Game_Manager_SingleGameRecommend::getGameInfo($value['game_id']);
	        $value['sort'] = $sort--;
	        $gameList[$key] = array_merge($game, $value);
	    }
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    if(! isset($info['status'])) {
	        $info['status'] = Game_Service_SingleList::STATUS_OPEN;
	    }
	    $this->assign('id', $index);
	    $this->assign('day_id', $dayId);
	    $this->assign('list', $gameList);
	    $this->assign('info', $info);
	    $this->assign('status_list', Game_Service_SingleList::$status_list);
	}
	
	public function listGameSortPostAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $sorts = $this->getInput('sort');
	    $status = $this->getInput('status');
	
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    if(! $info) {
	        $this->output('-1', '操作失败.');
	    }
	    $games = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GAMES];
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
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GAMES] = $games;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $info;
	    $recommendList[$index] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listDeleteAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    array_splice($recommendList, $index, 1);
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功');
	}
	
	public function aloneAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('status_list', Game_Service_SingleList::$status_list);
	    $this->assign('alone_template', Game_Service_SingleList::$alone_template);
	    $this->assign('day_id', $dayId);
	    $this->assignGroups();
	}
	
	public function aloneAddPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $data = $this->getInput(array('link_type', 'link'));
	    $info = $this->getInput(array('title', 'content', 'pgroup', 'status', 'day_id'));
	    $info = $this->cookAloneData($info);
	    $info['rec_type'] = Game_Service_SingleList::REC_TYPE_ALONE;
	    $alone = $this->cookRecommendAloneData($data);
	    
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $info;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_ALONE] = $alone;
	    $recommendList[] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function aloneEditAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    $alone = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_ALONE];
	    $this->assign('alone', $alone);
	    $this->assign('info', $info);
	    $this->assign('day_id', $dayId);
	    $this->assign('id', $index);
	    $this->assignGroups();
	    $this->assign('status_list', Game_Service_SingleList::$status_list);
	    $this->assign('alone_template', Game_Service_SingleList::$alone_template);
	}

	public function aloneEditPostAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $data = $this->getInput(array('link_type', 'link'));
	    $infoData = $this->getInput(array('title', 'content', 'pgroup', 'status'));
	    $infoData = $this->cookAloneData($infoData);
	    $aloneData = $this->cookRecommendAloneData($data);
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $editInfo = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    $alonInfo = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_ALONE];
	    if (! $editInfo) $this->output(-1, '编辑的内容不存在');
	    $editInfo['title'] = $infoData['title'];
	    $editInfo['status'] = $infoData['status'];
	    $editInfo['pgroup'] = $infoData['pgroup'];
	    $editInfo['content'] = $infoData['content'];
	    $alonInfo['link_type'] = $aloneData['link_type'];
	    $alonInfo['link'] = $aloneData['link'];
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $editInfo;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_ALONE] = $alonInfo;
	    $recommendList[$index] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function giftAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('status_list', Game_Service_SingleList::$status_list);
	    $this->assign('day_id', $dayId);
	}
	
	public function giftEditAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    $giftList = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GIFT];
	    foreach ($giftList as $key => $gift) {
            $giftInfo = Client_Service_Gift::getGift($gift['gift_id']);
	        $gift['giftName'] = $giftInfo['name'];
	        $giftList[$key] = $gift;
	    }
	    $this->assign('giftList', $giftList);
	    $this->assign('info', $info);
	    $this->assign('day_id', $dayId);
	    $this->assign('id', $index);
	    $this->assign('status_list', Game_Service_SingleList::$status_list);
	}

	public function giftAddPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $data = $this->getInput(array('sort', 'giftId'));
	    $infoData = $this->getInput(array('title', 'day_id', 'status'));
	    $giftData = $this->cookRecommendGiftData($infoData, $data);
	    $giftList =  array();
	    foreach ($giftData as $giftId => $sort) {
	        $giftList[] = array('gift_id' => $giftId, 'sort' => $sort);
	    }
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $infoData['rec_type'] = Game_Service_SingleList::REC_TYPE_GIFT;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $infoData;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GIFT] = $giftList;
	    $recommendList[] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}

	public function giftEditPostAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $data = $this->getInput(array('sort', 'giftId'));
	    $infoData = $this->getInput(array('title', 'day_id', 'status'));
	    $giftData = $this->cookRecommendGiftData($infoData, $data);
	    
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $editInfo = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	    $giftList = $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GIFT];
	    if (! $editInfo) $this->output(-1, '编辑的内容不存在');
	    $editInfo['title'] = $infoData['title'];
	    $editInfo['status'] = $infoData['status'];
	    $giftList = Common::resetKey($giftList, 'gift_id');
	    $newGiftList =  array();
	    foreach ($giftData as $giftId => $sort) {
	        if($giftList[$giftId]) {
	            $giftList[$giftId]['sort'] = $sort;
	            $newGiftList[] = $giftList[$giftId];
	        }else{
	            $newGiftList[] = array('gift_id' => $giftId, 'sort' => $sort);
	        }
	    }
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $editInfo;
	    $recommend[Game_Manager_SingleGameRecommend::SINGLEGAME_GIFT] = $newGiftList;
	    $recommendList[$index] = $recommend;
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	/**查询礼包名称*/
	public function queryGiftNameAction() {
		$giftId = $this->getInput('giftId');
		$giftInfo = Client_Service_Gift::getGift($giftId);
		if(! $giftInfo) $this->output(-1, '礼包不存在.');
		if(Client_Service_Gift::GIFT_STATE_OPENED != $giftInfo['status']) {
			$this->output(-1, '该礼包已下线.');
		}
		if(Resource_Service_Games::STATE_ONLINE != $giftInfo['game_status']) {
			$this->output(-1, '该礼包的游戏已下线.');
		}
		if (Common::getTime() >= $giftInfo['effect_end_time']) {
			$this->output(-1, '该礼包已过期.');
		}
		$temp = array();
		$this->output(0, html_entity_decode($giftInfo['name'], ENT_QUOTES));
	}
	
	public function editlogAction() {
	    $perpage = 20;
	    $page = intval($this->getInput('page'));
	    $dayId = intval($this->getInput('day_id'));
	    if ($page < 1) $page = 1;
	    $searchParams = array('day_id' => $dayId);
	    $sortParams = array('id' => 'DESC');
	    list($total, $list) = Game_Service_SingleEditLog::getPageList($page, $perpage, $searchParams, $sortParams);
	    foreach ($list as $key => $value) {
	        $user = Admin_Service_User::getUser($value['uid']);
	        $list[$key]['username'] = $user['username'];
	    }
	    $requestData=array();
	    $this->assign('day_id', $dayId);
	    $this->assign('list', $list);
	    $this->assign('total', $total);
	    $url = $this->actions['editlogUrl'].'?' . http_build_query($requestData) . '&';
	    $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
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

	private function checkBannerData($requestData) {
	    if(! ($requestData['title'])) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($requestData['title']) > 10) {
            $this->output(- 1, '标题不能超过10个字.');
        }
	    if(! isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
	    if(! isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
	    $result = Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link']);
	    if($result) {
	        $this->output(-1, $result);
	    }
	    if(! ($requestData['img'])) $this->output(-1, '普通图片不能为空.');
	    if(! ($requestData['img_high'])) $this->output(-1, '高清图片不能为空.');
	    return $requestData;
	}
	
	private function assignGroups() {
        list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
        array_unshift($groups, array("id" => 0, "title" => "全部"));
        $groups = Common::resetKey($groups, 'id');
        $this->assign('groups', $groups);
	}
	
	private function cookListData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($info['title']) > 15) {
            $this->output(- 1, '标题不能超过15个字.');
        }
		if(!isset($info['pgroup'])) $this->output(-1, '机组不能为空.');
		return $info;
	}

	private function cookRecommendGiftData($infoData, $listData) {
		if(!$infoData['title']) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($infoData['title']) > 15) {
            $this->output(- 1, '标题不能超过15个字.');
        }
	    $sortArr = $listData['sort'];
	    $giftIdArr = $listData['giftId'];
	    if(! $giftIdArr) {
	        $this->output(-1, "礼包不可以为空");
	    }
	    $giftArr = array();
	    foreach ($giftIdArr as $key => $giftId) {
	        if(! $giftId) {
	            $this->output(-1, "不能提交空礼包ID");
	        }
	        $giftArr[$giftId] = $sortArr[$key];
	    }
	    if(count($giftArr) != count($giftIdArr)) {
	        $this->output(-1, "有重复的礼包ID");
	    }
	    
	    /**单机不需要限制数量
	    if(count($giftArr) < 6) {
	        $this->output(-1, "礼包至少需要添加6个");
	    }
	    */
	    foreach ($giftArr as $giftId => $sort) {
	        $result = Game_Service_Util_Link::checkLinkValue(Game_Service_Util_Link::LINK_GIFT, $giftId);
    	    if($result) {
    		    $this->output(-1, $result . ":" . $giftId);
    		}
	    }
	    return $giftArr;
	}
	
	private function cookRecommendAloneData($requestData) {
		if(!isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
		if(!isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
		$result = Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link']);
		if($result) {
		    $this->output(-1, $result);
		}
		return $requestData;
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

	private function cookAloneData($requestData) {
	    if (Util_String::strlen($requestData['title']) > 15) {
	        $this->output(- 1, '标题不能超过15个字.');
	    }
	    if(! $requestData['content']) $this->output(-1, '描述不能为空.');
	    if (Util_String::strlen($requestData['content']) > 35) {
	        $this->output(- 1, '描述不能超过35个字.');
	    }
		return $requestData;
	}
	
	private function getEditLog($dayId) {
	    $searchParams = array('day_id' => $dayId);
	    $sortParams = array('create_time' => 'desc');
	    $log = Game_Service_SingleEditLog::getSingleEditLogBy($searchParams, $sortParams);
	    if($log) {
	        $user = Admin_Service_User::getUser($log['uid']);
	        $log['username'] = $user['username'];
	    }
	    return $log;
	}
	
	private function initPageData($dayId, $userId) {
	    $bannerList = Game_Manager_SingleGameRecommend::getRecommendBanner($dayId, $userId);
	    $recommend = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	    
	    $data = array('success' => true, 'msg' => "", 'data' => array());
	    $bannerList = Game_Manager_SingleGameRecommend::initBannerInfo($bannerList);
	    $recommendList = Game_Manager_SingleGameRecommend::initRecommend($recommend);
	    foreach ($bannerList as $key => $banner) {
	        $id = $banner['id'];
	        $banner['editHref'] = $this->actions['bannerEditUrl'] . "?day_id={$dayId}&id={$id}";
	        $banner['delHref'] = $this->actions['bannerDeleteUrl'] . "?day_id={$dayId}&id={$id}";
	        $banner['link'] = $this->actions['editUrl'] . "?dayId={$dayId}";
	        $bannerList[$key] = $banner;
	    }
	    foreach ($recommendList as $key => $recommend) {
	        $id = $recommend['id'];
	        if($recommend['type'] == 'gameList1' || $recommend['type'] == 'gameList2') {
	            $recommend['editHref'] = $this->actions['listGameEditUrl'] . "?day_id={$dayId}&id={$id}";
	        }elseif($recommend['type'] == 'image') {
	            $recommend['editHref'] = $this->actions['listImgEditUrl'] . "?day_id={$dayId}&id={$id}";
	        }elseif($recommend['type'] == 'openList') {
	            $recommend['editHref'] = $this->actions['aloneEditUrl'] . "?day_id={$dayId}&id={$id}";
	        }elseif($recommend['type'] == 'package') {
	            $recommend['editHref'] = $this->actions['giftEditUrl'] . "?day_id={$dayId}&id={$id}";
	        }
	        $recommend['delHref'] = $this->actions['listDeleteUrl'] . "?day_id={$dayId}&id={$id}";
	        $recommend['link'] = $this->actions['editUrl'] . "?dayId={$dayId}";
	        $recommendList[$key] = $recommend;
	    }
	    $data['data']['roll'] = $bannerList;
	    $data['data']['recommend'] = $recommendList;
	    return $data;
	}

	public function copyAction() {
	    $dayId = $this->getInput('day_id');
	    $type = $this->getInput('type');
	    if($type == 1) {
	        $to_day_id = strtotime($dayId);
	        $from_day_id = strtotime("-1 day", $to_day_id);
	        $data = Game_Service_SingleRecommend::getSingleRecommend($to_day_id);
	        if(! $data) {
	            Game_Service_SingleRecommend::copyRecommendListByDayId($from_day_id, $to_day_id);
	            Game_Manager_SingleGameRecommend::addLog($to_day_id, $this->userInfo['uid']);
	        }
	    }elseif($type == 3) {
	        $day_id = strtotime($dayId);
	        Game_Service_SingleRecommend::deleteRecommendListByDayId($day_id);
	    }
        $this->redirect("edit?day_id=".$dayId);
	}
	
	public function sortPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    if(! $userId) {
	        exit();
	    }
	    $banner = html_entity_decode($this->getInput('banner'));
	    $recommend = html_entity_decode($this->getInput('recommend'));
	    $bannerSort = json_decode($banner);
	    $recommendSort = json_decode($recommend);
	    $bannerList = Game_Manager_SingleGameRecommend::getRecommendBanner($dayId, $userId);
	    $recommendList = Game_Manager_SingleGameRecommend::getRecommendList($dayId, $userId);
	     
	    $sort = count($bannerSort);
	    foreach ($bannerSort as $index) {
	        $banner = $bannerList[$index];
	        $banner['sort'] = $sort--;
	        $bannerList[$index] = $banner;
	    }
	    $sort = count($recommendSort);
	    foreach ($recommendSort as $index) {
	        $rec = $recommendList[$index][Game_Manager_SingleGameRecommend::SINGLEGAME_INFO];
	        $rec['sort'] = $sort--;
	        $recommendList[$index][Game_Manager_SingleGameRecommend::SINGLEGAME_INFO] = $rec;
	    }
	    Game_Manager_SingleGameRecommend::updateRecommendBanner($dayId, $userId, $bannerList);
	    Game_Manager_SingleGameRecommend::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功.');
	}
	
}
