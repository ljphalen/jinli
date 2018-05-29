<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class BesttjController extends Api_BaseController {
	
	public $perpage = 10;
	
	//网络
	public $mNType = array(
			1 => 'wifi',
			2 => 'mobile',
			3 => 'all',
	);
	private $mNumType = array(
	    1 => '1',
	    2 => '9',
	    3 => '4'
	);
	private $mGameList = array();
	const BEST_NUM_ONE = '1';
	const BEST_NUM_NINE = '9';
	const BEST_NUM_FOUR = '4';
	const BEST_OPEN = '1';
	
	const BEST_TYPE_NUM_ONE = 1;
	const BEST_TYPE_NUM_NINE = 2;
	const BEST_TYPE_NUM_FOUR = 3;
	
	const NETWORK_WIFI = 1;
	const NETWORK_MOBILE = 2;
	const NETWORK_ALL = 3;
	const LOG_FILE = "apk_flash.log";
    
    /**
     * 闪屏API
     */
    public function IndexAction() {
    	$flash = $this->getInput('apk_flash_recommend');
    	$debugMsg = array('msg' => "闪屏API", 'parmes'=>$flash);
    	Util_Log::info(__CLASS__, self::LOG_FILE, $debugMsg);
    	$flashs = explode('|',$flash);
    	if($flashs){
    		$params = $this->foundGtypeToParams($flashs);
    		$params = $this->handleParam($params, $flashs);
    		$bestTjInfo =  $this->foundBesttjByGtypes($params);
    		$this->mGameList = Common::resetKey(Client_Service_Besttj::getIdxBesttjByOnlineBesttjId(intval($bestTjInfo['id'])), 'game_id');
    		$gameReturnList = $this->getBesttjList($flashs, $bestTjInfo);
    		if($gameReturnList['data']){
    			exit(json_encode($gameReturnList));
    		} else {
    			return '';
    		}
    	}
    }
    
    private function getBesttjList($flashs, $bestTjInfo) {
        if($flashs[1] >= 0 && $flashs[1] >= $bestTjInfo['update_time']){
            return array();
        }
        $webroot = Common::getWebRoot();
        $games = $this->getGameListByBestTj($bestTjInfo['btype']);
        $gameReturnList['sign'] = 'GioneeGameHall';
        $gameReturnList['version'] = $bestTjInfo['update_time'];
        $gameReturnList['title'] = html_entity_decode( $bestTjInfo['title'], ENT_QUOTES);
        $gameReturnList['coldDay'] = Game_Service_Config::getValue('game_client_bestime');
        $gameReturnList['networkLimit'] =  $bestTjInfo['ntype'] ? $this->mNType[$bestTjInfo['ntype']] : "all";
        $gameReturnList['descript'] = html_entity_decode( $bestTjInfo['guide'], ENT_QUOTES);
        $gameReturnList['showCount'] = $this->mNumType[$bestTjInfo['btype']];
        $gameList = array();
        $i = 0;
        if(!$games)  array();
        foreach($games as $key=>$value){
        				$gameList[$i]['PageUrl']  = $webroot.'/client/index/detail/?id='.$value['id'].'&pc=0&intersrc=flashrecom'.$bestTjInfo['id'].'&t_bi='.self::getSource();
        				$gameList[$i]['GameId']  = $value['id'];
        				$gameList[$i]['GameName']  = $value['name'];
        				$gameList[$i]['GameUrl']  = $value['link'];
        				$gameList[$i]['GamePackage']  = $value['package'];
        				$gameList[$i]['GameSize']  = $value['size'];
        				$gameList[$i]['SDKVersion']  = $value['version'];
        				$gameList[$i]['Resolution']  = $value['min_resolution_title']."-".$value['max_resolution_title'];
        				$gameList[$i]['IconUrl']  = $value['img'];
        				$gameList[$i]['resume'] = $this->mGameList[$value['id']]['game_message'] ? html_entity_decode($this->mGameList[$value['id']]['game_message'], ENT_QUOTES) : '';
        				if(count($games) <=1) $gameList[$i]['ImageUrl']  = Common::getAttachPath().$bestTjInfo['img'];
        				$i++;
        }
        $gameReturnList['data'] = $gameList;
        return $gameReturnList;
    }
    
   private function foundGtypeToParams($flashs) {
       $group = Resource_Service_Pgroup::getPgroupBymodels($flashs[2]);  //根据机型找出分组
       $group = Common::resetKey($group, 'id');
       $ids = array_unique(array_keys($group));
       if($ids){
           foreach($group as $key=>$value){
           				$gameList = array();
           				$gameList = explode(',',$value['p_title']);
           				if(in_array($flashs[2],$gameList)){
           				    $gameIdList[] = $value['id'];
           				}
           }
           if($gameIdList){
           		$params['gtype'] = array('IN',$gameIdList);
           } else {
               $params['gtype'] = Resource_Service_Pgroup::DEFAULT_PGROUP;//1;
           }
       } else {
           $params['gtype'] = Resource_Service_Pgroup::DEFAULT_PGROUP;//1;
       }
       return $params;
   }
   
   private function handleParam($params, $flashs) {
       $time = Common::getTime('Y-m-d H:i');
       $network = strtolower($this->getInput('network'));
       $version = $flashs[4];
       $params['start_time'] = array('<=', $time);
       $params['status'] = self::BEST_OPEN;
       
       if(strnatcmp($version, '1.5.2') < 0)  {
           $params['btype'] = self::BEST_TYPE_NUM_NINE;
       } else {
           if(strnatcmp($version, '1.5.8') < 0) {
               $params['btype'] = array('IN',
                       array(self::BEST_TYPE_NUM_ONE,
                             self::BEST_TYPE_NUM_NINE));
           } else {
               $params['btype'] = array('IN',
                       array(self::BEST_TYPE_NUM_ONE,
                             self::BEST_TYPE_NUM_NINE,
                             self::BEST_TYPE_NUM_FOUR));
           }
           if($network == 'wifi'){
               $params['ntype'] = array('IN',
                       array(self::NETWORK_WIFI,
                             self::NETWORK_ALL));
           } else {
               $params['ntype'] = array('IN',
                       array(self::NETWORK_MOBILE,
                             self::NETWORK_ALL));
           }
       }
       return $params;
   }
   
   private function foundBesttjByGtypes($params) {
       $bestTjInfo = Client_Service_Besttj::getBesttjByGtypes($params);  //找出分组信息
       if(!$bestTjInfo){
           $bestTjInfo = Client_Service_Besttj::getBesttjByGtypes(
                   array('gtype'=>Resource_Service_Pgroup::DEFAULT_PGROUP,
                         'status'=>self::BEST_OPEN, 
                         'start_time' => array('<=', Common::getTime('Y-m-d H:i'))));  //找出分组信息
       }
       return $bestTjInfo;
   }
   
   private function getGameListByBestTj($btype) {
       $gameIdsList = array_unique(array_keys($this->mGameList));
       if ($gameIdsList) {
           foreach($gameIdsList as $key=>$value){
               $infos = array();
               $infos = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
               if($infos['status']){
                   $gamesList[] = $infos;
               }
           }
       }
       if($btype == Resource_Service_Pgroup::DEFAULT_PGROUP) {
           $gamesList = array('0' => $gamesList[0]);
       }
       return $gamesList;
   }
}