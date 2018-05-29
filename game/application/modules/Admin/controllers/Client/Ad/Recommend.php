<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_Ad_RecommendController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Recommend/index',
		'addUrl' => '/Admin/Client_Ad_Recommend/add',
		'addPostUrl' => '/Admin/Client_Ad_Recommend/add_post',
		'editUrl' => '/Admin/Client_Ad_Recommend/edit',
		'editPostUrl' => '/Admin/Client_Ad_Recommend/edit_post',
		'deleteUrl' => '/Admin/Client_Ad_Recommend/delete',
		'uploadUrl' => '/Admin/Client_Ad_Recommend/upload',
		'uploadPostUrl' => '/Admin/Client_Ad_Recommend/upload_post',
	    
	    'bannerUrl' => '/Admin/Client_Ad_Turn/index',
	    'imgUrl' => '/Admin/Client_Ad_Picture/index',
	    'recImgUrl' => '/Admin/Client_Ad_Recpic/index',
	    'recListUrl' => '/Admin/Client_Ad_Subject/index',
	    'imgLogUrl' => '/Admin/Client_Ad_Recommend/index',
	    'oldListUrl' => '/Admin/Client_Ad_Recommendold/index',
	    
	);
	
	public $perpage = 20;
	public $ad_type = 2;
	
	public $appCacheName = array("APPC_Client_Index_index","APPC_Channel_Index_index","APPC_Kingstone_Index_index");
	//广告链接ID
	public $ad_ptype = array(
			1 => '内容',
			2 => '分类',
			3 => '专题',
			4 => '外链'
	);
	public $versionName = 'Ad_Version';
	
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
			$params['title'] = array('LIKE', $title);
		}
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
		$params['ad_type'] = $this->ad_type;
	    list($total, $ads) = Client_Service_Ad::getCanUseAds($page, $perpage, $params);

		$this->assign('ad_type', $this->ad_type);
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
		$info = Client_Service_Ad::getAd(intval($id));
		$this->assign('ad_types', $this->ad_types);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ad_type', $this->ad_type);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title','head', 'ad_type', 'ad_ptype', 'link', 'img', 'icon', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		
		if($info['ad_ptype'] == 1){
			$adInfo = Resource_Service_Games::getResourceByGames($info['link']);
			if(!$adInfo['status']) $this->output(-1, '内容ID不存在或者未上线');
			$tip = "内容";
		} else if($info['ad_ptype'] == 2){
			$adInfo = Resource_Service_Attribute::getBy(array('id'=>$info['link'],'at_type'=>1));
			$tip = "分类";
		} else if($info['ad_ptype'] == 3){
			$adInfo = Client_Service_Subject::getSubject($info['link']);
			$tip = "专题";
		}
	    if($info['ad_ptype'] != 4){
		  $msg = $this->_getMsg($adInfo, $tip,$info['ad_ptype'],$info['link']);
		}
		$result = Client_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
		list(, $ads) = Client_Service_Ad::getAllAd();
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
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title','head', 'ad_type', 'ad_ptype',  'link', 'img', 'icon', 'start_time', 'end_time', 'status'));
		$info['ad_type'] = $this->ad_type;
		$info = $this->_cookData($info);
		$id = $this->getInput('id');
		$edit_info = Client_Service_Ad::getAd(intval($id));
		if($info['ad_ptype'] == 1){
			$adInfo = Resource_Service_Games::getResourceGames($info['link']);
			if(!$adInfo['status']) $this->output(-1, '内容ID不存在或者未上线');
			$temp = $this->_getTypeData(1);
			$tip = "内容";
		} else if($info['ad_ptype'] == 2){
			$adInfo = Resource_Service_Attribute::getBy(array('id'=>$info['link'],'at_type'=>1));
			$temp = $this->_getTypeData(2);
			$tip = "分类";
		} else if($info['ad_ptype'] == 3){
			$adInfo = Client_Service_Subject::getSubject($info['link']);
			$temp = $this->_getTypeData(3);
			$tip = "专题";
		}
	    if($info['ad_ptype'] != 4){
			if ((in_array($info['link'],$temp) && $edit_info['link'] != $info['link']) || (in_array($info['link'],$temp) && $edit_info['link'] == $info['link'] && $edit_info['ad_ptype'] != $info['ad_ptype']) ) {
				$this->output(-1, $tip.'链接ID已经存在不能重复添加');
			} else if(!in_array($info['link'],$temp) && !$adInfo){
				$this->output(-1, $tip.'链接ID不存在请选择正确的'.$tip.'ID添加');
			}
		}
		$ret = Client_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '广告标题不能为空.'); 
	    if(!$info['link']){
			if($info['ad_ptype'] != 4) {
				$this->output(-1, '广告链接ID不能为空.');
			} else {
				$this->output(-1, '广告外链接不能为空.');
			}
		} else {
			if($info['ad_ptype'] == 4) {
				if(strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
					$this->output(-1, '链接地址不规范.');
				}
			}
		}
		if(!$info['ad_ptype']) $this->output(-1, '链接类型不能为空.');
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		return $info;
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
