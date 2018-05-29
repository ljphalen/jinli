<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 支付财务审核
 */
class PaycheckController extends Admin_BaseController {
	/**
	 * 个人信息列表
	 */
	public function personallistAction(){
		$limit = 12;
		$pages = 10;
		$page = $this->getInput("page") ? $this->getInput("page") : 1;
		$offset = ($page-1)*$limit;
		$keyword = $this->getInput('keyword');
		$where = ' where 1=1';
		if(!empty($keyword)){
			$where .= ' and a.nick_name like "%'.$keyword.'%"';
		}
		$sql = "select 	a.uid, a.icon, a.nick_name, b.real_name, b.id_number 
		from  admin_user as a  
		left join admin_user_info as b on a.uid = b.uid 
		$where and a.groupid=1 and b.designer_type=0   
		limit $offset, $limit";
		
		$designers = Db_Adapter_Pdo::fetchAll($sql);
		$this->assign('designers', $designers);

		$sql = "select 	count(*) as count  
		from  admin_user as a  
		left join admin_user_info as b on a.uid = b.uid 
		$where and a.groupid=1 and b.designer_type=0 ";
		$count = Db_Adapter_Pdo::fetch($sql);

		$this->showPages($count['count'], $page, $limit, $pages);
		$this->assign('type', 'personal');
		$this->assign("meunOn", "pay_paydesigner_paycheckpersonallist");
	}

	/**
	 * 企业信息列表
	 */
	public function companylistAction(){
		$limit = 12;
		$pages = 10;
		$page = $this->getInput("page") ? $this->getInput("page") : 1;
		$offset = ($page-1)*$limit;
		$keyword = $this->getInput('keyword');
		$where = ' where 1=1';
		if(!empty($keyword)){
			$where .= ' and a.nick_name like "%'.$keyword.'%"';
		}
		$sql = "select 	a.uid, a.icon, a.nick_name, b.real_name, b.id_number 
		from  admin_user as a  
		left join admin_user_info as b on a.uid = b.uid 
		$where and a.groupid=1 and b.designer_type=1   
		limit $offset, $limit";
		
		$designers = Db_Adapter_Pdo::fetchAll($sql);
		//Common::v($designers);

		$this->assign('designers', $designers);

		$sql = "select 	count(*) as count  
		from  admin_user as a  
		left join admin_user_info as b on a.uid = b.uid 
		$where and a.groupid=1 and b.designer_type=1 ";
		$count = Db_Adapter_Pdo::fetch($sql);

		$this->showPages($count['count'], $page, $limit, $pages);

		$this->assign('type', 'company');
		$this->assign("meunOn", "pay_paydesigner_paycheckcompanylist");
	}

	/**
	 * 个人信息
	 */
	public function personalAction(){
		$id = $this->getInput('id');

		$designer = Admin_Service_User::get($id);
		$this->assign('designer', $designer);

		$detail = Admin_Service_Userinfo::getby(array('uid'=>$id));
		$this->assign('detail', $detail);
		//Common::v($detail);

		$this->assign("meunOn", "pay_paydesigner_paycheckpersonal");
	}

	/**
	 * 企业信息
	 */
	public function companyAction(){
		$id = $this->getInput('id');

		$designer = Admin_Service_User::get($id);
		$this->assign('designer', $designer);

		$detail = Admin_Service_Userinfo::getby(array('uid'=>$id));
		$this->assign('detail', $detail);
		//Common::v($detail);

		$this->assign("meunOn", "pay_paydesigner_paycheckcompany");
	}

	/**
	 * 个人设计师提现列表
	 */
	public function personalapplyAction(){
		$limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;

        $offset = ($page - 1) * $limit;

        $where = " where 1=1";
        $keyword = $this->getInput('keyword');
        if (!empty($keyword)) {
            $where .= " and nick_name like '%$keyword%'";
        }
       	
       	$from_field = "from admin_user as a
		left join admin_user_info as b on a.uid = b.uid 
		left join (select * from pay_apply d where d.uid=uid order by created_time desc limit 1) as c on a.uid = c.uid 
		$where and b.designer_type=0 ";
       	// 0: 待审核 1： 审核通过 2: 运营审核失败    5财务审核失败  10财务支付
       	$sql = "select 	a.uid, b.real_name, b.id_number, b.bank, b.branch,
       	b.card_number, c.total, c.sys_income, c.designer_income, c.final_income,   
       	CASE 
		WHEN c.status=1  THEN 3 
		WHEN c.status=10 THEN 2 
		ELSE 1 
		END  as order_field 
		$from_field 
		order by order_field desc 	
		limit $offset, $limit";
		$applys = Db_Adapter_Pdo::fetchAll($sql);

		$from = strtotime(date('Y-m', strtotime('-1 month')));
		$to = strtotime(date('Y-m'));

		foreach($applys as $key=>$apply){
			$applys[$key]['order_field'] = 2;
			//销售额，平台分成， 个人分成
			$uid = $apply['uid'];
			$sql = "select sum(total) as total 
			from pay_order 
			where status=5 and created_time>=$from and created_time<$to and uid=$uid";

			$total = Db_Adapter_Pdo::fetch($sql);
			if(!empty($total['total'])){
				$applys[$key]['total'] = $total['total'];

				//计算平台分成及个人分成
				$income = Theme_Service_PayApply::getIncome($total['total'], 0);
				$applys[$key]['sys_income'] = $income['sys_income'];
				$applys[$key]['designer_income'] = $income['designer_income'];
			} else {
				$applys[$key]['sys_income'] = 0;
				$applys[$key]['designer_income'] = 0;
			}


			//本期提现
			if(!empty($apply['designer_income'])){
				$applys[$key]['cur_cash'] = $apply['designer_income'];
			} else {
				$applys[$key]['cur_cash'] = 0;
			}

			//前期可提现
			$sql = "select sum(total) as total 
			from pay_order 
			where status=5 and created_time<=$from and uid=$uid";
			$total2 = Db_Adapter_Pdo::fetch($sql);
			if(!empty($total2['total'])){
				$pre_income = Theme_Service_PayApply::getIncome($total['total'], 0);
				$applys[$key]['pre_cash'] = $pre_income['designer_income'];
			} else {
				$applys[$key]['pre_cash'] = 0;
			}

			//剩余可提现(本期+前期-申请金额)， 等于0则表示准确
			$applys[$key]['lastest_cash'] = $applys[$key]['pre_cash']+$applys[$key]['designer_income']-$apply['designer_income'];
		}
        $this->assign('applys', $applys);

        $sql = "select count(*) as count $from_field";
		$count = Db_Adapter_Pdo::fetch($sql);
        $this->showPages($count['count'], $page, $limit, $pages);

		$this->assign('type', 'personal');
		$this->assign("meunOn", "pay_paydesigner_paycheckpersonalapply");
	}

	/**
	 * 企业设计师提现列表
	 */
	public function companyapplyAction(){
		$limit = 12;
        $pages = 10;
        $page = $this->getInput("page") ? $this->getInput("page") : 1;

        $offset = ($page - 1) * $limit;

        $where = " where 1=1";
        $keyword = $this->getInput('keyword');
        if (!empty($keyword)) {
            $where .= " and nick_name like '%$keyword%'";
        }
       	
       	// 0: 待审核 1： 审核通过 2: 运营审核失败  3 财务审核成功     5财务审核失败  10财务支付
       	$sql = "select 	a.uid, c.real_name, c.id_number, c.bank, c.branch,
       	c.card_number, a.total, a.sys_income, a.final_income, 
       	a.income 
		from  pay_apply as a
		left join  admin_user as b on a.uid = b.uid 
		left join admin_user_info as c on a.uid = b.uid 
		$where and c.designer_type=0 and a.status=1
		limit $offset, $limit";
		$applys = Db_Adapter_Pdo::fetchAll($sql);
        $this->assign('applys', $applys);

        $sql = "select count(*) as count 
		from  pay_apply as a
		left join  admin_user as b on a.uid = b.uid 
		left join admin_user_info as c on a.uid = b.uid 
		$where and c.designer_type=1 and a.status=1";
		$count = Db_Adapter_Pdo::fetch($sql);
        $this->showPages($count['count'], $page, $limit, $pages);

		$this->assign('type', 'company');
		$this->assign("meunOn", "pay_paydesigner_paycheckcompanyapply");
	}
}
