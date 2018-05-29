<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 知物后台
 * StoryController
 *
 * @author ryan
 *
 */
class User_virtualController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/User_Virtual/index',
        'addUrl' => '/Admin/User_Virtual/add',
        'addPostUrl' => '/Admin/User_Virtual/add_post',
        'editUrl' => '/Admin/User_Virtual/edit',
        'editPostUrl' => '/Admin/User_Virtual/edit_post',
        'deleteUrl' => '/Admin/User_Virtual/delete',
        'uploadUrl' => '/Admin/User_Virtual/upload',
        'uploadPostUrl' => '/Admin/User_Virtual/upload_post',
    );

    public $perpage = 20;

    public $versionName = 'virtual_Version';

    /**
     *
     * 虚拟用户列表
     *
     */
    public function indexAction(){
        $page = intval($this->getInput('page'));
        $param = $this->getInput(array('uid', 'nickname', 'table'));

        $search['type'] = 1;  //虚拟用户
        if ($param['uid']) $search['uid'] = $param['uid'];
        if ($param['nickname']) $search['nickname'] = $param['nickname'];
        $table = 0;
        if (!empty($param['table'])) $table = $param['table'];

        $perpage = $this->perpage;
        $orderby = array('id'=>'DESC');

        if($table){
            list($total, $users) = User_Service_Uid::getList($table, $page, $perpage, $search, $orderby);
        }else{
            $total = 0;
            $users = User_Service_Uid::getsBy($search, $table);
        }
//        list($total, $users) = User_Service_Uid::getList($page, $perpage, $search, $orderby);
        $this->cookieParams();
        $url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('total', $total);
        $this->assign('users', $users);
        $this->assign('param', $param);
        $this->assign('url', $url);
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
        $info = User_Service_Uid::getUser(intval($id));
        $this->assign('info', $info);
    }

    /**
     *
     *删除
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = User_Service_Uid::getUser($id);
        if (!$info) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['avatar']);
        $result = User_Service_Uid::deleteUser($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     *
     * 添加提交
     */
    public function add_postAction(){
        $info = $this->getPost(array('nickname', 'mobile', 'avatar'));
        $info = $this->_cookData($info);

        $info['type'] = 1; //虚拟用户类型
        $info['uid'] = md5(time()); //虚拟uid

        $result = User_Service_Uid::addUser($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }

    /**
     *
     * 编辑提交
     */
    public function edit_postAction(){
        $info = $this->getPost(array('id', 'nickname', 'mobile', 'avatar'));
        $info = $this->_cookData($info);
        $result = User_Service_Uid::updateUser($info, intval($info['id']));
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
//        $info = pathinfo($ret['data']);
//        $attachPath = Common::getConfig('siteConfig', 'attachPath');
//        $path =  sprintf("%s%s", realpath($attachPath), $info['dirname']);
//        $img = $path."/".$info['basename'];
//        Util_Image::makeThumb($img, $img, 24, 24);
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
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
        if($info['mobile'] && !Common::checkMobile($info['mobile'])) $this->output(-1, '联系电话有误.');
        return $info;
    }
}