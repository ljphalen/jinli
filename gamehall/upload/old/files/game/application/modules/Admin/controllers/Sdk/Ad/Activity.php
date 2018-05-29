<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Sdk_Ad_ActivityController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Sdk_Ad_Activity/index',
		'addUrl' => '/Admin/Sdk_Ad_Activity/add',
		'addPostUrl' => '/Admin/Sdk_Ad_Activity/add_post',
		'editUrl' => '/Admin/Sdk_Ad_Activity/edit',
		'editPostUrl' => '/Admin/Sdk_Ad_Activity/edit_post',
		'deleteUrl' => '/Admin/Sdk_Ad_Activity/delete',
		'batchUpdateUrl'=>'/Admin/Sdk_Ad_Activity/batchUpdate',
		'uploadImgUrl' => '/Admin/Sdk_Ad_Activity/uploadImg',
		'uploadUrl' => '/Admin/Sdk_Ad_Activity/upload',
		'uploadPostUrl' => '/Admin/Sdk_Ad_Activity/upload_post',
	);
	
	public $perpage = 20;
	public $show_type_name = array(
			1 => '公告',
			2 => '活动',
			
	);
	public  $joinGameTypeName = array(0 => '全部',
			                          1 =>'定向',
			                          2 =>'排除');
	public $versionName = 'Sdk_Ad_Version';
	    
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {

		//参数
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$status = intval($this->getInput('status'));
		$title = trim($this->getInput('title'));
		$startTime = $this->getInput('start_time');
		$endTime = $this->getInput('end_time');
		$gameIds = intval($this->getInput('game_ids'));
		$gameName     = trim($this->getInput('name'));
		$showType = trim($this->getInput('show_type'));
		
		if ($page < 1) $page = 1;
		$params = array();
		$search = array();
		if($startTime && $endTime){
			$search['start_time'] = $startTime;
			$search['end_time'] = $endTime;
			$params['start_time'] = array(array('>=',strtotime($startTime)),array('<=',strtotime($endTime)));
			$params['end_time'] = array(array('>=',strtotime($startTime)),array('<=',strtotime($endTime)));
		}
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
		if($title) {
			$search['title'] = $title;
			$params['title'] = array('LIKE',$title);
		}
	 
		if($showType){
			$search['show_type'] = $showType;
			if($showType ==  3){
				$showType ='1,2';
			}
			$params['show_type'] = array('LIKE',$showType);
		}
		
		list($search, $params) = $this->foundParamId( $gameIds, $gameName, $params, $search );
			
		list($total, $ads) = Sdk_Service_Ad::getList($page, $perpage, $params,  array('sort'=>'DESC' ,'start_time'=>'DESC' ,'id'=>'DESC'));
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
		$this->assign("show_type_name", $this->show_type_name);
		$this->assign("joinGameTypeName", $this->joinGameTypeName);
	}
	
	private function foundParamId($gameIds, $gameName, $params, $search, $ret, $ids, $temp, $game_params, $games, $name_ids) {
	
		if($gameIds) {
			$search['game_ids'] = $gameIds;
			$params['game_ids'] = array('LIKE', $gameIds);
			$ret = Sdk_Service_Ad::getsBy($params);
			$ids = $this->fillGameId ($gameIds, $ret );
		}
		if($gameName){
			$search['name']  = $gameName;
			$game_params['name']  = array('LIKE', $gameName);
			$games = Resource_Service_Games::getGamesByGameNames($game_params);
			$games = Common::resetKey($games, 'id');
			$games = array_unique(array_keys($games));
			$ret = Sdk_Service_Ad::getsBy($params);
			$nameIds = $this->fillGameId ($gameIds, $ret );
		}
		
		unset($params['game_ids']);
		if($gameName || $gameIds){
			$params['join_game_type'] = Sdk_Service_Ad::JOIN_GAME_TYPE_ALL ;
			$ret = Sdk_Service_Ad::getsBy($params);
			$tempIds = Common::resetKey($ret, 'id');
			$tempIds = array_unique(array_keys($tempIds));
		}		
		
		if($gameIds && $gameName){		
			$ids = array_intersect($ids, $nameIds)? array_intersect($ids, $nameIds):array('0');
			$ids = array_merge($tempIds, $ids);	
			$params['id'] = array('IN', $ids);
		}elseif ($gameIds){
			$ids = array_merge($tempIds, $ids);
			$params['id'] = array('IN', $ids);
		}elseif ($gameName){
			$nameIds = array_merge($tempIds, $nameIds);
			$params['id'] = array('IN', $nameIds);
		}
		unset($params['join_game_type']);
		return array($search, $params);
	}
	
	private function fillGameId($gameIds, $info) {
		$ids = array();
		foreach ($info as $val){
			$temp = explode(',', $val['game_ids']);
			if( in_array($gameIds, $temp) || $val[join_game_type] == 0 ){
				$ids[$val['id']] = $val['id'];
			}
		}
		if(!$ids){
			$ids = array('0');
		}
		return $ids;
	}

	public function addAction() {
		
	}
	
	public function add_postAction() {
		$info = $this->getPost(array('sort','join_game_type', 'game_ids', 'title',  array('ad_content', '#s_z'), 'start_time', 'end_time', 'is_payment','status','show_type','img'));
		$info['ad_content'] = str_replace("<br />","",html_entity_decode($info['ad_content']));
		$info = $this->_cookData($info);
		$info['sort']  = intval($info['sort']);
		$info['title'] = trim($info['title']);
		$info['show_type'] = implode(',' , $info['show_type']);
		//判断是否重复
		$params['title'] = $info['title'];
		$ret = Sdk_Service_Ad::getBy($params);
		if($ret)  $this->output(-1, '名称不能重复');
		$this->isActivityInvalid($info);
		//插入数据
		$result = Sdk_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功',array('id'=>$result,'show_type'=>$info['show_type']));

	}
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='sort'){
			$ret = Sdk_Service_Ad::sortAd($info['sort']);
		}elseif($info['action'] =='open'){			
			$this->checkTimeOverlapForBatchOpen($info['ids']);
			$this->checkGameIdForBatchOpen($info['ids']);
			$data['version_time'] = Common::getTime();
			$data['status'] = 1 ;
			foreach ($info['ids'] as $key=>$id){
				$ret = Sdk_Service_Ad::updateAd($data, $id);
			}
		}elseif($info['action'] =='close'){
			$data['status'] = 0;
			foreach ($info['ids'] as $key=>$val){
				$ret = Sdk_Service_Ad::updateAd($data, $val);
			}
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$id = intval($this->getInput('id'));
		if (!$id) $this->output(-1, '操作失败');
		$info = Sdk_Service_Ad::getAd($id);
		$this->assign('show_type_name', $this->show_type_name);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','sort','join_game_type', 'game_ids', 'title',  array('ad_content', '#s_z'), 'start_time', 'end_time', 'is_payment','status','show_type','img'));
		$info['ad_content'] = str_replace("<br />","",html_entity_decode($info['ad_content']));
		$info = $this->_cookData($info);
		$info['sort']  = intval($info['sort']);
		$info['title'] = trim($info['title']);
		$info['show_type'] = implode(',' , $info['show_type']);	
		$id =  intval($this->getPost('id'));
		if (!$id) $this->output(-1, '操作失败');
		if($info['status']){
		   $this->isActivityInvalid($info);
		}
		//判断是否重复
		$params['title'] = $info['title'];
		$params['id'] = array('!=', $id);
		$ret = Sdk_Service_Ad::getBy($params);
		if($ret)  $this->output(-1, '名称不能重复');
	    $ret = Sdk_Service_Ad::updateAd($info, $id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.',array('id'=>$id,'show_type'=>$info['show_type'])); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['show_type'])$this->output(-1, '显示范围要选择.');
		if(!trim($info['title'])) $this->output(-1, '名称不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.'); 
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		if(!trim($info['ad_content'])) $this->output(-1, '内容不能为空.');
		if($info['end_time'] <= Common::getTime() && $info['status']) $this->output(-1, '不能添加过期活动.');
		//验证是否有图片
		if(in_array('2', $info['show_type'])){
			if(!$info['img']){
				$this->output(-1, '没有上传图片');
			}
		}
		if(intval($info['join_game_type']) == '0'){
			$info['game_ids'] = 0;
		}else{
			$info['game_ids'] = html_entity_decode($info['game_ids']);
			$gameIds = explode(',', $info['game_ids']);
			$gameIds = array_unique($gameIds);
			$this->checkGameId ( $gameIds );
			$info['game_ids'] = implode(',', $gameIds);
		}
		$info['version_time'] = Common::getTime();
		return $info;
	}
	
	private function checkGameId($gameIds) {
		foreach ($gameIds as $gameId){
			if(!intval($gameId)){
				$this->output(-1, '游戏ID：'.$gameId.' 非法');
			}
			$gameInfo = Resource_Service_Games::getBy(array('id'=>$gameId,'status'=>Resource_Service_Games::STATE_ONLINE));
			if(!$gameInfo){
				$this->output(-1, '游戏ID：'.$gameId.'已下线或游戏不存在。');
			}
			if($gameInfo['cooperate'] == Resource_Service_Games::COMMON_GAME){
				$this->output(-1, '游戏ID：'.$gameId.'不是联运游戏');
			}
		}
	}
	
	private function  checkGameIdForBatchOpen($ids){
		foreach ($ids as $id) {
			$info = Sdk_Service_Ad::getAd($id);
			if(!$info['join_game_type']) continue;
			$gameIds = explode(',', $info['game_ids']);
			foreach ($gameIds as $val){
				$gameInfo = Resource_Service_Games::getBy(array('id'=>$val,'status'=>Resource_Service_Games::STATE_ONLINE));
				if(!$gameInfo){
					$this->output(-1, '公告ID'.$id.',游戏ID：'.$val.'已下线');
				}
				if($gameInfo['cooperate'] == Resource_Service_Games::COMMON_GAME){
					$this->output(-1, '公告ID'.$id.',游戏ID：'.$val.'不是联运游戏');
				}
			}
		}
	}		
	
	private function checkTimeOverlapForBatchOpen($batchOpenAdIds) {
	    $adIds = $isTimeOverLap = $adIdsOverActivity =  array();
	    foreach ($batchOpenAdIds as $id) {
	        $info = Sdk_Service_Ad::getAd($id);
	        $OverActivityId = $this->checkIsOverActivity($info);
	        if($OverActivityId){
	            $adIdsOverActivity[] = $OverActivityId;
	        }
	        $isTimeOverLap = $this->getTimeOverLapGameId($info, $batchOpenAdIds);
	        $adIds = $isTimeOverLap;
	    }
	    
	    $this->outIsOverLapMsg($adIdsOverActivity);
	    $this->outIsTimeOverLapMsg($adIds, 'batchOpen');
	}
	
	private function checkIsOverActivity($info) {
	        if($info['end_time'] <= Common::getTime()) {
	            return  $info['id'];
	        }
	}
	
	private function outIsTimeOverLapMsg($adIds, $operationMethod = false) {
	    if(!$adIds){
	        return;
	    }
	     
	    $dailogMsg = '';
	    $dailogMsg .= '公告ID为：'.implode(',',array_unique($adIds));
	    if($operationMethod == 'batchOpen' ){
	        $dailogMsg.=' 公告时间重叠，请去掉勾选后提交。';
	    } else {
	        $dailogMsg.=' 公告时间重叠，请修改对应时间在提交。';
	    }
	     
	    $this->output(-1, $dailogMsg);
	}
	
	private function outIsOverLapMsg($adIds) {
	    if(!$adIds){
	        return;
	    }
	    
	    $dailogMsg = '';
	    $dailogMsg .= '公告ID为：'.implode(',',array_unique($adIds)).' 为过期公告，请重新选择.';
	    $this->output(-1, $dailogMsg);
	}
	
	private function getTimeOverLapGameId($info, $batchOpenAdIds) {
	    $timeOverLapGameId = array();
	    $activities = $this->invalidactivity($info, $batchOpenAdIds);
	    
	    if(!$activities){
	        return $timeOverLapGameId;
	    }
	    
	    $gameIds = explode(',', $info['game_ids']);
	    $gameIds = $this->convertArrayData($gameIds);
	    
	    foreach($activities as $key=>$value){
	        if($value['id'] == $info['id']) {
	            continue;
	        }
	        
	        $valueGameIds = explode(',', $value['game_ids']);
	        $valueGameIds = $this->convertArrayData($valueGameIds);
	        
	        if(!$info['join_game_type'] 
	         || array_intersect($gameIds, $valueGameIds) && $info['join_game_type'] == 1 && $value['join_game_type'] == 1
	         || $info['join_game_type'] == 2 && $value['join_game_type'] == 2
	         || $info['join_game_type'] != $value['join_game_type'] && array_diff($gameIds, $valueGameIds) && $info['join_game_type'] == 1
	         || $info['join_game_type'] != $value['join_game_type'] && !array_diff($gameIds, $valueGameIds) && array_diff($valueGameIds, $gameIds) && $info['join_game_type'] == 2
	         || $info['join_game_type'] != $value['join_game_type'] && array_diff($gameIds, $valueGameIds) && array_diff($valueGameIds, $gameIds) && $info['join_game_type'] == 2
	         || !$value['join_game_type']){
    	        $ret = $this->getIsTimeOverLapCondition($info, $value);
	            if($ret){
	                $timeOverLapGameId[] = $value['id'];
	            }
	        }
	    }
	    return $timeOverLapGameId;
	}
	
	public function convertArrayData($ids){
	    $adIds = array();
	
	    if(!$ids){
	        return $adIds;
	    }
	
	    foreach($ids as $key=>$value){
	        $adIds[] = intval($value);
	    }
	    return $adIds;
	}
	
	private function invalidactivity($info, $batchOpenAdIds = array()) {
	    $onlineActivities = $idsActivities = array();
	    if($info['show_type'] == Sdk_Service_Ad::SHOW_TYPE_ACTIVITY){
	        return $onlineActivities;
	    }
	    
	    if(!$batchOpenAdIds){
	        $onlineActivities = Sdk_Service_Ad::getsBy(
	                array('show_type' => array('LIKE', '1'),
	                        'status' => 1));
	        return $onlineActivities;
	    } 
	    
        $onlineActivities = Sdk_Service_Ad::getsBy(
                array('show_type' => array('LIKE', '1'),
                        'id' => array('NOT IN', $batchOpenAdIds),
                        'status' => 1));
        $idsActivities = Sdk_Service_Ad::getsBy(
                array('show_type' => array('LIKE', '1'),
                        'id' => array('IN', $batchOpenAdIds)));
        return array_merge($onlineActivities, $idsActivities);
	}
	
	private function isActivityInvalid($info) {
	    $adIds = $isTimeOverLap = array();
	    $isTimeOverLap = $this->getTimeOverLapGameId($info);
	    $adIds = $isTimeOverLap;
	    $this->outIsTimeOverLapMsg($adIds);
	}
	
	public function getIsTimeOverLapCondition($info, $currentActivity) {
	    if($info['start_time'] <= $currentActivity['start_time'] && $currentActivity['start_time'] <= $info['end_time']){
	        return true;
	    }
	    if($currentActivity['start_time'] <= $info['start_time'] && $info['start_time'] <= $currentActivity['end_time']){
	        return true;
	    }
	    if($info['start_time'] <= $currentActivity['start_time'] && $currentActivity['end_time'] <= $info['end_time']){
	        return true;
	    }
	    if($currentActivity['start_time'] <= $info['start_time'] && $info['end_time'] <= $currentActivity['end_time']){
	        return true;
	    }
	}
	
		
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Sdk_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		//Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Sdk_Service_Ad::deleteAd($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

    /**
     * 编辑器中上传图片
     */
    public function uploadImgAction() {
    	$ret = Common::upload('imgFile', 'sdkimg');
    	if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
    	exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
    }

    public function uploadAction() {
    	$imgId = $this->getInput('imgId');
    	$this->assign('imgId', $imgId);
    	$this->getView()->display('common/upload1.phtml');
    	exit;
    }
    
    public function upload_postAction() {
    	$ret = Common::upload('img', 'sdkimg');
    	$imgId = $this->getPost('imgId');
    	$this->assign('code' , $ret['data']);
    	$this->assign('msg' , $ret['msg']);
    	$this->assign('data', $ret['data']);
    	$this->assign('imgId', $imgId);
    	$this->getView()->display('common/upload1.phtml');
    	exit;
    }
}
