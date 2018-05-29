<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Cod_GuideController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cod_Guide/index',
		'picUrl' => '/Admin/Cod_Guide/pic',
		'txtUrl' => '/Admin/Cod_Guide/txt',
		'addUrl' => '/Admin/Cod_Guide/add',
		'addPostUrl' => '/Admin/Cod_Guide/add_post',
		'editUrl' => '/Admin/Cod_Guide/edit',
		'editPostUrl' => '/Admin/Cod_Guide/edit_post',
		'deleteUrl' => '/Admin/Cod_Guide/delete',
		'uploadUrl' => '/Admin/Cod_Guide/upload',
		'uploadPostUrl' => '/Admin/Cod_Guide/upload_post',
		'uploadImgUrl' => '/Admin/Cod_Guide/uploadImg',
		'copyUrl' => '/Admin/Cod_Guide/copy',
	    'batchUpdateUrl'=>'/Admin/Cod_Guide/batchUpdate',
	);
	
	public $perpage = 15;
	public $appCacheName = array('APPC_Front_Cod_index', 'APPC_Channel_Cod_index', 'APPC_Apk_Cod_index', 'APPC_Market_Cod_index', 'APPC_App_Cod_index');
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$type = $this->getInput('type');

		$param = $this->getInput(array('ptype', 'channel', 'title', 'channel_id', 'start_time'));


		$perpage = $this->perpage;
		
		$search = array();
		if ($param['ptype'])   $search['ptype'] = $param['ptype'];
		if ($param['channel']) $search['channel'] = array('LIKE', $param['channel']);
		if ($param['title'])   $search['title'] = array('LIKE', $param['title']);
		if ($param['channel_id']) $search['channel_id'] = $param['channel_id'];
        if(!empty($param['start_time']))$search['search_time'] = array(
          array('>=',strtotime($param['start_time'])),
          array('<',strtotime($param['start_time'])+3600*24),
        );
		list(, $guide_types) = Cod_Service_Type::getCanUseCodTypes(1, 100);
		$guide_types = Common::resetKey($guide_types, 'id');
		$this->assign('guide_types', $guide_types);
		
		if ($type) {
			if($type == 1){
				list($total, $result) = Cod_Service_Guide::getPicList($page, $perpage, $search);
			} else {
				list($total, $result) = Cod_Service_Guide::getTxtList($page, $perpage, $search);
			}
		} else {
			list($total, $result) = Cod_Service_Guide::getList($page, $perpage, $search);
		}
		$this->assign('search', $param);
		$this->assign('result', $result);
		$this->assign('type', $type);
		$this->assign('ptype', $this->ptype);
		$search['type'] = $type;
		$this->cookieParams();
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	public function picAction() {
		$page = intval($this->getInput('page'));
        $param = $this->getInput(array('ptype','channel','channel_id','start_time'));
		$perpage = $this->perpage;
	
		$search = array();
        if ($param['ptype']) $search['ptype'] = $param['ptype'];
        if ($param['channel']) $search['channel'] = array('LIKE', $param['channel']);
        if ($param['channel_id']) $search['channel_id'] = $param['channel_id'];
        if(!empty($param['start_time']))$search['search_time'] = array(
          array('>=',strtotime($param['start_time'])),
          array('<=',strtotime($param['start_time'])+3600*24),
        );
	
		list(, $guide_types) = Cod_Service_Type::getCanUseCodTypes(1, 100);
		$guide_types = Common::resetKey($guide_types, 'id');
		$this->assign('guide_types', $guide_types);
	
		list($total, $result) = Cod_Service_Guide::getPicList($page, $perpage, $search);
		$this->assign('search', $param);
		$this->assign('result', $result);
		$this->assign('ptype', $this->ptype);
	
		$this->cookieParams();
		$url = $this->actions['picUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('addUrl', $this->actions['addUrl'].'?pic=1');
		$this->assign('listUrl', $this->actions['picUrl']);
		$this->display('index');
		exit;
	}
	
	public function txtAction() {
		$page = intval($this->getInput('page'));
		$ptype = $this->getInput('ptype');
		$channel = $this->getInput('channel');
        $param = $this->getInput(array('ptype','channel','start_time'));
		$perpage = $this->perpage;
	
		$search = array();
        if ($param['ptype']) $search['ptype'] = $param['ptype'];
        if ($param['channel']) $search['channel'] = array('LIKE', $param['channel']);
        if(!empty($param['start_time']))$search['search_time'] = array(
          array('>=',strtotime($param['start_time'])),
          array('<=',strtotime($param['start_time'])+3600*24),
        );
	
		list(, $guide_types) = Cod_Service_Type::getCanUseCodTypes(1, 100);
		$guide_types = Common::resetKey($guide_types, 'id');
		$this->assign('guide_types', $guide_types);
	
		list($total, $result) = Cod_Service_Guide::getTxtList($page, $perpage, $search);
		$this->assign('search', $param);
		$this->assign('result', $result);
		$this->assign('ptype', $this->ptype);
	
		$this->cookieParams();
		$url = $this->actions['txtUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('addUrl', $this->actions['addUrl'].'?pic=0');
		$this->assign('listUrl', $this->actions['txtUrl']);
		$this->display('indextxt');
		exit;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		list(, $guide_types) = Cod_Service_Type::getCanUseCodTypes(1, 100);
		$this->assign('guide_types', $guide_types);
		
		$info = Cod_Service_Guide::getGuide(intval($id));
		
		list($info['module_id'], $info['cid']) = explode('_', $info['module_channel']);
		$this->assign('info', $info);
		
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
		list(, $guide_types) = Cod_Service_Type::getCanUseCodTypes(1, 100);
		$channel_id = intval($this->getInput('channel_id'));
		$this->assign('channel_id', $channel_id);
		$this->assign('cookie_ptype', Util_Cookie::get('admin_guide_ptype'));
		$this->assign('guide_types', $guide_types);
		
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
		$info = $this->getPost(array('sort', 'title', 'ptype', 'pptype', 
				'link', 'img', 'start_time', 'end_time', 'status','hits', 
				'color', 'channel', 'channel_id','module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);
		$cookie_ptype = Util_Cookie::set('admin_guide_ptype', $info['ptype']);
		$result = Cod_Service_Guide::addGuide($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'id', 'ptype', 'pptype', 
				'title', 'link', 'img', 'start_time', 'end_time', 
				'status','hits', 'color', 'channel', 'channel_id',
				'module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);
		$ret = Cod_Service_Guide::updateGuide($info, intval($info['id']));
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
		$type = intval($this->getInput('type'));
		$info = Cod_Service_Guide::getGuide(intval($id));
		
		list(, $guide_types) = Cod_Service_Type::getCanUseCodTypes(1, 100);
		$this->assign('guide_types', $guide_types);
		$this->assign('channel_id', $channel_id);
		$this->assign('info', $info);
	
		//channel_code
		if($info['module_channel']) {
			$module_channel = explode('_', $info['module_channel']);
			list(,$channel_code) = Gou_Service_ChannelCode::getsBy(array('module_id'=>$module_channel[0], 'cid'=>$module_channel[1], 'channel_id'=>$channel_id), array('id'=>'DESC'));
			$this->assign('channel_code', $channel_code);
				
			list(, $channel_names) = Gou_Service_ChannelName::getAll();
			$this->assign('channel_names', Common::resetKey($channel_names, 'id'));
			$this->assign('module_channel', $module_channel);
		}
	
		//module channel
		list($modules, $channels) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channels', $channels);
		$this->assign('type', $type);

		$this->assign('info', $info);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.'); 
        if (!$info['cid']) $this->output(-1, '平台不能为空.'); 
        if (!$info['module_id']) $this->output(-1, '模块不能为空.'); 
		
		//if(!$info['img']) $this->output(-1, '图片不能为空.'); 
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		
		//if(!$info['channel_code']) $this->output(-1, '渠道号不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能晚于结束时间.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Cod_Service_Guide::getGuide($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Cod_Service_Guide::deleteGuide($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
	        $ret = Cod_Service_Guide::sort($data);
	    }
	    //开启
	    if ($info['action'] == 'open') {
	        $ret = Cod_Service_Guide::updates($info['ids'], array('status'=>1));
	    }
	    //关闭
	    if ($info['action'] == 'close') {
	        $ret = Cod_Service_Guide::updates($info['ids'], array('status'=>0));
	    }
	    if (!$ret) $this->output('-1', '操作失败.');
	    $this->output('0', '操作成功.');
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
		$ret = Common::upload('imgFile', 'guide');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'guide');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}
