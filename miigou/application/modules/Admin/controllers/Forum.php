<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ForumController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/Forum/index',
		'addUrl' => '/Admin/Forum/add',
		'addPostUrl' => '/Admin/Forum/add_post',
		'editUrl' => '/Admin/Forum/edit',
		'editPostUrl' => '/Admin/Forum/edit_post',
		'deleteUrl' => '/Admin/Forum/delete',
		'deleteImgUrl' => '/Admin/Forum/delete_img',
		'uploadUrl' => '/Admin/Forum/upload',
		'uploadPostUrl' => '/Admin/Forum/upload_post',
		'uploadImgUrl' => '/Admin/Forum/uploadImg',

	);
	public $perpage = 20;
	public $status = array (
			1 => '未审核',
			2 => '审核通过',
			3 => '审核未通过',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		$param = $this->getInput(array('category_id', 'status', 'is_top'));
		$search = array();
		if ($param['category_id']) $search['category_id'] = $param['category_id'];
		if ($param['status']) $search['status'] = $param['status'];
		if ($param['is_top']) $search['is_top'] = $param['is_top'];
		
		list($total, $forums) = Gou_Service_Forum::getList($page, $perpage, $search, array('is_top'=>'DESC','id'=>'DESC'));		
		
		$this->assign('category', Common::getConfig('typeConfig', 'forum_type'));
		$this->assign('forums', $forums);
		$this->assign('status', $this->status);
	    $this->assign('search', $search);
	    $this->cookieParams();
	    $url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function addAction(){
		//分类
		$this->assign('category', Common::getConfig('typeConfig', 'forum_type'));
		$this->assign('status', $this->status);
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function add_postAction(){
		$info = $this->getPost(array('title', 'category_id', 'content', 'is_top', 'status'));
		$simg = $this->getPost('simg');
		$info = $this->_cookData($info);
		
		$rand_admin_user = rand(9990, 9999);
		$info['user_id'] = 0;
		
		if($info['category_id'] == 1) {
			$info['user_name'] = '挑战性官方';
		} else {
			$info['user_name'] = $rand_admin_user;
		}		
		
		$ret = Gou_Service_Forum::addForum($info);
		if (!$ret) $this->output(-1, '操作失败.');
		
		if($simg) {
			$gimgs = array();
			foreach($simg as $key=>$value) {
				if ($value != '') {
					$gimgs[] = array('forum_id'=>$ret, 'img'=>$value);
				}
			}
			
			if($gimgs) {
				$ret = Gou_Service_ForumImg::batchAddForumImg($gimgs);
				if (!$ret) $this->output(-1, '-操作失败.');
			}
		}
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gou_Service_Forum::getForum(intval($id));
		
		list(,$imgs) = Gou_Service_ForumImg::getList(0, 10, array('forum_id'=>intval($id)), array('id'=>'ASC'));
		$this->assign('imgs', $imgs);
		$this->assign('status', $this->status);
		
		$this->assign('category', Common::getConfig('typeConfig', 'forum_type'));
       $this->assign('info', $info);	
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id','title', 'category_id', 'content', 'is_top', 'status'));
		
		$rand_admin_user = rand(9990, 9999);
		
		if($info['category_id'] == 1) {
			$info['user_id'] = 0;
			$info['user_name'] = '挑战性官方';
		} else {
			$forum = Gou_Service_Forum::getForum($info['id']);
			if($forum['user_id'] == 0 && $info['category_id'] != $forum['category_id']) {
				$info['user_name'] = $rand_admin_user;
			}
			
		}
		
		$info = $this->_cookData($info);
		
		$ret = Gou_Service_Forum::updateForum($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		
		//修改的图片
		$upImgs = $this->getPost('upImg');
		foreach($upImgs as $key=>$value) {
			if ($key && $value) {
				Gou_Service_ForumImg::updateForumImg(array('img'=>$value), $key);
			}
		}
		//新增加的图片
		$simg = $this->getPost('simg');
		if ($simg[0] != null) {
			$gimgs = array();
			foreach($simg as $key=>$value) {
				if ($value != '') {
					$gimgs[] = array('forum_id'=>$info['id'], 'img'=>$value);
				}
			}
			$ret = Gou_Service_ForumImg::batchAddForumImg($gimgs);
			if (!$ret) $this->output(-1, '2-操作失败.');
		}
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_Forum::getForum($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Gou_Service_ForumImg::deleteByForumId($id);
		$ret = Gou_Service_Forum::deleteForum($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
		if(Util_String::strlen($info['title']) > 20 ) $this->output(-1, '标题不能超过20字.');
		if(!$info['category_id']) $this->output(-1, '分类不能为空.');
		if(!$info['content']) $this->output(-1, '内容不能为空.');
		return $info;
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
		$ret = Common::upload('imgFile', 'forum');
		$attachroot = Yaf_Application::app()->getConfig()->attachroot;
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => $attachroot . '/attachs' .$ret['data'])));
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'forum');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
        }

	
	/**
	 *
	 * Enter description here ...
	 */
	public function delete_imgAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ForumImg::getForumImg($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_ForumImg::deleteForumImg($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
