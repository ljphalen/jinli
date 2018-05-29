<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class NationalController extends Admin_BaseController {
	
	public $prizeLevels = array(
		'1'		=>'一等奖',
		'2'		=>'二等奖',
		'3'		=>'三等奖',
		'4'		=>'四等奖',
		'5'		=>'五等奖',
		'6'		=>'六等奖',
		'7'		=>'七等奖',
		'8'		=>'八等奖',
		'9'		=>'九等奖',
		'10'		=>'十等奖',
	);
	
	public $prizeTypes = array(
		'1'	=>'实物奖品',
		'2'	=>'金币'
	);
	
	public $statusList = array(
		'0'	=>'未领取',
		'1'	=>'已领取',
		'-1'	=>'已过期',
	);
	
	public $actions = array(
		'configUrl'			=>'/Admin/National/log',
		'indexUrl'			=>'/Admin/National/index',
		'editUrl'				=>'/Admin/National/edit',
		'eidtPostUrl'		=>'/Admin/National/editpost',
		'deleteUrl'			=>'/Admin/National/delete',
		'logUrl'				=>'/Admin/National/log',
		'uploadUrl'			=>'/Admin/National/upload',
		'uploadPostUrl'	=>'/Admin/National/upload_post',
	);

	public  function  configAction(){
		$keys = array('national_day_start_time',
				'national_day_end_time',
				'national_day_status',
				'national_day_rule',
				'national_day_times',
				'national_day_expires'
		);
		$postData = $this->getInput($keys);
		if($postData['national_day_times']){
			foreach ($postData as $k => $v) {
				Gionee_Service_Config::setValue($k, $v);
			}
			Event_Service_Activity::getConfigData(true);
			$this->output('0', '编辑成功！');
		}
		$params['3g_key'] = array('IN',$keys);
		$ret = Gionee_Service_Config::getsBy($params);
		foreach ($ret as $k=>$v){
			$data[$v['3g_key']] = $v['3g_value'];
		}
		$this->assign('data', $data);
	}
	
	public function indexAction(){
		$data = Event_Service_Activity::getGoodsDao()->getsBy(array(),array("sort"=>"DESC"));
		$this->assign('dataList', $data);
		$this->assign('prizeTypes', $this->prizeTypes);
		$this->assign('prizeLevels', $this->prizeLevels);
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$data = array();
		if($id){
			$data = Event_Service_Activity::getGoodsDao()->get($id);
		}
		$this->assign('data', $data);
		$this->assign('prizeLevels',	 $this->prizeLevels);
		$this->assign('prizeTypes', $this->prizeTypes);
		$this->assign('goodsList', $this->_activityGoodsList());
	}
	
	public function editPostAction(){
		$postData = $this->getInput(array("name",'number','prize_level','prize_type','prize_val','status','image','ratio','sort'));
		$id = $this->getInput('id');
		if($id){
			$ret = Event_Service_Activity::getGoodsDao()->update($postData,$id);
			Event_Service_Activity::getPrizeGoodsInfo($id,true);
		}else{
			$postData['add_time'] = time();
			$ret = Event_Service_Activity::getGoodsDao()->insert($postData);
		}
		if($ret ) {
			Event_Service_Activity::getPrizeGoodsList(true);
			$this->output('0','操作成功!');
		}
		$this->output('-1','操作失败!');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$ret = Event_Service_Activity::getGoodsDao()->delete($id);
		if($ret ) {
			$this->output('0','操作成功!');
		}
		$this->output('-1','操作失败!');
	}
	
	public function logAction(){
		$postData = $this->getInput(array('page','status','prize_id','username','export','sdate','edate'));
		$page = max(1,$postData['page']);
		$where = array();
		if(in_array($postData['status'],array_keys($this->statusList))){
			$where['prize_status'] = $postData['status'];
		}
		if(intval($postData['prize_id'])){
			$where['prize_id'] = $postData['prize_id'];
		}
		
		if(!empty($postData['username'])){
			$user = Gionee_Service_User::getUserByName($postData['username']);
			$where['uid'] = $user['id'];
		}
		
		!$postData['sdate']&&$postData['sdate'] = date("Y-m-d",strtotime("-7 day"));
		!$postData['edate']&&$postData['edate'] = date("Y-m-d",strtotime('+1 day'));
		$where['add_time'] = array(array(">=",strtotime($postData['sdate'])),array("<=",strtotime($postData['edate'])));
		$pageSize = $this->pageSize;
		if($postData['export']){
			$pageSize = 10000;
		}
		$total = Event_Service_Activity::getResultDao()->count($where);
		$data = Event_Service_Activity::getResultDao()->getList($pageSize*($page-1),$pageSize,$where,array("id"=>"DESC"));
		foreach ($data as $k=>$v){
			$user = Gionee_Service_User::getUser($v['uid']);
			$data[$k]['username']  = $user['username'];
			$prizeInfo = Event_Service_Activity::getGoodsDao()->get($v['prize_id']);
			$data[$k]['prize_name'] = $prizeInfo['name'];
			$data[$k]['prize_type']		=$prizeInfo['prize_type'];
			$data[$k]['prize_level'] 	= $prizeInfo['prize_level'];
			$data[$k]['prize_val']		 = $prizeInfo['prize_val'];
		}
		
		$prizeGoodsList = Event_Service_Activity::getGoodsDao()->getAll(array('prize_level'=>'ASC'));
		$header = array('ID','用户ID','奖品ID','奖品名','奖品等级','奖品状态','金币数/实物ID','中奖时间','领奖时间','过期时间','用户IP');
		if($postData['export']){
			$this->_export($data,'十一特别活动中奖日志',$header);
			exit();
		}
		
		$this->assign('params', $postData);
		$this->assign('data', $data);
		$httpParams = http_build_query($postData);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['logUrl']."?{$httpParams}&"));
		$this->assign('prizeLevels', $this->prizeLevels);
		$this->assign('prizeStatus', $this->statusList);
		$this->assign('prizeTypes', $this->prizeTypes);
		$this->assign('params', $postData);
		$this->assign('prizeGoodsList', $prizeGoodsList);
	}
	
	public function ajaxGetEventGoodsListAction(){
		$goodsList = $this->_activityGoodsList();
		$this->output('0','',$goodsList);
	}
	
	private function _activityGoodsList (){
		$where = array();
		$where['event_flag'] = 1;
		$where['start_time'] = array('<=',time());
		$where['end_time'] = array('>=',time());
		$goodsList = User_Service_Commodities::getsBy($where,array('id'=>"DESC"));	
		return $goodsList;
	}
	
	private  function _export($data,$title,$header=array()){
		$ret = array();
		foreach ($data as $k=>$v){
			$ret[$k]['id'] 					= $v['id'];
			$ret[$k]['uid'] 				= $v['uid'];
			$ret[$k]['prize_id'] 		= $v['prize_id'];
			$ret[$k]['prize_name'] =$v['prize_name'];
			$ret[$k]['prize_level'] 	= $this->prizeLevels[$v['prize_level']];
			$ret[$k]['prize_status'] = $this->statusList[$v['prize_status']];
			$ret[$k]['prize_val'] 		= $v['prize_val'];
			$ret[$k]['add_time']	 	= date('Y-m-d H:i:s',$v['add_time']);
			$ret[$k]['get_time'] 		=  $v['get_time']?date('Y-m-d H:i:s',$v['get_time']):'----';
			$ret[$k]['expire_time'] = $v['expire_time']?date('Y-m-d H:i:s',$v['expire_time']):'----';
			$ret[$k]['user_ip']			 = $v['user_ip'];
		}
		Common::export($ret, '', '', $title,$header);
	}
	
	//上传图片
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret   = Common::upload('img', 'national');
		$imgId = $this->getPost('imgId');
		$this->assign('imgId', $imgId);
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}