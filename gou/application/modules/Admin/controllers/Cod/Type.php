<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Cod_TypeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Cod_Type/index',
		'addUrl' => '/Admin/Cod_Type/add',
		'addPostUrl' => '/Admin/Cod_Type/add_post',
		'editUrl' => '/Admin/Cod_Type/edit',
		'editPostUrl' => '/Admin/Cod_Type/edit_post',
		'deleteUrl' => '/Admin/Cod_Type/delete',
		'uploadUrl' => '/Admin/Ad/upload',
		'uploadPostUrl' => '/Admin/Ad/upload_post',
		'uploadImgUrl' => '/Admin/Ad/uploadImg'
	);
	
	public $perpage = 20;
	public $appCacheName = array('APPC_Front_Cod_index', 'APPC_Channel_Cod_index', 'APPC_Apk_Cod_index');
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$perpage = $this->perpage;
		
		list($total, $result) = Cod_Service_Type::getList($page, $perpage);
		
		$this->assign('result', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Cod_Service_Type::getCodtype(intval($id));
		
		list($info['module_id'],$info['cid'])  = explode('_', $info['module_channel']);
		$this->assign('info', $info);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	/**
	 * get an subjct by subject_id
	 */
	public function getAction() {
		$id = $this->getInput('id');
		$info = Cod_Service_Type::getCodtype(intval($id));
		if(!$info) $this->output(-1, '操作失败.');
		$this->output(0, '', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
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
		$info = $this->getPost(array('sort', 'title', 'status', 'url', 'color', 'module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);
		list($total, $all) = Cod_Service_Type::getAllCodType();
		$tmp = array();
		foreach($all as $key=>$value){
			$tmp[] = $value['title'];
		}
		if(in_array($info['title'],$tmp)) $this->output(-1, '不能重复添加导购分类');
		$result = Cod_Service_Type::addCodtype($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'title', 'status', 'url', 'color', 'module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);
		$ret = Cod_Service_Type::updateCodtype($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '名称不能为空.'); 
		//if(!$info['img']) $this->output(-1, '图片不能为空.'); 
		if(!$info['url']) $this->output(-1, '链接不能为空.'); 
		if (strpos($info['url'], 'http://') === false || !strpos($info['url'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Cod_Service_Type::getCodtype($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Cod_Service_Type::deleteCodtype($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 */
	public function toolAction() {
		$cache_file = Common::getConfig('siteConfig', 'dataPath') . $this->gou_index_tool_file;
		if (is_file($cache_file)) {
			$info = include $cache_file;
		}
		$this->assign('tool', $info);
	}
	
	/**
	 * 
	 */
	public function edit_toolAction() {
		$gou_index_tools = $this->getPost('tool');
		$cache_file = Common::getConfig('siteConfig', 'dataPath') . $this->gou_index_tool_file;
		$ret = Util_File::write($cache_file, "<?php\n return ".str_replace("\'",'', var_export($gou_index_tools, TRUE))."\n?>");
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
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
		$ret = Common::upload('imgFile', 'guidetype');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => '/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'guidetype');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
