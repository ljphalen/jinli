<?php
/*
 * 全部应用
 */
class ClaimAction extends SystemAction
{
	public $model = "apks";	
	
	function _filter(&$map){
		$search = $map = MAP();
		$_search = $_REQUEST['_search'];
		$status = $_search['status'];
		
		$map['status']  = array('neq', 0);
		if (!empty($status))
			$map['status'] = $status;

		$timeStart = $timeEnd = 0;
		if(isset($_search['timeStart']) && $_search['timeStart'])
		{
			$timeStart = strtotime($_search["timeStart"]);
			$map["created_at"] = array('EGT', $timeStart);
		}
		if(isset($_search['timeEnd']) && $_search['timeEnd'])
		{
			$timeEnd = strtotime($_search["timeEnd"]) + 86399;
			$map["created_at"] = array('ELT', $timeEnd);
		}	

		if (!empty($timeEnd) && !empty($timeStart))
		{
			if ($timeEnd < $timeStart)
				$this->error("结束时间不能小于等于开始时间");
			$map["created_at"] = array("between", array($timeStart, $timeEnd));
		}
		
		//联运应用需要同时认领key
		//$map['is_join'] = 2;
		
		unset($map['timeStart']);
		unset($map['timeEnd']);
		
		$_REQUEST['orderField'] = 'id';
		$_REQUEST['orderDirection'] = 'desc';
	}
	
	/**
	 * 下线提示
	 */
	function offedit()
	{
		$apk_id = $_REQUEST['id'];
		$apkinfo = D("Apks")->where ( array ('id' => $apk_id) )->find ();
		$this->assign('vo',$apkinfo);
		$this->display();
	}
	
	/**
	 * 认领下线提示
	 */
	function offgetedit()
	{
		$apk_id = $_REQUEST['id'];
		$apkinfo = D("Apks")->where ( array ('id' => $apk_id) )->find ();
		$apkinfo["union"] = D("Union")->where(array("package"=>$apkinfo["package"], "author_id"=>$apkinfo["author_id"]))
							->getField("id, name");
		$this->assign('vo',$apkinfo);
		$this->display();
	}
	
	/**
	 * 认领下线应用
	 */
	function offget()
	{
		$app_id = intval($_REQUEST['app_id']);
		
		if(empty($app_id) || !$find = D("Apps")->find($app_id))
			$this->error("应用参数丢失，请检查！");
		
		$email = $this->_post("email","trim","");
		empty($email) && $this->error("隶属用户邮箱地址不能为空");
		$reason = $this->_post("reason","trim","");
		
		if(empty($reason))
			$this->error("认领原因不能为空");
		
		//判断此用户是否存在
		$account = D("Dev://Accounts")->getUserByEmail($email);
		empty($account)	&& $this->error("没有此用户");
		
		//判断认领用户和原始用户是否相同
		$appInfo = D("Dev://Apps")->getAppInfoById($app_id);
		$appInfo['author_id']==$account['id'] && $this->error("认领用户和原始用户相同");
	
		//更改应用的隶属关系(认领的应用为认领下线状态)
		$map['app_id'] = $app_id;
		$apkData = array("author_id"=>$account['id'],"author_name"=>$account['email']);
		$res = D("Apks")->data($apkData)->where($map)->save();
		Log::write(D("Apks")->_sql(), "Apks:Claim");
	
		$appData = array('author_id'=>$account['id'],'updated_at'=>time());
		$res = D("Apps")->data($appData)->where(array("id"=>$app_id))->save();
		Log::write(D("Apps")->_sql(), "Apks:Claim");

		if($res)
		{
			//记录认领日志
			$data = array("app_id"=>$app_id,"old_account_id"=>$appInfo['author_id'],"new_account_id"=>$account['id'],
					"new_account_email"=>$email.":".$account['nickname'],"reason"=>$reason);
			$res = D("ClaimLog")->addClaimLog($data);
			
			$old_account = D("Dev://Accounts")->getAccountById($appInfo['author_id']);
			
			//发送给被认领者
			$link = "http://".C('SITE_DEV_DOMAIN')."/apps/index";
			$subject = '【'.C("SMTP.SMTP_NAME").'】应用被认领通知';
			$body = '亲爱的开发者，您好：<br>
						您的应用《'.$find['app_name'].'》已被其他用户认领，原因为：'.$reason.'，如果对认领结果存在疑问可联系客服<br>
						<a href="'.$link.'" >快速进入金立游戏开发者中心</a><br>
						祝您使用愉快！';
			MessageModel::send($appInfo['author_id'], $subject, $body, MessageModel::SEND_ALL);
			
			//发送给成功认领人
			$subject = '【'.C("SMTP.SMTP_NAME").'】应用成功认领通知';
			$body = '亲爱的开发者，您好：<br>
					您已成功认领应用《'.$find['app_name'].'》，可进入我的应用查看<br>
					<a href="'.$link.'" >快速进入金立游戏开发者中心</a><br>
					祝您使用愉快！';
			MessageModel::send($account['id'], $subject, $body, MessageModel::SEND_ALL);
			
			/**
			 * 如果是联运应用，则需要往下执行
			 * add day 0928 shuhai #963
			 */
			$model = D("Union");
			$vo = $model->where(array("package"=>$appInfo["package"], "author_id"=>$appInfo['author_id']))->getField("id", true);
			if(!empty($vo))
			{
				//添加历史记录
				$_model = D('UnionAuthorize');
				$data = array();
				$data['status'] = -9;
				$data['note'] = sprintf("认领操作, from:%s, to:%s\n%s", $old_account['email'], $account['email'], $reason);
				
				foreach ($vo as $union_app_id)
				{
					$data['app_id'] = $union_app_id;

					$_model->create($data);
					$res = $_model->add();
					if(false === $res)
					{
						log::write($_model->_sql(), "Union::Claim");
						log::write($_model->getDbError(), "Union::Claim");
					}
				}

				//获取原公司资料，准备发邮件
				$account_info = D("AccountInfos")->where(array("author_id"=>$account['id']))->find();
				$model->where(array("package"=>$appInfo["package"], "author_id"=>$appInfo['author_id']))
					->data(array("author_id"=>$account['id'], "author"=>$email, "email"=>"", "company_name"=>$account_info['company']))
					->save();
				log::write($_model->_sql(), "Union::Claim");
				log::write($model->_sql(), "Union::Claim");
				
				//发送给被认领者
				$link = "http://".C('SITE_DEV_DOMAIN')."/union";
				$subject = '【'.C("SMTP.SMTP_NAME").'】联运应用被认领通知';
				$body = '亲爱的开发者，您好：<br>
						您的联运应用《'.$appInfo["package"].'》已被其他用户认领，原因为：'.$reason.'，如果对认领结果存在疑问可联系客服<br>
						<a href="'.$link.'" >快速进入金立游戏开发者中心</a><br>
						祝您使用愉快！';

				MessageModel::send($appInfo['author_id'], $subject, $body, MessageModel::SEND_ALL);
				
				//发送给成功认领人
				$subject = '【'.C("SMTP.SMTP_NAME").'】联运应用成功认领通知';
				$body = '亲爱的开发者，您好：<br>
						您已成功认领联运应用《'.$appInfo["package"].'》，可进入我的应用查看<br>
						<a href="'.$link.'" >快速进入金立游戏开发者中心</a><br>
						祝您使用愉快！';
				
				MessageModel::send($account['id'], $subject, $body, MessageModel::SEND_ALL);
			}
			$this->success("认领成功");
		}else{
			$this->error('认领失败');
		}
	}
	
	/**
	 * 认领日志
	 */
	function log()
	{
		$app_id = $this->_get("app_id", "intval", 0);
		$list = D("ClaimLog")->where(array("app_id"=>$app_id))->order(array("id"=>"desc"))->select();
		$this->assign("list", $list);
		$this->display();
	}
	
}