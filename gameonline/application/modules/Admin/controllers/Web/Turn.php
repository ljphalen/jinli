<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Web_TurnController extends Admin_BaseController {
	public $actions = array(
		'listUrl' => '/Admin/Web_Turn/index',
		'addUrl' => '/Admin/Web_Turn/add',
		'addPostUrl' => '/Admin/Web_Turn/add_post',
		'editUrl' => '/Admin/Web_Turn/edit',
		'editPostUrl' => '/Admin/Web_Turn/edit_post',
		'deleteUrl' => '/Admin/Web_Turn/delete',
		'uploadUrl' => '/Admin/Web_Turn/upload',
		'uploadPostUrl' => '/Admin/Web_Turn/upload_post',
	);

	public $perpage = 20;
	public $ad_type = 1; 
	//广告链接ID
	public $ad_ptype = array(
			1 => '内容',
			2 => '分类',
			3 => '专题',
			4 => '外链',
			5 => '活动',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$title = $this->getInput('title');
		$status = intval($this->getInput('status'));
		if ($page < 1) $page = 1;
		
		$params = array();
		$search = array();
		
		if($title) {
			$search['title'] = $title;
			$params['title'] = $title;
		}
		
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
		
		$params['ad_type'] = $this->ad_type;
	    list($total, $ads) = Web_Service_Ad::getCanUseAds($page, $perpage, $params);
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$this->assign('ad_ptype', $this->ad_ptype);
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
		$info = Web_Service_Ad::getAd(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', 'ad_ptype', 'link', 'img', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		if($info['ad_ptype'] == 1){
			$adInfo = Resource_Service_Games::getResourceByGames($info['link']);
			if(!$adInfo['status']) $this->output(-1, '内容ID不存在或者未上线');
			$tip = "内容";
		} else if($info['ad_ptype'] == 2){
			$adInfo = Resource_Service_Attribute::getResourceAttributeByTypeId($info['link'],1);
			$tip = "分类";
		} else if($info['ad_ptype'] == 3){
			$adInfo = Client_Service_Subject::getSubject($info['link']);
			$tip = "专题";
		} else if($info['ad_ptype'] == 5){
			$adInfo = Client_Service_News::getNews($info['link']);
			$tip = "活动";
		}
		if($info['ad_ptype'] != 4){
			$msg = $this->_getMsg($adInfo, $tip,$info['ad_ptype'],$info['link']);
		}
		$result = Web_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title',  'ad_ptype',  'link', 'img', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
 		$edit_info = Web_Service_Ad::getAd(intval($info['id']));
 		if($info['ad_ptype'] == 1){
 			$adInfo = Resource_Service_Games::getResourceGames($info['link']);
 			if(!$adInfo['status']) $this->output(-1, '内容ID不存在或者未上线');
 			$temp = $this->_getTypeData(1);
 			$tip = "内容";
 		} else if($info['ad_ptype'] == 2){
 			$adInfo = Resource_Service_Attribute::getResourceAttributeByTypeId($info['link'],1);
 			$temp = $this->_getTypeData(2);
 			$tip = "分类";
 		} else if($info['ad_ptype'] == 3){
 			$adInfo = Client_Service_Subject::getSubject($info['link']);
 			$temp = $this->_getTypeData(3);
 			$tip = "专题";
 		} else if($info['ad_ptype'] == 5){
 			$adInfo = Client_Service_News::getNews($info['link']);
 			$tip = "活动";
 		}
 		if($info['ad_ptype'] != 4){
 			if ((in_array($info['link'],$temp) && $edit_info['link'] != $info['link']) || (in_array($info['link'],$temp) && $edit_info['link'] == $info['link'] && $edit_info['ad_ptype'] != $info['ad_ptype']) ) {
 				$this->output(-1, $tip.'链接ID已经存在不能重复添加');
 			} else if(!in_array($info['link'],$temp) && !$adInfo){
 				$this->output(-1, $tip.'链接ID不存在请选择正确的1'.$tip.'ID添加');
 			}
 		}
		$ret = Web_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '广告标题不能为空.'); 
		if(!$info['link']) $this->output(-1, '广告链接不能为空.');
		if(!$info['ad_ptype']) $this->output(-1, '链接类型不能为空.');
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if($info['ad_ptype'] == 4) {
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}  
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Web_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Web_Service_Ad::deleteAd($id);
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
		$ret = Common::upload('img', 'webimg');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
    public function _getMsg($adInfo,$tip,$ad_ptype,$link) {
    	if(!$adInfo)  {
    		$this->output(-1, $tip.'链接ID不存在请选择正确的'.$tip.'ID添加');
    	} else {
    		$tmp = $this->_getData($ad_ptype);
    		if (in_array($link,$tmp)) $this->output(-1, $tip.'链接ID已经存在不能重复添加');
    	}
    }
    
    public function _getData($ad_ptype) {
    	list(, $ads) = Web_Service_Ad::getAllAd();
    	$tmp = array();
    	foreach($ads as $key=>$value){
    		if(($value['ad_type'] == $this->ad_type) && ($value['ad_ptype'] == $ad_ptype)){
    			$tmp[] = $value['link'];
    		}
    	}
    	return $tmp;
    }
    public function _getTypeData($type) {
    	list(, $ads) = Client_Service_Ad::getAllAd();
    	$tmp = array();
    	foreach($ads as $key=>$value){
    		if(($value['ad_type'] == $this->ad_type) && ($value['ad_ptype'] == $type)){
    			$tmp[] = $value['link'];
    		}
    	}
    	return $tmp;
    }
}