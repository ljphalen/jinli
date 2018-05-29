<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class AdController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Ad/index',
		'addUrl' => '/Admin/Ad/add',
		'addPostUrl' => '/Admin/Ad/add_post',
		'editUrl' => '/Admin/Ad/edit',
		'editPostUrl' => '/Admin/Ad/edit_post',
		'deleteUrl' => '/Admin/Ad/delete',
		'uploadUrl' => '/Admin/Ad/upload',
		'uploadPostUrl' => '/Admin/Ad/upload_post',
	);
	
	public $appCacheName = array('APPC_Front_Index_index', 'APPC_Channel_Index_index','APPC_Apk_Index_index','APPC_Market_Index_index','APPC_App_Index_index');
	public $versionName = 'Ad_Version';
	public $perpage = 20;
	public $ad_types = array(
		1=>'首页轮播广告'
	);

	/**
	 * 广告列表
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('start_time', 'ad_type'));

		$search = array();
		if($param['ad_type'])
			$search['ad_type'] = $param['ad_type'];
        if(!empty($param['start_time']))
			$search['start_time'] = array(
            	array('>=', strtotime($param['start_time']))
        	);

		list($total, $ads) = Fj_Service_Ad::getList($page, $this->perpage, $search);
		$this->assign('ad_types', $this->ad_types);
		$this->assign('search', $param);
		$this->assign('ads', $ads);
		$this->cookieParams();
		$url = $this->actions['listUrl']. '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	/**
	 * 添加广告
	 */
	public function addAction() {
		$this->assign('ad_types', $this->ad_types);
	}

	/**
	 * 处理添加
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', 'link', 'img', 'start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$result = Fj_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 编辑广告
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Ad::getAd(intval($id));
		$this->assign('ad_types', $this->ad_types);
		
		$this->assign('info', $info);
	}


	/**
	 * 处理编辑
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'id', 'ad_type', 'title', 'link', 'img', 'start_time', 'end_time', 'status'));
		$info = $this->_cookData($info);
		$ret = Fj_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	/**
	 * 删除广告
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
//		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Fj_Service_Ad::deleteAd($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '广告标题不能为空.');
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if(!$info['link']) $this->output(-1, '广告链接不能为空.');
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		$info['link'] = html_entity_decode($info['link']);
		return $info;
	}

	/**
	 * 上传页面
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 处理上传
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'ad');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		//create webp image
//		if ($ret['code'] == 0) {
//			$attachPath = Common::getConfig('siteConfig', 'attachPath');
//			$file = realpath($attachPath.$ret['data']);
//			image2webp($file, $file.'.webp');
//		}
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
