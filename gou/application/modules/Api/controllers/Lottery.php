<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 抽奖功能接口
 * @author huangsg
 */
class LotteryController extends Api_BaseController {
	
	//抽奖的最大取值
	private $lotteryNum = 1000;
	
	/**
	 * 抽奖
	 */
	public function indexAction(){
	    $system = '';
	    if(strpos(Util_Http::getServer('HTTP_USER_AGENT'), 'iPhone') !== false ) $system = 'ios';
		$imei = Activity_Service_Lotteryuser::getUid($system);
		if (empty($imei)){
			$imei= $this->getInput('imei');
			if (empty($imei)){
				$this->output(-1, 'IMEI号不能为空');
			}
		}
		$cate  = Activity_Service_Lotterycate::getBy(array('status'=>1),array('sort'=>'DESC','id'=>'DESC'));
		$cate_id = $cate['id'];
		$info  = Activity_Service_Lotteryuser::checkUserCanJoinLottery($imei, $cate_id);
		$count = $info['time'];
		$total_score = $info['score'];
		//每日3次免费
		$score = 0;
		if ($count < 3) {
			$score = 0;
		} elseif ($count<= 5) {    //最多抽五次，3次以后需要消耗积分
			$score = 50;
			if (intval($total_score) < $score) $this->output(-1, '积分不够.可以做任务赚取积分');
			$condition = array('uid' => $imei, 'sum_score' => array('>=', $score));
			Gou_Service_ScoreSummary::increment('sum_score', $condition, -$score);
		}else{
			$this->output(-1, '当日抽奖次数已达上限');
		}


		$contactinfo = Activity_Service_Lotteryuser::getUserContactInfo($imei,$cate_id);
		if(empty($contactinfo['phone_num'])){
			$contact = User_Service_Uid::getByUid($imei);
		}else{
			$contact['mobile']   = strval($contactinfo['phone_num']);
			$contact['nickname'] = strval($contactinfo['weixin']);
		}
		//获取可用抽奖奖品
		$awardList_new = Activity_Service_Awards::getAvailableAwards($cate_id);
		if(empty($awardList_new)) $this->output(-1, "奖品已被抽光光");
		// 抽奖
		$winner =  Activity_Service_Awards::getLottery($awardList_new);

		if (!in_array($winner['sort'], array(1, 4))) {
			$winner['code'] = Activity_Service_Awards::updateAfterWin($winner['awardId'], $imei, $contact['mobile'], $contact['nickname'], $awardList_new, $cate_id);
		} else {
			$winner['code'] = 'nil';
		}

		//获取用户积分以及用户抽奖次数，包含当前这次抽奖
		$info   = Activity_Service_Lotteryuser::checkUserCanJoinLottery($imei, $cate_id);
		//更新数据库奖品信息和记录中奖人信息
		$winner['imei']           = $imei;
		$winner['total_score']    = $info['score'];
		$winner['score']          = $score;
		$winner['count']          = $info['time'];
		$winner['mobile']         = strval($contact['mobile']);
		$winner['nickname']       = strval($contact['nickname']);
		$log = array('uid' => $imei, 'award_id' => $winner['awardId'], 'cate_id' => $cate_id, 'score' => $score);
		//添加记录
		Activity_Service_Log::add($log);
		$winner['contact'] = empty($contact['mobile'])?0:1;
		if (empty($winner['code'])|| empty($winner['awardId'])) {
			$this->output(-1, '抽奖失败，请重试.',$winner);
		} else {
			$this->output(0,  '恭喜您，中奖了。', $winner);
		}
	}
	


	/**
	 * 信息填完跳转地址：apk.gou.gionee.com
	 *
	 * 保存中奖人信息
	 */
	public function winnerinfoAction(){
        $system = '';
        if(strpos(Util_Http::getServer('HTTP_USER_AGENT'), 'iPhone') !== false ) $system = 'ios';
        $uid = Activity_Service_Lotteryuser::getUid($system);

		if (empty($uid))$this->output(-1, '非法环境');

		$nickname = $this->getPost('nickname');
		$id = $this->getPost('code');
		$mobile = $this->getPost('mobile');

		if (!empty($mobile) && !preg_match("/^1[3458][0-9]{9}$/",$mobile)) {
			$this->output(1, '请正确填写是手机号码。');
		}

		$data = array('imei' => $uid, 'phone_num' => $mobile, 'weixin' => $nickname);
		$ret = Activity_Service_Lotteryuser::updateUser($data,$id);

		if ($ret){
			$this->output(0, '信息保存成功。');
		} else {
			$this->output(1, '信息保存失败，请重试。');
		}

	}
	

	
}