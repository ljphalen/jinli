<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author luojiapeng
 *
 */
class Thirdinterface_GameController extends Api_BaseController {
	public $mConfigChannel = array('709ecb66c0795cf2e81a957c8350cf43'
			
							);
	
	const PAGE_LIMIT = 20 ;
	const MIN_PAGE_LIMIT = 1;
	const MAX_PAGE_LIMIT = 300;
	

	public function getGameListForAoRuanAction(){
	    $info = $this->getAoRuanRequestDataInfoForAoRuan();
	    $info = $this->checkInputParamsForAoRuan ( $info );
	    $gameListData = $this->getGameListDataForAoRuan ($info);
        $this->clientOutput($gameListData);	
	}
	
	
	private function getGameListDataForAoRuan($info) {
		
		$searchParams['op_time'][0] = array('>=', date('Y-m-d H:i:s',$info['starttime']));
		$searchParams['op_time'][1] = array('<=', date('Y-m-d H:i:s',$info['endtime']));
		list($total, $gameList) = Resource_Service_Games::getList($info['page'], $info['pagesize'], $searchParams);
		
		$data['total'] = $total ;
		$data['start'] = $info['page'];
		
		$gameListData = array();	
		$freeId = $this->getFreeId ();
		$adId   = $this->getAdId ();
		$recordNum = 0;
		foreach ($gameList as $gameInfo){
			$gameListData[] = $this->makeGameInfoForAoRuan ( $gameInfo ,$freeId, $adId);
			$recordNum ++;
		}
		$data['num']   = $recordNum;
		$data['items'] = $gameListData;
		return $data;
	}
	
	private function getAdId() {
		return  (ENV == 'product')? 22: 42;
	}

	
	private function getFreeId() {
		return  (ENV == 'product')? 2 : 32;
	}

	
	private function makeGameInfoForAoRuan($gameInfo, $freeId, $adId) {
		//版本信息
		$gameVersionInfo = Resource_Service_Games::getGameVersionInfo ( $gameInfo['id'] );
		//所需安卓的最小的版本
		$minVersionInfo = Resource_Service_Attribute::getBy(array('id'=>$gameVersionInfo['min_sys_version']));
		//游戏分数
		$gameScore = Resource_Service_Score::getByScore(array('game_id' => $gameInfo['id']));
		//主分类
		$mainCategory = Resource_Service_GameCategory::getMainCategory( $gameInfo['id'] );
		
		$isFreeFlag = $gameInfo['price'] == $freeId ? 0 : 1;
		$isAd       = $gameInfo['price'] == $adId ? 1 : 0;
		$downUrl = Resource_Service_GameData::filterDownloadUrl($gameVersionInfo['link']);
		
		$gameListData = array('packageName'=> $gameInfo['package'],
				               'description'=>html_entity_decode($gameInfo['descrip']),
							   'minVersion'=>'Android '.(isset($minVersionInfo['title'])?$minVersionInfo['title']:'1.6'),
					           'minVersionCode'=>0,
					           'name'=>html_entity_decode($gameInfo['name']),
					           'categoryName'=>$mainCategory['parent']['title'],
					           'developer'=>$gameInfo['developer'],
					           'iconUrl'=> self::getIconUrl($gameInfo['big_img'], $gameInfo['mid_img'],$gameInfo['img']),
					           'screenshotsUrl'=>self::getScreenshotsUrl($gameInfo['id']),
					           'rating'=>$gameScore['score']?$gameScore['score']:0,
					           'versionName'=>$gameVersionInfo['version'],
					           'versionCode'=>$gameVersionInfo['version_code'],
					           'tag'=>$gameInfo['label'],
					           'downloadTimes'=>intval($gameInfo['downloads']),
					           'downloadUrl'=>$downUrl[0],
					           'apkMd5'=>$gameVersionInfo['md5_code'],
					           'apkSize'=>intval(self::sizeConvert($gameVersionInfo['size'])),
					           'createTime'=>intval($gameInfo['create_time']),
					           'updateTime'=>intval(strtotime($gameInfo['op_time'])),
					           'signatureMd5'=>md5($gameVersionInfo['fingerprint']),
					           'updateInfo'=>$gameVersionInfo['changes'],
					           'brief'=>html_entity_decode($gameInfo['resume']),
					           'appPermission'=>'',
							   'isAd'=>$isAd,
					           'isPaid'=> $isFreeFlag,
					           'isSecurity'=> 0
		);
		
		return $gameListData;
	}

	
	private function getIconUrl($bigImg, $midImg, $smallImg){
		$iconImg = array();
		if($bigImg){
			return $bigImg;
		}
		if($midImg){
			return $bigImg;
		}
		if($smallImg){
			return $bigImg;
		}
		
	}
	
	private function getScreenshotsUrl( $gameId ){		
		//缩图
		$screensImg = Resource_Service_Img::getsBy(array('game_id'=>$gameId));
		$flag = 0;
		foreach ($screensImg as $val){
			if($flag == 5){
				break;
			}
			$img[] = $val['img'];
			$flag++;
		}	
		return implode(',', $img);
	}

	private function sizeConvert($size){
		if(!$size){
			return ;
		}	
		return $size*1024*1024;
		
	}
	
	
	 private function checkInputParamsForAoRuan($info) {

		if(!is_array($info)){
			$this->clientOutput(array());
		}
	 	
		if(!in_array($info['from'], $this->mConfigChannel)){
		 	$this->clientOutput(array());	
		}
		
		$info['page'] = intval($info['page']) < 1 ? 1:intval(($info['page']));
		$info['pagesize'] = $this->getPageLimit (intval($info['pagesize']) );
		if(!$info['starttime']){
			$this->clientOutput(array());
		}	
		if(!$info['endtime']){
			$this->clientOutput(array());
		}
		
		if(intval($info['starttime']) > intval($info['endtime'])){
			$this->clientOutput(array());
		}
        
		return $info;
	 }
	 
	 
	private function getPageLimit($pagesize) {
	    return  ($pagesize < self::MIN_PAGE_LIMIT || $pagesize > self::MAX_PAGE_LIMIT)? self::PAGE_LIMIT : $pagesize;
	}

	private function getAoRuanRequestDataInfoForAoRuan() {
		$info = $this->getInput(array('from',
				                      'page',
				                      'pagesize',
				                      'starttime',
				                      'endtime'
				                       ));
		return $info;
	}
	
	
	
	public function getGameListForWanKaAction(){
		$info = $this->getRequestDataInfoForWanKa();
		$info = $this->checkInputParamsForWanKa ( $info );
		$gameListData = $this->getGameListDataForWanKa ($info);
		$this->clientOutput($gameListData);
	}
	
	
	public function getGameInfoByPackageForWanKaAction(){
		$pakageName = $this->getInput('package_name');
	
		if(!$pakageName){
			$this->clientOutput(array());
		}
	
		$params['package'] = $pakageName;
		$gameInfo = Resource_Service_Games::getBy($params);
		if(!$gameInfo){
			$this->clientOutput(array());
		}
		$freeId = $this->getFreeId();
		$gameData['info'] = $this->makeGameInfoForWanKa ( $gameInfo, $freeId);
		$this->clientOutput($gameData);
	
	
	}
	
	private function makeGameInfoForWanKa($gameInfo, $freeId) {
		//版本信息
		$gameVersionInfo = Resource_Service_Games::getGameVersionInfo ( $gameInfo['id'] );
		//所需安卓的最小的版本
		$minVersionInfo = Resource_Service_Attribute::getBy(array('id'=>$gameVersionInfo['min_sys_version']));
		//游戏分数
		$gameScore = Resource_Service_Score::getByScore(array('game_id' => $gameInfo['id']));
		//主分类
		$mainCategory = Resource_Service_GameCategory::getMainCategory( $gameInfo['id'] );
	
		$isFreeFlag = $gameInfo['price'] == $freeId ? 0 : 1;
		$downUrl = Resource_Service_GameData::filterDownloadUrl($gameVersionInfo['link']);
			
		$gameData= array('app_name'=>html_entity_decode($gameInfo['name']),
						'rating'=>$gameScore['score']?$gameScore['score']:0,
						'd_cnt'=>intval($gameInfo['downloads']),
						'app_type'=>$mainCategory['parent']['title']?$mainCategory['parent']['title']:'',
						'description'=>html_entity_decode($gameInfo['descrip']),
						'version'=>isset($minVersionInfo['title'])?$minVersionInfo['title']:'1.6',
						'size'=>$gameVersionInfo['size'].'M',
						'icon'=> self::getIconUrl($gameInfo['big_img'], $gameInfo['mid_img'],$gameInfo['img']),
						'package'=> $gameInfo['package'],
						'd_url'=>$downUrl[0],
						'is_paid'=> $isFreeFlag,
						'createTime'=>intval($gameInfo['create_time']),
						'updateTime'=>intval(strtotime($gameInfo['op_time'])),
	
		);
		return  $gameData;
	}
	
	
	
	private function getGameListDataForWanKa($info) {
		$searchParams['op_time'][0] = array('>=', date('Y-m-d H:i:s',$info['startTime']));
		$searchParams['op_time'][1] = array('<=', date('Y-m-d H:i:s',$info['endTime']));
		$orderBy = array('op_time'=>'ASC');
	
		list($total, $gameList) = Resource_Service_Games::getList($info['page'], $info['pageSize'], $searchParams, $orderBy);
		$data['total_cnt'] = $total ;
		$data['has_more'] = ($info['page']*$info['pageSize'] > $total)? 0:1 ;
		$gameListData = array();
		$freeId = $this->getFreeId();
		foreach ($gameList as $gameInfo){
			$gameListData[] = $this->makeGameInfoForWanKa($gameInfo, $freeId);
		}
		$data['list'] = $gameListData;
		return $data;
	}
	

	
	private function checkInputParamsForWanKa($info) {
	
		if(!is_array($info)){
			$this->clientOutput(array());
		}
		 
		$info['page'] = intval($info['pn']) < 1 ? 1:intval(($info['pn']));
		$info['pageSize'] = $this->getPageLimit (intval($info['rn']) );
		if(!$info['start']){
			$this->clientOutput(array());
		}
		if(!$info['end']){
			$this->clientOutput(array());
		}
		if(intval($info['start']) > intval($info['end'])){
			$this->clientOutput(array());
		}
		$info['startTime'] = $info['start'];
		$info['endTime'] = $info['end'];
	
		return $info;
	}
	
	
	private function getRequestDataInfoForWanKa() {
		$info = $this->getInput(array('pn',
				'rn',
				'start',
				'end'
		));
		return $info;
	}
	

   
}