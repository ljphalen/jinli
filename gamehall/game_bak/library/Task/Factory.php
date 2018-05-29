<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-任务工厂
 * @author fanch
 *
 */
class Task_Factory{
	const STATUS = 1;
	
	/**
	 * 根据任务名称创建有效的任务对象
	 * @param obj $event
	 */
	public static function getTaskInstances($event){
		$taskConfig = Common::getConfig('taskConfig','route');
		$taskClassList = $taskConfig[$event->mName];
		$taskList = self::getEffectiveTask($taskClassList);

		$taskInstances = array();
		if($taskList){
		  foreach ($taskList as $task){
			 $taskObj = new $task['taskClass']();
			 $taskObj->setTaskRecord($task['dbRecord']);
			 $taskInstances[] = $taskObj;
		  }
		}
		return $taskInstances;
	}
	
	private static function getEffectiveTask($taskList){
		$time = Common::getTime();
		$taskType = array_keys($taskList);
		$params = array(
				'htype' => array('IN',$taskType),
				'status' => self::STATUS,
				'hd_start_time' => array('<=', $time),
				'hd_end_time' => array('>=', $time),
		);
		$taskItems = Client_Service_Acoupon::getsByAcouponActivities($params);
		$tasks = array();
		if($taskItems){
		    foreach ($taskItems as $item) {
			    $tasks[] = array(
					'dbRecord' =>$item,
					'taskClass' => $taskList[$item['htype']]
			    );
			}
		}
		return $tasks;
	}
}