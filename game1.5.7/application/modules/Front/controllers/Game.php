<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class GameController extends Front_BaseController{
	
	/**
	 * 游戏详情页
	 */
	public function detailAction(){
		Common::addSEO($this,'游戏详情页');
		$id = $this->getInput('id');
		$from = $this->getInput('from');
		//游戏基本数据
		$game = Resource_Service_Games::getGameAllInfo(array('id'=> $id));
	    
		//没有记录跳转
	    if(!$game){
	    	$str  = $this->redirect('/Front/Error/index/');
	    	exit;
	    }
	    
		//游戏礼包 在线游戏礼包
		$params = array('game_id'=>$id, 'status' => 1, 'game_status'=>1);
		$params['effect_start_time'] = array('<=', Common::getTime());
		$params['effect_end_time'] = array('>=', Common::getTime());
		list(,$gift) = Client_Service_Gift::getList(1, 3, $params);
		//相关推荐
		$recommIds = Client_Service_Recommend::getRecommendGames(array('GAMEC_RESOURCE_ID'=> $id));
		if($recommIds){
			foreach($recommIds as $key=>$value){
				$tmp = Resource_Service_Games::getGameAllInfo(array('id'=>$value['GAMEC_RECOMEND_ID']));
				if($tmp) $recommGames[] = $tmp;
			}
		}
		//验证此应用是否安全
		$safe_str= unserialize($game['certificate']);
		$safe_arr = Common::applyIsSafe($safe_str);
		$this->assign('safe_arr', $safe_arr);
	
		//取得配置文件
		$ami_web_gid = Game_Service_Config::getValue('ami_web_gid');
		$clientlink = Resource_Service_Games::getGameAllInfo(array('id'=>$ami_web_gid));
		$this->assign('clientlink', $clientlink);
		$this->assign('game', $game);
		$this->assign('gift', $gift);
		$this->assign('recommGames', $recommGames);	
		$this->assign('from', $from);
		//统计参数
		if($from){
			$tj_object = 'gamedetail'.$id.'_'.$from;
			$tj_intersrc = 'isearch_'.$from.'_gid'.$id;
		}else{
			$tj_object = 'gamedetail'.$id;
			$tj_intersrc = 'gamedetail_gid'.$id;
		}
		$this->assign('tj_object', $tj_object);
		$this->assign('tj_intersrc', $tj_intersrc);
		
		//取得配置文件
		$ami_web_game_share_text = Game_Service_Config::getValue('ami_web_game_share_text');
		$this->assign('ami_web_game_share_text', $ami_web_game_share_text);
	}	
}