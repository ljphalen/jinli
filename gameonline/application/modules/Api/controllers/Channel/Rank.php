<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Channel_RankController extends Api_BaseController {
	
	public $perpage = 10;	
    
    /**
     *
     */
    public function indexAction() {
        $flag = intval($this->getInput('flag'));
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		
		$games = array();
		$resource_games = array();
		$resource_ids = array();
		$client_games = array();
		$bi_games = array();
		$game_ids = array();
		
		if($flag == 1){
			//最新游戏
			$limit = Game_Service_Config::getValue('game_rank_newnum');
			$this->perpage = min($limit, $this->perpage);
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, array('status'=>1), array('create_time'=>'DESC'));
			$total = $limit;
		} else {
			$limit = Game_Service_Config::getValue('game_rank_mostnum');
			list(, $bi_games) = Client_Service_Rank::getMostGames($configs['game_rank_mostnum'],'');
			$resource_ids = Common::resetKey($bi_games, 'GAME_ID');
			$resource_ids = array_unique(array_keys($resource_ids));
			$game_ids = array_intersect($resource_ids, $client_games);
			
			
			if($game_ids){
				list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, array('id'=>$game_ids,'top'=>$configs['game_rank_mostnum']));
			}
		}
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	return $this->_jsonData($games, $page, $hasnext ,$intersrc,$flag);
    }
    
    
    private  function _jsonData($games, $page, $hasnext ,$intersrc,$flag) {
        $temp = array();
		$webroot = Common::getWebRoot();
		
		list(, $sys_version) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>5,'status'=>1));
		$sys_version = Common::resetKey($sys_version, 'id');
		
		list(, $resolution) = Resource_Service_Attribute::getList(1, 100,array('at_type'=>4,'status'=>1));
		$resolution = Common::resetKey($resolution, 'id');
		
		if($intersrc){
			$t_id = $intersrc;
		} else {
			if($flag == 1){
				$t_id = 'Newrelease';
			} else {
				$t_id = 'Mostdownload';
			}
		}
		
		$oline_versions = Resource_Service_Games::getIdxVersionByVersionStatus(1);
		$oline_versions = Common::resetKey($oline_versions, 'game_id');
		
		foreach($games as $key=>$value) {
			$href = urldecode($webroot.'/channel/index/detail?id=' . $value['id'].'&flag='.$flag.'&intersrc='.$t_id.'&t_bi='.$this->getSource());
			$temp[] = array(
						'id'=>$value['id'],
						'name'=>$value['name'],
						'resume'=>$value['resume'],
						'size'=>$oline_versions[$value['id']]['size'].'M',
						'link'=>Common::tjurl($webroot.'/channel/index/tj', $value['id'], $intersrc, $oline_versions[$value['id']]['link']),
						'alink'=>urldecode($webroot.'/channel/index/detail?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
						'img'=>urldecode($value['img']),
						'data-Info'=>$value['name'].','.$href.','.$value['id'].','.$oline_versions[$value['id']]['link'].','.$value['package'].','.$oline_versions[$value['id']]['size'].','.'Android'.$sys_version[$oline_versions[$value['id']]['min_sys_version']]['title'].','.$resolution[$oline_versions[$value['id']]['min_resolution']]['title'].'-'.$resolution[$oline_versions[$value['id']]['max_resolution']]['title'],
			);
		}
    	$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    	 
    
    }
    
    /**
     * 默认显示地址
     */
    public function rankUrlAction() {
    	$configs = Game_Service_Config::getAllConfig();
    	echo  $configs['game_client_rank'];
    }
}