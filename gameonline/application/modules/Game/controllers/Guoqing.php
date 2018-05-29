<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 下载活动页面控制器
 * @author fanch
 *
 */
class GuoqingController extends Game_BaseController{
	
	public $actions = array(
			'indexUrl' => '/guoqing/index/',
			'tjUrl' => '/guoqing/tj'
	);
	
	public function indexAction(){
		
		$sc = $this->getInput('source');
		$t_bi = $this->getInput('t_bi');
		$intersrc = $this->getInput('intersrc');
		if (!$t_bi) $this->setSource();
		

		$time = Common::getTime();
		$day = date("d", time());
		//$day = '01';
		
		//活动进行的标识，１为正在进行，２为活动未开始，３为活动已结束
		$flag = 1;
	    
		
		$curr_time =  Common::getTime();
		//$curr_time =  strtotime('2014-10-08');
		//活动没开始(当前时间小于10月１号)
		if( $curr_time < 1412092800 ) $flag = 2;
		//活动已经结束(当前时间大于10月8号)
		if( $curr_time >= 1412697600 ) $flag = 3;
		
		
		//积分排行榜
		$params['score'] = array('>', 0);
		list(, $rank) = Festival_Service_GuoQing::getList(1, 10, $params, array('score'=>'DESC','create_time'=>'ASC','id'=>'ASC'));
	    // 组装数据 小于10条，则补全十条
	    $rank_num = count($rank);
	    $num = array('3','5','7','8');
	    if($rank_num < 10 && $rank_num){
	    	$score  =  $rank[0]['score'];
	    	for ( $i = 1; $i <= 10 - $rank_num ;$i ++  ){
	    		$temp = array('id' => $i,
	    				        'uname'=> '1'. $num[rand(0,3)].rand(2, 9).'****'. rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9),
	    				        'score'=> $score,
	    				);
	    		array_unshift($rank,$temp);
	    	}
	    }
	    
	    // 取得题库、
	    $curr_answers =  $this->_datiDate($day);
	    //默认取得第一道
	    $number = 1 ;
	    $is_finish = 0;
	    //登录信息
		$online = Account_Service_User::checkOnline2();
		//登录
		if($online['uuid']) {
			//查找当天的日志
			$log = Festival_Service_GuoQing::getBy(array('uuid'=>$online['uuid']));
			//获取提示游戏以及下载量
			$curr_tips =  $this->_tipsDate($day);
			foreach($curr_tips as $key=>$value){
				$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['id']));
				$curr_tips[$key]['downloads'] = ($value['id'] == '117' ? '4859306' : $info['downloads']);
				$curr_tips[$key]['star'] = ($info['web_star'] ? $info['web_star'] : $value['star']);
			}
			$score = ($log ? $log['score'] : 0);
			//答题日志
			$answer_log = Festival_Service_GuoQingLog::getByLog(array('day_id'=>$day,'uuid'=>$online['uuid']), array('create_time' => 'DESC', 'id' => 'DESC'));
        
	        if($answer_log && $answer_log['answer_id'] < 20 ){
	        	$number = $answer_log['answer_id'] +1 ;
	        }elseif($answer_log && $answer_log['answer_id'] == 20){
	        	$is_finish = 1 ;
	        }
		}

	
		$curr_time = date('Ymd', time());

		//$question = $curr_answers['$number'];
		$this->assign('quetion', $curr_answers[$number]);
		$this->assign('id', $curr_time.$number);
		$this->assign('number', $number);
		$this->assign('is_finish', $is_finish);
		$this->assign('intersrc', $intersrc);
		$this->assign('rank', $rank);
		$this->assign('online', $online);
		$this->assign('flag', $flag);
		$this->assign('curr_tips', $curr_tips);
		$this->assign('score', $score);
		$this->assign('source', $this->getSource());
	}
	
	/**
	 * 获取题目,答题
	 */
	public function answerAction(){
		$data = $this->getPost(array('id', 'number', 'uname', 'daan', 'sign'));
		$online = Account_Service_User::checkOnline2();
		$msg = '';
		//未登录
		if(!$online['uuid']) {
			//返回数据
			exit(json_encode(array(
					'success' => true ,
					'msg' => $msg,
					'data' => array('isLogin'=>'-1')
			)));
		}
		
		$curr_time = date('Ymd', time());
		$time = Common::getTime();
		
		//活动以及结束
		if( $time >= 1412697600 ) {
			//返回数据
			exit(json_encode(array(
					'success' => true ,
					'msg' => $msg,
					'data' => array('end'=>'-1')
			)));
		}
		
		
		
		$day = date("d", time());
		//$day = '01';
		
		//查找当前用户总记录
		 $user = Festival_Service_GuoQing::getBy(array('uuid'=>$online['uuid']));
		
		//当天日志
		$log = Festival_Service_GuoQingLog::getByLog(array('day_id'=>$day,'uuid'=>$online['uuid']), array('create_time' => 'DESC', 'id' => 'DESC'));
		
		//当天用户已答完
		if($user['day_id']== $day && $user['answer_id'] == 20) {
			exit(json_encode(array(
					'success' => true ,
					'msg' => $msg,
					'data' => array(
							'hasChance'=>'false',
							'score'=> $user['score'],
					)
			)));
		}
		
		//获取当天的题库
		$curr_answers =  $this->_datiDate($day);
		
		//计算当天即将下发的题目
		if(!$log) {
			$number = 1;
		} else {
			//小于每天答题的20条记录
			$number = $log['answer_id']+1;
		}
		//签名
		$sign = substr(md5('guoqing-'. $number . '-'. $online['uuid'] . '-' . $time), 6, 8). '.' . $time;
		//当天即将下发的答题数据
		$answer =  $curr_answers[$number];
		$answer['id'] = $curr_time.$number;
		$answer['number'] = $number;
		$answer['sign'] = $sign;
		$answer['score'] = $user['score'] ? $user['score']:0;
		unset($answer['daan']);
			
		//请求的答题内容
		if($data['id'] == '-1' || $data['daan'] == '') {
			exit(json_encode(array(
					'success' =>  true,
					'msg' => $msg,
					'data' => $answer
			)));
		}
		
		//开始答题
		if($data['daan'] && $data['number']){
			//非法请求
			/*if($data['sign']){
				list($token, $curtime) = explode('.', $data['sign']);
				$sign = substr(md5('guoqing-'. $number . '-'. $online['uuid'] .'-' . $time), 6, 8). '.' . $time;
				if($sign != $token) $this->output('-1','非法访问');
			}*/
			
			//判断是否答过的题目 状态status 0 答错，1答对 2 已经答过(为跳跃)
			$dg_log = Festival_Service_GuoQingLog::getByLog(array('day_id'=>$day,'answer_id' => $data['number'], 'uuid'=>$online['uuid']));
			if($dg_log && $user['answer_id'] < 20 && $user['day_id'] == $day){
				$answer['status'] = 2;
				exit(json_encode(array(
						'success' => true ,
						'msg' => $msg,
						'data' => $answer
				)));
			}
			
			
			if(!$dg_log  && $user['day_id'] < $day && $data['number'] != 1){
				exit(json_encode(array(
						'success' => true ,
						'msg' => $msg,
						'data' => $answer
				)));
			}
			
			
			
			//要处理的答题数据
			$number = $data['number'];
			//获取当前的题目
			$curr_answer =  $curr_answers[$number];
			//答题结果处理
			$status = ($curr_answer['daan'] == $data['daan']) ? 1 :0;
			$tscocre = ($curr_answer['daan'] == $data['daan']) ? 1 :0;

			//用户的答题积分
			$score = $user ? $user['score'] + $tscocre : $tscocre;
			//写日志
			$this->_logDate($score, $day, $number, $online['uuid'],$online['uname'], $data['daan'], $status);
			
			//已经答完当天所有题目，返回数据
			if($number == 20){
				exit(json_encode(array(
						'success' => true ,
						'msg' => $msg,
						'data' => array('hasChance'=>'false',
								        'score'=> $score,
								)
				)));
			}
		
			$sign = substr(md5('guoqing-'. $number . '-'. $online['uuid'] . '-' . $time), 6, 8). '.' . $time;
			//答题结果
			if($status) {
				$answer =  $curr_answers[$number+1];
				$answer['id'] = $curr_time.$number;
				$answer['number'] = $number+1;
				$answer['status'] = 1;
				$answer['sign'] = $sign;
				$answer['score'] = $score;
				unset($answer['daan']);
				
				exit(json_encode(array(
						'success' => $status  ? true : false ,
						'msg' => $msg,
						'data' => $answer
				)));
				
			} else {
				//返回当前错误答案数据
				exit(json_encode(array(
						'success' => true ,
						'msg' => $msg,
						'data' => array(
								'id'=> $curr_time.$number,
								'status'=> 0,
								'score' => $score,
								'sign'  => $sign,
								'daan'  =>$curr_answer['daan'],
						)
				)));
			}
		}
	}
	
	
	/**
	 * 获取积分排行榜
	 * 
	 */
	public function getScoreAction(){
		
		$params['score'] = array('>', 0);
		list(, $rank) = Festival_Service_GuoQing::getList(1, 10, $params, array('score'=>'DESC','create_time'=>'ASC','id'=>'ASC'));
		$data = array();
		foreach ($rank as $val){
			$data[] = array('id'   => $val['id'],
						    'uname'=> substr($val['uname'], 0,3).'****'.substr($val['uname'], 7,4),
							'score'=> $val['score'],
				      );	
		}
		
		//组装数据
		$rank_num = count($data);
		if($rank_num < 10 && $rank_num){
			$score  =  $data[0]['score'];
			for ( $i = 1; $i <= 10 - $rank_num ;$i ++  ){
				$temp = array('id' => $i,
						'uname'=> '138****45'.$i.($i-1),
						'score'=> $score,
				);
				array_unshift($data,$temp);
			}
		}
		exit(json_encode(array(
				'success' => true ,
				'msg' => '',
				'data' => $data
		)));
	}
	
	/**
	 * 获取当天的题库
	 * @param unknown_type $day
	 * @return array
	 */
	private static function _datiDate($day){
		$answers =  Common::getConfig("answerConfig", 'answer');
		$quota = array(
				"01" => $answers[0],
				"02" => $answers[1],
				"03" => $answers[2],
				"04" => $answers[3],
				"05" => $answers[4],
				"06" => $answers[5],
				"07" => $answers[6],
		);
		return $quota[$day];
	}
	
	/**
	 * 获取当天提示的游戏
	 * @param unknown_type $day
	 * @return array
	 */
	private static function _tipsDate($day){
		$tips =  Common::getConfig("answerConfig", 'tips');
		$quota = array(
				"01" => $tips[0],
				"02" => $tips[1],
				"03" => $tips[2],
				"04" => $tips[3],
				"05" => $tips[4],
				"06" => $tips[5],
				"07" => $tips[6],
		);
		return $quota[$day];
	}
	
	/**
	 * 写日志
	 * @param unknown_type $log
	 * @param unknown_type $score
	 * @param unknown_type $day
	 * @param unknown_type $number
	 * @param unknown_type $uname
	 * @param unknown_type $daan
	 * @param unknown_type $status
	 */
	private static function _logDate($score, $day, $number, $uname, $uname_un, $daan, $status){
		$user = Festival_Service_GuoQing::getBy(array('uuid' => $uname));
		$time = Common::getTime();
		if($user){
			//如果有日志就更新总积分
			Festival_Service_GuoQing::upBydate(array('score'=>$score,'answer_id'=>$number, 'day_id'=>$day, 'create_time'=>$time),array('uuid'=>$uname));
		} else {
			//没有就添加用户总积分
			$usr_answer = array(
					'id' => '',
					'uuid' => $uname,
					'uname' => $uname_un,
					'day_id' => $day,
					'answer_id' => 1,
					'score' => $score,
					'create_time' => $time,
			);
			Festival_Service_GuoQing::add($usr_answer);
		}
		
		//添加用户答题日志记录
		$answer_log = array(
				'id' => '',
				'uuid' => $uname,
				'uname' => $uname_un,
				'day_id' => $day,
				'answer_id' => $number,
				'score' => ($status ? 1 : 0),
				'create_time' => $time,
				'daan' => $daan,
				'status' => $status,
		);
		Festival_Service_GuoQingLog::addLog($answer_log);
		return true;
	}
	
	/**
	 * get hits
	 * @return boolean
	 */
	public function tjAction(){
	 $url = html_entity_decode(html_entity_decode($this->getInput('_url')));
	 if (strpos($url, '?') === false) {
	 	$url = $url.'?t_bi='.$this->getSource();
	 } else {
	 	$url = $url.'&t_bi='.$this->getSource();
	 }
	 $this->redirect($url);
	}
}