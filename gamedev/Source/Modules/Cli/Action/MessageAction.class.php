<?php
class MessageAction extends CliBaseAction
{
	private $send_max_num = 10;	//每次最多发送条数
	
	/**
	 * 与账号相关的（重置密码、审核注册账号）——注册邮箱
	 * 与应用相关的（审核应用，上下线收到邮件，发布消息）——联系人邮箱
	 */
	public function send()
	{
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
					while (1)
					{
						$user_map = array(
							//发送到所有的联系人邮箱
							//'status' => AccountinfoModel::STATUS_SUC,
							'id' => array('GT',$account_id),
						);
						$user_list = $accounts_m->where($user_map)->order('id asc')
							 					->field("account_id as id, contact_email as email")
							 					->limit(0,100)
							 					->select();

						if (! empty ( $user_list ))
						{
							foreach ( $user_list as $user_key => $user_val ) {
								$this->_sendMessage ( $val, $user_val );
							}
							
							$account_id = $user_val ['id'];
							// 更新发送到id
							$message_update = array (
									'state' => MessageModel::STATE_LOAD,
									'now_account' => $user_val ['id'],
									'send_time' => time () 
							);
							$message_m->where ( array("id"=>$val['id']) )->save ( $message_update );
						} else {
							break;
						}
					}
				}else   //发送到导入用户
				{
					$user_list = explode ( ',', $val ['receiver_account'] );
					if (! empty ( $user_list ))
					{
						foreach ( $user_list as $user_key => $user_id )
						{
							$user_val = $accounts_m->field("account_id as id, contact_email as email")->find ( $user_id );
							$this->_sendMessage ( $val, $user_val );
						}
					}
				}
				/*
				 * 更新消息发送状态
				 */
				//更新发送到id
			 	$message_update = array(
		 								'state' => MessageModel::STATE_COMPLATE,
										'now_account' => $user_val['id'], 
										'send_time' => time() 
									);
			 	$message_m->where(array("id"=>$val['id']))->save($message_update);
				
			}
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
 			//var_dump(D('Dev://AccountMessage')->getLastSql());exit;
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