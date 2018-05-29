<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Client_BaseController{

    public $actions = array(
        'listUrl' => '/client/category/index',
        'detailUrl' => '/client/category/detail/',
        'indexlUrl' => '/client/index/detail/',
        'tjUrl' => '/client/index/tj'
    );

    public $perpage = 10;
    /**
     *
     * index page view
     */
    public function indexAction() {
        Common::addSEO($this,'分类');
        $this->assign('source', $this->getSource());
        $this->assign('cache', Game_Service_Config::getValue('game_client_cache'));

    }

    public function detailAction(){
        Common::addSEO($this,'分类详情');
        $id = intval($this->getInput('id'));
        $intersrc = $this->getInput('intersrc');
        $page = intval($this->getInput('page'));
        $sp = $this->getInput('sp');
        if ($page < 1) $page = 1;

        if($id == 100){
            $this->assign('title', '全部游戏');
        }else if($id == 101){
            $this->assign('title', '最新游戏');
        }
        //游戏大厅版本
        $checkVer = $this->checkAppVersion();
        $pageData = array();
        $hasNext = false;
        $total = 0;

        if($id == 'caini'){
            list($pageData, $hasNext, $total) = $this->getGuessData($page, $sp); //tbd
        } else {
            $parmas = array('categoryId' => 0, 'parentId' => $id, 'page' => $page);
            $orderBy = Resource_Index_CategoryList::CATLIST_PARENT_ORDERBY_DEFAULT;
            if(100 == $id) {
                $orderBy = Resource_Index_CategoryList::CATLIST_GAMES_ORDERBY_NEW;
            }else if(101 == $id){
                $orderBy = Resource_Index_CategoryList::CATLIST_GAMES_ORDERBY_HOT;
            }

            if (in_array($id, array(100, 101))) {
                $parmas['parentId'] = Resource_Index_CategoryList::CATLIST_GAMES_PARENT_ID;
            }
            $idxKey = Resource_Index_CategoryList::createCatIdxkey($parmas['parentId'], $parmas['categoryId'], $orderBy);
            list($pageData, $hasNext, $total) = Resource_Service_GameListData::getPage($idxKey, $page);
        }

        $this->assign('checkver', $checkVer);
        $this->assign('sp', $sp);
        $this->assign('pageData', $pageData);
        $this->assign('hasnext', $hasNext);
        $this->assign('page', $page);
        $this->assign('id', $id);
        $this->assign('source', $this->getSource());
        $this->assign('intersrc', $intersrc);
    }

    /**
     * subject json list
     */
    public function moreCtAction(){
        $id = intval($this->getInput('id'));
        $intersrc = $this->getInput('intersrc');
        $page = intval($this->getInput('page'));
        $sp = $this->getInput('sp');
        if ($page < 1) $page = 1;
        if($id == 100){
            $this->assign('title', '全部游戏');
        }else if($id == 101){
            $this->assign('title', '最新游戏');
        }
        $pageData = array();
        $hasNext = false;
        $total = 0;

        if($id == 'caini'){
            list($pageData, $hasNext, $total) = $this->getGuessData($page, $sp);
            if($pageData){
                $pageData = Resource_Service_GameListFormat::output($pageData);
            }
        } else {
            $parentId = (in_array($id, array(100, 101))) ? Resource_Index_CategoryList::CATLIST_GAMES_PARENT_ID : $id;
            $categoryId = 0;
            $orderBy = Resource_Index_CategoryList::CATLIST_PARENT_ORDERBY_DEFAULT;

            if(100 == $id) {
                $orderBy = Resource_Index_CategoryList::CATLIST_GAMES_ORDERBY_NEW;
            }else if(101 == $id){
                $orderBy = Resource_Index_CategoryList::CATLIST_GAMES_ORDERBY_HOT;
            }

            $listData = Resource_PageCache_CatList::getPage($page, $parentId, $categoryId, $orderBy);
            $pageData = $listData['list'];
            $hasNext = $listData['hasNext'];
            $total = $listData['total'];
        }
        $this->output(0, '', array('list'=>$pageData, 'hasnext'=>$hasNext, 'curpage'=>$page));
    }

    private function getGuessData($page, $sp){
        $imei = end(explode('_',$sp));
        $imcrc = crc32(trim($imei));
        $params = array(
            'page' => $page,
            'perPage' => $this->perpage,
            'imcrc' => $imcrc,
            'sortType' => 'new',
        );
        $data = array(array(), false, 0);
        list($pageData, $hasNext, $total) = Resource_PageCache_CatGuessList::getPage($params);
        if(!$pageData){
            return $data;
        }
        return array($pageData, $hasNext, $total);
    }
}
