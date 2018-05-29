<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class LockController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Lock/index',
		'addUrl' => '/Admin/Lock/add',
		'addPostUrl' => '/Admin/Lock/add_post',
		'editUrl' => '/Admin/Lock/edit',
		'editPostUrl' => '/Admin/Lock/edit_post',
		'deleteUrl' => '/Admin/Lock/delete',
		'step1Url' => '/Admin/Lock/add_step1',
		'step2Url' => '/Admin/Lock/add_step2'
	);
	
	public $perpage = 20;
	public $appCacheName = 'APPC_Front_Index_index';
	public $channels = array(
		'1'=>'精灵锁屏',
		'2'=>'金立锁屏'
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$channel_id = intval($this->getPost('channel_id'));
		$keyword = $this->getPost('keyword');
		if ($page < 1) $page = 1;
		
		//get search params
		$search = array();
		if ($channel_id) $search['channel_id'] = $channel_id;
		if ($keyword) $search['title'] = array('LIKE', $keyword);
		
		//get Lock list
		list($total, $lock) = Lock_Service_Lock::getList($page, $this->perpage, $search, array('sort'=>'DESC', 'id'=>'DESC'));
		$this->assign('lock', $lock);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('channels', $this->channels);
		//get pager
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_Lock::getLock(intval($id));
		
		$this->assign('channels', $this->channels);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * taobaoke Lock list
	 */
	public function add_step1Action() {
		$page = intval($this->getInput('page'));
		$channel_id = intval($this->getInput('channel_id'));
		$keyword = $this->getInput('keyword');
		
		if ($page < 1) $page = 1;
		if(!$channel_id) $channel_id = 1;
		
		//精灵锁屏
		if($channel_id == 1) {
			list($total, $list) = Lock_Service_QiiFile::getList($page, $this->perpage, array('keyword'=>$keyword), 'out_id', 'DESC');
			foreach ($list as $key=>$value) {
				$list[$key]['id'] = $value['out_id'];
				$list[$key]['title'] = $value['zh_title'];
			}
		} else {
			list($total, $list) = Lock_Service_File::getList($page, $this->perpage, array('keyword'=>$keyword, 'status'=>4));
			foreach ($list as $key=>$value) {
				$list[$key]['icon'] = $value['img_png'];
			}
		}
		
		//已添加的锁屏
		list(,$has_list) = Lock_Service_Lock::getsBy(array('channel_id'=>$channel_id), array('id'=>'DESC'));
		$has_list = Common::resetKey($has_list, 'file_id');
		
		$this->assign('channels', $this->channels);
		$this->assign('channel_id', $channel_id);
		$this->assign('keyword', $keyword);
		$this->assign('has_list', $has_list);
		$this->assign('list', $list);
		$url = $this->actions['step1Url'] .'/?'. http_build_query(array('keyword'=>$keyword, 'channel_id'=>$channel_id)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * add Lock page show
	 */
	public function add_step2Action() {
		$file_id = $this->getInput('file_id');
		$channel_id = $this->getInput('channel_id');
		
		if($channel_id == 1) {
			$file = Lock_Service_QiiFile::getBy(array('out_id'=>$file_id));
			$info = array(
					'id'=>$file['out_id'],
					'title'=>$file['zh_title'],
					'icon'=>$file['icon'],
			);
		} else {
			$file = Lock_Service_File::getFile($file_id);
			$info = array(
					'id'=>$file['id'],
					'title'=>$file['title'],
					'icon'=>$file['img_png'],
			);
		}
				
		$this->assign('channel_id', $channel_id);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'file_id', 'channel_id', 'hits', 'title', 'icon','ispush'));
		$result = Lock_Service_Lock::addLock($info);
		$file_id = Lock_Service_Lock::getLastInsertId();

		//发送push消息
		if($info['ispush'] == 1){
			$queue = Common::getQueue();
			$queue->noRepeatPush('lock_push_msg', $file_id);
		}
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'hits'));
		$ret = Lock_Service_Lock::updateLock($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_Lock::getLock($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Lock_Service_SubjectFile::deleteBy(array('lock_id'=>$id));
		$result = Lock_Service_Lock::deleteBy(array('id'=>$id));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
