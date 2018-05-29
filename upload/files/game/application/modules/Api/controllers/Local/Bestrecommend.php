<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_BestrecommendController extends Api_BaseController {
	public $perpage = 10;
    
	/**
	 * 推荐游戏
	 */
    public function bestListAction() {
    	$page = intval($this->getInput('page'));
    	$intersrc = $this->getInput('intersrc');
    	$gameId = intval($this->getInput('gameId'));
    	$sp = $this->getInput('sp');
    	$imei = end(explode('_',$sp));
    	$imcrc = crc32(trim($imei));
    	
    	$data = $this->getBestGameList($page, $gameId, $intersrc);
    	$this->localOutput('','',$data);
    }
    
    /**
     * 推荐游戏列表的数据
     * @param unknown_type $page
     * @param unknown_type $intersrc
     * @return array
     */
    private  function getBestGameList($page, $gameId, $intersrc) {
    	
		if ($page < 1) $page = 1;
		$gameIds = $games = $params = $data = array();
        $ids = Client_Service_Recommend::getRecommendGames(array('GAMEC_RESOURCE_ID'=>$gameId));
		if($ids){
			foreach($ids as $key=>$value){
				$gameInfo = Resource_Service_Games::getBy(array('id'=>$value['GAMEC_RECOMEND_ID']));
				if($gameInfo['status']){
					$gameIds[] = $value['GAMEC_RECOMEND_ID'];
				}
			}
		}

		if($gameIds){
			$params['id'] = array('IN',$gameIds);
			list($total, $games) = Resource_Service_Games::search($page, $this->perpage, $params);
			$checkVer = $this->checkAppVersion();
			$jsonData = Resource_Service_Games::getClientGameData($games, $intersrc, $checkVer, 0);
			$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
			$data = array('list'=>$jsonData, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		}
		
		return $data;
    }  
}