<?php

class Async_Cache {

	const INFO = "info";
	const SPL = "spl";
	
	private static $_PREFIX = "async";
	private static $_KEY_SEPARATOR = '_';
	private static $_EXPIRE = 86400;

	private static $_inputTaskKeys = "inputTaskKeys";
	private static $_finishTaskKeys = "finishTaskKeys";
	private static $_taskDataKey = "taskDataKey";
	private static $_taskSPLKey = "taskSPLKey";
	private static $_singleExecuteKeys = "singleExecuteKeys";
	
	public static function inputTask($taskId) {
	    $key = self::getInputTaskKey();
	    $time = self::getCurrentTime();
        $cache = Cache_Factory::getCache();
        $cache->hSet($key, $taskId, $time, self::$_EXPIRE);
	}
	
	public static function saveTaskData($taskId, $task, $method, $args) {
	    $data = array('task_id' => $taskId, 'task' => $task, 'method' => $method, 'args' => json_encode($args));
        $cache = Cache_Factory::getCache();
	    $key = self::getTaskDataKey($taskId);
        $cache->set($key, $data, self::$_EXPIRE);
	}
	
	public static function saveTaskSPL($taskId, $subject, $observer, $args, $result, $useTime) {
	    $data = array('task_id' => $taskId, 'subject' => $subject, 'observer' => $observer, 'args' => json_encode($args), 'result' => $result, 'use_time' => $useTime);
        $cache = Cache_Factory::getCache();
	    $key = self::getTaskSPLKey($taskId);
        $cache->hSet($key, $observer, json_encode($data), self::$_EXPIRE);
	}
	
	public static function finishTask($taskId, $result) {
	    $key = self::getFinishTaskKey();
	    $time = self::getCurrentTime();
	    $data = array('task_id' => $taskId, 'time' => $time, 'result' => $result);
        $cache = Cache_Factory::getCache();
        $cache->listRPush($key, $data, self::$_EXPIRE);
	}

	public static function getFinishedTaskCounts() {
	    $key = self::getFinishTaskKey();
	    $cache = Cache_Factory::getCache();
	    return $cache->listLength($key);
	}
	
	public static function popFinishedTask($count) {
        $result = array();
        if($count <= 0) return $result;
	    $key = self::getFinishTaskKey();
	    $cache = Cache_Factory::getCache();
        $finishList = $cache->listLPops($key, $count);
        foreach ($finishList as $data) {
            $taskId = $data['task_id'];
            $task = self::propTaskData($taskId);
            $inputTime = self::popInputTask($taskId);
            $endTime = $data['time'];
            $task['start_time'] = intval($inputTime / 1000);
            $task['end_time'] = intval($endTime / 1000);
            $task['use_time'] = $endTime - $inputTime;
            $task['result'] = $data['result'];
            
            $tmp[self::INFO] = $task;
            $tmp[self::SPL] = self::popTaskSPLList($taskId);
            $result[] = $tmp;
        }
        return $result;
	}
	
	public static function popExpiredTasks() {
	    $cache = Cache_Factory::getCache();
	    $key = self::getInputTaskKey();
	    $inputTaskList = $cache->hGetAll($key);
        $expiredInput = array();
	    $time = self::getCurrentTime();
	    foreach ($inputTaskList as $taskId => $inputTime) {
	        if($inputTime + 3600000 > $time) {//一小时过期
	            continue;
	        }
	        $expiredInput[$taskId] = $inputTime;
            $cache->hDel($key, $taskId);
	    }
        $result = array();
        foreach ($expiredInput as $taskId => $inputTime) {
            $task = self::propTaskData($taskId);
            $task['start_time'] = intval($inputTime / 1000);
            $task['end_time'] = 0;
            $task['use_time'] = 0;
            $task['result'] = -1;
            
            $tmp[self::INFO] = $task;
            $tmp[self::SPL] = array();
            $result[] = $tmp;
        }
	    return $result;
	}
	
	public static function getCurrentTime() {
	    $time = intval(microtime(true) * 1000);
	    return $time;
	}

	private static function popInputTask($taskId) {
	    $key = self::getInputTaskKey();
	    $cache = Cache_Factory::getCache();
	    $time = $cache->hGet($key, $taskId);
	    $cache->hDel($key, $taskId);
	    return $time;
	}
	
	private static function propTaskData($taskId) {
        $cache = Cache_Factory::getCache();
	    $key = self::getTaskDataKey($taskId);
        $result = $cache->get($key);
	    if($result != false) {
	        $cache->delete($key);
	    }
        return $result;
	}
	
	private static function popTaskSPLList($taskId) {
        $cache = Cache_Factory::getCache();
	    $key = self::getTaskSPLKey($taskId);
	    $splList = $cache->hGetAll($key);
	    if($splList != false) {
	        $cache->delete($key);
	    }
	    $result = array();
	    foreach ($splList as $key => $params) {
	        $result[] = json_decode($params, true);
	    }
	    return $result;
	}
	
	public static function getSingleFlg($taskKey) {
	    $key = self::getSingleExecuteKey();
	    $cache = Cache_Factory::getCache();
	    return $cache->hGet($key, $taskKey);
	}
	
	public static function inputSilgleFlg($taskKey) {
	    $key = self::getSingleExecuteKey();
	    $time = self::getCurrentTime() + 300 * 1000;
	    $cache = Cache_Factory::getCache();
	    $cache->hSet($key, $taskKey, $time, self::$_EXPIRE);
	}
    
	public static function deleteSilgleFlg($taskKey) {
	    $key = self::getSingleExecuteKey();
	    $cache = Cache_Factory::getCache();
	    $cache->hDel($key, $taskKey);
	}
    
	public static function deleteSilgleFlgList() {
	    $key = self::getSingleExecuteKey();
	    $time = self::getCurrentTime();
	    $cache = Cache_Factory::getCache();
	    $list = $cache->hGetAll($key);
	    foreach ($list as $taskKey => $expireTime) {
	        if($expireTime < $time) {
	            $cache->hDel($key, $taskKey);
	        }
	    }
	}
	
	private static function getSingleExecuteKey() {
	    $key = self::$_KEY_SEPARATOR . self::$_PREFIX . self::$_KEY_SEPARATOR . self::$_singleExecuteKeys;
	    return $key;
	}
	
	private static function getInputTaskKey() {
	    $key = self::$_KEY_SEPARATOR . self::$_PREFIX . self::$_KEY_SEPARATOR . self::$_inputTaskKeys;
	    return $key;
	}

	private static function getFinishTaskKey() {
	    $key = self::$_KEY_SEPARATOR . self::$_PREFIX . self::$_KEY_SEPARATOR . self::$_finishTaskKeys;
	    return $key;
	}
	
	private static function getTaskDataKey($taskId) {
	    $key = self::$_KEY_SEPARATOR . self::$_PREFIX . self::$_KEY_SEPARATOR;
	    $key .=  self::$_taskDataKey . self::$_KEY_SEPARATOR . $taskId;
	    return $key;
	}
	
	private static function getTaskSPLKey($taskId) {
	    $key = self::$_KEY_SEPARATOR . self::$_PREFIX . self::$_KEY_SEPARATOR;
	    $key .=  self::$_taskSPLKey . self::$_KEY_SEPARATOR . $taskId;
	    return $key;
	}
	
}
