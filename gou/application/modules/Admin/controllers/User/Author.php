<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 知物后台
 * StoryController
 *
 * @author ryan
 *
 */
class User_AuthorController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/User_Author/index',
        'addUrl' => '/Admin/User_Author/add',
        'addPostUrl' => '/Admin/User_Author/add_post',
        'editUrl' => '/Admin/User_Author/edit',
        'editPostUrl' => '/Admin/User_Author/edit_post',
        'uploadImgUrl' => '/Admin/User_Author/uploadImg',
        'deleteUrl' => '/Admin/User_Author/delete',
        'uploadUrl' => '/Admin/User_Author/upload',
        'uploadPostUrl' => '/Admin/User_Author/upload_post',
    );

    public $perpage = 20;

    /**
     *
     * 知物列表
     *
     */
    public function indexAction(){
        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;
        //搜索参数
        $search = array();
        $orderby = array('id'=>'DESC');
        //获取知物列表
        list($total, $result) = Gou_Service_UserAuthor::getList($page, $perpage, $search, $orderby);
        $url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
        $this->assign('data', $result);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }

    /**
     *
     *添加
     *
     */
    public function addAction(){

    }

    /**
     * 编辑
     *
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Gou_Service_UserAuthor::get(intval($id));
        $this->assign('info', $info);
    }

    /**
     *
     *删除
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Gou_Service_UserAuthor::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
        $result = Gou_Service_UserAuthor::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     *
     * 添加提交
     */
    public function add_postAction(){
        $info = $this->getPost(array('uid','nickname','username', 'avatar'));
        $info = $this->_cookData($info);
        $result = Gou_Service_UserAuthor::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }

    /**
     *
     * 编辑提交
     */
    public function edit_postAction(){
        $info = $this->getPost(array('id','uid','nickname','username', 'avatar'));
        $info = $this->_cookData($info);
        $result = Gou_Service_UserAuthor::update($info, intval($info['id']));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }

    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }
    /**
     *
     * 上传页面
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'avatar');
        $info = pathinfo($ret['data']);
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
        $path =  sprintf("%s%s",realpath($attachPath),$info['dirname']);
        $img=$path."/".$info['basename'];
        $res=Util_Image::makeThumb($img,$img,24,24);
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
        if(!$info['nickname']) $this->output(-1, '昵称不能为空.');
        if(!$info['avatar']) $this->output(-1, '头像不能为空.');
        return $info;
    }
}