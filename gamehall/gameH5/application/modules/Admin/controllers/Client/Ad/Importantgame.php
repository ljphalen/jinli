<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author huyuke
 *
 */
class Client_Ad_ImportantgameController extends Admin_BaseController {
    public $actions = array(
        'listUrl' => '/Admin/Client_Ad_Importantgame/index',
        'addUrl' => '/Admin/Client_Ad_Importantgame/add',
        'addPostUrl' => '/Admin/Client_Ad_Importantgame/addPost',
        'editUrl' => '/Admin/Client_Ad_Importantgame/edit',
        'editPostUrl' => '/Admin/Client_Ad_Importantgame/editPost',
        'deleteUrl' => '/Admin/Client_Ad_Importantgame/delete',
        'batchUpdateUrl'=>'/Admin/Client_Ad_Importantgame/batchUpdate',
        'settingsUrl'=>'/Admin/Client_Ad_Importantgame/settings',
        'settingsPostUrl'=>'/Admin/Client_Ad_Importantgame/settingsPost'
    );

    public $perpage = 20;

    public function indexAction() {
        $perpage = $this->perpage;
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $search = array('id'=>array('>', 0));
        $orderBy = array('add_time' => 'DESC');

        list($total, $games) = Client_Service_ImportantGame::getList($page, $perpage, $search, $orderBy);

        $this->assign('games', $games);
        $this->assign('total', $total);
        $url = $this->actions['listUrl'] . '/?' . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    /**
     *
     * Enter description here ...
     */
    public function addPostAction() {
        $gameInfo = $this->getPost(array('name', 'package'));
        $gameInfo = $this->_cookData($gameInfo);

        foreach($gameInfo as $key => $value) {
            $gameInfo[$key] = trim($value);
        }

        $gameInfo['add_time'] = Common::getTime();


        //查询需添加的游戏是否已存在
        $data['package'] = $gameInfo['package'];
        $ret  = Client_Service_ImportantGame::getByPackage($data);
        if ($ret) $this->output(-1, '不能重复添加一个游戏');

        $result = Client_Service_ImportantGame::addGame($gameInfo);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 编辑游戏页面
     * Enter description here ...
     */
    public function editAction() {
        $id = $this->getInput('id');
        $game_info = Client_Service_ImportantGame::getById(intval($id));
        $this->assign('game_info', $game_info);
    }

    /**
     *
     * 批量操作
     */
    function batchUpdateAction() {
        $id = $this->getInput('id');
        $gameInfoList = $this->getPost(array('action', 'ids'));
        if (!count($gameInfoList['ids'])) {
            $this->output(-1, '没有可操作的项.');
        }
        sort($gameInfoList['ids']);
        if($gameInfoList['action'] =='delete'){
            $ret = Client_Service_ImportantGame::deleteGames($gameInfoList['ids']);
        }
        if (!$ret) $this->output('-1', '操作失败.');
        $this->output('0', '操作成功.');
    }

    /**
     *
     * Enter description here ...
     */
    public function addAction() {
    }

    /**
     *
     * Enter description here ...
     */
    public function editPostAction() {
        $this->output(0, '敬请期待.');
    }

    /**
     *
     * Enter description here ...
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $result = Client_Service_ImportantGame::deleteGame($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    public function settingsAction() {
        $state = Game_Service_Config::getValue('clear_games_from_other_appstore');
        $this->assign('state', $state);
    }

    /**
     * Enter description here ...
     */
    public function settingsPostAction() {
        $state = $this->getInput('state');
        $ret = Game_Service_Config::setValue('clear_games_from_other_appstore', $state);
        //if (!$ret) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData($gameInfo) {
        if(!$gameInfo['name']) $this->output(-1, 'XX, 您没有输入游戏名称');
        if(!$gameInfo['package']) $this->output(-1, 'XX, 您没有输入游戏apk包名');
        return $gameInfo;
    }

}
