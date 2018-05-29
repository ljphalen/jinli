<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_GuessController extends Api_BaseController {
	public $perpage = 10;
    
	/**
	 * 猜你喜欢列表
	 */
    public function guessListAction() {
    	$page = intval($this->getInput('page'));
    	$intersrc = $this->getInput('intersrc');
    	$sp = $this->getInput('sp');
    	$imei = end(explode('_',$sp));
    	$imcrc = crc32(trim($imei));
    	
    	$data = $this->guessList($page, $intersrc, $imcrc);
    	$this->localOutput('','',$data);
    }
    
    private  function guessList($page, $intersrc,$imcrc) {
    	
		if ($page < 1) $page = 1;
		$resource_games = array();
		
		$params['imcrc'] = $imcrc;
		
		//get games list
		$gues = Client_Service_Guess::getGamesByImCrc( $imcrc );
		$ids = explode(',',$gues['game_ids']);
	    if($gues){
				list($total, $games) = Resource_Service_Games::search($page, $this->perpage, array('id'=>array('IN', $ids),'status'=>1));
	    }
		
		//如果猜你喜欢没有数据家用默认的代替
		if(!$gues){
			list($total, $games) = Client_Service_Game::geGuesstList($page, $this->perpage, array('game_status'=>1,'status'=>1));
		}
		$temp = array();
		$webroot = Common::getWebRoot();
    	
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		
		$type = ($gues ? 0 : 1);
		$tmp = Resource_Service_Games::getClientGameData($games, $intersrc, $checkVer, $type);
	
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		return $data;
    } 
}