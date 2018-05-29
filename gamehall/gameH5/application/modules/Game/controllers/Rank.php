<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class RankController extends Game_BaseController{
	
	public $actions = array(
	                'listUrl' => '/rank/index',
	                'detailUrl' => '/rank/detail/',
	                'detailAjaxUrl' => '/rank/detailAjax/',
	                'gameDetailUrl' => '/index/detail/',
	);

	const PERPAGE = 10;
	const INDEX_PERPAGE = 5;
	const DETAIL_PERPAGE = 10;
	/**
	 * 
	 * index page view
	 */
    public function indexAction() {
		$config = Game_Service_Rank::getH5OpenRankType();
		$data = array();
		foreach ($config as $rankType => $rankName) {
		    $rankItem = array();
		    $rankItem['name'] = $rankName;
		    $rankItem['href'] = $this->actions['detailUrl'].'?rankType='.$rankType;
		    list($total, $gameList) = $this->getGamesData($rankType, 1, self::INDEX_PERPAGE);
		    $rankItem['data'] = $gameList;
		    $data[] = $rankItem;
		}
		$this->assign('rankList', json_encode(array('list' => $data)));
	}
	
	public function detailAction() {
	    $rankType = $this->getInput('rankType');
	    $rankTitle = Game_Service_Rank::$sRankType[$rankType];
	    $page = $this->getPageInput();
	    list($total, $gameList) = $this->getGamesData($rankType);
	    $rankData = array(
	                    'name' => $rankTitle,
	                    'data' => $gameList,
	                    'hasNext' => $page * self::DETAIL_PERPAGE < $total,
	                    'curPage' => $page,
	                    'ajaxUrl' => $this->actions['detailAjaxUrl'].'?$rankType='.$rankType
	    );
	    $this->assign('title', $rankTitle);
	    $this->assign('rankData', json_encode($rankData));
	}
	
	public function detailAjaxAction() {
	    $rankType = $this->getInput('rankType');
	    $page = $this->getPageInput();
	    list($total, $gameList) = $this->getGamesData($rankType, $page);
	    $hasNext = $page * self::DETAIL_PERPAGE < $total;
	    $data = array('list'=>$gameList, 'hasNext'=>$hasNext, 'curPage'=>$page);
	    $this->ajaxOutput($data);
	}
	
	private function getGamesData($rankType, $page = 1, $limit = self::DETAIL_PERPAGE) {
	    list($total, $gameList) = Game_Service_Rank::getGameListByType($rankType, 1, $limit);
	    $games = array();
	    foreach ($gameList as $game) {
	       $games[] = array(
	                       'name' => $game['name'],
	                       'stars' => $game['web_star'],
	                       'size' => $game['size'].'M',
	                       'info' => $game['resume'],
	                       'typeName' => $game['category_title'],
	                       'href' => $this->actions['gameDetailUrl'].'?id='.$game['id'],
	                       'download' => $game['link'],
	                       'imgUrl' => $game['img'],
	       );
	    }
	    return array($total, $games);
	}
}
