<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_UidController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl'		=> '/Admin/User_Uid/index',
		'editUrl' 		=> '/Admin/User_Uid/edit',
		'editPostUrl' 	=> '/Admin/User_Uid/edit_post',
		'scoreUrl'		=> '/Admin/User_Score/list',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('uid', 'scoreid', 'nickname', 'table'));
		
		if (!empty($param['uid'])) $search['uid'] = $param['uid'];
		if (!empty($param['scoreid'])) $search['scoreid'] = $param['scoreid'];
		if (!empty($param['nickname'])) $search['nickname'] = $param['nickname'];
        $table = 0;
		if (!empty($param['table'])) $table = $param['table'];

		$order = array();
		if($this->getInput('id')) $order = array('id'=>'DESC');
		if($this->getInput('score')) $order = array('score'=>'DESC');
		
		$perpage = $this->perpage;
        if($table){
            list($total, $users) = User_Service_Uid::getList($table, $page, $perpage, $search, $order);
        }else{
            $total = 0;
            $users = User_Service_Uid::getsBy($search, $table);
        }

		$user_list = Common::resetKey($users, 'uid');
		$uids = array_keys($user_list);
		
		$summary = array();
		if($uids) $summary = Gou_Service_ScoreSummary::getsBy(array('uid'=>array('IN', $uids)));

		$summary = Common::resetKey($summary, 'uid');

		foreach($users as &$item){
			if(array_key_exists($item['uid'], $summary)){
				$item['summary'] = $summary[$item['uid']];
			}
		}
		$this->cookieParams();
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param, $order) . '&';
        if($table){
            $url_order = $this->actions['indexUrl'] .'/?'. http_build_query(array_merge($param, $order)) . '&';
            $this->assign('pager', Common::getPages($total, $page, $perpage, $url_order));
        }
		$this->assign('total', $total);
		$this->assign('users', $users);
		$this->assign('param', $param);
		$this->assign('url', $url);
	}

    public function editAction(){
        $id = $this->getInput('id');
        $user = User_Service_Uid::getUser(intval($id));

        $this->assign('user', $user);
    }

    public function edit_postAction(){
        $user = $this->getInput(array('id', 'nickname'));
		$user = $this->_cookData($user);
		$result = User_Service_Uid::updateUser($user, $user['id']);

		if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

	/**
	 * @param $summary
	 * @return mixed
	 */
	private function _cookData($user) {
		$name_len = Util_String::strlen($user['nickname']);
		if($name_len<2 || $name_len > 24) $this->output(-1, '请输入2-24位字符.');

		if(!empty($user['nickname'])) $user['nickname']=$user['nickname'];

		return $user;
	}

}