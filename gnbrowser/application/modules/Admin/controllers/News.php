<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class NewsController extends Admin_BaseController{
	
	public $actions = array(
		'listUrl' => '/Admin/News/index',
		'addUrl' => '/Admin/News/add',
		'addPostUrl' => '/Admin/News/add_post',
		'editUrl' => '/Admin/News/edit',
		'editPostUrl' => '/Admin/News/edit_post',
		'editStatusPostUrl' => '/Admin/News/edit_status_post',
		'deleteUrl' => '/Admin/News/delete',
		'uploadUrl' => '/Admin/News/upload',
		'uploadPostUrl' => '/Admin/News/upload_post',

	);
	
	public $perpage = 40;
	public $newstype;
	
	/**
	 *
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$this->newstype = Common::getConfig('apiConfig', 'news');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		
		$type = $this->getInput('type_id');
		
		if ($type) {
			$param['type_id'] = $type;
			
			$perpage = $this->perpage;
			list($total, $news) = Gionee_Service_News::getList($page, $perpage, $param);
			
			$this->assign('news', $news);
			$url = $this->actions['listUrl'] .'/?'. http_build_query($param) . '&';
			$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
			$this->assign('type', $type);
			$this->assign('param',$param);
		}
		
		$this->assign('newstype', $this->newstype);
		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction(){
		$this->assign('newstype', $this->newstype);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction(){
		$info = $this->getPost(array('sort','img', 'title', 'url', 'content', 'ontime', 'status', 'istop', 'start_time','type_id'));
		if (!$info['type_id']) $this->output(-1, '分类不能为空.');
		if (!$info['title']) $this->output(-1, '标题不能为空.');
		if (!$info['url']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['url'], 'http://') === false || !strpos($info['url'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		if (!$info['ontime']) $this->output(-1, '发布时间不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		$ret = Gionee_Service_News::addNews($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */

	public function editAction(){
		$id = $this->getInput('id');
		$info = Gionee_Service_News::getNews(intval($id)); 
        $this->assign('info', $info);	
        $this->assign('newstype', $this->newstype);
	}

	/**
	 * 
	 * Enter description here ...
	 */

	public function edit_postAction(){
		$info = $this->getPost(array('id', 'type_id', 'img', 'sort', 'title','url', 'content', 'ontime', 'status', 'istop','start_time'));
		if (!$info['type_id']) $this->output(-1, '分类不能为空.');
		if (!$info['title']) $this->output(-1, '标题不能为空.');
		if (!$info['url']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['url'], 'http://') === false || !strpos($info['url'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if (!$info['img']) $this->output(-1, '图片不能为空.');
		if (!$info['ontime']) $this->output(-1, '发布时间不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		$ret = Gionee_Service_News::updateNews($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 批量修改状态
	 */
	public function edit_status_postAction() {
		$ids = $this->getPost(array('ids','oids'));		
		if (!$ids['ids']) $this->output(-1, '请选择要显示的新闻.');
		//设置原选中的状态为0
		Gionee_Service_News::updateStatusByIds($ids['oids'], 0);
				
		$ret = Gionee_Service_News::updateStatusByIds($ids['ids'], 1);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gionee_Service_News::getNews($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gionee_Service_News::deleteNews($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
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
		$ret = $this->_upload('img');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('news/upload.phtml');
		exit;
        }

	/**
	 * 
	 * Enter description here ...
	 */

	private function _upload($name) {
		$img = $_FILES[$name]; 
		if ($img['error'] == 4) {
			exit(json_encode(array('error' => 1,'message' => '请选择要上传的图片！')));
		}
		$allowType = array('jpg' => '','jpeg' => '','png' => '','gif' => '');
		$savePath = BASE_PATH . 'data/attachs/news/' . date('Ym'); 
		$uploader = new Util_Upload($allowType);
		if (!$ret = $uploader->upload('img', date('His'), $savePath)) {
			return Common::formatMsg(-1, '上传失败');
		}
		$url = '/news/'.date('Ym') .'/'. $ret['newName'];
		return Common::formatMsg(0, '', $url);
	}
	
	

}
