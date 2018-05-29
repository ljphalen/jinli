<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_SubjectController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Client_Subject/index',
        'addUrl' => '/Admin/Client_Subject/add',
        'addPostUrl' => '/Admin/Client_Subject/add_post',
        'editUrl' => '/Admin/Client_Subject/edit',
        'editPostUrl' => '/Admin/Client_Subject/edit_post',

        'gamesAddUrl' => '/Admin/Client_Subject/gamesAdd',
        'listGamesPostUrl' => '/Admin/Client_Subject/listGamesPost',
        'listGamesSortUrl' => '/Admin/Client_Subject/listGamesSort',
        'listGamesSortPostUrl' => '/Admin/Client_Subject/listGamesSortPost',
        'costomGamesAddUrl' => '/Admin/Client_Subject/costomGamesAdd',
        'costomGamesPostUrl' => '/Admin/Client_Subject/costomGamesPost',
        'customPostUrl' => '/Admin/Client_Subject/customPost',
        'customGamesSortUrl' => '/Admin/Client_Subject/customGamesSort',
        'customGamesSortPostUrl' => '/Admin/Client_Subject/customGamesSortPost',
        'deleteUrl' => '/Admin/Client_Subject/delete',
        'uploadPostUrl' => '/Admin/Client_Subject/upload_post',
        'uploadUrl' => '/Admin/Client_Subject/upload',


        //没用的
        'editCtUrl' => '/Admin/Client_Subject/editCt',
        'addCtUrl' => '/Admin/Client_Subject/addCt',
        'uploadImgUrl' => '/Admin/Client_Subject/uploadImg',
        'viewUrl' => '/front/subject/goods/',
        'batchUpdateUrl'=>'/Admin/Client_Subject/batchUpdate',
    );

    public $perpage = 20;
    public $appCacheName = array("APPC_Channel_Subject_index","APPC_Client_Subject_index","APPC_Kingstone_Subject_index");

    public function indexAction() {
        $page = intval($this->getInput('page'));

        $perpage = $this->perpage;
        $params = $this->getInput(array('title', 'start_time', 'end_time', 'status', 'sub_type', 'pgroup'));
        $search =  $this->getSearchParams($params);

        $this->cookieParams();
        list($total, $subjects) = Client_Service_Subject::getList($page, $perpage,$search);

        $subjects = $this->initGameNames($subjects);

        $this->assign('subjects', $subjects);
        $this->assign('params', $params);
        $url = $this->actions['listUrl'].'/?' . http_build_query($params) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign("total", $total);
        $this->assign("subType", Client_Service_Subject::$subType);
        $this->assign("viewTpl", Client_Service_Subject::$viewTpl);
        $this->assignGroups();
    }

    public function addAction() {
        $this->assign("subType", Client_Service_Subject::$subType);
        $this->assignGroups();
    }

    public function add_postAction() {
        $info = $this->getPost(array('sort', 'title','resume', 'icon', 'img', 'start_time', 'end_time', 'sub_type', 'pgroup', 'hdinfo'));
        $info = $this->cookData($info);
        $info['create_time'] = Common::getTime();
        $info['status'] = Client_Service_Subject::SUBJECT_STATUS_INVALID;

        $userId = $this->userInfo['uid'];
        $subjectId = Common::getTime();
        $subject[Client_Manager_Subject::INFO] = $info;
        Client_Manager_Subject::updateSubject($subjectId, $userId, $subject);
        $this->output(0, '操作成功', $subjectId);
    }

    public function editAction() {
        $subjectId = intval($this->getInput('id'));
        $from = $this->getInput('from');
        $userId = $this->userInfo['uid'];
        if($from == 'list') {
            $subject = Client_Manager_Subject::loadSubject($subjectId, $userId);
        }else{
            $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        }
        $info = $subject[Client_Manager_Subject::INFO];
        $this->assign('info', $info);
        $this->assign('id', $subjectId);
        $this->assign("subType", Client_Service_Subject::$subType);
        $this->assignGroups();
    }

    public function edit_postAction() {
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];
        $info = $this->getPost(array('sort', 'title','resume', 'icon', 'img', 'start_time', 'end_time', 'pgroup', 'hdinfo'));
        $postData = $this->cookData($info);

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $editInfo = $subject[Client_Manager_Subject::INFO];
        if (! $editInfo) $this->output(-1, '编辑的内容不存在');
        $newInfo = $this->getNewData($postData, $editInfo);
        $subject[Client_Manager_Subject::INFO] = $newInfo;
        Client_Manager_Subject::updateSubject($subjectId, $userId, $subject);
        $this->output(0, '操作成功.');
    }

    public function gamesAddAction() {
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];
        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        if (! $subject[Client_Manager_Subject::INFO]) {
            $this->output(-1, '编辑的内容不存在');
        }
        $info = $subject[Client_Manager_Subject::INFO];
        if($info['sub_type'] == Client_Service_Subject::SUBTYPE_LIST) {
            $this->gamesAddList($subjectId, $subject);
        }else if($info['sub_type'] == Client_Service_Subject::SUBTYPE_CUSTOM) {
            $this->gamesAddCustom($subjectId, $subject);
        }
    }

    private function gamesAddList($subjectId, $subject) {
        $games = $subject[Client_Manager_Subject::GAMES];
        foreach ($games as $key => $value) {
            $game = $this->getGameInfo($value['game_id']);
            $games[$key] = array_merge($game, $value);
        }

        $tags = array();
        $tags['专题列表'] = $this->actions['listUrl'];
        $this->assign('tags', $tags);

        $this->assign('hiddens', array('id' => $subjectId));
        $this->assign('games', $games);

        $this->assign('postUrl', $this->actions['listGamesPostUrl']);
        $this->assign('preStepUrl', $this->actions['editUrl'] . "?id=" . $subjectId);
        $this->assign('nextStepUrl', $this->actions['listGamesSortUrl'] . "?id=" . $subjectId);

        Yaf_Dispatcher::getInstance()->disableView();
        $this->getView()->display("common/games.phtml");
    }

    private function gamesAddCustom($subjectId, $subject) {
        $userId = $this->userInfo['uid'];
        $info = $subject[Client_Manager_Subject::INFO];
        if($info['status'] == Client_Service_Subject::SUBJECT_STATUS_INVALID) {
            $info['status'] = Client_Service_Subject::SUBJECT_STATUS_OPEN;
            $info['view_tpl'] = 1;
        }


        $items = $subject[Client_Manager_Subject::ITEMS];
        if(! $items) {
            $items = array();
            $size = 4;
            for ($i = 0; $i < $size; $i++) {
                $items[] = array(
                    'sub_id' => $info['id'],
                    'sort' => $size - $i,
                    'title' => '',
                    'resume' => '',
                    'view_tpl' => 0,
                );
            }
            $subject[Client_Manager_Subject::ITEMS] = $items;
            Client_Manager_Subject::updateSubject($subjectId, $userId, $subject);
        }

        $itemGames = $subject[Client_Manager_Subject::GAMES];
        foreach ($itemGames as $key => $games) {
            foreach ($games as $key2 => $game) {
                $gameInfo = Resource_Service_GameData::getGameAllInfo($game['game_id']);
                $game['gameName'] = $gameInfo['name'];
                $game['gameIcon'] = $gameInfo['img'];
                $itemGames[$key][$key2] = $game;
            }
        }

        $this->assign('itemGames', $itemGames);
        $this->assign('items', $items);
        $this->assign('info', $info);
        $this->assign('id', $subjectId);
        $this->assign('viewTpl', Client_Service_Subject::$viewTpl[$info['sub_type']]);
        $this->assign('costomTpl', Client_Service_Subject::$costomTpl);
        Yaf_Dispatcher::getInstance()->disableView();
        $this->getView()->display("client/subject/custom.phtml");
    }

    /**
     * 保存列表游戏
     */
    public function listGamesPostAction () {
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];

        $data = $this->getInput('games');
        if(! $data) {
            $this->output('-1', '游戏为空，不可以提交');
        }
        $games = array_unique($data);
        $gameSize = count($games);
        if ($gameSize != count($data)) {
            $this->output('-1', '有重复游戏，请删除后提交');
        }
        if ($gameSize < Client_Service_SubjectGames::SHOW_NUM) {
            $this->output('-1', '列表至少添加'.Client_Service_SubjectGames::SHOW_NUM.'个游戏');
        }

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $info = $subject[Client_Manager_Subject::INFO];
        if (! $info) {
            $this->output(- 1, '编辑的内容不存在');
        }
        $oldGames = $subject[Client_Manager_Subject::GAMES];
        $oldGames = Common::resetKey($oldGames, 'game_id');
        $newGames = array();
        foreach ($games as $gameId) {
            $game = $oldGames[$gameId];
            if(! $game) {
                $game = array(
                    'sort' => 0,
                    'resource_game_id' => $gameId,
                    'status' => 0,
                    'game_status' => Resource_Service_Games::STATE_ONLINE,
                    'subject_id' => $info['id'],
                    'game_id' => $gameId,
                    'item_id' => 0,
                    'resume' => ''
                );
            }
            $newGames[] = $game;
        }
        $subject[Client_Manager_Subject::GAMES] = $newGames;
        Client_Manager_Subject::updateSubject($subjectId, $userId, $subject);
        $this->output('0', '操作成功.');
    }

    private function asyncGameAcoupon($gameIds, $subjectId){
        $params =  array(
            'htype' => 2,
            'game_object' =>2,
            'subject_id' =>$subjectId
        );
        $taskData = Client_Service_TaskHd::getsBy($params);

        if($taskData) {
            Async_Task::execute('Async_Task_Adapter_GameListData', 'updteGamesAcoupon', $gameIds);
            Async_Task::execute('Async_Task_Adapter_ExtraUpdate', 'gameRewardAcoupon');
            Async_Task::execute('Async_Task_Adapter_GameListData', 'updateRewardAcoupon');
        }
    }

    /**
     * 推荐游戏排序
     */
    public function listGamesSortAction() {
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $list = $subject[Client_Manager_Subject::GAMES];
        $sort = count($list);
        foreach($list as $key=>$value){
            $game = $this->getGameInfo($value['game_id']);
            $value['sort'] = $sort--;
            $list[$key] = array_merge($game, $value);
        }
        $info = $subject[Client_Manager_Subject::INFO];
        if($info['status'] == Client_Service_Subject::SUBJECT_STATUS_INVALID) {
            $info['status'] = Client_Service_Subject::SUBJECT_STATUS_OPEN;
            $info['view_tpl'] = 1;
        }

        $this->assign('info', $info);
        $this->assign('id', $subjectId);
        $this->assign('list', $list);
        $this->assign('viewTpl', Client_Service_Subject::$viewTpl[$info['sub_type']]);
    }

    /**
     * 推荐游戏排序保存
     */
    public function listGamesSortPostAction() {
        $sort = $this->getInput('sort');
        $status = $this->getInput('status');
        $view_tpl = $this->getInput('view_tpl');
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $info = $subject[Client_Manager_Subject::INFO];
        if(! $info) {
            $this->output('-1', '操作失败.');
        }

        $games = $subject[Client_Manager_Subject::GAMES];
        foreach($games as $key=>$value){
            $gameId = $value['game_id'];
            $games[$key]['sort'] = $sort[$value['game_id']];
        }
        $subject[Client_Manager_Subject::GAMES] = $games;
        $oldStatus = $info['status'];
        if($status != $info['status'] || $view_tpl != $info['view_tpl']) {
            $info['status'] = $status;
            $info['view_tpl'] = $view_tpl;
        }
        $subject[Client_Manager_Subject::INFO] = $info;

        $oldGameIds = $this->getSubjectGameIds($subjectId);

        $flg = Client_Manager_Subject::saveSubject2DB($subject);
        if($flg) {
            if($info['id'] && $oldStatus != $status && $status == Client_Service_Subject::SUBJECT_STATUS_CLOSE) {
                $this->closeAd($info['id'])
                ;
            }
            Client_Manager_Subject::deleteSubject($subjectId, $userId);
        }

        $newGames = Common::resetKey($games, 'game_id');
        $newGameIds = array_keys($newGames);
        $allGameIds = array_unique(array_merge($oldGameIds, $newGameIds));

        $this->asyncGameAcoupon($allGameIds, $subjectId);
        $this->output('0', '操作成功.');
    }

    /**
     * 编辑自定义游戏
     */
    public function costomGamesAddAction() {
        $data = $this->getInput(array('id', 'item_id', 'view_tpl', 'title', 'resume'));
        $subjectId = intval($data['id']);
        $item_id = intval($data['item_id']);
        $userId = $this->userInfo['uid'];

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $info = $subject[Client_Manager_Subject::INFO];
        if (! $info) $this->output(-1, '编辑的内容不存在');

        $itemGames = $subject[Client_Manager_Subject::GAMES];
        $items = $subject[Client_Manager_Subject::ITEMS];

        $item = $items[$item_id - 1];
        $games = $itemGames[$item_id - 1];
        foreach ($games as $key => $value) {
            $game = $this->getGameInfo($value['game_id']);
            $games[$key] = array_merge($game, $value);
        }

        $tags = array();
        $tags['专题列表'] = $this->actions['listUrl'];
        $this->assign('tags', $tags);

        $this->assign('hiddens', $data);
        $this->assign('games', $games);

        $this->assign('postUrl', $this->actions['costomGamesPostUrl']);
        $this->assign('preStepUrl', $this->actions['gamesAddUrl'] . "?id=" . $subjectId);
        $this->assign('nextStepUrl', $this->actions['customGamesSortUrl'] . "?id={$subjectId}&item_id={$item_id}");

        Yaf_Dispatcher::getInstance()->disableView();
        $this->getView()->display("common/games.phtml");
    }

    /**
     * 保存自定义游戏
     */
    public function costomGamesPostAction () {
        $this->checkCostomGamesAdd();
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];
        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $info = $subject[Client_Manager_Subject::INFO];
        $itemGames = $subject[Client_Manager_Subject::GAMES];
        $items = $subject[Client_Manager_Subject::ITEMS];
        if (! $info) {
            $this->output(- 1, '编辑的内容不存在');
        }
        $item_id = intval($this->getInput('item_id')) - 1;
        $view_tpl = intval($this->getInput('view_tpl'));
        $title = $this->getInput('title');
        $resume = $this->getInput('resume');

        $item = $items[$item_id];
        $oldGames = $itemGames[$item_id];
        $oldGames = Common::resetKey($oldGames, 'game_id');
        $postGameList = $this->getInput('games');

        $newGames = array();
        foreach ($postGameList as $gameId) {
            $game = $oldGames[$gameId];
            if(! $game) {
                $game = array(
                    'sort' => 0,
                    'resource_game_id' => $gameId,
                    'status' => 0,
                    'game_status' => Resource_Service_Games::STATE_ONLINE,
                    'subject_id' => $info['id'],
                    'game_id' => $gameId,
                    'item_id' => 0,
                    'resume' => ''
                );
            }
            $newGames[] = $game;
        }

        $itemGames[$item_id] = $newGames;
        $subject[Client_Manager_Subject::GAMES] = $itemGames;

        if($item['view_tpl'] != $view_tpl) {
            $item['view_tpl'] = $view_tpl;
        }
        if($title && $item['title'] != $title) {
            $item['title'] = $title;
        }
        if($resume && $item['resume'] != $resume) {
            $item['resume'] = $resume;
        }
        $items[$item_id] = $item;
        $subject[Client_Manager_Subject::ITEMS] = $items;

        Client_Manager_Subject::updateSubject($subjectId, $userId, $subject);
        $this->output('0', '操作成功.');
    }

    private function checkCostomGamesAdd() {
        $view_tpl = intval($this->getInput('view_tpl'));
        $data = $this->getInput('games');
        if(!$data) {
            $this->output('-1', '游戏为空，不可以提交');
        }
        $games = array_unique($data);
        $gameSize = count($games);
        if ($gameSize != count($data)) {
            $this->output('-1', '有重复游戏，请删除后提交');
        }
        if ($gameSize != $view_tpl) {
            $this->output('-1', '列表需要添加'.$view_tpl.'个游戏');
        }
    }

    /**
     * 自定义游戏排序
     */
    public function customGamesSortAction() {
        $item_id = intval($this->getInput('item_id'));
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $info = $subject[Client_Manager_Subject::INFO];
        if (! $info) $this->output(-1, '编辑的内容不存在');

        $itemGames = $subject[Client_Manager_Subject::GAMES];
        $items = $subject[Client_Manager_Subject::ITEMS];
        $item = $items[$item_id - 1];
        $games = $itemGames[$item_id - 1];
        $sort = count($games);
        foreach ($games as $key => $value) {
            $game = $this->getGameInfo($value['game_id']);
            $value['sort'] = $sort--;
            $games[$key] = array_merge($game, $value);
        }

        $this->assign('id', $subjectId);
        $this->assign('item_id', $item_id);
        $this->assign('view_tpl', $item['view_tpl']);
        $this->assign('list', $games);
    }

    /**
     * 推荐游戏排序保存
     */
    public function customGamesSortPostAction() {
        $item_id = intval($this->getInput('item_id'))-1;
        $sort = $this->getInput('sort');
        $subjectId = intval($this->getInput('id'));
        $userId = $this->userInfo['uid'];

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $info = $subject[Client_Manager_Subject::INFO];
        if(! $info) {
            $this->output('-1', '操作失败.');
        }
        $itemGames = $subject[Client_Manager_Subject::GAMES];
        $games = $itemGames[$item_id];
        foreach($games as $key=>$value){
            $gameId = $value['game_id'];
            $games[$key]['sort'] = $sort[$value['game_id']];
        }
        $itemGames[$item_id] = $games;
        $subject[Client_Manager_Subject::GAMES] = $itemGames;
        Client_Manager_Subject::updateSubject($subjectId, $userId, $subject);
        $this->output('0', '操作成功.');
    }

    /**保存自定义专题*/
    public function customPostAction() {
        $subjectId = intval($this->getInput('id'));
        $status = $this->getInput('status');
        $view_tpl = $this->getInput('view_tpl');
        $itemTitles = $this->getInput('title');
        $itemResumes = $this->getInput('resume');
        $gameResumes = $this->getInput('game_resume');
        $userId = $this->userInfo['uid'];

        $subject = Client_Manager_Subject::getSubject($subjectId, $userId);
        $info = $subject[Client_Manager_Subject::INFO];
        if(! $info) {
            $this->output('-1', '操作失败.');
        }
        $gameResumeData = array();
        foreach ($gameResumes as $key => $resume) {
            $keys = split('_', $key);
            $gameResumeData[$keys[0]][$keys[1]] = $resume;
        }

        $items = $subject[Client_Manager_Subject::ITEMS];
        $itemGames = $subject[Client_Manager_Subject::GAMES];
        $size = count($items);
        for ($i = 1; $i <= $size; $i++) {
            $item = $items[$i-1];
            if(! $itemTitles[$i]) $this->output(-1, "内容{$i}标题不能为空.");
            if(! $itemResumes[$i]) $this->output(-1, "内容{$i}描述不能为空.");
            if(! isset(Client_Service_Subject::$costomTpl[$item['view_tpl']])) {
                $this->output('-1', "内容{$i}游戏没有添加完成.");
            }
            $item['title'] = $itemTitles[$i];
            $item['resume'] = $itemResumes[$i];
            $newGames = array();
            $games = $itemGames[$i-1];
            foreach ($games as $key => $game) {
                if(! isset($gameResumeData[$i][$game['game_id']])) {
                    continue;
                }
                $game['resume'] = $gameResumeData[$i][$game['game_id']];
                $games[$key] = $game;
            }
            $itemGames[$i-1] = $games;
            $items[$i-1] = $item;
        }

        $subject[Client_Manager_Subject::ITEMS] = $items;
        $subject[Client_Manager_Subject::GAMES] = $itemGames;
        $oldStatus = $info['status'];
        if($status != $info['status'] || $view_tpl != $info['view_tpl']) {
            $info['status'] = $status;
            $info['view_tpl'] = $view_tpl;
        }
        $subject[Client_Manager_Subject::INFO] = $info;

        $oldGameIds = $this->getSubjectGameIds($subjectId);

        $flg = Client_Manager_Subject::saveSubject2DB($subject);
        if($flg) {
            if($info['id'] && $oldStatus != $status && $status == Client_Service_Subject::SUBJECT_STATUS_CLOSE) {
                $this->closeAd($info['id']);
            }
            Client_Manager_Subject::deleteSubject($subjectId, $userId);
        }

        $newGames = Common::resetKey($itemGames, 'game_id');
        $newGameIds = array_keys($newGames);
        $allGameIds = array_unique(array_merge($oldGameIds, $newGameIds));
        $this->asyncGameAcoupon($allGameIds, $subjectId);

        $this->output(0, '操作成功.');
    }

    public function deleteAction() {
        $id = intval($this->getInput('id'));
        //查找对应的游戏
        $info = Client_Service_Subject::getSubject($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['icon']);
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
        $gameIds = $this->getSubjectGameIds($id);
        $result = Client_Service_Subject::deleteSubject($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->closeAd($id);
        $this->syncGameAcoupon($gameIds);
        $this->output(0, '操作成功');
    }

    private function syncGameAcoupon($gameIds){
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updteGamesAcoupon', $gameIds);
        Async_Task::execute('Async_Task_Adapter_ExtraUpdate', 'gameRewardAcoupon');
    }

    private function getSubjectGameIds($subjectId){
        $games = Client_Service_SubjectGames::getSubjectAllItemsGames($subjectId);
        $games = Common::resetKey($games, 'game_id');
        $gameIds = array_keys($games);
        return $gameIds;
    }

    //批量操作
    public function batchUpdateAction() {
        $id = $this->getInput('id');
        $info = $this->getPost(array('action', 'ids', 'sort'));
        if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
        sort($info['ids']);
        if($info['action'] =='sort'){
            $ret = Client_Service_Subject::updateListSort($info['ids'], $info['sort']);
        }
        if (!$ret) $this->output('-1', '操作失败.');
        $this->output('0', '操作成功.');
    }

    private function closeAd($subject_id) {
        //首页图片广告对应的专题也下线
        Client_Service_Ad::updateByAd(array('status'=>Client_Service_Subject::SUBJECT_STATUS_CLOSE), array('ad_type'=>9, 'ad_ptype'=>3, 'link'=> $subject_id));
        //首页的专题列表的间隔图片链接对应的分类也下线
        Client_Service_Ad::updateByAd(array('status'=>Client_Service_Subject::SUBJECT_STATUS_CLOSE), array('ad_type'=>10, 'ad_ptype'=>3, 'link'=> $subject_id));
        //首页推荐相关
        Game_Service_RecommendList::updateSubjectStatus($subject_id, Client_Service_Subject::SUBJECT_STATUS_CLOSE);
        Game_Service_GameWebRecommend::updateSubjectStatus($subject_id, Client_Service_Subject::SUBJECT_STATUS_CLOSE);
        Game_Service_SingleRecommend::updateSubjectStatus($subject_id, Client_Service_Subject::SUBJECT_STATUS_CLOSE);
    }

    /**设置游戏名称*/
    private function initGameNames($list) {
        foreach ($list as $key => $value) {
            $games = Client_Service_SubjectGames::getSubjectAllItemsGames($value["id"]);
            $games = Common::resetKey($games, 'game_id');
            $idList = array_keys($games);
            $gameNameList = Resource_Service_Games::getGameNameListBy($idList);
            $value["game_name"] = implode(', ', array_values($gameNameList));
            $list[$key] = $value;
        }
        return $list;
    }

    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    public function upload_postAction() {
        $ret = Common::upload('img', 'subject');
        $imgId = $this->getPost('imgId');
        $this->assign('code' , $ret['data']);
        $this->assign('msg' , $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**查询参数*/
    private function getSearchParams($search) {
        $params = array();
        if ($search['title']) $params['title'] = array('LIKE', $search['title']);
        if ($search['start_time']) $params['start_time'] = array('>=', strtotime($search['start_time']));
        if ($search['end_time'] && $search['start_time']) $params['end_time'] = array('<=', strtotime($search['end_time']));
        if (strlen($search['status']) > 0) $params['status'] =  intval($search['status']);
        if (strlen($search['sub_type']) > 0) $params['sub_type'] =  intval($search['sub_type']);
        if (strlen($search['pgroup']) > 0) $params['pgroup'] =  intval($search['pgroup']);
        return $params;
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

    private function cookData($info) {
        if(!$info['title']) $this->output(-1, '名称不能为空.');
        if(!$info['resume']) $this->output(-1, '简述不能为空.');
        $resume = strip_tags(html_entity_decode($info['resume']));
        if(! $resume) $this->output(-1, '简述不能为空.');
        if(!$info['icon']) $this->output(-1, '图标不能为空.');
        if(!$info['img']) $this->output(-1, '图片不能为空.');
        if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
        if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
        $info['start_time'] = strtotime(date('Y-m-d H:00:00', strtotime($info['start_time'])));
        $info['end_time'] = strtotime(date('Y-m-d H:00:00', strtotime($info['end_time'])));
        if($info['start_time'] >= $info['end_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
        return $info;
    }

    private function assignGroups() {
        list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
        array_unshift($groups, array("id" => 0, "title" => "全部"));
        $groups = Common::resetKey($groups, 'id');
        $this->assign('groups', $groups);
    }

    private static function getNewData($postData, $oldData) {
        foreach ($postData as $key => $value) {
            if ($value == $oldData[$key]) {
                continue;
            }
            $oldData[$key] = $value;
        }
        return $oldData;
    }

    /**
     *
     * edit games
     */
    public function editCtAction() {
        $id = $this->getInput('id');
        $page = intval($this->getInput('page'));
        $name = $this->getInput('name');

        $search = $params = array();
        if ($name) $search['name'] = array('LIKE',$name);
        $params['subject_id'] = $id;

        $info = Client_Service_Subject::getSubject(intval($id));
        $this->assign('info', $info);

        $oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
        $oline_versions = Common::resetKey($oline_versions, 'game_id');

        list(, $subject_games) = Client_Service_Game::getSubjectBySubjectId($params);
        $subject_games = Common::resetKey($subject_games, 'resource_game_id');
        $resource_game_ids = array_unique(array_keys($subject_games));

        if (count($resource_game_ids)) {
            $search['id'] = array('IN',$resource_game_ids);
            $search['status'] = 1;
            list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $search);
            $games = Common::resetKey($games, "id");
        }

        $this->cookieParams();
        $this->assign('total', $total);
        $this->assign('oline_versions', $oline_versions);
        $this->assign('subject_games', $subject_games);
        $this->assign('games', $games);
        $this->assign('name', $name);
        $url = $this->actions['editCtUrl'].'/?id='.$id.'&' . 'name=' . $name."&";
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
        $this->cookieParams();
    }


    /**
     * get an subjct by subject_id
     */
    public function getAction() {
        $id = $this->getInput('id');
        $info = Client_Service_Subject::getSubject(intval($id));
        if(!$info) $this->output(-1, '操作失败.');
        $this->output(0, '', $info);
    }

    /**
     *
     * add games
     */
    public function addCtAction() {
        $id = $this->getInput('id');
        $page = $this->getInput('page');
        $s = $this->getInput(array('name','status','isadd', 'category_id'));
        $params = $search = array();

        if ($s['name']) {
            $search['name'] = $s['name'];
            $params['name'] = array('LIKE',$s['name']);
        }
        if ($s['status']) $search['status'] = $s['status'] - 1;
        if ($s['isadd']) $search['isadd'] = $s['isadd'];
        if ($s['category_id']) $search['category_id'] = $s['category_id'];

        $info = Client_Service_Subject::getSubject(intval($id));
        $this->assign('info', $info);

        //游戏库分类列表
        $categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1));
        $this->assign('categorys', $categorys);

        $oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
        $oline_versions = Common::resetKey($oline_versions, 'game_id');

        //获取本地所有游戏
        $client_games = Resource_Service_Games::getsBy(array('status'=>1));
        if(count($client_games)) {
            $client_games = Common::resetKey($client_games, 'id');
            $this->assign('client_games', $client_games);
            $resource_game_ids = array_keys($client_games);
            $this->assign('resource_game_ids', $resource_game_ids);
            $params['id'] = array('IN',$resource_game_ids);
            if ($s['category_id']) {
                $game_ids = Resource_Service_Games::getIdxGameResourceCategoryBy(array('category_id'=>$s['category_id'],'game_status'=>1));
                $game_ids = Common::resetKey($game_ids, 'game_id');
                $ids = array_keys($game_ids);
                if($ids){
                    $params['id'] = array('IN',$ids);
                } else {
                    $params['id'] = 's';
                }
            }

            //获取主题索引表游戏
            list(, $idx_games) = Client_Service_Game::getSubjectBySubjectId(array('subject_id'=>intval($id),'game_status'=>1));
            $idx_games = Common::resetKey($idx_games, 'resource_game_id');
            $idx_games = array_keys($idx_games);
            $this->assign('idx_games', $idx_games);

            //已经添加
            if ($search['isadd'] == 2) {
                if($s['category_id']){
                    if(array_intersect($ids,$idx_games)){
                        $params['id'] = array('IN',array_intersect($ids,$idx_games));
                    } else {
                        $params['id'] = 0;
                    }
                } else {
                    if(array_intersect($resource_game_ids,$idx_games)){
                        $params['id'] = array('IN',array_intersect($resource_game_ids,$idx_games));
                    } else {
                        $params['id'] = 0;
                    }
                }

            }
            //未添加
            if ($search['isadd'] == 1) {
                if($s['category_id']){
                    if(array_diff($resource_game_ids,$idx_games)){
                        if(array_intersect(array_diff($resource_game_ids,$idx_games),$ids)){
                            $params['id'] = array('IN',array_intersect(array_diff($resource_game_ids,$idx_games),$ids));
                        }
                    } else {
                        $params['id'] = 0;
                    }
                } else {
                    if(array_diff($resource_game_ids,$idx_games)){
                        $params['id'] = array('IN',array_diff($resource_game_ids,$idx_games));
                    } else {
                        $params['id'] = 0;
                    }
                }
            }

            if(!$params['id'])	$games = '';
            list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $params);
        }
        $this->assign('total', $total);
        $this->cookieParams();
        $this->assign('games', $games);
        $this->assign('search', $search);
        $this->assign('oline_versions', $oline_versions);
        $url = $this->actions['addCtUrl'].'/?id='.$id.'&' . http_build_query($search) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));

    }

    /**
     *
     * games list
     */
    public function add_step2Action() {

    }

    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'subject');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/" .$ret['data'])));
    }

}