<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LotteryController extends Ios_BaseController {
	
	/**
	 * 抽奖显示页面
	 */
	public function indexAction(){
		$imei = Activity_Service_Lotteryuser::getUid('ios');
		$cache = Common::getCache();
		//获取最近10位中奖人信息

		//取出当前有效的抽奖活动
		$cat_cond = array('status' => 1,
			'start_time' => array('<=', time()),
			'end_time' => array('>=', time())
		);
		$row = Activity_Service_Lotterycate::getBy($cat_cond, array('sort' => 'DESC', 'start_time' => 'DESC', 'id' => 'DESC'));

		if (empty($imei)||empty($row)){
			echo "<div style='margin:auto;padding:100px 0;width:300px;'>亲，非常抱歉，暂无抽奖活动~<br/>建议亲微信添加：<b>shopping8019</b>，联系小惠参获取最新资讯！</div>";
			exit();
		}
		$cate_id = $row['id'];
		$user   = Activity_Service_Lotteryuser::checkUserCanJoinLottery($imei, $cate_id);

		list(,$awards)  = Activity_Service_Awards::getsBy(array('cate_id'=>$cate_id),array('sort'=>'ASC'));
		$awards_new = Common::resetKey($awards,'id');

		$top10Winners = $cache->get('top10Winners');
		if (!$top10Winners) {
			$top10Winners = Activity_Service_Lotteryuser::getTheLastTop10Winner($cate_id);
			if (!empty($top10Winners)) {
				foreach ($top10Winners as $k=>&$v){
					$v['award_name'] = $awards_new[$v['award_id']]['award_name'];
					$v['nickname']   = $v['weixin'];
					$v['mobile']     = $v['phone_num'];
				}
			}
			$cache->set('top10Winners', $top10Winners, 60);
		}



		$this->assign('top10Winners', $top10Winners);
		$this->assign("imei", $imei);
		$this->assign("awards", $awards_new);
		$this->assign("user", $user);
		$this->assign('title', '狂欢不打烊 春节抽好礼');
	}

}