<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 下载活动页面控制器
 * @author fanch
 *
 */
class MonactController extends Game_BaseController{
	
	public $actions = array(
			'indexUrl' => '/monact/index/',
			'addlogUrl' => '/monact/addlog/',
			'rollUrl' => '/monact/roll/',
			'tjUrl' => '/monact/tj'
	);
	
	
	/**
	 * 抽奖动作
	 */
	public function indexAction(){
		
		$sc = $this->getInput('source');
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		
		$is_mobile = Common::checkMobileRequest();
		$webroot = Common::getWebRoot();
		
		
		if(!$is_mobile)  {
			$src = $webroot . '/api/qrcode/index?p='. urlencode($webroot.'/monact/index/');
			header("Content-Type: text/html; charset=utf-8");
			echo '<head><title>游戏大厅中秋活动  扫一扫 参与精彩活动</title></head>';
			echo '<div style="margin:0 auto;text-align:center;">';
 			echo '<img src='.$src.' alt="QR code" width="250" height="250"/>';
 			echo '<p>扫一扫 参与精彩活动</p>';
 			echo '</div>';
			exit;
		}
		
		//添加访客唯一ID
		$uuid = $this->getUuid();
		if(!$uuid) {
			$this->setUuid();
			$uuid = $this->getUuid();
		}
		
		//抽奖资格判断
		$status = Festival_Service_Monprize::checkChance($uuid);
		//if(!$status) $this->output('0','你已没有抽奖资格啦。');
		
		//生成150个时间点
		
		$quota = array(
				"2014-09-06" => array(150, array('2'=>18, "3"=>132)),
				"2014-09-07" => array(150, array('2'=>18, "3"=>132)),
				"2014-09-08" => array(151, array("1"=>1, '2'=>14, "3"=>136)),
		);
		
		
		
		$time = date('Y-m-d', time());
		$day = date("d", time());
		
		
		$curr_time = Common::getTime();
		

		//* 当前时间小于2014-09-06 00:00:00 或者大于　2014-09-09 00:00:00都不中奖
		if($curr_time < 1409932800 || $curr_time >= 1410192000){  
			$prize = 0;
		} else {
		   if($day == '06'){
			list($num, $config) = $quota['2014-09-06'];
		   } else if($day == '07'){
			list($num, $config) = $quota['2014-09-07'];
		   } else if($day == '08'){
			list($num, $config) = $quota['2014-09-08'];
		   }
		   
		   //开始抽奖
		   $prize = Festival_Service_Monprize::start($uuid, $time, $num, $config);
		}
		

		$time = Common::getTime();
		$sign = substr(md5('monact-'. $prize . '-'. $uuid . '-' . $time), 6, 8). '.' . $time;
		$this->assign('prize', $prize);
		$this->assign('sign', $sign);
		
		
		$this->assign('status', $status);
		$this->assign('source', $this->getSource());
	}

	/**
	 * 写入日志
	 */
	public function addlogAction(){
		$data = $this->getPost(array('prize', 'sign'));
		$uuid = $this->getUuid();
		
		if(!$uuid || !$data['sign']) $this->output('-1','非法请求');
		list($token, $time) = explode('.', $data['sign']);
		$sign = substr(md5('monact-'. $data['prize'] . '-'. $uuid .'-' . $time), 6, 8);
		if($sign != $token) $this->output('-1','非法访问');
		
		$prize_log = array(
				'id' => '',
				'activity_id' => 1,
				'user_id' => $uuid,
				'name' => '',
				'tel' => '',
				'prize' => $data['prize'],
				'status' => 0,
		 );
		  
		  
		 $day = date("Y-m-d", time());
		 $start_time = strtotime($day.' 00:00:00');
		 $end_time = strtotime($day.' 23:59:59');
		 $params = array(
		  		'user_id' => $uuid,
		  		'create_time' => array(
		  				array('>=', $start_time), array('<=', $end_time)
		  		),
		 );
		  
		 //查找该用户当天有没有记录
		 $ret = Festival_Service_Log::getByLog($params);
		  
		 //有抽奖机会的话(当天没有抽过奖)，写入日志
		 if (!$ret) $ret_id = Festival_Service_Log::addLog($prize_log);
		 $this->output('0', '', array('ret_id'=> $ret_id));
	}
	
	/**
	 * 提交抽奖后联系人信息
	 */
	public function rollAction(){
		$data = $this->getPost(array('mobile','prize','name', 'sign', 'ret_id'));
		$uuid = $this->getUuid();
		
		if(!$uuid || !$data['mobile'] || !$data['sign'] || !$data['name']) $this->output('-1','非法请求');
		list($token, $time) = explode('.', $data['sign']);
		$sign = substr(md5('monact-'. $data['prize'] . '-'. $uuid .'-' . $time), 6, 8);
		if($sign != $token) $this->output('-1','非法访问');
		//添加抽奖日志
		
		$ret = Festival_Service_Log::updateLog(array('name'=>$data['name'],'tel'=>$data['mobile']),array('id'=>$data['ret_id']));
		$this->output('0', '', array('status'=> '1'));
	}
	
	/**
	 * get hits
	 * @return boolean
	 */
	public function tjAction(){
	 $url = html_entity_decode(html_entity_decode($this->getInput('_url')));
	 $this->redirect($url);
	}
}
