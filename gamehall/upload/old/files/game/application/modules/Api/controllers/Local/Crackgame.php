<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_CrackgameController extends Api_BaseController {
	public $perpage = 10;
    /**
     * 破解列表的数据
     */
    public function CrackgameListAction() {
    	$page     = intval($this->getInput('page'));
    	$page     = $page?$page:1;
    	$gameData = $this->getCrackGameListFromCache($page);
    	$this->formatOutputGameData($gameData, $page);
    }
   
    
    private  function formatOutputGameData($gameData,$page){
    	$data['list']    = $gameData['list'];
    	$data['hasnext'] = $gameData['hasNext'];
    	$data['curpage'] = $page;
    	$data['totalCount'] = $gameData['total'];
    	$this->localOutput('','',$data);
    }
    
    /**
     * 破解游戏列表的数据
     * @param unknown_type $page
     * @param unknown_type $intersrc
     * @return 
     */
    private  function getCrackGameListFromCache($page) { 
    	if ($page < 1) $page = 1;
    	$parentId    = (ENV=='product')?'157':'139';
    	$categoryId  = 0;
    	$orderBy     = Resource_Index_CategoryList::CATLIST_PARENT_ORDERBY_DEFAULT;
    	$cacheData = Resource_PageCache_CatList::getPage($page, $parentId, $categoryId, $orderBy);
    	return  $cacheData;
    }
}