<?php
/**
 * 帐号管理控制器
 * @author jiazhu
 * @version 2013-12-19
 */
class AccountsAction extends SystemAction 
{
	public function _filter(&$map){
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		//$map['id'] = array('egt',2);
	}

	/**
	 * (non-PHPdoc)
	 * @see SystemAction::index()
	 */
	public function index()
	{
		$m = D('Dev://accounts');
		
		$do_action = $_REQUEST['do'];
		//组合map
		$map = array();
		if ($_REQUEST['id'] != '') $map['accounts.id'] = $_REQUEST['id'];
		if ($_REQUEST['status'] != '') $map['account_infos.status'] = $_REQUEST['status'];
		if ($_REQUEST['created_s']) $map['accounts.created_at'][] = array('egt',$_REQUEST['created_s']);
		if ($_REQUEST['created_e']) $map['accounts.created_at'][] = array('elt',$_REQUEST['created_e'].' 23:59:59');
		if ($_REQUEST['audit_admin_name'])
		{
			$admin_list = D('Admin')->where(array('account' => array('like','%'.$_REQUEST['audit_admin_name'].'%')))->select();
			if (is_array($admin_list))
			{
				foreach ($admin_list as $val)
				{
					$admin_arr[] = $val['id'];
				}
				$map['audit_admin_id'] = array('in',$admin_arr);
			}else 
			{
				$map['audit_admin_id'] = array('eq',-1);
			}
		}
		if ($_REQUEST['email'] != '') $map['email'] =array('like','%'.$_REQUEST['email'].'%');
		if ($_REQUEST['company'] != '') $map['account_infos.company'] = array('like','%'.$_REQUEST['company'].'%');
		if ($_REQUEST['contact'] != '') $map['account_infos.contact'] = array('like','%'.$_REQUEST['contact'].'%');
		$count =  $m->join(' account_infos ON accounts.id = account_infos.account_id')->where($map)->count ( '*' );
		//var_dump($m->getlastsql());
		if ($count > 0) {
			
			/*
			 * 数据导出断点
			 */
			if ($_REQUEST['do_export'])
			{
				$this->do_export($map);
			}
			
			//数据统计
			$group_data = $m->field("account_infos.status,count('account.id') as nums ")->join(' account_infos ON accounts.id = account_infos.account_id')->group('account_infos.status')->where($map)->select();
			//var_dump($group_data);exit;
			$this->assign('gruop_data',$group_data);
			
			import ( "ORG.Util.Page" );
			
			//创建分页对象
			if(!empty( $_REQUEST['numPerPage'] ))
			{
				$listRows = intval($_REQUEST ['numPerPage']) ? intval($_REQUEST ['numPerPage']) : C('PAGE_LISTROWS');
				Cookie::set("numPerPage", intval($_REQUEST ['numPerPage']));
			}
			if(Cookie::get("numPerPage") > 0)
			{
				$listRows = Cookie::get("numPerPage");
				Cookie::set("numPerPage", $listRows);
			}
			$_GET[C('VAR_PAGE')] = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
			$listRows = empty($listRows) ? C('PAGE_LISTROWS') : $listRows;
			
			$p = new Page ( $count, $listRows );
		$voList = $m->join(' account_infos ON accounts.id = account_infos.account_id')->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('accounts.id DESC')->select();
		//var_dump($m->getlastsql(),$p->firstRow . ',' . $p->listRows);
			//追加信息where
			if (is_array($voList))
			{
				foreach ($voList as $key => $val)
				{
					//审核人
					if ($val['audit_admin_id'] > 0)
					{
						$admin_info = D('admin')->find($val['audit_admin_id']);
						$voList[$key]['audit_admin_name'] = $admin_info['account'];
					}
					
				}
			}
		
		//var_dump($m->getLastSql(),$list);
			
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			
			//分页显示
			$page = $p->show ();
			
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $listRows );
		$this->assign ( 'currentPage', !empty($_GET[C('VAR_PAGE')])?$_GET[C('VAR_PAGE')]:1);
			
		Cookie::set ( '_currentUrl_', __SELF__ );
		$this->assign ('map', $map);
		if ($do_action == 'list_check')
		{
			$this->display ('list_check');
		}else 
		{
			$this->display ();
		}
		
		return;
	}
	
	
	public function do_export($map)
	{
		$m = D('Dev://accounts');
		$voList = $m->join(' account_infos ON accounts.id = account_infos.account_id')->where($map)->order('accounts.id DESC')->select();
		if (empty($voList)) $this->error('数据为空');
		$data[0] = array('ID', '注册日期', '审核日期' ,	'类型', '注册帐号（邮箱）', 	'审核状态', 	'审核人', '公司名称', '联系人','手机号', '营业执照注册号', '税务号');
		foreach ($voList as $key => $val)
		{
			$admin_info = D('admin')->find($val['audit_admin_id']);
			$data[$key+1] = array(
				$val['id'],
				$val['created_at'],
				$val['audit_at'],
				audit_admin_id>  0?'修改':'新增',
				$val['email'],
				AccountinfoModel::getStatus($val['status']),
				$admin_info['account'],
				$val['company'],
				$val['contact'],
				$val['phone'],
				$val['passport_num'],
				$val['tax_number'],						
			);
		}
		//var_dump($data);exit;
		import('ORG.Util.PhpExcel',LIB_PATH);
		$xls = new PhpExcel ( 'UTF-8', false, 'My Sheet' );
		$xls->addArray ( $data );
		$cname = 'accounts'. date ( 'YmdHis' );
		$xls->generateXML ( $cname );
		exit();
	}
	
	
	public function _before_index() 
	{
		$status_list = AccountinfoModel::getStatus();
		$this->assign('status_list',$status_list);
	}
	
	public function search()
	{
		$status_list = AccountinfoModel::getStatus();
		$this->assign('status_list',$status_list);
		$this->display();
	}
	
	/**
	 * 审核详情页
	 */
	public function audit()
	{
		$id = $this->_get('id');
		$accounts = D('Dev://accounts')->find($id);
		$account_infos = D('Dev://account_infos')->where(' account_id ='.$id)->find();
		$this->assign('accounts',$accounts);
		$this->assign('account_infos',$account_infos);
		$this->display();
	}
	
	/**
	 * 审核操作
	 */
	public function auditDo()
	{
		$id = $this->_get('id');
		$accounts = D('Dev://accounts')->find($id);
		
		$audited_list = AuthlogModel::getAudited();
		$reason_list = AuthlogModel::getReason();
		$this->assign('audited_list',$audited_list);
		$this->assign('reason_list',$reason_list);
		$this->assign('accounts',$accounts);
		$this->display();
	}
	
	/**
	 * 审核结果保存
	 */
	public function auditSave()
	{
		$id = $this->_post('id');
		$audited = $this->_post('audited');
		$remarks = $this->_post('remarks');
		
		//$account = D('Dev://Accounts')->find($id);
		$account_info = D('Dev://Accountinfo')->getAccInfo($id);
		$contact_email = $account_info['contact_email'];
		//修改住表
		$accounts_data = array(
			'audit_at' => date('Y-m-d H:i:s'),
			'audit_admin_id' => $_SESSION['authId'],
		);
		if ($audited == AuthlogModel::AUDITED_YES)
		{
			$accounts_data['status'] = AccountsModel::STATUS_PASS;
		}
		D('Dev://Accounts')->updateAccounts($id,$accounts_data);
		
		//修改infos表
		$info_data = array(
			'status' => $audited == AuthlogModel::AUDITED_YES?AccountinfoModel::STATUS_SUC:AccountinfoModel::STATUS_NOT,
			'updated_at' => date('Y-m-d H:i:s'),
		);
		D('Dev://Accountinfo')->updateAccInfo($id,$info_data);
		
		
		//入审核日志
		$log_data = array(
			'admin_id' => $_SESSION['authId'],
			'account_id' => $id,
			'audited' =>$audited,
			'dateline' => time(),
			'remarks' => AuthlogModel::getReason($remarks),
		);	
		if ($remarks == 100000)
		{
			$log_data['remarks'] = $this->_post('reason_content');
		}
		D('Authlog')->data ( $log_data )->add ();

		//发送邮件
		$subject = '【'.C("SMTP.SMTP_NAME").'】帐号审核通知';
		if ($audited == AuthlogModel::AUDITED_YES)
		{
			$link =  "http://" . C ( "SITE_DEV_DOMAIN" ).U('Apps/index');
			$body = '亲爱的开发者，您好：<br>
						您提交的资料审核通过。可前往提交应用和查看应用审核状态<br>
						<a href="'.$link.'" >快速进入（进入我的应用）</a><br>
						祝您使用愉快！';
		}else 
		{
			$link =  "http://" . C ( "SITE_DEV_DOMAIN" ).U('User/info');
			$body = '亲爱的开发者，您好：<br>
						您提交的资料审核不通过，原因如下：<br>
						'.$log_data['remarks'].'<br>
						请重新提交资料。<br>
						<a href="'.$link.'" >快速进入（进入帐户资料）</a><br>
						祝您使用愉快！';
		}

		try {
			smtp_mail ( $contact_email, $subject, $body );
		} catch (Exception $e) {
			$error_msg = $e->getMessage();
			Log::write($error_msg, Log::ERR);
		}
		
		$this->success('操作成功',"closeCurrent");
	}
	
	/**
	 * 用户详细资料
	 */
	public function details()
	{
		$id = $this->_get('id');
		$do = $this->_get('do');
		$accounts = D('Dev://accounts')->find($id);
		$account_infos = D('Dev://account_infos')->where(array("account_id"=>$id))->find();
		$this->assign('accounts',$accounts);
		$this->assign('account_infos',$account_infos);
		$this->assign('do',$do);
		$this->display();
	}

	/**
	 * 资料修改保存
	 * 
	 */
	public function updateDetails()
	{
		$id = $this->_post('id');
		$data = array(
				'company' => $this->_post('company'),
				'passport_num' => $this->_post('passport_num'),
		);
		$uploadList = Helper("Upload")->_upload("user");
		if (is_array ( $uploadList )) {
			$data['company_passport'] = $uploadList[0]['filepath'];
		}
		
		if(empty($data["company"]) || empty($data["passport_num"]))
			$this->error("数据不能为空");
		
		D('Dev://Accountinfo')->updateAccInfo($id, $data);
		$this->success('操作成功', "closeCurrent");
	}
	
	/**
	 * 用户详情
	 */
	public function show()
	{
		$id = $this->_get('id');
		$accounts = D('Dev://accounts')->find($id);
		$account_infos = D('Dev://account_infos')->where(array("account_id"=>$id))->find();
		$this->assign('accounts',$accounts);
		$this->assign('account_infos',$account_infos);
		$this->display();
	}
	
	/*
	 * 统计
	 */
	public function stat()
	{
		$max_day = 30;
		$d_accounts = D('Dev://Accounts');
		
		$date_arr = $reg_count = $check_count = array();
		
		for ($i = 0; $i < $max_day; $i++)
		{
			$now_date = Date('Y-m-d',mktime(0,0,0,date('m'),Date('d')-$i));
			$date_arr[$i] = '"'.$now_date.'"';
			
			//注册数
			$reg_count[$i] = $d_accounts->where('left(`created_at`,10)="'.$now_date.'"')->count();
			
			//审核数
			$check_count[$i] = $d_accounts->join(' account_infos ON accounts.id = account_infos.account_id')->where('account_infos.status='.AccountinfoModel::STATUS_SUC.' and left(`audit_at`,10)="'.$now_date.'"')->count ( '*' );
			
			//通过率
			$pass_rate[$i] = $reg_count[$i]==0?0:floor($check_count[$i]*100/$reg_count[$i]);
		}
		//var_dump($date_arr,$reg_count,$check_count);exit;
		$this->assign('date_arr',$date_arr);
		$this->assign('reg_count',$reg_count);
		$this->assign('check_count',$check_count);
		$this->assign('pass_rate',$pass_rate);
		$this->display();
		 
		
	}
}