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
	const VERSION_NAME_151     =  1;
	const VERSION_NAME_152_157 =  2;
	const VERSION_NAME_158     =  3;
	private $mClientVersionType = array(self::VERSION_NAME_151=>array(self::BEST_TYPE_NUM_NINE),
										self::VERSION_NAME_152_157=>array(self::BEST_TYPE_NUM_ONE,
												                          self::BEST_TYPE_NUM_NINE),
										self::VERSION_NAME_158=>array(self::BEST_TYPE_NUM_ONE,
												                      self::BEST_TYPE_NUM_NINE,
												                      self::BEST_TYPE_NUM_FOUR)			
	);
	private $mNetworkType = array( self::NETWORK_WIFI  => array(self::NETWORK_WIFI,
                                                                self::NETWORK_ALL),
			                       self::NETWORK_MOBILE => array(self::NETWORK_MOBILE,
                             									 self::NETWORK_ALL)   	
			                     );
    
    /**
     * 闪屏API
     */
    public function IndexAction() {
    	$flash = $this->getInput('apk_flash_recommend');
    	$network = strtolower($this->getInput('network'));
        $this->saveStartPageBehaviour();
    	$debugMsg = array('msg' => "闪屏API", 'parmes'=>$flash);
    	Util_Log::info(__CLASS__, self::LOG_FILE, $debugMsg);
    	if(!$flash){
    		return '';
    	}
    	$flashs = explode('|', $flash);    	
        $groupId       =  $this->getGroupId ( $flashs[2] );
        $networkType   =  $this->getNetwordType ( $network );
        $clientVersionType =  $this->getClientVertionType ( $flashs[4] );
        
        $returnGameInfo = $this->getBesttjCachedata ( $groupId, $networkType, $clientVersionType);
        if($flashs[1] < 0 || $flashs[1] < $returnGameInfo['version']){
        	$returnGameInfo = $this->formatReturnGameInfo($returnGameInfo);
        	exit(json_encode($returnGameInfo));
        } else {
        	return '';
        }
    }
    
    private function formatReturnGameInfo($returnGameInfo){
    	foreach ($returnGameInfo['data'] as $key=>$val){
    		$returnGameInfo['data'][$key]['PageUrl'] = $val['PageUrl'].'&t_bi='.self::getSource();
    	}
    	return $returnGameInfo;
    }
	
	private function getBesttjCachedata($groupId, $networkType, $clientVersionType ) {
		$version = Client_Service_Besttj::getDataVersion ();
        $localCache  = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
        $dataKeyName = Client_Service_Besttj::getDataKeyName ($groupId, $networkType, $clientVersionType, $version);
        $bestTjInfoData = $localCache->get($dataKeyName);
        if($bestTjInfoData === false){
        	$bestTjInfoData = $this->getBesttTjInfoData ($groupId, $networkType, $clientVersionType);
        	$localCache->set($dataKeyName, $bestTjInfoData, Client_Service_Besttj::CACHE_EXPIRE);
        }
        return $bestTjInfoData;
        
	}
	
	private function getBesttTjInfoData($groupId, $networkType, $clientVersionType) {
		$params = $this->foundGtypeToParams($groupId);
		$params = $this->handleParam($params, $networkType, $clientVersionType);
		$bestTjInfo =  $this->foundBesttjByGtypes($params);
		$gameIds = Client_Service_Besttj::getIdxBesttjByOnlineBesttjId(intval($bestTjInfo['id']));
		$this->mGameList = Common::resetKey($gameIds, 'game_id');
		$gameReturnList = $this->getBesttjList($bestTjInfo);
		return $gameReturnList;
	}


	
	private function getClientVertionType($version) {
		if(strnatcmp($version, '1.5.2') < 0)  {
        	$clientVersionType = self::VERSION_NAME_151;
        } else {
        	if(strnatcmp($version, '1.5.8') < 0) {
        		$clientVersionType =self::VERSION_NAME_152_157;
        	} else {
        		$clientVersionType = self::VERSION_NAME_158;
        	}
        }
        return $clientVersionType;
	}

	
	
	private function getNetwordType($network) {
		if($network == 'wifi'){
        	 $networkType = self::NETWORK_WIFI; 					
        } else {
        	$networkType = self::NETWORK_MOBILE;
        }
	   return $networkType;
	}

	
	private function getGroupId($mode) {
		$group = Resource_Service_Pgroup::getPgroupBymodels($mode);  //根据机型找出分组
		$group = Common::resetKey($group, 'id');
		$ids = array_unique(array_keys($group));
		if($ids){
	       foreach($group as $key=>$value){
	       				$gameList = array();
	       				$gameList = explode(',',$value['p_title']);
	       				if(in_array($mode, $gameList)){
	       				    $grouId = $value['id'];
	       				}
	       }         
	       if($grouId){
	       		$grouIds = $grouId;
	       } else {
	            $grouIds = Resource_Service_Pgroup::DEFAULT_PGROUP;//1;
	       }
		 } else {
		        $grouIds = Resource_Service_Pgroup::DEFAULT_PGROUP;//1;
		 }
		 return $grouIds;
	}

    
	private function saveStartPageBehaviour() {
		$imei = trim($this->getInput('imei'));
		if (!$imei) {
			$sp = $this->getInput('sp');
			$imei = Common::parseSp($sp, 'imei');
		}
		if (!$imei) {
			return;
		}
		$behaviour = new Client_Service_ClientBehaviour($imei, Client_Service_ClientBehaviour::CLIENT_HALL);
        $behaviour->saveBehaviours(Client_Service_ClientBehaviour::ACTION_GET_START_PAGE);
	}

    private function getBesttjList($bestTjInfo) {
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
        				$gameList[$i]['PageUrl']  = $webroot.'/client/index/detail/?id='.$value['id'].'&pc=0&intersrc=flashrecom'.$bestTjInfo['id'];
        				$gameList[$i]['GameId']  = $value['gameid'];
        				$gameList[$i]['GameName']  = html_entity_decode($value['name'], ENT_QUOTES);
        				$gameList[$i]['GameUrl']  = $value['link'];
        				$gameList[$i]['GamePackage']  = $value['package'];
        				$gameList[$i]['GameSize']  = $value['size'];
        				$gameList[$i]['SDKVersion']  = $value['version'];
        				$gameList[$i]['Resolution']  = '240*320-1080*1920';
        				$gameList[$i]['IconUrl']  = $value['img'];
        				$gameList[$i]['resume'] = $this->mGameList[$value['gameid']]['game_message'] ? html_entity_decode($this->mGameList[$value['gameid']]['game_message'], ENT_QUOTES) : '';
        				if(count($games) <=1) $gameList[$i]['ImageUrl']  = Common::getAttachPath().$bestTjInfo['img'];
        				$i++;
        }
        $gameReturnList['data'] = $gameList;
        return $gameReturnList;
    }
    
   private function foundGtypeToParams($groupId) {
       $params['gtype'] = $groupId;
       return $params;
   }
   
   private function handleParam($params, $networkType, $clientVersionType) {
   	   $currTime = Util_TimeConvert::floor(Common::getTime(), Util_TimeConvert::RADIX_HOUR);
       $params['start_time'] = array('<=', $currTime);
       $params['status'] = self::BEST_OPEN;
       
       if($clientVersionType == self::VERSION_NAME_151)  {
           $params['btype'] = array('in', $this->mClientVersionType[$clientVersionType] );
       } else {
           $params['btype'] = array('in', $this->mClientVersionType[$clientVersionType] );
           $params['ntype'] = array('in', $this->mNetworkType[$networkType]);   
       }
       return $params;
   }
   
   private function foundBesttjByGtypes($params) {
       $bestTjInfo = Client_Service_Besttj::getBesttjByGtypes($params);  //找出分组信息
       if(!$bestTjInfo){
       	   $currTime = Util_TimeConvert::floor(Common::getTime(), Util_TimeConvert::RADIX_HOUR);
           $bestTjInfo = Client_Service_Besttj::getBesttjByGtypes(
                   array('gtype'=>Resource_Service_Pgroup::DEFAULT_PGROUP,
                         'status'=>self::BEST_OPEN, 
                         'start_time' => array('<=', Common::getTime('Y-m-d H:i'))));  //找出分组信息
       }
       return $bestTjInfo;
   }
   
   private function getGameListByBestTj($btype) {
       $gameIdsList = array_unique(array_keys($this->mGameList));  
       $gamesList = Resource_Service_GameListData::getList($gameIdsList);       
       if($btype == Resource_Service_Pgroup::DEFAULT_PGROUP) {
           $gamesList = array('0' => $gamesList[0]);
       }
       return $gamesList;
   }
}
