<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 免流量用户上传
 * @author master
 *
 */
class TrafficController extends Log_BaseController {
	/**
	 * 免流量上传配置参数
	 */
	public function configAction() {
		$cfg = array(
				'size' => 10,//上传要求apk流量满10M上传一次。
				'isupload' => 1,//上传开关,1
		);
		$this->output2(0, '', $cfg);
	}
	
	/**
	 * 接口说明
	 */
	public function indexAction(){
		exit('Traffic upload v 1.0');
	}
	
	/**
	 * 免流量上报 参数
	 * model=GN305
	 * sp=GN305_1.5.3.a_4.1.1_Android4.1.1_480*800_I01000_wifi_FD34645D0CF3A18C9FC4E2C49F11C510
	 * taskFlag=7180301651415166358004
	 * packageName=com.elextech.happyfarm.am
	 * nickname=136****3537
	 * uname=13632563537
	 * imei=FD34645D0CF3A18C9FC4E2C49F11C510
	 * gameId=108
	 * imsi=89860063191439800136
	 * uploadSize=0.12
	 * gameName=HappyFarm
	 * operator=cmcc
	 * sysVersion=Android4.1.1
	 * version=1.5.3.a
	 * sign=be4725e7bcba800544cbd3f9324fc49f
	 * activityId=1
	 * client_pkg=gn.com.android.gamehall
	 * taskStatus=2
	 * ntype=wifi
	 * gameSize=38.39
	 * brand=GiONEE
	 * uuid=1ABBD714B98D442FA982538DEF9D993C
	 * 
	 */
	public function uploadAction(){
		//可加入redis缓冲机制满10条记录写库一次防止频繁入库操作。
		$request = $this->getInput(array(
				'activityId', 'imsi', 'uuid', 'uname', 'nickname',
				'imei', 'model', 'version', 'sysVersion', 'client_pkg', 
				'operator',	'ntype', 'gameId', 'gameName', 'gameSize',
				'taskFlag', 'uploadSize', 'taskStatus', 'encrypt'));
		if($request['encrypt']!= md5('GameHall-' . $request['activityId'] . $request['gameId'] . $request['imsi'] . $request['taskFlag'])) exit('Access Denied!');
		//写入日志库
		$ret = Glog_Service_TrafficLog::addLog($request);
		if($ret) {
			//更新日志处理进度表数据
			$lastId = Glog_Service_TrafficLog::getLastInsertId();
			$item = Glog_Service_FreedlProgress::getBy(array('table_ymd' => date('Ymd')));
			if(!$item){
				Glog_Service_FreedlProgress::insert(array('table_ymd' => date('Ymd'), 'last_id' => $lastId, 'create_time'=>Common::getTime()));
			} else {
				Glog_Service_FreedlProgress::updateBy(array('last_id' => $lastId), array('table_ymd' => date('Ymd')));
			}
		}
		//返回执行反馈
		$response = array(
				"taskFlag" =>"{$request['taskFlag']}",
				"imsi" => "{$request['imsi']}",
				"gameId" => "{$request['gameId']}",
				"uploadSize" => "{$request['uploadSize']}",
				"result" => $ret ? "success" : "fail",
		);
		$this->output2(0, '', $response);
	}
	
}