<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务子类[返利活动]：登录客户端
 * @author fanch
 *
 */
class Task_Tasks_LoginClient extends Task_Tasks_Base{ 
	
	/**
	 * 消息模板
	 */
	protected function onCreateMessage($good, $data){
	    $time = Common::getTime();
		$msgTemplate = array(
				'Acoupon'=>array(
				        'type' =>  103,
				        'top_type' =>  100,
				        'totype' =>  1,
				        'status' =>  0,
				        'start_time' =>  $time,
				        'end_time' =>  strtotime('2050-01-01 23:59:59'),
						'title' => '您获得'. $data['denomination'] . 'A券',
						'msg' => '恭喜，您已完成' . $data['task_name'] . '活动，获得' . $data['denomination'] . 'A券奖励！请在有效期内使用！',
						'sendInput' => $data['uuid']
				),
		);
		return $msgTemplate[$good];
	}
}