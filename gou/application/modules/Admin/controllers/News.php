<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * NewsController
 * 
 * @author fanzh
 *
 */
class NewsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/News/index',
		'addUrl' => '/Admin/News/add',
		'addPostUrl' => '/Admin/News/add_post',
		'editUrl' => '/Admin/News/edit',
		'editPostUrl' => '/Admin/News/edit_post',
		'deleteUrl' => '/Admin/News/delete',
		'uploadUrl' => '/Admin/News/upload',
		'uploadPostUrl' => '/Admin/News/upload_post',
	);
	
	public $perpage = 20;
	
	public $newstype = array(
				1 => '聚合阅读',
				2 => '导航'
	);
	
	/**
	 * 
	 * 新闻列表
	 * 
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$ptype = $this->getInput('type_id');
		$perpage = $this->perpage;

		//搜索参数
		$search = array();
		if($ptype) {
			$search['type_id'] = $ptype;
			if($ptype == 1) {
				$orderby = array('sort'=>'DESC', 'id'=>'DESC');
			} else {
				$orderby = array('pub_time'=>'DESC', 'sort'=>'DESC');
			}
		} else {
			$orderby = array('sort'=>'DESC', 'id'=>'DESC');
		}
		//获取新闻列表
		list($total, $result) = Gou_Service_News::getList($page, $perpage, $search, $orderby);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';

		$this->assign('search', $search);
		$this->assign('news', $result);
		$this->assign('newstype', $this->newstype);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	
	/**
	 * 
	 *添加新闻
	 * 
	 */
	public function addAction(){
		$this->assign('newstype', $this->newstype);
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}

	/**
	 * 编辑新闻
	 * 
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_News::getNews(intval($id));
		$this->assign('newstype', $this->newstype);
		
		list($info['module_id'], $info['cid']) = explode('_', $info['module_channel']);
		$this->assign('info', $info);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	/**
	 *
	 *删除新闻
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_News::getNews($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Gou_Service_News::deleteNews($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *  
	 * 添加提交
	 */
	public function add_postAction(){
		$info = $this->getPost(array('sort', 'type_id', 'category', 'title', 'link', 'img', 'status', 'pub_time', 'start_time','module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);
		$result = Gou_Service_News::addNews($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
		
	}
	
	/**
	 *
	 * 编辑提交
	 */
	public function edit_postAction(){
		$info = $this->getPost(array('sort', 'id', 'type_id', 'category', 'title', 'link', 'img', 'status', 'pub_time', 'start_time', 'module_id', 'cid', 'channel_code'));
		$info = $this->_cookData($info);	
		$result = Gou_Service_News::updateNews($info, intval($info['id']));		
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	
	}
	
	/**
	 * 
	 * 上传页面
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 
	 * 上传动作
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'news');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
    	if(!$info['title']) $this->output(-1, '新闻标题不能为空.');
    	if(!$info['link']) $this->output(-1, '新闻链接不能为空.');
    	if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
    		$this->output(-1, '链接地址不规范.');
    	}
    	//if(!$info['img']) $this->output(-1, '图片不能为空.');
    	if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
    	if(!$info['pub_time']) $this->output(-1, '发布时间不能为空.');
    	$info['start_time'] = strtotime($info['start_time']);
    	$info['pub_time'] = strtotime($info['pub_time']);
    	return $info;
    }
}