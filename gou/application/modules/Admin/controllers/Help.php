<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 教程后台
 * HelpController
 *
 * @author Milo
 *
 */
class HelpController extends Admin_BaseController {

	public $actions = array(
		'indexUrl' => '/Admin/Help/index',
		'listUrl' => '/Admin/Help/list',
		'previewUrl' => '/Help/detail',
		'addUrl' => '/Admin/Help/add',
		'topUrl' => '/Admin/Help/top',
		'activeUrl' => '/Admin/Help/active',
		'addPostUrl' => '/Admin/Help/add_post',
		'editUrl' => '/Admin/Help/edit',
		'editPostUrl' => '/Admin/Help/edit_post',
        'uploadImgUrl' => '/Admin/Help/uploadImg',
		'deleteUrl' => '/Admin/Help/delete',
		'uploadUrl' => '/Admin/Help/upload',
		'uploadPostUrl' => '/Admin/Help/upload_post',
	);

	public $perpage = 15;


	public function indexAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;


        $search['status'] = 1;
        $param = $this->getInput(array('title'));
        if (!empty($param['title'])) $search['title'] = array('LIKE',$param['title']);

        $orderby = array('id'=>'DESC');

		//作者列表
		list($total, $result) = Gou_Service_Help::getList($page, $perpage, $search, $orderby);

        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('param',$param);
		$this->assign('data', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}

	/**
	 *
	 *添加知物
	 *
	 */
	public function addAction(){

	}

	/**
	 * 编辑知物
	 *
	 */
	public function editAction() {
        $id = $this->getInput('id');
		$info = Gou_Service_Help::get(intval($id));
		$this->assign('info', $info);
	}

	/**
	 *
	 *删除知物
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
        $type = $this->getInput('type');

		$info = Gou_Service_Help::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['images']);
        $result = Gou_Service_Help::delete($id);


		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 *
	 * 添加提交
	 */
	public function add_postAction(){
		$info = $this->getPost(array('title','content'));
        $info = $this->_cookData($info);
        $result = Gou_Service_Help::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功',$result);
	}

	/**
	 *
	 * 编辑提交
	 */
	public function edit_postAction(){

        $info = $this->getPost(array('id','title','content'));
        $info = $this->_cookData($info);
		$result = Gou_Service_Help::update($info, intval($info['id']));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');

	}

    /**
     * 置顶/取消置顶
     *
     */
    public function topAction(){
        $id = $this->getPost('id');
        $type = $this->getPost('type');
        $info['recommend'] = $type;
		$result = Gou_Service_Help::update($info, intval($id));
		if (!$result) $this->output(-1, '操作失败');
        Gou_Service_Config::setValue($this->versionName, Common::getTime());
		$this->output(0, '操作成功');

	}

    /**
     * 激活/撤稿
     *
     */
    public function activeAction(){
        $info = $this->getPost(array('id', 'status'));
        $info['is_cancel'] = intval(!$info['status']);
		$result = Gou_Service_Help::update($info, intval($info['id']));
		if (!$result) $this->output(-1, '操作失败');
        Gou_Service_Config::setValue($this->versionName, Common::getTime());
		$this->output(0, '操作成功');
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
    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'Help');
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
    	$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}

//`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
//`title` varchar(100) NOT NULL DEFAULT '',
//`content` text DEFAULT '',
//`hits` int(10) NOT NULL DEFAULT 0,
    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {

    	if(!$info['title']) $this->output(-1, '标题不能为空.');
    	if(!$info['content']) $this->output(-1, '内容不能为空.');
        $info['content'] = $this->updateImgUrl($info['content']);
        
        $info['summary'] = htmlspecialchars_decode($info['summary']);
        $info['start_time'] = strtotime($info['start_time']);
        $info['end_time'] = strtotime($info['end_time']);

        if($info['status']==1&&$info['start_time']>time()+180)$this->output(-1, '已发布时间现在起保持在3分钟以内.');
        if($info['status']==2&&$info['start_time']<time())$this->output(-1, '预发布时间不能小于当前时间.');
        $info['status']=$info['status']>0?1:0;
        $info['content'] = $this->updateImgUrl($info['content']);
    	return $info;
    }
}