<?php

if(!function_exists('php_cli_msg_thread_shutdown'))
{
	function php_cli_msg_thread_shutdown()
	{
		echo "all job done".PHP_EOL;
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_msg_thread.lock";
		unlink($logLock);
		exit(0);
	}
}

register_shutdown_function('php_cli_msg_thread_shutdown');

class MessageAction extends CliBaseAction
{
	private $send_max_num = 10;	//每次最多发送条数
	
	protected function thread_checker()
	{
		$logPath = realpath(EVENT_LOG_PATH);
		$logLock = $logPath."php_cli_msg_thread.lock";
		if(!is_dir($logPath))
		{
			$this->printf("%s path not found, exit.", EVENT_LOG_PATH);
			exit(0);
		}
		
		if(is_file($logLock))
		{
			$this->printf("MessageAction Thread found, exit.");
			exit(0);
		}
		
		//添加进程文件锁
		touch($logLock);
	}
	
	/**
	 * 与账号相关的（重置密码、审核注册账号）——注册邮箱
	 * 与应用相关的（审核应用，上下线收到邮件，发布消息）——联系人邮箱
	 */
	public function send()
	{
		$this->thread_checker();
		
		//获取要发送通知
		$message_m = D('Dev://Message');
		$account_message = D('Dev://AccountMessage');
		$accounts_m = D('Dev://Accountinfo');
		
		$this->printf("--- message send start ---");
		
		$map = array(
				'type' => MessageModel::TYPE_SYS,
				'state' => MessageModel::STATE_INIT,
		);
		$list = $message_m->where($map)->order('id asc')->limit(0,10)->select();
		if (!empty($list))
		{
			foreach ($list as $key => $val)
			{
				$this->printf("MESSAGE CRONT FIND JOB: %d->%s", $val["id"], $val["title"]);

				//获取接收用户
				if($val['receiver_account'] == MessageModel::RECEIVE_ALL)  //所有用户
				{
					$account_id = $val['now_account'];
					
					//首先把状态改为发送中
				 	$message_update = array(
				 							'state' => MessageModel::STATE_LOAD,
				 							'send_time' => time() 
				 						);
				 	$message_m->where(array("id"=>$val['id']))->save($message_update);
				 	
				 	$total_count = $total_job_count = $accounts_m->count();
				 	$success_send = 0;
					while ($total_count > 0)
					{
						$user_map = array(
							//发送到所有的联系人邮箱
							//'status' => AccountinfoModel::STATUS_SUC,
							'id' => array('GT',$account_id),
						);
						$user_list = $accounts_m->where($user_map)->order('id asc')
							 					->field("account_id as id, contact_email as email")
							 					->limit(0,100)
							 					->order("id asc")
							 					->select();

						if (! empty ( $user_list ))
						{
							foreach ( $user_list as $user_key => $user_val ) {
								$this->_sendMessage ( $val, $user_val );
								
								$this->printf("MESSAGE CRONT SEND: %d->%s", $user_val["id"], $user_val["email"]);

								$account_id = $user_val ['id'];
								// 更新发送到id
								$message_update = array (
										'now_account' => $user_val ['id'],
										'send_time' => time () 
								);
								$message_m->where ( array("id"=>$val['id']) )->save ( $message_update );
								$success_send ++;
							}
						}
						
						$total_count --;
					}
					
					$this->printf("MESSAGE CRONT SEND RESULT : total %s, success %s", $total_job_count, $success_send);
				}else{//发送到导入用户
					$user_list = explode ( ',', $val ['receiver_account'] );
					if (! empty ( $user_list ))
					{
						sort($user_list);
						$success_send = 0;
						foreach ( $user_list as $user_key => $user_id )
						{
							//跳到上次发送的记录
							if($val["now_account"] >= $user_id)
								continue;
							
							$user_val = $accounts_m->field("account_id as id, contact_email as email")->where(array("account_id"=>$user_id))->find ();
							if(!empty($user_val))
							{
								$this->_sendMessage ( $val, $user_val );
								$this->printf("MESSAGE CRONT SEND: %d->%s", $user_val["id"], $user_val["email"]);
								
								//更新消息发送状态
								$message_update = array(
										'now_account' => $user_val['id'],
										'send_time' => time()
								);
								$message_m->where(array("id"=>$val['id']))->save($message_update);
								
								$success_send ++;
							}
						}
					}
					
					$this->printf("MESSAGE CRONT SEND RESULT : total %s, success %s", count($user_list), $success_send);
				}
				
				//任务完成，更新消息发送状态
				$message_update = array(
						'state' => MessageModel::STATE_COMPLATE,
				);
				$message_m->where(array("id"=>$val['id']))->save($message_update);
			}
		}else{
			$this->printf("MESSAGE CRONT RESULT: NO JOB TODO");
		}
		$this->printf("--- message send end ---");
	}
	
	/**
	 * 发送消息
	 * @param array $val message表中信息
	 * @param array $user_val 用户主表信息
	 */
	private function _sendMessage($val, $user_val)
	{
		//发站内信
 		if ($val['send_type'] == MessageModel::SEND_MESSAGE || $val['send_type'] == MessageModel::SEND_ALL)
 		{
 			$message_data = array(
 				'account_id' => $user_val['id'],
 				'message_id' => $val['id'],
 				'add_time' => time(),
 			);
 			D('Dev://AccountMessage')->data($message_data)->add();
 		}
 		
 		//发邮件
 		if ($val['send_type'] == MessageModel::SEND_ALL || $val['send_type'] == MessageModel::SEND_EMAIL)
 		{
 			$sendemail = $user_val['email'];
 			$subject = $val['title'];
 			$body = $val['content'];
 			smtp_mail ( $sendemail, $subject, $body );
 		}
 		return true;
	}
}