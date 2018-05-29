<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class GroupController extends Admin_BaseController {
    
    public $actions = array(
        'listUrl' => '/Admin/Group/index',
    	'editUrl' => '/Admin/Group/edit',
    	'editPostUrl' => '/Admin/Group/edit_post',
    );
    
    public $perpage = 20;
    
    /**
     * 
     * Enter description here ...
     */
    public function indexAction() {
		$page = intval($this->getInput('page'));
				
		list($total, $groups) = Admin_Service_Group::getList($page, $this->perpage);
		$this->assign('groups', $groups);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['listUrl'] . '/?'));
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function editAction() {
    	$groupid = $this->getInput('groupid');
    	$groupInfo = Admin_Service_Group::getGroup($groupid);
    	$menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);
    	$level = $menuService->getMainLevels();
		$this->assign('level', $level);
    	$this->assign('groupInfo', $groupInfo);
    }
    
    /**
     * 
     * Enter description here ...
     */
    public function edit_postAction() {
    	$info = $this->getPost(array('descrip','rvalue','groupid'));
		$result = Admin_Service_Group::updateGroup($info, $info['groupid']);
		if (!$result) $this->output(-1, '修改失败.');
		$this->output(0, '修改成功.');
    }    
}
