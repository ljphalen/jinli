<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_Ad_StartController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Start/index',
		'addUrl' => '/Admin/Client_Ad_Start/add',
		'addPostUrl' => '/Admin/Client_Ad_Start/add_post',
		'editUrl' => '/Admin/Client_Ad_Start/edit',
		'editPostUrl' => '/Admin/Client_Ad_Start/edit_post',
		'deleteUrl' => '/Admin/Client_Ad_Start/delete',
		'uploadUrl' => '/Admin/Client_Ad_Start/upload',
		'uploadPostUrl' => '/Admin/Client_Ad_Start/upload_post',
	);
	
	public $perpage = 20;
	public $ad_type = Client_Service_Ad::AD_TYPE_START;
	public $versionName = 'Start_Ad_Version';
	    
	//广告链接类型
	public $ad_ptypes = array(
			Client_Service_Ad::ADLINK_TYPE_GAMEID => '内容',
			Client_Service_Ad::ADLINK_TYPE_CATEGOTY => '分类',
			Client_Service_Ad::ADLINK_TYPE_SUBJECT => '专题',
			Client_Service_Ad::ADLINK_TYPE_ACTIVITY => '活动',
			Client_Service_Ad::ADLINK_TYPE_LINK => '网址',
			Client_Service_Ad::ADLINK_TYPE_GIFT => '礼包',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$status = intval($this->getInput('status'));
		if ($page < 1) $page = 1;
		
		$params = array();
		$search = array();
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
		$params['ad_type'] = $this->ad_type;
	    list($total, $ads) = Client_Service_Ad::getCanUseAds($page, $perpage, $params,array('start_time'=>'DESC','id'=>'DESC'));
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$this->assign('ad_ptypes', $this->ad_ptypes);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Ad::getAd(intval($id));
		$this->assign('ad_type', $this->ad_type);
		$this->assign('ad_ptypes', $this->ad_ptypes);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ad_type', $this->ad_type);
		$this->assign('ad_ptypes', $this->ad_ptypes);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title','head', 'ad_type', 'ad_ptype', 'link', 'img', 'icon', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		
		$this->checkInput($info);
		
		//重叠区间判断
		$items  = Client_Service_Ad::getsBy(array('ad_type' => $this->ad_type));
		$check = $this->_checkRegion($info, $items);
		if (!$check) $this->output(-1, '添加的时间区间，不能出现重叠。');
		
		$result = Client_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}	
	
	public function checkInput($info) {
		if($info['ad_ptype'] == 1){
			$adInfo = Resource_Service_Games::getResourceByGames($info['link']);
			$tip = "内容";
		} else if($info['ad_ptype'] == 2){
			$adInfo = Resource_Service_Attribute::getBy(array('id'=>$info['link'],'at_type'=>1));
			$tip = "分类";
		} else if($info['ad_ptype'] == 3){
			$adInfo = Client_Service_Subject::getSubject($info['link']);
			$tip = "专题";
		} else if($info['ad_ptype'] == 5){
			$adInfo = Client_Service_Hd::getHd($info['link']);
			$tip = "活动";
		} else if($info['ad_ptype'] == 6){
			$adInfo = Client_Service_Gift::getGift($info['link']);
			$tip = "礼包";
		}
		
		if($info['ad_ptype'] != 4){
			$this->getMsg($adInfo, $tip, $info['ad_ptype'], $info['link']);
		}
	}
	
	public function getMsg($adInfo, $tip, $ad_ptype, $link) {
		if(!$adInfo || $adInfo == false)  {
			$this->output(-1, $tip.'链接ID不存在请选择正确的'.$tip.'ID添加');
		} else {
			$listData = $this->getListData($ad_ptype);
			if (in_array($link, $listData)) $this->output(-1, $tip.'链接ID已经存在不能重复添加');
		}
	}
	
	public function getListData($ad_ptype) {
		list(, $ads) = Client_Service_Ad::getsBy(array('ad_type'=>$this->ad_type));
		$listData = array();
		foreach($ads as $key=>$value){
			if(($value['ad_type'] == $this->ad_type) && ($value['ad_ptype'] == $ad_ptype)){
				$listData[] = $value['link'];
			}
		}
		return $listData;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'head','ad_type', 'ad_ptype',  'link', 'img', 'icon', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		$this->checkInput($info);

		//重叠区间判断
		$items  = Client_Service_Ad::getsBy(array('ad_type' => $this->ad_type, 'id'=>array('!=', $info['id'])));
		$check = $this->_checkRegion($info, $items);
		if (!$check) $this->output(-1, '添加的时间区间，不能出现重叠。');
				
		$ret = Client_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['link']){
			if($info['ad_ptype'] == 1 || $info['ad_ptype'] == 2 || $info['ad_ptype'] == 3 || $info['ad_ptype'] == 5
					 || $info['ad_ptype'] == 6) {
				$this->output(-1, 'ID不能为空.');
			} else if($info['ad_ptype'] == 4){
				$this->output(-1, '链接地址不能为空.');
			}
		} else {
			if($info['ad_ptype'] == 4) {
				if(strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
					$this->output(-1, '链接地址不规范.');
				}
			}
		}
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.'); 
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		return $info;
	}

	private function _checkRegion($info, $items){
		$flag = true;
		if(!$items) return $flag;
		foreach ($items as $value){
			if((intval($info['start_time']) <= intval($value['end_time'])) && (intval($value['start_time']) <= intval($info['end_time']))){
				$flag = false;
				break;
			}
		}
		return $flag;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Client_Service_Ad::deleteAd($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
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
		$ret = Common::upload('img', 'ad');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
