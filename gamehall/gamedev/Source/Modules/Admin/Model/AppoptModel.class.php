<?php
class AppoptModel extends RelationModel
{
	protected $trueTableName = 'apks';
	protected $tablePrefix = '';
	
	/*
	 * 批量下线（封号）
	 */
	function closeAccount($account_id)
	{
		if(empty($account_id)) {
			return FALSE;
		}
		//同步队列
		$redisHelper = helper('Redis');
		$ONLINE_OFFLINE_REDIS_QUEUE_KEY = C('apk.ONLINE_OFFLINE_REDIS_QUEUE_KEY');
		
		$rs = D("Apks")->where(array("author_id"=>$account_id,"status"=>3))->select();
		if(!empty($rs)){
			$apkidArr = array();
			foreach ($rs as $key=>$value){
				$this->batchOfflineApp($value['app_id']);
				$apkidArr[]=$value['id'];
				//入队操作
				$redisHelper->lPush($ONLINE_OFFLINE_REDIS_QUEUE_KEY, $value['id']);
			}
		}
		if(!empty($apkidArr)){
			$result = $this->batchOfflineApk($apkidArr);
		}
		if($result===false) {
			return false;
		}else {
			return TRUE;
		}
	}
	/*
	 * 批量上线（解禁）
	 */
	function openAccount($account_id)
	{
		if(empty($account_id)) {
			return true;
		}
		//同步队列
		$redisHelper = helper('Redis');
		$ONLINE_OFFLINE_REDIS_QUEUE_KEY = C('apk.ONLINE_OFFLINE_REDIS_QUEUE_KEY');
		
		$rs = D("Apks")->where(array("author_id"=>$account_id,"status"=>'-4'))->select();
		if(!empty($rs)){
			foreach ($rs as $key=>$value){
				$this->onlineApk($value['id']);
				$this->onlineApp($value['app_id']);
				//入队操作
				$redisHelper->lPush($ONLINE_OFFLINE_REDIS_QUEUE_KEY, $value['id']);
			}
		}
		
		if($rs===false) {
			return false;
		}else {
			return TRUE;
		}
	}
	/*
	 * 上线APP
	 */
	function onlineApp($app_id)
	{
		if(empty($app_id)) {
			return FALSE;
		}
		//解禁上线
		$status = 1;
		$rs = $this->execute('UPDATE apps SET status='.$status.' WHERE id='.$app_id);
		if($rs===false) {
			return false;
		}else {
			return true;
		}
	}
	/*
	 * 上线APK
	 */
	function onlineApk($apk_id)
	{
		if(empty($apk_id)) {
			return FALSE;
		}
		//解禁上线
		$status = 3;
		$rs = $this->execute('UPDATE apks SET status='.$status.' WHERE id='.$apk_id);
		if($rs===false) {
			return false;
		}else {
			return true;
		}
	}
	/*
	 * 批量下线APP
	 */
	function batchOfflineApp($app_id)
	{
		if(empty($app_id)) {
			return FALSE;
		}
		//封号下线
		$status = -4;
		$rs = $this->execute('UPDATE apps SET status='.$status.' WHERE id='.$app_id);
		if($rs===false) {
			return false;
		}else {
			return true;
		}
	}
	
	
	/*
	 * 批量下线APK
	 */
	function batchOfflineApk($apk_ids)
	{
		if(empty($apk_ids)) {
			return FALSE;
		}
		if(is_array($apk_ids)) {
			$idstr = implode(',',$apk_ids);
		}
		//封号下线
		$status = -4;
		$rs = $this->execute('UPDATE apks SET status='.$status.' WHERE id in('.$idstr.')');
		if($rs===false) {
			return false;
		}else {
			return true;
		}
	}
		
}