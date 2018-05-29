<?php
// 后台用户模块
class AuthlogAction extends SystemAction {
	
	protected $_export_config = array(
			'email' => array('注册帐号','getAccountField','callback','{{field_val}},email','account_id'),
			'company' => array('简称','getAcctionInfoField','callback','{{field_val}},company','account_id'),
			'created_at' => array('注册日期','getAccountField','callback','{{field_val}},created_at','account_id'),
			'dateline' => array('审核日期','exdate','function','"Y-m-d H:i:s",{{field_val}}'),
			'audited' => array('审核结果','AuthlogModel::getAudited','function'),
			'remarks' => array('审核备注'),
			'admin_id' => array('审核人','getAdminInfo','callback'),
	); //审核日期 	审核结果 	审核备注 	审核人
	
	public function _filter(&$map){
//		$_search = MAP();
//		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		
		$search = $map = MAP();
		$_search = $_REQUEST['_search'];
		
		$author = $_search['author'];
		if(isset($author) && $author)
		{
			$accounts = D("Dev://Accounts")->getUserByEmail($author);
			$map['account_id'] = $accounts['id'];
		}
		
		$account_id = $this->_get('account_id');
		if ($account_id)
		{
			$map['account_id'] = $account_id;
		}
		
		$account = $_search['account'];
		if(isset($account) && $account)
		{
			$admin_info = D("Admin")->where(array('account'=>$account ))->find();
			if(!empty($admin_info)){
				$map['admin_id'] = intval($admin_info['id']);
			}
		}
		if(isset($_search['timeStart']) && $_search['timeStart']){
			$timeStart = strtotime($_search["timeStart"]." 00:00:00");
			$map['dateline'] = array('EGT',$timeStart);
		}
		if(isset($_search['timeEnd']) && $_search['timeEnd']){
			$timeEnd = strtotime($_search["timeEnd"]." 23:59:59");
			$map["created_at"] = array('ELT',$timeEnd);
		}	
		if($_search['timeStart'] && $_search['timeEnd']){
			if ($timeEnd <= $timeStart) {
				$this->error("结束时间不能小于等于开始时间");
			}
			$map['dateline'] = array("between", array($timeStart, $timeEnd));
		}
		$_REQUEST['orderField'] = 'id';
		$_REQUEST['orderDirection'] = 'desc';

	}

	public function _before_index() {
		/* 使用关联模型  */
		$status_list = AccountinfoModel::getStatus();
		$this->assign('status_list',$status_list);
		
		$audited = AuthlogModel::getAudited();
		$this->assign('audited',$audited);
		
		$_REQUEST['orderField'] = 'id';
		$_REQUEST['orderDirection'] = 'desc';
	}

	public function details()
	{
		$id = $this->_get('id');
		$authlog = D('Authlog')->find($id);
		$accounts = D('Dev://accounts')->find($authlog['account_id']);
		$account_infos = D('Dev://account_infos')->where(' account_id ='.$authlog['account_id'])->find();
		$admin_info = D('Admin')->find($authlog['admin_id']);
		$audited = AuthlogModel::getAudited();
		
		$this->assign('authlog',$authlog);
		$this->assign('accounts',$accounts);
		$this->assign('account_infos',$account_infos);
		$this->assign('audited',$audited);
		$this->assign('admin_info',$admin_info);
		$this->display();
	}
	
	//临时为导出准备
	public function getAccountField($id,$field)
	{
		$res = D('Dev://Accounts')->field($field)->find($id);
		return $res[$field];
	}
	
	public function getAcctionInfoField($account_id,$field)
	{
		$res = D('Dev://AccountInfos')->field($field)->where(array('account_id' => $account_id))->find();
		return $res[$field];
	}
	
	public function getAdminInfo($id)
	{
		$admin_info = D("Admin")->where( array('id'=>$id) )->find();
		return $admin_info['account'];
	}
}