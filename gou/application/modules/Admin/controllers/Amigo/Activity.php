<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author huangsg
 *
 */
class Amigo_ActivityController extends Admin_BaseController {
	
	public $actions = array(
			'indexUrl' => '/Admin/Amigo_Activity/index',
			'listUrl' => '/Admin/Amigo_Activity/list',
			'addUrl' => '/Admin/Amigo_Activity/add',
			'addPostUrl' => '/Admin/Amigo_Activity/add_post',
			'editUrl' => '/Admin/Amigo_Activity/edit',
			'editPostUrl' => '/Admin/Amigo_Activity/edit_post',
			'deleteUrl' => '/Admin/Amigo_Activity/delete',
			'uploadUrl' => '/Admin/Amigo_Activity/upload',
			'uploadPostUrl' => '/Admin/Amigo_Activity/upload_post',
			'uploadImgUrl' => '/Admin/Amigo_Activity/uploadImg',
	        'batchUpdateUrl'=>'/Admin/Amigo_Activity/batchUpdate',
	);
	public $type = array(0=>'热门活动',1=>'长期活动');
	public $tag = array(1=>'淘宝',2=>'天猫',3=>'京东',4=>'1号店',5=>'苏宁',6=>'国美',7=>'美丽说',);
	public $perpage = 15;
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$params  = $this->getInput(array('title','type'));
		$search = array();
		if ($params['title']) $search['title'] = array('LIKE',$params['title']);
        $time = time();
        if ($params['type'] == 1) { //正在进行
            $search['start_time'] = array('<=', $time);
            $search['end_time']   = array('>=', $time);
        }
		if ($params['type']==2) $search['end_time'] = array('<', $time);//已过期
		if ($params['type']==3) $search['start_time'] = array('>', $time);//还未开始
        $search['type']=0;
		list($total, $list) = Amigo_Service_Activity::getList($page, $this->perpage, $search, array('sort'=>'DESC'));
		
		$this->assign('list', $list);
		$this->assign('params', $params);
		$url = $this->actions['indexUrl'] .'/?'.http_build_query($params).'&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}
	public function listAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;

		$params  = $this->getInput(array('title','tag_id'));
		$search = array();
		if ($params['title']) $search['title'] = array('LIKE',$params['title']);
		if(!empty($params['tag_id'])) $search['tag_id'] = $params['tag_id'];
        $search['type']=1;
		list($total, $list) = Amigo_Service_Activity::getList($page, $this->perpage, $search, array('sort'=>'DESC'));
        list(,$tags)=Amigo_Service_Tag::getAllTag();
        $tags = Common::resetKey($tags,'id');
		$this->assign('tags',$tags );
		$this->assign('list', $list);
		$this->assign('params', $params);
		$url = $this->actions['listUrl'] .'/?'.http_build_query($params).'&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}

	public function addAction(){

        $this->assign('type',$this->type);
        list(,$tags)=Amigo_Service_Tag::getAllTag();
        $this->assign('tags',$tags );
	}
	
	public function add_postAction(){
		$info = $this->getPost(array('title', 'type', 'tag_id', 'content', 'start_time', 'end_time', 'sort',
				'link', 'status', 'img'));
		$info = $this->_checkData($info);
		$ret = Amigo_Service_Activity::add($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Amigo_Service_Activity::getOne($id);

        $this->assign('type',$this->type);
        list(,$tags)=Amigo_Service_Tag::getAllTag();
        $this->assign('tags',$tags );
		$this->assign('info', $info);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'title','type', 'tag_id', 'content', 'start_time', 'end_time', 'sort',
				'link', 'status', 'img'));
		$info = $this->_checkData($info);
		$ret = Amigo_Service_Activity::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Amigo_Service_Activity::getOne($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Amigo_Service_Activity::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	private function _checkData($info){
		if (!$info['title']) $this->output(-1, '名称不能为空.');
		if (!$info['img']) $this->output(-1, '图标不能为空.');

		if(!$info['type']){
            if(empty($info['start_time']) || empty($info['end_time'])){
                $this->output(-1, '请完整填写时间');
            }
            $info['start_time'] = strtotime($info['start_time']);
            $info['end_time'] = strtotime($info['end_time']);

            if ($info['start_time'] >= $info['end_time']){
                $this->output(-1, '开始时间不能超过结束时间');
            }
        }
        $info['content'] = $this->updateImgUrl($info['content']);

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
		$ret = Common::upload('imgFile', 'AmigoActivity');
		$adminroot = Yaf_Application::app()->getConfig()->adminroot;
       if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
       exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'AmigoActivity');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		//create webp image
		if ($ret['code'] == 0) {
			$attachPath = Common::getConfig('siteConfig', 'attachPath');
			$file = realpath($attachPath.$ret['data']);
			//image2webp($file, $file.'.webp');
		}
		$this->getView()->display('common/upload.phtml');
		exit;
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
	        $ret = Amigo_Service_Activity::sort($data);
	    }
	    //开启
	    if ($info['action'] == 'open') {
	        $ret = Amigo_Service_Activity::updates($info['ids'], array('status'=>1));
	    }
	    //关闭
	    if ($info['action'] == 'close') {
	        $ret = Amigo_Service_Activity::updates($info['ids'], array('status'=>0));
	    }
	    if (!$ret) $this->output('-1', '操作失败.');
	    $this->output('0', '操作成功.');
	}

}