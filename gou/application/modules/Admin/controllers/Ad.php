<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 * channel 	H5:1,
 * apk(预装版):2,
 * channel(渠道版)：3,
 * market(穷购物):4
 * app(app版):5
 * ios(ios版):6
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
		'uploadImgUrl' => '/Admin/Ad/uploadImg',
		'copyUrl' => '/Admin/Ad/copy',
	    'batchUpdateUrl'=>'/Admin/Ad/batchUpdate',
	);
	
	public $appCacheName = array('APPC_Front_Index_index', 'APPC_Channel_Index_index','APPC_Apk_Index_index','APPC_Market_Index_index','APPC_App_Index_index');
	public $versionName = 'Ad_Version';
	public $perpage = 10;
    public $ad_types = array(
        1 => array(1 => '首页广告', 2 => '顶部横幅广告', 3 => '中部横幅广告'),
		2 => array(1 => '首页轮播广告', 2 => '淘热卖广告(闪屏)', 3 => '特色专区1（网页最左边）', 4 => '特色专区2（网页最右边）',
			5 => '便捷服务（网页版便捷服务）', 7 => '新版便捷服务（本地化便捷服务）', 6 => '分类搜索广告', 8 => '新版特色专区（本地化三个口版本）',
			9 => '特色专区V2.3（本地化四个口版本）', 10 => '特色专区V2.5.1（金立预装本地化四个口版本）'),    //预装版
        3 => array(1 => '首页轮播广告', 2 => '特色专区1', 3 => '特色专区2'),                    //渠道版
        4 => array(1 => '首页轮播广告', 2 => '特色专区1', 3 => '特色专区2'),                    //穷购物
        5 => array(1 => '首页广告', 3 => '中部横幅广告'), //app版本
        6 => array(1 => '首页轮播广告', 2 => '特色专区', 3 => '便捷服务', 4 => '分类搜索广告', 5 => 'V1.3特色专区', 6 => 'V1.3便捷服务'),    //ios版
    );
	
	//客户端actions配置
	public $client_actions = array(
        'com.gionee.client.ThirdParty'=>'正常（第三方web页），砍价详情',
        'com.gionee.client.SpecialPrice'=>'天天特价',
        'com.gionee.client.StoryList'=>'知物列表',
    	 //'com.gionee.client.StoryDetail'=>'知物详情',
    	 'com.gionee.client.CutList'=>'砍价列表',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('start_time','channel_id', 'ad_type', 'title'));

		$perpage = $this->perpage;
		
		$search = array();
		if($param['ad_type']) $search['ad_type'] = $param['ad_type'];
		$search['channel_id'] = $param['channel_id'];
        if(!empty($param['start_time']))$search['search_time'] = array(
            array('>=',strtotime($param['start_time'])),
            array('<',strtotime($param['start_time'])+3600*24),
        );
        if($param['title']) $search['title'] = array('LIKE', trim($param['title']));
		list($total, $ads) = Gou_Service_Ad::getList($page, $perpage, $search);
		$this->assign('ad_types', $this->ad_types[$param['channel_id']]);
		$this->assign('search', $param);
		$this->assign('ads', $ads);
		$this->cookieParams();
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Ad::getAd(intval($id));
		$this->assign('ad_types', $this->ad_types[$info['channel_id']]);
		
		list($info['module_id'], $info['cid']) = explode('_', $info['module_channel']);
		$this->assign('info', $info);
		
		if($info['channel_id'] == 2 || $info['channel_id'] == 6) {
		    list(, $ptypes) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
		    $this->assign('ptype', $ptypes);
		}
		
	    if($info['channel_id'] == 2) {
		    $this->assign('actions', $this->client_actions);
		}

        $this->assign('channel_id', $info['channel_id']);
        $this->assign('ad_type', $info['ad_type']);

		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$channel_id = intval($this->getInput('channel_id'));
		$this->assign('ad_types', $this->ad_types[$channel_id]);
		$this->assign('channel_id', $channel_id);
		$ad_type = $this->getInput('ad_type');
		$this->assign('ad_type', $ad_type);
		
		if($channel_id == 2 || $channel_id == 6) {
		    list(, $ptypes) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
		    $this->assign('ptype', $ptypes);
		}
		
		if($channel_id == 2) {
		    $this->assign('actions', $this->client_actions);
		}
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', 'ad_ptype', 
				'link', 'activity', 'clientver', 'img', 'start_time', 'end_time', 'status', 'hits',
				'channel_id', 'module_id', 'cid', 'channel_code', 'ptype_id', 'is_recommend', 'action'));
		$info = $this->_cookData($info);
		$result = Gou_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'id', 'ad_type', 'ad_ptype', 
				'title', 'link', 'activity', 'clientver', 'img', 'start_time', 'end_time', 'status',
				'hits', 'channel_id', 'module_id', 'cid', 'channel_code', 'ptype_id', 'is_recommend', 'action'));
		$info = $this->_cookData($info);
		$ret = Gou_Service_Ad::updateAd($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function copyAction() {
		$id = $this->getInput('id');
		$channel_id = intval($this->getInput('channel_id'));
		$info = Gou_Service_Ad::getAd(intval($id));
		$this->assign('ad_types', $this->ad_types[$channel_id]);
		$this->assign('channel_id', $channel_id);
		$this->assign('info', $info);
        $this->assign('ad_type', $info['ad_type']);
		
		//channel_code
		if($info['module_channel']) {
			$module_channel = explode('_', $info['module_channel']);
			list(,$channel_code) = Gou_Service_ChannelCode::getsBy(array('module_id'=>$module_channel[0], 'cid'=>$module_channel[1], 'channel_id'=>$channel_id), array('id'=>'DESC'));
			$this->assign('channel_code', $channel_code);
			
			list(, $channel_names) = Gou_Service_ChannelName::getAll();
			$this->assign('channel_names', Common::resetKey($channel_names, 'id'));
			$this->assign('module_channel', $module_channel);
		}
		
		if($channel_id == 2 || $channel_id == 6) {
		    list(, $ptypes) = Type_Service_Ptype::getsBy(array('status'=>1,'pid'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
		    $this->assign('ptype', $ptypes);
		}
		if($channel_id == 2) {
		    $this->assign('actions', $this->client_actions);
		}
		
		//module channel
		list($modules, $channels) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channels', $channels);
		
		$this->assign('info', $info);
	}
	
	/*
	 * 批量操作
	 * 
	 */
	function batchUpdateAction() {
	    $info = $this->getPost(array('action', 'ids', 'sort'));
	    if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
	    
	    //排序
	    if($info['action'] =='sort'){
	        $data = array();
	        foreach ($info['ids'] as $value) {
	            $data[$value] =  $info['sort'][$value];
	        }
	        $ret = Gou_Service_Ad::sort($data);
	    }
	    //开启
	    if ($info['action'] == 'open') {
	        $ret = Gou_Service_Ad::updates($info['ids'], array('status'=>1));
	    }
	    //关闭
	    if ($info['action'] == 'close') {
	        $ret = Gou_Service_Ad::updates($info['ids'], array('status'=>0));
	    }
	    if (!$ret) $this->output('-1', '操作失败.');
	    $this->output('0', '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '广告标题不能为空.'); 
		if(!$info['cid']) $this->output(-1, '平台不能为空.'); 
		if(!$info['module_id']) $this->output(-1, '模块不能为空.'); 
		if(!$info['img']) $this->output(-1, '广告图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if(!$info['link']) $this->output(-1, '广告链接不能为空.');
        if($info['activity'] && intval($info['clientver']) == 0) $this->output(-1, '客户端版本号不能为空.');
		if(in_array(array(9, 5), $info['ad_type'])){
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		//if(!$info['channel_code']) $this->output(-1, '渠道号不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
//		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_Ad::deleteAd($id);
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
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'ad');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
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
		//create webp image
		if ($ret['code'] == 0) {
			$attachPath = Common::getConfig('siteConfig', 'attachPath');
			$file = realpath($attachPath.$ret['data']);
			image2webp($file, $file.'.webp');
		}
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
