<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class FileController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/File/index',
		'addUrl' => '/Admin/File/add',
		'addPostUrl' => '/Admin/File/add_post',
		'editUrl' => '/Admin/File/edit',
		'editPostUrl' => '/Admin/File/edit_post',
		'editStatusUrl' => '/Admin/File/editstatus',
		'editStatusPostUrl' => '/Admin/File/editstatus_post',
		'deleteUrl' => '/Admin/File/delete',
		'uploadUrl' => '/Admin/File/upload',
		'uploadPostUrl' => '/Admin/File/upload_post',
		'detailUrl' => '/Admin/File/detail',
	);
	
	public $perpage = 20;
	public $status = array(
		1=>'已提交',
		2=>'未通过',
		3=>'已通过',
		4=>'上架',
		5=>'下架'
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
				
		$param = $this->getInput(array('title', 'file_type', 'size_id', 'status', 'order_by'));
		if($param['title']) $search['title'] = $param['title'];
		if($param['file_type']) $search['file_type'] = $param['file_type'];
		if($param['size_id']) $search['size_id'] = $param['size_id'];
		if($param['status']) $search['status'] = $param['status'];
		
		//如果当前登录的用户是设计师，则只显示本人的文件列表
		if($this->userInfo['groupid'] == 1) $search['user_id'] = $this->userInfo['uid'];
		
		//排序方式
		$order_by = $param['order_by'] ? $param['order_by'] : 'id';
		
		//分辨率
		list(,$file_size) = Lock_Service_Size::getAllSize();		
		//分类
		list(,$file_type) = Lock_Service_FileType::getAllFileType();
		$file_type = Common::resetKey($file_type, 'id');
		list($total, $files) = Lock_Service_File::getList($page, $perpage, $search, $order_by, 'DESC');
		$this->assign('files', $files);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('file_type', $file_type);
		$this->assign('file_size', Common::resetKey($file_size, 'id'));
		$this->assign('status', $this->status);
		$this->assign('order_by', $order_by);
		$this->assign('search', $search);
		$this->assign('groupid', $this->userInfo['groupid']);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		if($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
		//分辨率
		list(,$size) = Lock_Service_Size::getAllSize();		
		//分类
		list(,$file_type) = Lock_Service_FileType::getAllFileType();
		$id = $this->getInput('id');
		$info = Lock_Service_File::getFile(intval($id));

		$file_types = Lock_Service_FileTypes::getByFileId($info['id']);
		$file_size = Lock_Service_FileSize::getByFileId($info['id']);
		
		$this->assign('info', $info);
		$this->assign('file_type', Common::resetKey($file_type, 'id'));
		$this->assign('file_types', Common::resetKey($file_types, 'type_id'));
		$this->assign('file_size', Common::resetKey($file_size, 'size_id'));
		$this->assign('size', Common::resetKey($size, 'id'));
		$this->assign('groupid', $this->userInfo['groupid']);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		if($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
		//分辨率
		list(,$size) = Lock_Service_Size::getAllSize();		
		//分类
		list(,$file_type) = Lock_Service_FileType::getAllFileType();
		
		//自动勾选上次的分类和分辨率
		list(,$file) =  Lock_Service_File::getList(1, 1, array(), 'id', 'DESC');
		if($file) {
			//分类
			$file_types = Lock_Service_FileTypes::getByFileId($file[0]['id']);
			//分辨率
			$file_size = Lock_Service_FileSize::getByFileId($file[0]['id']);
			
			$this->assign('file_types', Common::resetKey($file_types, 'type_id'));
			$this->assign('file_size', Common::resetKey($file_size, 'size_id'));
		}
		$this->assign('file_type', Common::resetKey($file_type, 'id'));
		$this->assign('size', Common::resetKey($size, 'id'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		if($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
		$info = $this->getPost(array('title', 'file', 'file_type', 'file_size', 'icon', 'img_gif', 'img_png', 'summary', 'descript', 'designer', 'price', 'size_id'));
		$info['user_id'] = $this->userInfo['id'];
		$info = $this->_cookData($info);
		
		//文件
		$file_data = array(
			'user_id'=> $this->userInfo['uid'],
			'title'=> $info['title'],
			'file'=> $info['file'],
			'file_size'=> $info['file_size'],
			'icon'=> $info['icon'],
			'img_gif'=> $info['img_gif'],
			'img_png'=> $info['img_png'],
			'summary'=> $info['summary'],
			'descript'=> $info['descript'],
			'designer'=> $info['designer'],
			'price'=> $info['price'],
			'size_id'=> $info['size_id'],
		);
		
		$user = array(
			'user_id'=>$this->userInfo['uid'],
			'username'=>$this->userInfo['username']		
		);
		
		$result = Lock_Service_File::add($user, $file_data, $info['file_type'], $info['size_id']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		if($this->userInfo['groupid'] != 1) $this->redirect($this->actions['listUrl']);
		$info = $this->getPost(array('id', 'title', 'file_type', 'file', 'file_size', 'icon', 'img_gif', 'img_png', 'summary', 'descript', 'designer', 'price', 'size_id'));
		$info = $this->_cookData($info);
		
		$file_data = array(
			'title'=>$info['title'],
			'file'=>$info['file'],
			'file_size'=>$info['file_size'],
			'icon'=>$info['icon'],
			'img_gif'=>$info['img_gif'],
			'img_png'=>$info['img_png'],
			'summary'=>$info['summary'],
			'descript'=>$info['descript'],
			'designer'=>$info['designer'],
			'price'=>$info['price'],
			'status'=>1
		);
		
		//分类
		$type_data = array();
		foreach ($info['file_type'] as $key=>$value) {
			$type_data[$key]['id'] = '';
			$type_data[$key]['file_id'] = $info['id'];
			$type_data[$key]['type_id'] = $value;
		}
		
		//分类
		$size_data = array();
		foreach ($info['size_id'] as $key=>$value) {
			$size_data[$key]['id'] = '';
			$size_data[$key]['file_id'] = $info['id'];
			$size_data[$key]['size_id'] = $value;
		}
		
		//记录日志
		$log_data = array(
				'uid'=>$this->userInfo['uid'],
				'username'=>$this->userInfo['username'],
				'message'=>$this->userInfo['username'].'修改了文件：<a href=/Admin/File/detail/?id='.$info['id'].'>'.$info['title'].'</a>',
				'file_id'=>$info['id']
		);
		
		//给测试发消息
		$message_data = array();
		$group_id = 2;
		//消息内容
		list(,$users) = Admin_Service_User::getList(1, 20, array('groupid'=>$group_id));
		if($users) {
			foreach ($users as $key=>$value) {
				$message_data[$key]['id'] = '';
				$message_data[$key]['uid'] = $value['uid'];
				$message_data[$key]['content'] = $this->userInfo['username'].'修改了文件：<a href=/Admin/File/detail/?id='.$info['id'].'>'.$info['title'].'</a>';
				$message_data[$key]['status'] = 0;
				$message_data[$key]['create_time'] = Common::getTime();
			}
		}
		$result = Lock_Service_File::update($info['id'], $file_data, $type_data, $size_data, $log_data, $message_data);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editstatusAction() {
		if(!in_array($this->userInfo['groupid'], array(2, 3))) $this->redirect($this->actions['listUrl']);
		$id = $this->getInput('id');
		$info = Lock_Service_File::getFile(intval($id));
		
		//日志
		list(, $logs) = Admin_Service_AdminLog::getList(1, 100, array('file_id'=>$info['id']));
		
		//不同的用户组操作的权限不一样
		
		if($this->userInfo['groupid'] == 2) {
			unset($this->status[4]);
			unset($this->status[5]);
		}elseif ($this->userInfo['groupid'] == 3) {
			if($info['status'] == 3) {
				unset($this->status[1]);
				unset($this->status[5]);
			}else{
				unset($this->status[1]);
				unset($this->status[2]);
				unset($this->status[3]);
			}
		}else {
			$status = array();
		}
		
		$this->assign('info', $info);
		$this->assign('logs', $logs);
		$this->assign('status', $this->status);
		$this->assign('groupid', $this->userInfo['uid']);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editstatus_postAction() {
		if(!in_array($this->userInfo['groupid'], array(2, 3))) $this->redirect($this->actions['listUrl']);
		$info = $this->getPost(array('id', 'status', 'message', 'ostatus','ispush'));
		
		if(!in_array($this->userInfo['groupid'], array(2, 3))) $this->output(-1, '无权限操作.');
		if($info['status'] == 2 && !$info['message']) $this->output(-1, '请填写未通过原因.');
		
		//有修改才操作数据库
		if($info['status'] != $info['ostatus'] || $info['message']) {
		
			$file_data = array('status'=>$info['status'], 'ispush'=>$info['ispush']);
			
			$file = Lock_Service_File::getFile($info['id']);
			//记录日志
			$message = $this->userInfo['username'].'将文件 "<a href=/Admin/File/detail/?id='.$info['id'].'>'.$file['title'].'</a>"状态更新为"'.$this->status[$info['status']].'"';
			if($info['message']) $message .=  ',备注：'.$info['message'];
			$log_data = array(
				'uid'=>$this->userInfo['uid'],
				'username'=>$this->userInfo['username'],
				'message'=>$message,
				'file_id'=>$info['id']
			);
			
			//发送消息--确定发送对象
			
			$group_id = 0;
			$message_data = array();
			
			if($info['status'] == 1) {
				$group_id = 2;
			}elseif ($info['status'] == 2) {
				$group_id = 1;
			}elseif ($info['status'] == 3) {
				$group_id = 3;
			}else{
				$group_id = 0;
			}
			//消息内容
			if($group_id) {
				list(,$users) = Admin_Service_User::getList(1, 20, array('groupid'=>$group_id));
				$content = '文件"<a href=/Admin/File/detail/?id='.$info['id'].'>'.$file['title'].'</a>"审核"'.$this->status[$info['status']].'"';
				if($info['message']) $content .= ',原因：'.$info['message'];
				if($users) {
					foreach ($users as $key=>$value) {
						$message_data[$key]['id'] = '';
						$message_data[$key]['uid'] = $value['uid'];
						$message_data[$key]['content'] = $content;
						$message_data[$key]['status'] = 0;
						$message_data[$key]['create_time'] = Common::getTime();
					}
				}
			}
			
			$result = Lock_Service_File::editStatus($info['id'], $info['status'], $file_data, $log_data, $message_data);
			if (!$result) $this->output(-1, '操作失败');	
		}
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['file']) $this->output(-1, '请上传zip文件.');
		if(!$info['icon']) $this->output(-1, '缩略图不能为空.');
		if(!$info['img_gif']) $this->output(-1, 'gif预览图不能为空.');
		if(!$info['img_png']) $this->output(-1, 'png预览图不能为空.');
		if(!$info['title']) $this->output(-1, '标题不能为空.');		
		if(!$info['file_type']) $this->output(-1, '请选择分类.');	
		if(!$info['file_size']) $this->output(-1, '文件大小不能为空.');
		if(!$info['summary']) $this->output(-1, '简要描述不能为空.');
		if(Util_String::strlen($info['summary'])  > 11) $this->output(-1, '简要描述不能超过11个字.');
		if(!$info['descript']) $this->output(-1, '详细描述不能为空.');
		if(Util_String::strlen($info['descript']) > 200) $this->output(-1, '详细描述不能超过200个字.');
		if(!$info['designer']) $this->output(-1, '设计师不能为空.');
		if($info['price']) {
			if(!is_numeric($info['price'])) $this->output(-1, '价格填写不正确.');
			if(strpos($info['price'], '.')) {
				$price = explode('.', $info['price']);
				if(Util_String::strlen($price[1]) > 2 || Util_String::strlen($price[1]) < 1) $this->output(-1, '价格填写不正确.');
			}			
		}
		if(!$info['size_id']) $this->output(-1, '请选择分辨率.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_File::getFile($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		if ($info['status'] == 4) $this->output(-1, '该文件是已上架状态，无法删除');
		if (!in_array($this->userInfo['groupid'], array(1, 3))) $this->output(-1, '没有权限删除');
		//记录日志
		$log_data = array(
				'uid'=>$this->userInfo['uid'],
				'username'=>$this->userInfo['username'],
				'message'=>$this->userInfo['username'].'删除了文件：'.$info['title'],
				'file_id'=>$info['id']
		);
		$result = Lock_Service_File::delete($id, $log_data);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function detailAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_File::getFile(intval($id));
		if(!$info || !$id) $this->redirect($this->actions['listUrl']);		
		
		//分类
		$file_types = Lock_Service_FileTypes::getByFileId($info['id']);
		
		$ids = array();
		foreach ($file_types as $key=>$value) {
			$ids[$key]  = $value['type_id'];
		}		
		$file_type = Lock_Service_FileType::getByIds($ids);
		
		$str_type = '';
		foreach ($file_type as $key=>$value) {
			$str_type .= strlen($str_type) ? ','.$value['name'] :  $value['name'] ;
		}
		$info['file_type'] = $str_type;
		
		//分辨率
		$file_size = Lock_Service_FileSize::getByFileId($info['id']);
		
		$size_ids = array();
		foreach ($file_size as $key=>$value) {
			$size_ids[$key]  = $value['size_id'];
		}
		$sizes = Lock_Service_Size::getByIds($size_ids);
		$str_size = '';
		foreach ($sizes as $key=>$value) {
			$str_size .= strlen($str_size) ? ','.$value['size'] :  $value['size'] ;
		}
		$info['file_size'] = $str_size;
		
		$this->assign('info', $info);
		$this->assign('size', Common::resetKey($file_size, 'id'));
		$this->assign('info', $info);
		$this->assign('group', $this->userInfo['groupid']);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Lock_Service_File::uploadFile('file', 'file');
		$this->assign('code' , $ret['code']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', json_encode($ret['data']));
		$this->getView()->display('file/upload.phtml');
		exit;
	}
}
