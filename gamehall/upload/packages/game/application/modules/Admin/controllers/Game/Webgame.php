<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网游频道
 * Game_WebgameController
 * @author wupeng
 */
class Game_WebgameController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Game_Webgame/index',
		'openListUrl' => '/Admin/Game_Gameopen/index',

		'editUrl' => '/Admin/Game_Webgame/edit',
		'editPostUrl' => '/Admin/Game_Webgame/editPost',
		'copyUrl' => '/Admin/Game_Webgame/copy',
	    'editlogUrl' => '/Admin/Game_Webgame/editlog',
	    'buttonConfigUrl' => '/Admin/Client_Navigation/index?module=WEB_GAME',

	    'bannerAddUrl' => '/Admin/Game_Webgame/bannerAdd',
	    'bannerEditUrl' => '/Admin/Game_Webgame/bannerEdit',
	    'bannerDeleteUrl' => '/Admin/Game_Webgame/bannerDelete',
	    'bannerAddPostUrl' => '/Admin/Game_Webgame/bannerAddPost',
	    'bannerEditPostUrl' => '/Admin/Game_Webgame/bannerEditPost',

	    'listGameAddUrl' => '/Admin/Game_Webgame/listGameAdd',
	    'listGameEditUrl' => '/Admin/Game_Webgame/listGameEdit',
	    'listGameAddPostUrl' => '/Admin/Game_Webgame/listGameAddPost',
	    'listGameEditPostUrl' => '/Admin/Game_Webgame/listGameEditPost',
	    'listGameSelectUrl' => '/Admin/Game_Webgame/listGameSelect',
	    'listGameSelectPostUrl' => '/Admin/Game_Webgame/listGameSelectPost',
	    'listGameSortUrl' => '/Admin/Game_Webgame/listGameSort',
	    'listGameSortPostUrl' => '/Admin/Game_Webgame/listGameSortPost',

	    'listImgAddUrl' => '/Admin/Game_Webgame/listImgAdd',
	    'listImgEditUrl' => '/Admin/Game_Webgame/listImgEdit',
	    'listImgAddPostUrl' => '/Admin/Game_Webgame/listImgAddPost',
	    'listImgEditPostUrl' => '/Admin/Game_Webgame/listImgEditPost',

	    'listOpenSelectUrl' => '/Admin/Game_Webgame/listOpenSelect',
	    'listOpenSelectPostUrl' => '/Admin/Game_Webgame/listOpenSelectPost',
	    'listOpenSortUrl' => '/Admin/Game_Webgame/listOpenSort',
	    'listOpenSortPostUrl' => '/Admin/Game_Webgame/listOpenSortPost',
	    'listOpenQueryUrl' => '/Admin/Game_Webgame/listOpenQuery',

	    'hotGiftOpenUrl' => '/Admin/Game_Webgame/hotGiftOpen',
	    'listDeleteUrl' => '/Admin/Game_Webgame/listDelete',
	    'uploadUrl' => '/Admin/Game_Webgame/upload',
	    'uploadPostUrl' => '/Admin/Game_Webgame/upload_post',

	    'sortPostUrl' => '/Admin/Game_Webgame/sortPost',
	);

	public function indexAction() {
	    $month = $this->getInput('date');
	    if(! $month) $month = date("Y-m");
	    $month = strtotime($month);
	    $list = $this->getMonthData($month);
	    $lastMonthDay = strtotime("-1 day", $month);
	    $lastList = Game_Service_GameWebRecommend::getGameWebRecommendBy(array('day_id' => $lastMonthDay));
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
	    $list = Game_Service_GameWebRecommend::getGameWebRecommendListBy($searchParams, $sortParams);
	    $list = Game_Manager_WebRecommendList::initMonthList($list, $month);
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
	        Game_Manager_WebRecommendList::loadRecommendList($dayId, $userId);
	    }
	    
	    $data = $this->initPageData($dayId, $userId);
	    $this->assign('data', $data);
	    $log = $this->getEditLog($dayId);
	    $this->assign('log', $log);
	    $this->assign('day_id', $dayId);
	    $this->assign('from', $from);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function copyAction() {
	    $dayId = $this->getInput('day_id');
	    $type = $this->getInput('type');
	    if($type == 1) {
	        $to_day_id = strtotime($dayId);
	        $from_day_id = strtotime("-1 day", $to_day_id);
	        $data = Game_Service_GameWebRecommend::getGameWebRecommend($to_day_id);
	        if(! $data) {
	            Game_Service_GameWebRecommend::copyRecommendListByDayId($from_day_id, $to_day_id);
	            Game_Manager_WebRecommendList::addLog($to_day_id, $this->userInfo['uid']);
	        }
	    }
	    $this->redirect("edit?day_id=".$dayId);
	}

	public function hotGiftOpenAction () {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $listKey = -1;
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    foreach ($recommendList as $key => $recommend) {
	        $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	        if($info['rec_type'] == Game_Service_GameWebRec::REC_TYPE_GIFT) {
	            $listKey = $key;
	            break;
	        }
	    }
	    if($listKey != -1) {
	        $recommend = $recommendList[$listKey];
	        $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	        $info['status'] = Game_Service_GameWebRec::STATUS_OPEN;
	        $recommendList[$listKey] = $recommend;
	    }else{
	        $info = array();
	        $info['day_id'] = $dayId;
	        $info['status'] = Game_Service_GameWebRec::STATUS_OPEN;
	        $info['rec_type'] = Game_Service_GameWebRec::REC_TYPE_GIFT;
	        $recommend = array(Game_Manager_WebRecommendList::WEBGAME_INFO => $info);
	        $recommendList[] = $recommend;
	    }
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
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
	    $bannerList = Game_Manager_WebRecommendList::getRecommendBanner($dayId, $userId);
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    
	    $sort = count($bannerSort);
	    foreach ($bannerSort as $index) {
	        $banner = $bannerList[$index];
	        $banner['sort'] = $sort--;
	        $bannerList[$index] = $banner;
	    }
	    $sort = count($recommendSort);
	    foreach ($recommendSort as $index) {
	        $rec = $recommendList[$index][Game_Manager_WebRecommendList::WEBGAME_INFO];
	        $rec['sort'] = $sort--;
	        $recommendList[$index][Game_Manager_WebRecommendList::WEBGAME_INFO] = $rec;
	    }
	    Game_Manager_WebRecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);    
	    $this->output(0, '操作成功.');
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
	    $bannerList = Game_Manager_WebRecommendList::getRecommendBanner($dayId, $userId);
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);

	    if(! $bannerList) {
	        $this->output(-1, '轮播图未添加');
	    }
	    if(! $recommendList) {
	        $this->output(-1, '推荐列表未添加');
	    }
	    
	    $sort = count($bannerSort);
	    foreach ($bannerSort as $index) {
	        $banner = $bannerList[$index];
	        $banner['sort'] = $sort--;
	        $bannerList[$index] = $banner;
	    }
	    $sort = count($recommendSort);
	    foreach ($recommendSort as $index) {
	        $rec = $recommendList[$index][Game_Manager_WebRecommendList::WEBGAME_INFO];
	        $rec['sort'] = $sort--;
	        $recommendList[$index][Game_Manager_WebRecommendList::WEBGAME_INFO] = $rec;
	    }
	    
	    $flg = Game_Service_GameWebRecommend::saveRecommend($dayId, $bannerList, $recommendList);
	    if(! $flg) {
	        $this->output(-1, '保存失败.');
	    }
	
	    $editInfo = Game_Service_GameWebRecommend::getGameWebRecommend($dayId);
	    if (! $editInfo) {
	        $recList = array();
	        $recList['day_id'] = $dayId;
	        $recList['create_time'] = Common::getTime();
	        Game_Service_GameWebRecommend::addGameWebRecommend($recList);
	    }
	
	    if(strtotime(date("Y-m-d")) == $dayId) {
		    Async_Task::execute('Async_Task_Adapter_UpdateWebRecCache', 'update');
	    }
	    Game_Manager_WebRecommendList::deleteRecommend($dayId, $userId);
	    Game_Manager_WebRecommendList::addLog($dayId, $userId);
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
	    $bannerList = Game_Manager_WebRecommendList::getRecommendBanner($dayId, $userId);
	    if(count($bannerList) >=4) {
	        $this->output(-1, 'Banner图不能多于4个.');
	    }
	    $bannerList[] = $postData;
	    Game_Manager_WebRecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功');
	}
	
	public function bannerEditAction() {
	    $dayId = $this->getInput('day_id');
	    $id = $this->getInput('id');
	    $userId = $this->userInfo['uid'];
	
	    $bannerList = Game_Manager_WebRecommendList::getRecommendBanner($dayId, $userId);
	    $info = $bannerList[$id];
	
	    $this->assign('id', $id);
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
	
	    $bannerList = Game_Manager_WebRecommendList::getRecommendBanner($dayId, $userId);
	    $editInfo = $bannerList[$index];
	    if (! $editInfo) $this->output(-1, '编辑的内容不存在');
	
	    $editInfo = Game_Manager_WebRecommendList::getNewData($postData, $editInfo);
	    $bannerList[$index] = $editInfo;
	    Game_Manager_WebRecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功.');
	}
	
	public function bannerDeleteAction() {
	    $dayId = $this->getInput('day_id');
	    $index = $this->getInput('id');
	    $userId = $this->userInfo['uid'];
	    $bannerList = Game_Manager_WebRecommendList::getRecommendBanner($dayId, $userId);
	    array_splice($bannerList, $index, 1);
	    Game_Manager_WebRecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功');
	}
	
	public function listOpenSelectAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $games = array();
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    foreach ($recommendList as $recommend) {
	        if(! isset($recommend[Game_Manager_WebRecommendList::WEBGAME_OPEN])) {
	            continue;
	        }
	        $games = $recommend[Game_Manager_WebRecommendList::WEBGAME_OPEN];
	        break;
	    }
	    foreach ($games as $key => $value) {
	        $open = Game_Service_GameOpen::getGameOpen($value['open_id']);
	        $game = Game_Manager_WebRecommendList::getGameInfo($open['game_id']);
	        $game['id'] = $value['open_id'];
	        $game['gameId'] = $open['game_id'];
	        $game['open_time'] = date('Y-m-d H:i', $open['open_time']);
	        $game['server_name'] = $open['server_name'];
	        $games[$key] = $game;
	    }
	    $this->assign('games', $games);
	    $this->assign('day_id', $dayId);
	}
	
	public function listOpenSelectPostAction () {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $openList = $this->getInputOpenList();
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $listKey = $this->getOpenIndex($recommendList);
	    if($listKey != -1) {
	        $recommend = $recommendList[$listKey];
	    }else{
	        $info = array('day_id' => $dayId, 'status' => Game_Service_GameWebRec::STATUS_OPEN, 'rec_type' => Game_Service_GameWebRec::REC_TYPE_OPEN);
	        $recommend = array(Game_Manager_WebRecommendList::WEBGAME_INFO => $info);
	    }
	    
	    $oldGamesList = $recommend[Game_Manager_WebRecommendList::WEBGAME_OPEN];
	    $oldGamesList = Common::resetKey($oldGamesList, 'open_id');
	    
        $newOpenList = array();
	    foreach ($openList as $openId) {
	        if($oldGamesList[$openId]) {
	            $game = $oldGamesList[$openId];
	        }else{
	            $game = array('open_id' => $openId, 'sort' => 0, 'open_status' => Game_Service_GameOpen::STATUS_OPEN);
	        }
	        $newOpenList[] = $game;
	    }
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_OPEN] = $newOpenList;
	    if($listKey != -1) {
	        $recommendList[$listKey] = $recommend;
	    }else{
	        $recommendList[] = $recommend;
	    }
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listOpenSortAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];

	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $listKey = $this->getOpenIndex($recommendList);
	    $list = $recommendList[$listKey][Game_Manager_WebRecommendList::WEBGAME_OPEN];
	    $sort = count($list);
	    foreach($list as $key=>$value) {
	        $open = Game_Service_GameOpen::getGameOpen($value['open_id']);
	        $game = Game_Manager_WebRecommendList::getGameInfo($open['game_id']);
	        $game['sort'] = $sort--;
	        $game['gameId'] = $open['game_id'];
	        $game['open_time'] = date('Y-m-d H:i', $open['open_time']);
	        $game['server_name'] = $open['server_name'];
	        $list[$key] = $game;
	    }
	    $this->assign('day_id', $dayId);
	    $this->assign('list', $list);
	}
	
	public function listOpenSortPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $sorts = $this->getInput('sort');
	    
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $listKey = $this->getOpenIndex($recommendList);
	    if($listKey == -1) {
	        $this->output('-1', '操作失败.');
	    }
	    $list = $recommendList[$listKey][Game_Manager_WebRecommendList::WEBGAME_OPEN];
	    foreach($list as $key=>$value) {
	        $value['sort'] = $sorts[$key];
	        $list[$key] = $value;
	    }
	    $sortCriteria = array(
	        'sort' => array(SORT_DESC, SORT_NUMERIC),
	        'game_id' => array(SORT_ASC, SORT_NUMERIC)
	    );
	    $list = Util_ArraySort::multiSort($list, $sortCriteria);
	    $recommendList[$listKey][Game_Manager_WebRecommendList::WEBGAME_OPEN] = $list;
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	
	    $this->output('0', '操作成功.');
	}

	public function listOpenQueryAction() {
        $name = $this->getInput('name');
	    $page = $this->getInput('page');
	    $dayId = $this->getInput('day_id');
	    $selected = $this->getInput('selected');
	    $name = html_entity_decode($name);

	    $gameParams = array();
	    $gameParams['status'] = 1;
	    $gameParams['game_status'] = Resource_Service_Games::STATE_ONLINE;
	    $gameParams['open_time'] = array('>=', $dayId);
	    if($selected) {
	        $gameParams['id'] = array('NOT IN', $selected);
	    }
	    if(strlen($name) > 0) {//游戏名称检索
	        $params = array();
	        $params['status'] = 1;
	        $params['name'] = array('LIKE', $name);
	        $gameIdList = Resource_Service_Games::getGameIdListBy($params);
	        $gameIdList = Common::resetKey($gameIdList, 'id');
	        $gameParams['game_id'] = array('IN', array_keys($gameIdList));
	    }
	    if(! $page || $page <0) {
	        $page=1;
	    }
	    $sortParams = array('open_time'=>'asc', 'id'=>'asc');
	    list($total, $gameData) = Game_Service_GameOpen::getPageList($page, 10, $gameParams, $sortParams);
	    
	    $data = array();
	    $list = array();
	    foreach($gameData as $value) {
	        $game = Game_Manager_WebRecommendList::getGameInfo($value['game_id']);
	        $game['id'] = $value['id'];
	        $game['gameId'] = $value['game_id'];
	        $game['open_time'] = date('Y-m-d H:i', $value['open_time']);
	        $game['server_name'] = $value['server_name'];
	        $list[] = $game;
	    }
	    $data['list'] = $list;
	    $data['total'] = $total;
	    $data['page'] = $page;
	    $data['pageSize'] = ceil($total/10);
	    $this->output('0', '查询成功.', $data);
	}
	
	public function listImgAddAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $this->assign('day_id', $dayId);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	    $this->assign('status_list', Game_Service_GameWebRecommend::$status_list);
	}
	
	public function listImgEditAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id];
	    $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    $img = $recommend[Game_Manager_WebRecommendList::WEBGAME_IMAGE];
	    $this->assign('img', $img);
	    $this->assign('info', $info);
	    $this->assign('day_id', $dayId);
	    $this->assign('id', $id);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	    $this->assign('status_list', Game_Service_GameWebRecommend::$status_list);
	}
	
	public function listImgAddPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $data = $this->getInput(array('link_type', 'link', 'img'));
	    $info = $this->getInput(array('title', 'status', 'day_id'));
		if(!isset($info['title'])) $this->output(-1, '标题不能为空.');
	    $info['rec_type'] = Game_Service_GameWebRec::REC_TYPE_IMAGE;
	    $img = $this->cookRecommendImgData($data);
	    
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO] = $info;
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_IMAGE] = $img;
	    $recommendList[] = $recommend;
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listImgEditPostAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $mainInputData = $this->getInput(array('title', 'status'));
		if(! isset($mainInputData['title'])) $this->output(-1, '标题不能为空.');
	    
	    $imgInputData = $this->getInput(array('link_type', 'link', 'img'));
	    $imgData = $this->cookRecommendImgData($imgInputData);
	
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id];
	    $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    if (! $info) $this->output(-1, '编辑的内容不存在');
	    $info['title'] = $mainInputData['title'];
	    $info['status'] = $mainInputData['status'];
	    $oldImgData = $recommend[Game_Manager_WebRecommendList::WEBGAME_IMAGE];
	    $editInfo = Game_Manager_WebRecommendList::getNewData($imgData, $oldImgData);
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO] = $info;
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_IMAGE] = $editInfo;
	    $recommendList[$id] = $recommend;
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listGameAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('rec_type', Game_Service_RecommendNew::$rec_type);
	    $this->assign('list_template', Game_Service_GameWebRec::$list_template);
	    $this->assign('day_id', $dayId);
	    $this->assignGroups();
	}
	
	public function listGameAddPostAction() {
	    $data = $this->getInput(array('title', 'content', 'day_id', 'pgroup', 'template'));
	    $dayId = $data['day_id'];
	    $userId = $this->userInfo['uid'];
	    $info = $this->cookListData($data);
	    $info['rec_type'] = Game_Service_GameWebRec::REC_TYPE_LIST;
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommendList[] = array(Game_Manager_WebRecommendList::WEBGAME_INFO => $info);
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功', count($recommendList)-1);
	}
	
	public function listGameEditAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $id = $this->getInput('id');
	    
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id];
	    $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    
	    $this->assignGroups();
	    $this->assign('info', $info);
	    $this->assign('id', $id);
	    $this->assign('day_id', $dayId);
	    $this->assign('rec_type', Game_Service_RecommendNew::$rec_type);
	    $this->assign('list_template', Game_Service_GameWebRec::$list_template);
	}
	
	public function listGameEditPostAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $listData = $this->getInput(array('title', 'content', 'pgroup', 'template'));
	    $postData = $this->cookListData($listData);
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id];
	    $oldData = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    if (! $oldData) $this->output(-1, '编辑的内容不存在');
	    
	    $editInfo = Game_Manager_WebRecommendList::getNewData($postData, $oldData);
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO] = $editInfo;
	    $recommendList[$id] = $recommend;
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功');
	}
	
	public function listGameSelectAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	     
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id];
	    $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    if (! $info) $this->output(-1, '编辑的内容不存在');
	    
	    $games = $recommend[Game_Manager_WebRecommendList::WEBGAME_GAMES];
	    foreach ($games as $key => $value) {
	        $game = Game_Manager_WebRecommendList::getGameInfo($value['game_id']);
	        $games[$key] = array_merge($game, $value);
	    }
	    
	    $this->assign('hiddens', array('id' => $id, 'day_id' => $dayId));
	    $this->assign('games', $games);
	    
	    $this->assign('postUrl', $this->actions['listGameSelectPostUrl']);
	    $this->assign('nextStepUrl', $this->actions['listGameSortUrl'] . "?day_id={$dayId}&id=" . $id);
	    $this->assign('preStepUrl', $this->actions['listGameEditUrl'] . "?day_id={$dayId}&id=" . $id);
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
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    if (! $recommend){
	        $this->output(- 1, '编辑的内容不存在');
	    }
	    $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    $needGameCounts = Game_Service_GameWebRec::$list_template_counts[$info['template']];
	    if ($gameSize < $needGameCounts) {
	        $this->output('-1', '列表至少添加'.$needGameCounts.'个游戏');
	    }
	    $oldGamesList = $recommend[Game_Manager_WebRecommendList::WEBGAME_GAMES];
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
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_GAMES] = $newGamesList;
	    $recommendList[$index] = $recommend;
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output('0', '操作成功.');
	}
	
	public function listGameSortAction() {
	    $id = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];

	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$id];
	    $gameList = $recommend[Game_Manager_WebRecommendList::WEBGAME_GAMES];
	    $sort = count($gameList);
	    foreach($gameList as $key=>$value){
	        $game = Game_Manager_WebRecommendList::getGameInfo($value['game_id']);
	        $value['sort'] = $sort--;
	        $gameList[$key] = array_merge($game, $value);
	    }
	    $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    if(! isset($info['status'])) {
	        $info['status'] = Game_Service_GameWebRec::STATUS_OPEN;
	    }
	    $this->assign('id', $id);
	    $this->assign('day_id', $dayId);
	    $this->assign('list', $gameList);
	    $this->assign('info', $info);
	    $this->assign('status_list', Game_Service_GameWebRecommend::$status_list);
	}
	
	public function listGameSortPostAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $sorts = $this->getInput('sort');
	    $status = $this->getInput('status');
	
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    $recommend = $recommendList[$index];
	    $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	    if(! $info) {
	        $this->output('-1', '操作失败.');
	    }
	    $games = $recommend[Game_Manager_WebRecommendList::WEBGAME_GAMES];
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
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_GAMES] = $games;
	    $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO] = $info;
	    $recommendList[$index] = $recommend;
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	
	    $this->output('0', '操作成功.');
	}
	
	public function listDeleteAction() {
	    $index = intval($this->getInput('id'));
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $recommendList = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    array_splice($recommendList, $index, 1);
	    Game_Manager_WebRecommendList::updateRecommendList($dayId, $userId, $recommendList);
	    $this->output(0, '操作成功');
	}
	
	public function editlogAction() {
	    $perpage = 20;
	    $page = intval($this->getInput('page'));
	    $dayId = intval($this->getInput('day_id'));
	    if ($page < 1) $page = 1;
	    $searchParams = array('day_id' => $dayId);
	    $sortParams = array('id' => 'DESC');
	    list($total, $list) = Game_Service_GameWebRecEditLog::getPageList($page, $perpage, $searchParams, $sortParams);
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

	private function getInputOpenList() {
	    $data = $this->getInput('games');
	    if(! $data) {
	        $this->output('-1', '推荐游戏为空，不可以提交');
	    }
	    $dataLength = count($data);
	    $openList = array_unique($data);
	    if (count($openList) != $dataLength) {
	        $this->output('-1', '有重复游戏，请删除后提交');
	    }
	    $needGameCounts = Game_Service_GameWebRecOpen::SHOW_NUMS;
	    if ($dataLength < $needGameCounts) {
	        $this->output('-1', '列表需要添加'.$needGameCounts.'个游戏');
	    }
// 	    if ($dataLength > $needGameCounts) {
// 	        $this->output('-1', '列表只需要'.$needGameCounts.'个游戏');
// 	    }
	    return $openList;
	}
	
	private function getOpenIndex($recommendList) {
	    $listKey = -1;
	    foreach ($recommendList as $key => $recommend) {
	        $info = $recommend[Game_Manager_WebRecommendList::WEBGAME_INFO];
	        if($info['rec_type'] == Game_Service_GameWebRec::REC_TYPE_OPEN) {
	            $listKey = $key;
	            break;
	        }
	    }
	    return $listKey;
	}

	private function checkBannerData($requestData) {
	    if(! ($requestData['title'])) $this->output(-1, '标题不能为空.');
	    if(! isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
	    if(! isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
	    $result = Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link']);
	    if($result) {
	        $this->output(-1, $result);
	    }
	    if(! ($requestData['img'])) $this->output(-1, '图片不能为空.');
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

	private function getEditLog($dayId) {
	    $searchParams = array('day_id' => $dayId);
	    $sortParams = array('create_time' => 'desc');
	    $log = Game_Service_GameWebRecEditLog::getGameWebRecEditLogBy($searchParams, $sortParams);
	    if($log) {
	        $user = Admin_Service_User::getUser($log['uid']);
	        $log['username'] = $user['username'];
	    }
	    return $log;
	}
	
	private function initPageData($dayId, $userId) {
	    $bannerList = Game_Manager_WebRecommendList::getRecommendBanner($dayId, $userId);
	    $recommend = Game_Manager_WebRecommendList::getRecommendList($dayId, $userId);
	    
	    $data = array('success' => true, 'msg' => "", 'data' => array());
	    $bannerList = Game_Manager_WebRecommendList::initBannerInfo($bannerList);
	    $recommendList = Game_Manager_WebRecommendList::initRecommend($recommend);
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
	            $recommend['editHref'] = $this->actions['listOpenSelectUrl'] . "?day_id={$dayId}";
	        }
	        $recommend['delHref'] = $this->actions['listDeleteUrl'] . "?day_id={$dayId}&id={$id}";
	        $recommend['link'] = $this->actions['editUrl'] . "?dayId={$dayId}";
	        $recommendList[$key] = $recommend;
	    }
	    $data['data']['roll'] = $bannerList;
	    $data['data']['recommend'] = $recommendList;
	    return $data;
	}

	public function testAction() {
        $api = Util_CacheKey::getApi(Util_CacheKey::WEBGAME, Util_CacheKey::WEBGAME_LIST);
        $keys = Util_Api_Cache::getValidKeys($api);
        var_dump($keys);
	    exit();
	}
	
}
