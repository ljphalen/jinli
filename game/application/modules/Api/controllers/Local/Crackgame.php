<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_CrackgameController extends Api_BaseController {
	public $perpage = 10;
    /**
     * 破解列表的数据
     */
    public function CrackgameListAction() {
    	$page = intval($this->getInput('page'));
    	$intersrc = $this->getInput('intersrc');
    	$sp = $this->getInput('sp');
    	$imei = end(explode('_',$sp));
    	$imcrc = crc32(trim($imei));
    	 
    	$data = $this->getCrackGameList($page, $intersrc);
    	$this->localOutput('','',$data);
    }
    
    
    /**
     * 破解游戏列表的数据
     * @param unknown_type $page
     * @param unknown_type $intersrc
     * @return 
     */
    private  function getCrackGameList($page, $intersrc) {
    	 
    	if ($page < 1) $page = 1;
    	$orderBy = array('sort'=>'DESC', 'game_id'=>'DESC');
		//v1.5.6版本标识符
		$categoryParams['parent_id'] = (ENV=='product')?'157':'139';
		$categoryParams['game_status'] = 1;
		//游戏大厅|艾米游戏过滤
		if($this->filter){
			$params['game_id'] = array('NOT IN', $this->filter);
		}
		list($total, $games) = Resource_Service_GameCategory::getListByMainCategory($page, $this->perpage, $categoryParams, $orderBy);
    	$checkVer = $this->checkAppVersion();
    	$tmp = Resource_Service_Games::getClientGameData($games, $intersrc, $checkVer, 1);
    
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page))> 0 ? true : false;
    	$data = array('list'=>$tmp, 'hasnext'=>$hasnext,'curpage'=>$page, 'totalCount'=>$total);
    	return $data;
    }
}