<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_AppsController extends Admin_BaseController {
	
	public $actions = array(
			'listUrl' => '/Admin/Resource_Apps/index',
			'addUrl' => '/Admin/Resource_Apps/add',
			'addPostUrl' => '/Admin/Resource_Apps/add_post',
			'editUrl' => '/Admin/Resource_Apps/edit',
			'editPostUrl' => '/Admin/Resource_Apps/edit_post',
			'updateStatusUrl' => '/Admin/Resource_Apps/status',
			'versionUrl' => '/Admin/Resource_Apps/version',
			'addVersionUrl' => '/Admin/Resource_Apps/add_version',
			'addVersionPostUrl' => '/Admin/Resource_Apps/add_version_post',
			'editVersionUrl' => '/Admin/Resource_Apps/edit_version',
			'editVersionPostUrl' => '/Admin/Resource_Apps/edit_version_post',
			'replaceVersionUrl' => '/Admin/Resource_Apps/replace_version',
			'replaceVersionPostUrl' => '/Admin/Resource_Apps/replace_version_post',
			'uploadUrl' => '/Admin/Resource_Apps/upload',
			'uploadPostUrl' => '/Admin/Resource_Apps/upload_post',
			'uploadApkUrl' => '/Admin/Resource_Apps/uploadApk',
			'deleteImgUrl'=>'/Admin/Resource_Apps/deleteImg',
			'uploadApkPostUrl' => '/Admin/Resource_Apps/upload_Apkpost',
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if($page < 1) $page = 1;
		$perpage = $this->perpage;
		$search = $this->getInput(array('status', 'name'));
		$params = array();
		if ($search['name']) $params['name'] = array('LIKE',$search['name']);
		if ($search['status']) $params['status'] = $search['status'] - 1;
		
		list($total, $apps) = Resource_Service_Apps::getList($page, $perpage,$params);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('apps', $apps);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
		
	}
	
	/**
	 * 添加应用
	 */
	public function addAction() {
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('name', 'resume', 'descrip', 'imgId', 'min_os', 'class', 'score', 'belong'));
		$info = $this->_cookApp($info);
		$info['create_time'] = time();
		$ret = Resource_Service_Apps::addApps($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$appId = Resource_Service_Apps::getLastInsertId();

		$simg = $this->getInput('simg');
		if (count($simg)) {
			$tmp = array();
			foreach ($simg as $key => $value) {
				$tmp[] = array('app_id'=>$appId, 'img'=>$value);
			}
			Resource_Service_Img::add($tmp);	
		}
		$this->output(0, '操作成功.',array('appId'=>$appId));
	}

	public function deleteImgAction() {
		$id = $this->getInput('id');
		$ret = Resource_Service_Img::delete($id);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 编辑应用
	 */
	public function editAction(){
		$appId = $this->getInput('id');
		$data = Resource_Service_Apps::get($appId);

        $simg = Resource_Service_Img::getsBy(array('app_id'=>$data['id']));
        $this->assign('simg', $simg);
		$this->assign('data', $data);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'name', 'resume', 'descrip', 'imgId', 'min_os', 'class', 'score', 'belong'));
		$info = $this->_cookApp($info);
		$info['create_time'] = time();
		$ret = Resource_Service_Apps::updateApps($info);

		$simg = $this->getInput('simg');
		if (count($simg)) {
			$tmp = array();
			foreach ($simg as $key => $value) {
				$tmp[] = array('app_id'=>$info['id'], 'img'=>$value);
			}
			Resource_Service_Img::add($tmp);	
		}

		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 上下线应用
	 */
	public function statusAction(){
		$info = $this->getInput(array('id', 'status'));
		$info['create_time'] = time();
		$data = Resource_Service_Apps::get($info['id']);
		if(empty($data['link'])) $this->output(-1, '未添加上线版本信息.');
		$ret = Resource_Service_Apps::updateStatus($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 应用版本列表
	 */
	public function versionAction(){
		$appId = $this->getInput('id');
		$data = Resource_Service_Apps::get($appId);
		$versions = Resource_Service_AppsVersion::getsBy(array('app_id' => $appId));
		$this->assign('data', $data);
		$this->assign('versions', $versions);
	}
	
	/**
	 * 添加应用版本
	 */
	public function add_versionAction(){
		$appId = $this->getInput('id');
		$data = Resource_Service_Apps::get($appId);
		$this->assign('data', $data);
	}
	
	public function add_version_postAction(){
		$info = $this->getPost(array('app_id', 'link', 'package', 'version_code', 'version', 'size', 'status'));
		$info = $this->_cookVersion($info);
		$info['create_time'] = time();
		$ret = Resource_Service_AppsVersion::addVersion($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 编辑应用版本
	 */
	public function edit_versionAction(){
		$versionId = $this->getInput('id');
		$appId = $this->getInput('app_id');
		$appData = Resource_Service_Apps::get($appId);
		$versionData = Resource_Service_AppsVersion::get($versionId);
		$this->assign('appData', $appData);
		$this->assign('versionData', $versionData);
	}
	
	public function edit_version_postAction(){
		$info = $this->getPost(array('id','app_id', 'package', 'link','version_code', 'version', 'size', 'status'));
		$info = $this->_cookVersion($info);
		$info['create_time'] = time();
		$ret = Resource_Service_AppsVersion::updateVersion($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 替换应用版本
	 */
	public function replace_versionAction(){
		$versionId = $this->getInput('id');
		$appId = $this->getInput('app_id');
		$appData = Resource_Service_Apps::get($appId);
		$versionData = Resource_Service_AppsVersion::get($versionId);
		$this->assign('appData', $appData);
		$this->assign('versionData', $versionData);
	}
	
	public function replace_version_postAction(){
		$info = $this->getPost(array('id','app_id', 'package','replace_link', 'link','version_code', 'version', 'size', 'status'));
		$info = $this->_cookVersion($info);
		//附件路径
		$attachPath = Common::getConfig('siteConfig', 'appsPath');
		//原始文件目录
		$old = $attachPath . $info['replace_link'];
		$new = $attachPath . $info['link'];
		//替换操作
		$ret = Util_File::mv($new, $old);
		if(!$ret) $this->output(-1, 'apk替换失败.');
		//文件信息还原
		$info['link'] = $info['replace_link'];
		$info['create_time'] = time();
		$ret = Resource_Service_AppsVersion::updateVersion($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 添加ftp文件到附件
	 */
	public function add_ftp_appAction() {
		$file = $this->getInput('filename');
		if(!$file) $this->output(-1, '请填写ftp上传后的文件名');
		$oldFile = Common::getConfig('siteConfig', 'ftpPath') .'/'. $file;
		if(!file_exists($oldFile)) $this->output(-1, 'ftp上传的文件不存在。');
		//目标路径
		$attachPath = Common::getConfig('siteConfig', 'appsPath');
		//保存目录
		$savePath = sprintf('/resource/%s/', date('Ym'));
		Util_Folder::mkRecur($attachPath . $savePath);
		//获取文件扩展名
		$newFile = date('His'). '.' . strtolower(end(explode(".", $file)));
		$saveFile =sprintf('%s%s%s',$attachPath, $savePath, $newFile);
		//移动文件到附件
		$ret = Util_File::mv($oldFile,$saveFile);
		if(!$ret) $this->output(-1, 'apk操作失败.');
		
		$this->output(0, '', array('file'=>$savePath . $newFile));
	}
	
	
	/**
	 * 获取包信息
	 */
	public function get_appAction() {
		$url = $this->getInput('link');
		if(!$url) $this->output(-1, '请先上传应用包');
		$file = Common::getConfig('siteConfig', 'appsPath') . $url;
		$info = Apk_Service_Aapt::info($file);
		$temp['package'] = $info['package'];
		$temp['version_code'] = $info['version_code'];
		$temp['version'] = $info['version'];
		$temp['size'] =  sprintf("%.2f", $info['size'] /1024);
		$this->output(0, '', array('list'=>$temp));
	}
	
	/**
	 * 上传图标
	 */
	public function uploadAction() {
		$info = $this->getInput(array('imgId'));
		$this->assign('imgId', $info['imgId']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 上传APK
	 * Enter description here ...
	 */
	public function uploadApkAction() {
		$info = $this->getInput(array('apkId'));
		$this->assign('apkId', $info['apkId']);
		$this->getView()->display('common/uploadApk.phtml');
		exit;
	}
	
	public function upload_postAction() {
		$ret = Common::upload('img', 'resource');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	public function upload_ApkpostAction() {
		$ret = Common::uploadApk('apk', 'resource');
		$apkId = $this->getPost('apkId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('apkId', $apkId);
		$this->getView()->display('common/uploadApk.phtml');
		exit;
	}
	
	private function _cookVersion($info){
		if(!$info['link']) $this->output(-1, '应用地址不能为空.');
		if(!$info['package']) $this->output(-1, '应用包名不能为空.');
		if(!$info['version_code']) $this->output(-1, 'version_code不能为空.');
		if(!$info['version']) $this->output(-1, '版本号不能为空.');
		if(!$info['size']) $this->output(-1, '大小不能为空.');
		return $info;
	}

	private function _cookApp($info){
		if(!$info['name']) $this->output(-1, '应用名称不能为空.');
		if(!$info['resume']) $this->output(-1, '简述不能为空.');
		if(!$info['descrip']) $this->output(-1, '应用介绍不能为空.');
		if(!$info['imgId']) $this->output(-1, '图标不能为空.');
		return $info;
	}
}