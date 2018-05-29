<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Config_HelpController extends Admin_BaseController {
	

//`id` int(10) NOT NULL AUTO_INCREMENT,
//`preg` varchar(500) NOT NULL DEFAULT '',
//`url` varchar(500) NOT NULL DEFAULT '',
//`status` tinyint(3) NOT NULL DEFAULT 0,

    public $actions = array(
      'indexUrl' => '/Admin/Config_Help/index',
      'listUrl' => '/Admin/Config_Help/index',
      'previewUrl' => '/Config_Help/detail',
      'addUrl' => '/Admin/Config_Help/add',
      'topUrl' => '/Admin/Config_Help/top',
      'activeUrl' => '/Admin/Config_Help/active',
      'addPostUrl' => '/Admin/Config_Help/add_post',
      'editUrl' => '/Admin/Config_Help/edit',
      'editPostUrl' => '/Admin/Config_Help/edit_post',
      'uploadImgUrl' => '/Admin/Config_Help/uploadImg',
      'deleteUrl' => '/Admin/Config_Help/delete',
      'uploadUrl' => '/Admin/Config_Help/upload',
      'uploadPostUrl' => '/Admin/Config_Help/upload_post',
    );

    public $perpage = 15;
    public $versionName = 'Config_Version';

    public function indexAction(){
        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $param = array();
        $search = array();

        $orderby = array('id'=>'DESC');

        //作者列表
        list($total, $result) = Gou_Service_ConfigHelp::getList($page, $perpage, $search, $orderby);
        $hids = array_keys(Common::resetKey($result, 'help_id'));
        $cond = array('id' => array('IN', $hids));
        list(, $hlps) = Gou_Service_Help::getsBy($cond, array());
        $hlps = Common::resetKey($hlps, 'id');
        foreach ($result as $k => &$v) {
            $v['help'] = $hlps[$v['help_id']]['title'];
        }
        $url = $this->actions['indexUrl'] . '/?';
        $this->assign('param', $param);
        $this->assign('data', $result);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }

    public function addAction(){
        list(,$hlp)=Gou_Service_Help::getList(1,100,array('status'=>1));
        $this->assign('hlps',$hlp);
    }


    public function editAction() {
        $id = $this->getInput('id');
        $info = Gou_Service_ConfigHelp::get(intval($id));
        list(,$hlp)=Gou_Service_Help::getList(1,100,array('status'=>1));
        $this->assign('hlps',$hlp);
        $this->assign('info', $info);
    }


    public function deleteAction() {
        $id = $this->getInput('id');
        $type = $this->getInput('type');

        $info = Gou_Service_ConfigHelp::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['images']);
        $result = Gou_Service_ConfigHelp::delete($id);

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    public function add_postAction(){
        $info = $this->getPost(array('preg','help_id','status'));
        $info = $this->_cookData($info);
        $result = Gou_Service_ConfigHelp::add($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功',$result);
    }


    public function edit_postAction(){

        $info = $this->getPost(array('id','preg','help_id','status'));
        $info = $this->_cookData($info);
        $result = Gou_Service_ConfigHelp::update($info, intval($info['id']));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }


    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
        if(!$info['preg']) $this->output(-1, '规则不能为空');
        if(!$info['help_id']) $this->output(-1, '请先选择购物教程页面.');
        return $info;
    }
}
