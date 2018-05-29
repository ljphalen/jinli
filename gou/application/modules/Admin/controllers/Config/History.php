<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @class Config_HistoryController
 * @author Milo
 * @package Admin
 */
class Config_HistoryController extends Admin_BaseController {



    public $actions = array(
      'indexUrl' => '/Admin/Config_History/index',
      'listUrl' => '/Admin/Config_History/index',
      'previewUrl' => '/Config_History/detail',
      'addUrl' => '/Admin/Config_History/add',
      'topUrl' => '/Admin/Config_History/top',
      'activeUrl' => '/Admin/Config_History/active',
      'addPostUrl' => '/Admin/Config_History/add_post',
      'editUrl' => '/Admin/Config_History/edit',
      'editPostUrl' => '/Admin/Config_History/edit_post',
      'uploadImgUrl' => '/Admin/Config_History/uploadImg',
      'deleteUrl' => '/Admin/Config_History/delete',
      'uploadUrl' => '/Admin/Config_History/upload',
      'uploadPostUrl' => '/Admin/Config_History/upload_post',
    );

    public $perpage = 15;

    public $versionName = 'History_Config_Version';
    public function indexAction(){

        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $orderby = array('id'=>'DESC');

        list($total, $result) = Gou_Service_ConfigHistory::getList($page, $perpage, array(), $orderby);
        $url = $this->actions['indexUrl'] . '/?';
        $this->assign('data', $result);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }


    public function addAction(){

    }


    public function editAction() {

        $id = $this->getInput('id');
        $info = Gou_Service_ConfigHistory::get(intval($id));
        $this->assign('info', $info);
    }


    public function deleteAction() {

        $id = $this->getInput('id');
        $type = $this->getInput('type');

        $info = Gou_Service_ConfigHistory::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['images']);
        $result = Gou_Service_ConfigHistory::delete($id);

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    public function add_postAction(){

        $info = $this->getPost(array('preg','type','src','status'));
        $info = $this->_cookData($info);
        $result = Gou_Service_ConfigHistory::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功',$result);
    }


    public function edit_postAction(){

        $info = $this->getPost(array('id','preg','type','src','status'));
        $info = $this->_cookData($info);
        $result = Gou_Service_ConfigHistory::update($info, intval($info['id']));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {

        if(!$info['preg']) $this->output(-1, '规则不能为空.');
        if(!$info['type']) $this->output(-1, '类型名称不能为空.');

        return $info;
    }

}
