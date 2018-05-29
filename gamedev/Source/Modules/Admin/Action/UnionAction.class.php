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
			$e = empty($_search["endDay"]) ? 0 : strtotime($_search["endDay"]) + 86399;
			
			if(!empty($s))
				$map["created_at"] = array('EGT', $s);
			if(!empty($e))
				$map["created_at"] = array('ELT', $e);
			if(!empty($s) && !empty($e))
			{
				if($e < $s)
					$this->error("结束时间不能小于等于开始时间");
				$map["created_at"] = array("between", array($s, $e)); 
			}
		}
		
		if(!empty($_search["channel"]) && $_search["channel"] == 'notall')
			$map["channel"] = array("neq", '游戏大厅');
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
		
		//没有审核通过的注册账号申请的key不可以进行审核的操作
		if(1 != D("Dev://Accountinfo")->where(array("account_id"=>$vo["author_id"], "status"=>AccountinfoModel::STATUS_SUC))->count())
			$this->error("注册账号未审核通过，不能审核");

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
		set_time_limit(120);
		
		$model = D($this->model);
		
		$id = $this->_post("id", "intval", 0);
		$status = $this->_post("status", "trim", "-2");
		if(empty($id) || !$vo = $model->find($id))
			$this->error("记录不存在");
		
		//没有审核通过的注册账号申请的key不可以进行审核的操作
		if(1 != D("Dev://Accountinfo")->where(array("account_id"=>$vo["author_id"], "status"=>AccountinfoModel::STATUS_SUC))->count())
			$this->error("注册账号未审核通过，不能审核");
		
		//准备审核记录的参数
		$log = array("app_id"	=>$id,
					 "status"	=>$status,
					 "apply_at"	=>empty($vo["updated_at"]) ? $vo["created_at"] : $vo["updated_at"]);
		
		//上传文件开始
		$uploadList = Helper("Upload")->_upload("test");
		if (is_array ( $uploadList )) {
			$log['doc_file'] = $uploadList[0]['filepath'];
		}

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
			//税务资料不能为空
			$taxInfo = D("Accountinfo")->where(array("account_id"=>$vo["author_id"]))->find();
			if(empty($taxInfo["tax_number"]) || empty($taxInfo["tax_passport"]))
				$this->error("税务资料不完整，不能审核");
			
			//如果申请联运时没有成功同步公司名
			if(empty($vo['company_name']))
				$vo['company_name'] = $taxInfo["company"];
			
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
			$vo = array_merge($vo, $data);
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
		$id = $this->_get("id", "trim", "0");
		$account_id = $this->_get("account_id", "intval", 0);
		$this->account = D("Accounts")->where(array("id"=>$account_id))->find();
		$this->account_infos = D("AccountInfo")->where(array("account_id"=>$account_id))->find();
		$this->account_tax = D("AccountTax")->where(array("account_id"=>$account_id))->find();
		
		$this->vo = D($this->model)->find($id);
		$this->display();
	}
	
	protected function send_mail_success($key)
	{
		$link = U("union/key_detail@dev", array("id"=>$key['id']));
		
		$account_info = D('Dev://Accountinfo')->getAccInfo($key['author_id']);
		$sendemail = $account_info['contact_email'];
		
		$subject = sprintf("%s【%s】密钥信息", C("SMTP.SMTP_NAME"), $key['name']);
		$body = <<<EEE
亲爱的开发者，您好：<br>
<p>您的游戏【%s，%s】已审核通过，密钥信息如下：</p>
<p>APIKey ：%s<br>
SecretKey ：%s<br>
PublicKey ：%s<br>
PrivateKey：%s</p>
<p>SDK下载地址： http://dev.game.gionee.com/help/sdk.html </p>
<p>你还未设置NotifyURL，请前往设置NotifyURL后将“密钥信息及SDK文档”一键发送至技术邮箱。</p>
<p>如有疑问，请联系：dev.game@gionee.com</p>
EEE;
		$body = sprintf($body,
		$key['name'], $key['package'], $key['api_key'], $key['secret_key'], $key['notify_key'], $key['pay_key']);
		
		smtp_mail ( $sendemail, $subject, $body );
	}
	
	protected function send_mail_fail($game, $note)
	{
		$link = U("union/key_detail@dev", array("id"=>$game['id']));
		
		$account_info = D('Dev://Accountinfo')->getAccInfo($game['author_id']);
		$sendemail = $account_info['contact_email'];
		
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
			return $model->_type[$val];
		else 
			return $model->_status[$val];
	}
	
	public function accountStatus($author_id)
	{
		$Accountinfo = D("Dev://AccountTax");
		return $Accountinfo->status_to_string($author_id);
	}
	
	function offget()
	{
		$model = D($this->model);
		
		$id = $this->_post("id", "intval", 0);
		$email = $this->_post("email", "trim", 0);
		$reason = $this->_post("reason", "trim", 0);
		
		if(empty($reason) || empty($id) || empty($email))
			$this->error('必填字段不能为空');

		if(empty($id) || !$vo = $model->find($id))
			$this->error("要认领的记录不存在");
		
		if(empty($email) || !$account = D("Accounts")->where(array('email'=>$email))->find())
			$this->error("要分配的开发者账号不存在");
		
		if(empty($account['email']) || $email != $account['email'] || $vo['author_id'] == $account['id'])
			$this->error("要分配的开发者账号不合法");
		
		//添加历史记录
		$_model = D('UnionAuthorize');
		$data = array();
		$data['app_id'] = $id;
		$data['status'] = -9;
		$data['note'] = sprintf("认领操作, from:%s, to:%s\n%s", $account['email'], $email, $reason);
		$_model->create($data);
		$res = $_model->add();
		if(false === $res)
		{
			log::write($_model->_sql(), Log::EMERG);
			log::write($_model->getDbError(), Log::EMERG);
			$this->error('系统错误，入库操作未成功');
		}
		
		//获取原公司资料，准备发邮件
		$account_info = D("AccountInfos")->where(array("author_id"=>$account['id']))->find();
		$model->data(array("id"=>$id, "author_id"=>$account['id'], "author"=>$email, "email"=>"", "company_name"=>$account_info['company']))->save();
		
		//发送给被认领者
		$link = "http://".C('SITE_DEV_DOMAIN')."/union";
		$old_account = D("Dev://Accounts")->getAccountAll($vo['author_id']);
		$subject = '【'.C("SMTP.SMTP_NAME").'】联运应用被认领通知';
		$body = '亲爱的开发者，您好：<br>
						您的应用《'.$vo['name'].'》已被其他用户认领，原因为：'.$reason.'，如果对认领结果存在疑问可联系客服<br>
						<a href="'.$link.'" >快速进入金立游戏开发者中心</a><br>
						祝您使用愉快！';
		smtp_mail($old_account['contact_email'], $subject, $body);
		
		//发送给成功认领人
		$account = D("Dev://Accounts")->getAccountAll($account['author_id']);
		$subject = '【'.C("SMTP.SMTP_NAME").'】联运应用成功认领通知';
		$body = '亲爱的开发者，您好：<br>
						您已成功认领应用《'.$vo['name'].'》，可进入我的应用查看<br>
						<a href="'.$link.'" >快速进入金立游戏开发者中心</a><br>
						祝您使用愉快！';
		
		smtp_mail($account['contact_email'], $subject, $body);
		$this->success("认领成功");
	}
	
	function offgetedit()
	{
		$model = D($this->model);
	
		$id = $this->_get("id", "intval", 0);
		if(empty($id) || !$vo = $model->find($id))
			$this->error("记录不存在");
		
		$vo['account'] = D("Accounts")->find($vo['author_id']);
		
		$this->assign("vo", $vo);
		$this->display();
	}
}