<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 资讯后台
 *
 * @author tiansh
 *
 */
class InfoController extends Admin_BaseController {

	public $actions = array(
		'indexUrl' => '/Admin/Info/index',
	    'previewUrl' => '/Info/detail',
		'addUrl' => '/Admin/Info/add',
		'topUrl' => '/Admin/Info/top',
		'activeUrl' => '/Admin/Info/active',
		'addPostUrl' => '/Admin/Info/add_post',
		'editUrl' => '/Admin/Info/edit',
		'editPostUrl' => '/Admin/Info/edit_post',
        'uploadImgUrl' => '/Admin/Info/uploadImg',
		'deleteUrl' => '/Admin/Info/delete',
		'uploadUrl' => '/Admin/Info/upload',
		'uploadPostUrl' => '/Admin/Info/upload_post',
	);

    public $type = array(
        0=>'海淘资讯',1=>'海淘攻略'
    );

	public $perpage = 15;

	/**
	 *
	 * 资讯发布列表
	 *
	 */
    public function indexAction(){
        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;
        
        $param = $this->getInput(array('status', 'title', 'type', 'start_time'));
        if(!isset($param['type']))     $param['type']  = -1;
        if (!empty($param['title']))  $search['title'] = array('LIKE', trim($param['title']));
        if ($param['type']!=-1)       $search['type']  = $param['type'];
        if (!empty($param['status'])){
            //$search['start_time'] = array('>=',time());
            if($param['status']) $search['status'] = $param['status'];
        }
        if ($param['start_time']) $search['start_time'] = array('>=', strtotime($param['start_time']));
        
        $orderby = array('is_recommend'=>'DESC','sort'=>'DESC','create_time'=>'DESC');
        
        list($total, $result) = Dhm_Service_Info::getList($page, $perpage, $search, $orderby);
        list(,$footer) = Dhm_Service_Footer::getAll();
        $footer = Common::resetKey($footer,'id');
        $url = $this->actions['indexUrl'].'/?' . http_build_query($param) . '&';
        $this->assign('data',  $result);
        $this->assign('footer', $footer);
        $this->assign('param',  $param);
        $this->assign('type',   $this->type);
        $this->assign('pager',  Common::getPages($total, $page, $perpage, $url));
        $this->assign('status', Dhm_Service_Info::$status);
        $this->cookieParams();
	}

    

	/**
	 *
	 *添加资讯
	 *
	 */
	public function addAction(){
        list(,$footer) = Dhm_Service_Footer::getAll();
        $this->assign('status',  Dhm_Service_Info::$status);
        $this->assign('dir',     'info');
        $this->assign('footer',  $footer);
        $this->assign('type',    $this->type);
        $this->assign('ueditor', true);
	}

	/**
	 * 编辑资讯
	 *
	 */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Dhm_Service_Info::get(intval($id));
        list(,$footer) = Dhm_Service_Footer::getAll();
        
        $this->assign('info',    $info);
        $this->assign('status',  Dhm_Service_Info::$status);
        $this->assign('dir',     'info');
        $this->assign('footer',  $footer);
        $this->assign('type',    $this->type);
        $this->assign('ueditor', true);
    }

	/**
	 *
	 *删除资讯
	 */
    public function deleteAction() {
        $id = $this->getInput('id');
        
        $info = Dhm_Service_Info::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $this->dropThumb(intval($info['id']));
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['images']);
        $result = Dhm_Service_Info::delete($id);
        
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 删除缩略图
     * @param $id
     */
    private function dropThumb($id){
        $info = Dhm_Service_Info::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');

        $images = explode(',', $info['images']);

        foreach ($images as $img) {
            Util_File::del(Common::getConfig('siteConfig', 'attachPath') .$img);
        }
    }
    
	/**
	 * 添加提交
	 */
	public function add_postAction(){
        $pre_id = $this->getPost('pre_id');
        if(!empty($pre_id)){
            foreach ($pre_id as $id) {
                $result= Dhm_Service_Info::delete($id);
             }
        }
        $info = $this->getPost(array('title', 'sort', 'status', 'footer_id', 'type', 'start_time', 'summary', 'content'));
        $info = $this->_cookData($info);
        $result = Dhm_Service_Info::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功',$result);
	}

	/**
	 * 编辑提交
	 */
	public function edit_postAction(){
        $pre_id = $this->getPost('pre_id');
        $info = $this->getPost(array('id', 'title', 'sort', 'status', 'footer_id', 'type', 'start_time', 'summary', 'content'));
        $result= Dhm_Service_Info::deletes('id', $pre_id);
        $this->dropThumb(intval($info['id']));
        $info = $this->_cookData($info);
        $result = Dhm_Service_Info::update($info, intval($info['id']));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

	}

    /**
     * 置顶/取消置顶
     */
    public function topAction(){
        $id = $this->getPost('id');
        $type = $this->getPost('type');
        $info['is_recommend'] = $type;
        $result = Dhm_Service_Info::update($info, intval($id));
        if (!$result) $this->output(-1, '操作失败');
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
	 * 上传动作
	 */
	public function upload_postAction() {
		$ret = Common::upload('images', 'info');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
    
    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'info');
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
    }


    /**
     * 从文章提取图片地址生成缩略图
     * @param string $content 文章内容
     * @return bool|string
     */
    public function createThumb($content){
        $str = htmlspecialchars_decode($content);
        $pattern = '/<img\s.*src=[\"\'](.*)[\"\']/isU';
        preg_match_all($pattern,$str,$match);
        if(empty($match[1])){
            return false;
        }

        //android版有三张缩略图
        $images = array_slice($match[1], 0, 3);
        $thumbs = Util_Imagick::makeThumb($images, 'info', 160, 160);
        
        return implode(',', $thumbs);
    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
        if(!$info['title']) $this->output(-1, '标题不能为空.');
        if(!$info['content']) $this->output(-1, '内容不能为空.');
        if(!$info['summary']) $this->output(-1, '摘要不能为空.');
        
        $info['content'] = $this->updateImgUrl($info['content']);
        $info['images'] = $this->createThumb($info['content']);
        
        if(isset($info['summary'])) $info['summary'] = htmlspecialchars_decode($info['summary']);
        if(isset($info['start_time'])) $info['start_time'] = strtotime($info['start_time']);
        if(isset($info['end_time'])) $info['end_time'] = strtotime($info['end_time']);
        
        return $info;
    }
}