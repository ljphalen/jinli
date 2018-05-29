<?php
class UnionAction extends SystemAction
{
	public $model = "Union";
	
	//编号 	APPID 	申请时间 	渠道 	注册帐号 	公司名称 	包名称 	应用名称 	类型 	审核状态 税务信息
	protected $_export_config = array(
		'id' => array('编号'),
		'appid' => array('APPID'),
		'created_at' => array('申请时间','applyDate','callback','{{field_val}}','id'),
		'channel' => array('渠道'),
		'email' => array('注册帐号','getAccountField','callback','{{field_val}},email','author_id'),
		'company_name' => array('公司名称'),
		'package' => array('包名称'),
		'name' => array('应用名称'),
		'type' => array('类型','unionConst','callback','type,{{field_val}}'),
		'status' => array('审核状态','unionConst','callback','status,{{field_val}}'),
		'author_id' => array('税务信息','accountStatus','callback'),
	
	);
	
	public function _filter(&$map){
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		
		//按时间搜索
		if(!empty($_search["startDay"]) || !empty($_search["endDay"]))
		{
			$s = empty($_search["startDay"]) ? 0 : strtotime($_search["startDay"]);
			$e = empty($_search["endDay"]) ? 0 : strtotime($_search["endDay"]) + 3600*24;
			$map["created_at"] = array("between", array($s, $e)); 
		}
	}
	
	/**
	 * 审核应用申请，发放ApiKey
	 */
	function authorize()
	{
		$model = D($this->model);
		
		$id = $this->_get("id", "intval", 0);
		if(empty($id) || !$vo = $model->find($id))
			exit("记录不存在");

		$company = array_merge(
					D("Dev://Accountinfo")->where(array("account_id"=>$vo['author_id']))->find(),
					D("Dev://Accounts")->where(array("id"=>$vo['author_id']))->find()
				   );
		
		$this->assign("company", $company);
		$this->assign("vo", $vo);
		$this->display("authorize");
	}
	
	function save_authorize()
	{
		set_time_limit(60);
		
		$model = D($this->model);
		
		$id = $this->_post("id", "intval", 0);
		$status = $this->_post("status", "trim", "-2");
		if(empty($id) || !$vo = $model->find($id))
			$this->error("记录不存在");
		
		//准备审核记录的参数
		$log = array("app_id"	=>$id,
					 "status"	=>$status,
					 "apply_at"	=>empty($vo["updated_at"]) ? $vo["created_at"] : $vo["updated_at"]);
		
		//拒绝请求
		if($status == $model::DENY)
		{
			$note = $this->_post("note", "trim", "");
			$other = $this->_post("other", "trim", "");
			if(empty($note) && empty($other))
				$this->error("请输入拒绝原因");
			
			//更新审核信息
			$model->authorize($id, $model::DENY);
			//添加审核记录
			$log["note"] = empty($note) ? $other : $note;
			D("UnionAuthorize")->log($log);
			
			//发送失败通知
			$this->send_mail_fail($vo, $log["note"]);
		}
		elseif($status == $model::ALLOW)
		{
			$data = array(
					"submit_time"		=> date("YmdHis"),
					"package_name"		=> $vo['package'],
					"company_name"		=> $vo['company_name'],
					"app_name"			=> $vo['name'],
					"type"				=> $vo['type'],
					"channel"			=> $vo['channel'],
			);
			
			$result = helper("ApiKey")->app_apply($data);
			$data = (array)$result;
			if(empty($data) || !isset($data["status"]))
				$this->error("请求ApiKey服务接口时返回数据无法识别");

			if(20000000 != $data["status"])
				$this->error(sprintf("申请ApiKey时出错，%d:%s", $data["status"], $data["description"]));
			
			$data["id"] = $id;
			if($model->save($data))
			{
				//更新审核信息
				$model->authorize($id, $model::ALLOW);
				//添加审核记录
				D("UnionAuthorize")->log($log);
				//税务资料审核状态通过
				D("Dev://AccountTax")->save_tax(array("account_id"=>$vo["author_id"], "status"=>1));
			}
			
			//发送成功通知
			$this->send_mail_success($vo);
		}

		$this->success("审核成功", "closeCurrent");
	}
	
	function authorize_log()
	{
		$model = D($this->model);
		
		$id = $this->_get("id", "intval", 0);
		if(empty($id) || !$vo = $model->find($id))
			$this->error("记录不存在");
		
		$list = D("UnionAuthorize")->where(array("app_id"=>$id))->select();
		
		$this->assign("vo", $vo);
		$this->assign("list", $list);
		$this->display();
	}
	
	function details()
	{
		$account_id = $this->_get("account_id", "intval", 0);
		$this->account = D("Accounts")->where(array("id"=>$account_id))->find();
		$this->account_infos = D("AccountInfo")->where(array("account_id"=>$account_id))->find();
		$this->account_tax = D("AccountTax")->where(array("account_id"=>$account_id))->find();
		$this->display();
	}
	
	protected function send_mail_success($game)
	{
		$link = U("union/key_detail@dev", array("id"=>$game['id']));
		$sendemail = D("Dev://Accounts")->where(array("id"=>$game['author_id']))->getField('email');
		$subject = '【'.C("SMTP.SMTP_NAME")."】{$game['name']}游戏联运申请通过";
		$body = "亲爱的开发者，您好：<br>";
		$body .= "&nbsp;&nbsp;&nbsp;&nbsp;您在金立游戏开发者平台申请的Key已经审核通过，应用信息：<br>";
		$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;包名称：".$game['package'];
		$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;应用名称：".$game['name'];
		$body .= "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=".$link.">快速进入我的应用</a>";
		$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;祝您使用愉快！";
		
		smtp_mail ( $sendemail, $subject, $body );
	}
	
	protected function send_mail_fail($game, $note)
	{
		$link = U("union/key_detail@dev", array("id"=>$game['id']));
		$sendemail = D("Dev://Accounts")->where(array("id"=>$game['author_id']))->getField('email');
		$subject = '【'.C("SMTP.SMTP_NAME")."】{$game['name']}游戏联运申请不通过";
		$body = "亲爱的开发者，您好：<br>";
		$body .= "&nbsp;&nbsp;&nbsp;&nbsp;您在金立游戏开发者平台的{$game['name']}联运申请未通过审核，可能的原因如下：<br>";
		$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;{$note}";
		$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;如有疑问，请联系工作人员。";
		$body .= "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;<a href=".$link.">快速进入我的应用</a>";
		$body .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;祝您使用愉快！";
		
		smtp_mail ( $sendemail, $subject, $body );
	}
	
	/*
	 * 为导出准备的申请时间
	 */
	public function applyDate($id)
	{
		$vo = D($this->model)->find($id);
		$time = $vo['updated_at']?$vo['updated_at']:$vo['created_at'];
		return date('Y-m-d H:i:s',$time);
	}
	
	public function unionConst($field,$val)
	{
		$model = D($this->model);
		if ($field == 'type')
		{
			return $model->_type[$val];
		}else 
		{
			return $model->_status[$val];
		}
	}
	
	public function accountStatus($author_id)
	{
		$Accountinfo = D("Dev://AccountTax");
	  return $Accountinfo->status_to_string($author_id);
	}
}