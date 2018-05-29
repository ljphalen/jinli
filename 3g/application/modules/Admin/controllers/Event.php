<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class EventController extends  Admin_BaseController {
	
	public  $actions = array(
				'typeUrl'					=>"/Admin/Event/type",
				'typeEditUrl'			=>'/Admin/Event/typeEdit',
				'typePostUrl'			=>'/Admin/Event/typePost',
				'typeDelUrl'			=>'/Admin/Event/typeDel',
				'goodsListUrl'			=>'/Admin/Event/goodsList',
				'goodsEditUrl'		=>'/Admin/Event/goodsEdit',
				'goodsPostUrl'		=>'/Admin/Event/goodsPost',
				'goodsDelUrl'			=>'/Admin/Event/goodsDel',
				'goodsDetailUrl'	=>'/Admin/Event/goodsDetail',
				'prizeListUrl'			=>'/Admin/Event/prizelist',
				'prizeEditUrl'			=>'/Admin/Event/prizeEdit',
				'prizePostUrl'			=>'/Admin/Event/prizePost',
				'prizeDelUrl'			=>'/Admin/Event/pirzeDel',
				'logsUrl'					=>'/Admin/Event/logs',
				'uploadUrl'			=>'/Admin/National/upload',
				'uploadPostUrl'	=>'/Admin/National/upload_post',
	);
	
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
            '99'=>'全部',
			'0'	=>'未领取',
			'1'	=>'已领取',
			'-1'	=>'已过期',
            '-2'	=>'已放弃',
	);
	
	public $types = array(
			'1' => '虚拟商品',
			'2' => '实物商品',
	);
	
	public function typeAction(){
		$page =$this->getInput('page');
		$page = max($page,1);
		$total = Event_Service_Activity::getEventTypeDao()->count(array());
		$dataList= Event_Service_Activity::getEventTypeDao()->getList($this->pageSize*($page-1),$this->pageSize,array(),array('id'=>"DESC"));
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['typeUrl']."&"));
		
	}
	
	public function typeEditAction(){
		$id = $this->getInput('id');
		$data = array();
		if(intval($id)){
			$data = Event_Service_Activity::getEventTypeDao()->get($id);
		}
		$this->assign('data', $data);
	}
	
	public function typePostAction(){
		$postData = $this->getInput(array('name','type_sign','start_time','end_time','valid_minutes','max_times', 'status','rule','id'));
		if(empty($postData['start_time'])||empty($postData['end_time'])){
			$this->output('-1','时间不能为空');
		}
		$postData['start_time'] = strtotime($postData['start_time']);
		$postData['end_time'] = strtotime($postData['end_time']);
		if(intval($postData['id'])){
			Event_Service_Activity::getEventTypeDao()->update($postData, $postData['id']);
            Event_Service_Activity::getActivityTypeInfoBySign($postData['type_sign'],true);
		}else{
			unset($postData['id']);
			$postData['add_time'] = time();
			$ret   = Event_Service_Activity::getEventTypeDao()->insert($postData);
            Event_Service_Activity::getActivityTypeInfoBySign($postData['type_sign'],true);
		}
		$this->output('0','操作成功');
	}
	
	public function typeDelAction(){
		$id  = $this->getInput('id');
		Event_Service_Activity::getEventTypeDao()->delete($id);
		$this->output('0','操作成功');
	}
	
	public function goodsListAction(){
		$postData = $this->getInput(array('page', 'status', 'cat_id'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (isset($postData['status']) && $postData['status'] >= 0) {
			$where['status'] = $postData['status'];
		}
		if (!empty($postData['cat_id'])) {
			$where['cat_id'] = $postData['cat_id'];
		}
		$where['event_flag'] = 1;
		list($total, $data) = User_Service_Commodities::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		foreach ($data as $k => $v) {
			$category             = User_Service_Category::get($v['cat_id']);
			$data[$k]['cat_name'] = $category['name'];
		}
		$catList = User_Service_Category::getsBy(array("status" => 1, "group_id" => 3), array("id" => 'DESC'));
		$this->assign('cateList', $catList);
		$this->assign('data', $data);
		$this->assign('statusList', array('0' => '关闭', '1' => '开启'));
		$this->assign('status', $postData['status']);
		$this->assign('cat_id', $postData['cat_id']);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "/?"));
		$this->assign('goodsType', $this->types);
	}
	
	public function goodsEditAction(){
		$id   = $this->getInput('id');
		$data = array();
		if (intval($id)) {
			$data = User_Service_Commodities::get($id);
			if ($data['goods_type'] == 1) {//为虚拟商品才查询相关信息
				$cardMsg         = User_Service_CardInfo::get($data['card_info_id']);
				$cardList        = User_Service_CardInfo::getsBy(array('type_id' => $cardMsg['type_id']));
				$subVirtualTypes = User_Service_CardInfo::getCetegory(array('group_type' => $data['virtual_type_id']), array("type_id"));
				$this->assign('subVirtualTypes', $subVirtualTypes);
				$this->assign('cardList', $cardList);
				$this->assign('cardMsg', $cardMsg);
			}
		}
		$catList = User_Service_Category::getsBy(array('status' => 1, 'group_id' => 3), array(
				'sort' => 'DESC',
				'id'   => 'DESC'
		));
		$this->assign('category', $catList);
		$virtualTypes = Common::getConfig('userConfig', 'virtual_type_list');
		$this->assign('virtualTypes', $virtualTypes);
		$this->assign('goodsType', $this->types);
		$this->assign('data', $data);
	}
	
	public function goodsPostAction(){
		$postData                = $this->getInput(array(
				'id',
				'cat_id',
				'name',
				'link',
				'price',
				'start_time',
				'end_time',
				'number',
				'is_special',
				'goods_type',
				'scores',
				'sort',
				'image',
				'status',
				'card_info_id',
				'virtual_type_id',
				'title',
				'num_ratio',
				'show_number',
		));
		$postData['event_flag'] = 1;
		$postData['description'] = htmlspecialchars($_POST['description']);
		if (intval($postData['card_info_id'])) {
			$cardInfo = User_Service_CardInfo::get($postData['card_info_id']);
		}
		
		if (intval($postData['id'])) {
			$postData['edit_time'] = time();
			$postData['edit_user'] = $this->userInfo['username'];
			$res                   = User_Service_Commodities::update($postData, $postData['id']);
		} else {
			$res = User_Service_Commodities::add($postData);
		}
		if ($res) {
			Admin_Service_Log::op($postData);
			User_Service_Commodities::getGoodsList(true);
			$this->output('0', '编辑成功！');
		} else {
			$this->output('-1', '编辑失败！');
		}
	}
	
	public  function goodsDetailAction(){
		$postData  = $this->getInput(array("id",'page','activity_id','prize_status'));
		$page = max($postData['page'],1);
		$offpage = ($page-1)*$this->pageSize;
		$where = array();
		$where['prize_id'] = $postData['id'];
		if($postData['activity_id']){
			$where['activity_id'] = $postData['activity_id'];
		}
		if(in_array($postData['prize_status'],array('-1','0','1'))){
			$where['prize_status'] = $postData['prize_status'];
		}
		$total = Event_Service_Activity::getResultDao()->count($where);
		$data = Event_Service_Activity::getResultDao()->getList($offpage,$this->pageSize,$where,array("id"=>"DESC"));
		foreach ($data as $k=>$v){
			$typeData = Event_Service_Activity::getEventTypeDao()->get($v['activity_id']);
			$data['type_name'] = $typeData['name'];
			$user = Gionee_Service_User::getUser($v['uid']);
			$data[$k]['username'] = $user['username'];
		}
		
		$activityList = Event_Service_Activity::getEventTypeDao()->getsBy(array(),array("id"=>'DESC'));
		$this->assign('activityList', $activityList);
		$statusList = array('0'=>"待处理",'1'=>'已成功','-1'=>'已取消');
		$this->assign('statusList', $statusList);
		$goods =User_Service_Commodities::get($postData['id']);
		$this->assign('goods', $goods);
		$this->assign('data', $data);
		$parmsHtml = http_build_query($postData);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['goodsDetail']."?$parmsHtml&"));
		$this->assign('params', $postData);
	}
	
	public function prizeListAction(){
		$typeId = $this->getInput('type_id');
		$where =array();
        $data=array();
		if(intval($typeId)){
			$where['activity_type'] = $typeId;
            $data = Event_Service_Activity::getGoodsDao()->getsBy($where,array("sort"=>"ASC"));
		}else{
            $data = Event_Service_Activity::getGoodsDao()->getsBy($where,array("id"=>"DESC"));
        }
		

		foreach ($data as $k=>$v){
			$type = Event_Service_Activity::getEventTypeDao()->get($v['activity_type']);
			$data[$k]['type_name'] = $type['name'];
		}
		$activites = Event_Service_Activity::getEventTypeDao()->getsBy(array(),array("id"=>'DESC'));
		$this->assign('dataList', $data);
		$this->assign('activites', $activites);
		$this->assign('typeid', $typeId);
		$this->assign('prizeTypes', $this->prizeTypes);
		$this->assign('prizeLevels', $this->prizeLevels);
	}
	
	public function prizeEditAction(){
		$id = $this->getInput('id');
		$data = array();
		if($id){
			$data = Event_Service_Activity::getGoodsDao()->get($id);
		}
		$typeList = Event_Service_Activity::getEventTypeDao()->getsBy(array(),array("id"=>'DESC'));
		$this->assign('data', $data);
		$this->assign('prizeLevels',	 $this->prizeLevels);
		$this->assign('prizeTypes', $this->prizeTypes);
		$this->assign('typeList', $typeList);
		$this->assign('goodsList', $this->_activityGoodsList());
		
	}
	
	public function prizePostAction(){
		$postData = $this->getInput(array('activity_type', "name",'number','show_number','prize_level','prize_type','prize_val','status','image','ratio','sort','start_time','end_time'));


		$id = $this->getInput('id');
		if(!empty($postData['start_time']) && !empty($postData['end_time'])){
			$postData['start_time'] = strtotime($postData['start_time']);
			$postData['end_time'] = strtotime($postData['end_time']);
		}else{
			unset($postData['start_time']);
			unset($postData['end_time']);
		}
		if($id){
          //  $postData['show_number'] = $postData['number'];
			$ret = Event_Service_Activity::getGoodsDao()->update($postData,$id);
			Event_Service_Activity::getPrizeGoodsInfo($id,true);
            Event_Service_Activity::getDrawingPrizeGoodsInfo($id,true);
            Event_Service_Activity::getPrizeList($postData['activity_type'],true);
		}else{
			$postData['add_time'] = time();
         //   $postData['show_number'] = $postData['number'];
			$ret = Event_Service_Activity::getGoodsDao()->insert($postData);
            Event_Service_Activity::getPrizeList($postData['activity_type'],true);
		}
		if($ret ) {
			Event_Service_Activity::getPrizeGoodsList(true);
            Event_Service_Activity::getPrizeList($postData['activity_type'],true);

			$this->output('0','操作成功!');
		}
		$this->output('-1','操作失败!');
	}
	
	public function prizeDelAction(){
		$id = $this->getInput("id");
		Event_Service_Activity::getGoodsDao()->delete($id);
		$this->output('-1','操作失败!');
	}
	
	public function logsAction(){
		$postData = $this->getInput(array('page','status','prize_id','username','export','sdate','edate','type_id'));
		$page = max(1,$postData['page']);
		$where = array();
		if(in_array($postData['status'],array_keys($this->statusList))){
            if($postData['status']!='99'){ $where['prize_status'] = $postData['status'];}
		}
		if(intval($postData['prize_id'])){
			$where['prize_id'] = $postData['prize_id'];
		}
		
		if(!empty($postData['username'])){
			$user = Gionee_Service_User::getUserByName($postData['username']);
			$where['uid'] = $user['id'];
		}
		
		if(!empty($postData['type_id'])){
			$where['activity_id']  = $postData['type_id'];
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
			$this->_export($data,'活动中奖日志',$header);
			exit();
		}
		$typeList = Event_Service_Activity::getEventTypeDao()->getsBy(array(),array("id"=>'DESC'));
		$this->assign('typeList', $typeList);
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