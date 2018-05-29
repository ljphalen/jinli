<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Client_Ad_ActivityController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Activity/index',
		'addUrl' => '/Admin/Client_Ad_Activity/add',
		'addPostUrl' => '/Admin/Client_Ad_Activity/add_post',
		'editUrl' => '/Admin/Client_Ad_Activity/edit',
		'editPostUrl' => '/Admin/Client_Ad_Activity/edit_post',
		'deleteUrl' => '/Admin/Client_Ad_Activity/delete',
		'uploadUrl' => '/Admin/Client_Ad_Activity/upload',
		'uploadPostUrl' => '/Admin/Client_Ad_Activity/upload_post',
		'batchUpdateUrl'=>'/Admin/Client_Ad_Activity/batchUpdate',
		'setUrl' 	=>  '/Admin/Client_Ad_Activity/setting'
	);
	
	public $perpage = 20;
	public $ad_type = 8;
	public $ad_ptype = array(
			  1 => '内容',
			  2 => '分类',
			  3 => '专题',
			  4 => '外链',
			  5 => '活动',
			);
	
	public $appCacheName = array("APPC_Client_Index_index","APPC_Channel_Index_index","APPC_Kingstone_Index_index");
	public $versionName = 'Ad_Version';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$status = intval($this->getInput('status'));
		$ad_ptype = intval($this->getInput('ad_ptype'));
		$title = $this->getInput('title');
		if ($page < 1) $page = 1;
		
		$params = array();
		$search = array();
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
		if($title) {
			$search['title'] = $title;
			$params['title'] = array('LIKE',$title);
		}
		if($ad_ptype){
			$search['ad_ptype'] = $ad_ptype;
			$params['ad_ptype'] = $ad_ptype;
		}
		$params['ad_type'] = $this->ad_type;
		$ad_types = $this->ad_ptype;
		
	    list($total, $ads) = Client_Service_Ad::getCanUseAds($page, $perpage, $params);
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$this->assign('ad_types', $ad_types);
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
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('ad_type', $this->ad_type);
	}
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='sort'){
			$ret = Client_Service_Ad::sortAd($info['sort']);
		} 
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'head', 'ad_type', 'ad_ptype', 'link', 'start_time', 'end_time', 'status'));
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
		} else if($info['ad_ptype'] == 5){
			$adInfo = Client_Service_Hd::getHd($info['link']);
			$tip = "活动";
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
		$info = $this->getPost(array('id', 'sort', 'title', 'ad_type', 'ad_ptype',  'link', 'start_time', 'end_time', 'status'));
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
		} else if($info['ad_ptype'] == 5){
			$adInfo = Client_Service_Hd::getHd($info['link']);
			$tip = "活动";
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
		if(!$info['link']) $this->output(-1, '链接地址不能为空.');
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
    
    public function settingAction() {
    	$settingKey 	= 'WELFARE_TASK_CONFIG';
   		if($_SERVER['REQUEST_METHOD'] == 'POST') {
   			$input = $this->getInput('switch');
   			$res = Game_Service_Config::setValue($settingKey, $input);
   			if($res) {
   				$this->output(0,'设置成功');
   			}   			
   			$this->output(-1,'设置失败');
   		}   
   		$config = Game_Service_Config::getValue($settingKey);   		
   		$this->assign('config', $config);   
    }
}
