<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Activity_LotteryusersController extends Admin_BaseController {
	public $actions = array(
		'userListUrl'=>'/Admin/Activity_Lotteryusers/list',
		'userInfoUrl'=>'/Admin/Activity_Lotteryusers/user',
		'userUpdateUrl'=>'/Admin/Activity_Lotteryusers/user_update',
	);
	
	public $perpage = 20;
	
	/**
	 * 中奖名单
	 */
	public function listAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$param = $this->getInput(array('id', 'award_id', 'imei', 'phone_num', 'qq', 'weixin','status', 'start_time', 'end_time', 'cate_id'));
	
		//搜索功能参数处理
		if (!empty($param['id'])) $search['id'] = $param['id'];
		if (!empty($param['award_id'])) $search['award_id'] = $param['award_id'];
		if (!empty($param['cate_id'])) $search['cate_id'] = $param['cate_id'];
		if (!empty($param['phone_num'])) $search['phone_num'] = $param['phone_num'];
		if (!empty($param['weixin'])) $search['weixin'] = $param['weixin'];
		if (!empty($param['imei'])) $search['imei'] = $param['imei'];
		if (!empty($param['qq'])) $search['qq'] = $param['qq'];
		if (!empty($param['status'])) $search['status'] = $param['status'];
		if (!empty($param['start_time'])) $search['dateline'] = array('>=', strtotime($param['start_time'] . ' 00:00:00'));
		if (!empty($param['end_time'])) $search['dateline'] = array('<=', strtotime($param['end_time'] . ' 23:59:59'));
		if (!empty($param['start_time']) && !empty($param['end_time'])) {
			$search['dateline'] = array(
					array('>=', strtotime($param['start_time'] . ' 00:00:00')),
					array('<=', strtotime($param['end_time'] . ' 23:59:59'))
			);
		}
		
		list($total, $users) = Activity_Service_Lotteryuser::getList($page, $perpage, $search);
		$awards = Activity_Service_Awards::getAll();
		if (!empty($awards)) {		//奖品名称加入到数组
			foreach ($users as $key=>$val) {
				foreach ($awards as $v)
				if ($val['award_id'] == $v['id'])
					$users[$key]['award_name'] = $v['award_name'];
			}
		}
	
		$this->assign('award', $awards);
		$this->assign('list', $users);
		$this->assign('param', $search);
		$this->assign('dateline', array('start_time'=>$param['start_time'], 'end_time'=>$param['end_time']));
		
		$cate = Activity_Service_Lotterycate::getAllCategory();
		$this->assign('category', $cate);
		
		$this->cookieParams();
		$url = $this->actions['userListUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('total', $total);
	}
	
	/**
	 * 更新中奖人信息
	 */
	public function userAction() {
		$id = $this->getInput('id');
		$info = Activity_Service_Lotteryuser::getUser($id);
		$awards = Activity_Service_Awards::getAll();
		$awardsArray = array();
		foreach ($awards as $val){
			$awardsArray[$val['id']] = $val['award_name'];
		}
		$info['award_name'] = $awardsArray[$info['award_id']];
		$this->assign('info', $info);
	}
	
	public function user_updateAction() {
		$info = $this->getPost(array('id', 'phone_num', 'qq', 'status', 'remark', 'cate_id', 'weixin'));
		$result = Activity_Service_Lotteryuser::updateUser($info, $info['id']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}