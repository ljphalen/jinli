<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class PtnerController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/admin/ptner/index',
		'bindUrl' => '/admin/ptner/bind',
		'editUrl'=>'/admin/ptner/edit',
		'editPostUrl'=>'/admin/ptner/edit_post',
		'deleteUrl'=>'/admin/ptner/delete',
		'detailUrl'=>'/admin/ptner/detail',
		'sbindUrl'=>'/admin/ptner/sbind',
        'addUrl'=>'/admin/ptner/add',
        'addPostUrl'=>'/admin/ptner/add_post'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $partners) = Wifi_Service_Ptner::getList($page, $perpage);
		
		$this->assign('partners', $partners);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	public function bindAction() {
		$uid = $this->getInput('uid');
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $devices) = Wifi_Service_Device::getList($page, $perpage);
		$this->assign('uid', $uid);
		$this->assign('devices', $devices);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}

    public function editAction() {
        $id = $this->getInput('id');
        $info = Wifi_Service_Ptner::get($id);
        $this->assign('info', $info);
    }

    public function edit_postAction() {
        $info = $this->getInput(array('id','username', 'passwd','repasswd', 'phone', 's_name', 's_detail'));
        self::_cookData($info);

        $ptner = Wifi_Service_Ptner::getBy(array('phone'=>$info['phone']));
        if ($ptner && ($ptner['id'] != $info['id'])) $this->output(-1, '手机号已经注册');

        $ret = Wifi_Service_Ptner::update($info, $info['id']);
        if (!$ret) $this->output(-1, '商户编辑失败');
        $this->output(0, '商户编辑成功.');
    }

    public function addAction() {

    }

    public function add_postAction() {
        $info = $this->getInput(array('username', 'passwd','repasswd', 'phone', 's_name', 's_detail'));
        self::_cookData($info);

        $ptner = Wifi_Service_Ptner::getBy(array('phone'=>$info['phone']));
        if ($ptner) $this->output(-1, '手机号已经注册');
        $ptner = Wifi_Service_Ptner::getBy(array('username'=>$info['username']));
        if ($ptner) $this->output(-1, '用户名已经存在');

        $ret = Wifi_Service_Ptner::add($info);
        if (!$ret) $this->output(-1, '商户添加失败');
        $this->output(0, '商户添加成功.');
    }

    private function _cookData($data) {
        if (!$data['username']) $this->output(-1, '用户名不能为空.');
        if (!$data['passwd']) $this->output(-1, '密码不能为空.');
        if ($data['passwd'] != $data['repasswd']) {
            $this->output(-1, '两次密码不一致.');
        }
        if (!$data['phone']) $this->output(-1, '手机号码不能为空.');
        if (!$data['s_name']) $this->output(-1, '店铺名称不能为空.');
        if (!$data['s_detail']) $this->output(-1, '地址不能为空.');
        return $data;
    }
	
	public function sbindAction() {
		$info = $this->getInput(array('uid', 'id'));
		$ret = Wifi_Service_Device::update(array('uid'=>$info['uid']), $info['id']);
		$ret = Wifi_Service_Ptner::update(array('status'=>1), $info['uid']);
		if (!$ret) $this->output(-1,'绑定失败');
		$this->output(0,'绑定成功.');
	}

    public function deleteAction() {
        $id = $this->getInput('uid');
        $result = Wifi_Service_Ptner::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }
}
