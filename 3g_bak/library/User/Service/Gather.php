<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

//用户金币表

class User_Service_Gather {

	//添加信息
	public static function add($params) {
		if (!is_array($params)) return false;
		$ret =  self::_getDao()->insert($params);
		if($ret){
			return self::_getDao()->getLastInsertId();
		}
		return ;
	}

	public static function get($id) {
		if (!is_numeric($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	public static function update($params = array(), $id) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->update($params, $id);
	}

	public static function updateBy($params = array(), $where = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->updateBy($params, $where);
	}

	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function count($params){
		 return self::_getDao()->count($params);
	}
	
	public static function getSumScoresInfo($where =array()) {
		return self::_getDao()->getSumScoresInfo($where);
	}

	public static function getSumData($params){
		return self::_getDao()->getSumScoresInfo($params);
	}
	/**
	 *
	 * @param array $goods        商品的信息
	 * @param int   $uid          用户ID
	 * @param int   $goods_number 兑换商品的个数
	 * @param int   $scoreType    冻结积分的动作类型
	 *                            冻结金币并写日志
	 */
	public static function frozenUserScores($goods, $uid, $goods_number = 1, $scoreType = 0) {
		$totalCost = $goods['scores'] * $goods_number; //总共消费的金币
		$userScoreInfo  =  User_Service_Gather::getInfoByUid($uid);
		if ($userScoreInfo['remained_score'] - $totalCost < 0) {//对不起,您的金币不足已兑换该商品!
			return false;
		}
		//更新金币表
		$initScores = $userScoreInfo['remained_score']; //初始金币数
		$userScoreInfo['remained_score'] -= $totalCost;
		$userScoreInfo['affected_score'] = $totalCost;
		$userScoreInfo['frozed_score'] += $totalCost;
		$ret = User_Service_Gather::update($userScoreInfo, $userScoreInfo['id']);
		if (!intval($scoreType)) {
			$category  = User_Service_Category::get($goods['cat_id']);
			$scoreType = $category['score_type'];
		}
		User_Service_Gather::getInfoByUid($uid,true);
		self::_writeLog($uid, $initScores, $userScoreInfo['remained_score'], $scoreType, 3,$goods['id']);
		return $ret ;
	}

	//写积分日志
	private function _writeLog($uid, $beforeScore, $afferScore, $scoreType, $groupType, $goodsId = 0) {
		$affectedScore = bcsub($afferScore, $beforeScore);
		$datas[] = array(
			'uid'            => $uid,
			'group_id'       => $groupType,
			'before_score'   => $beforeScore,
			'after_score'    => $afferScore,
			'affected_score' => $affectedScore,
			'score_type'     => $scoreType ? $scoreType : 101,//冻结积分的动作类型
			'add_time'       => time(),
			'date'           => date('Ymd', time()),
			'fk_earn_id'     => $goodsId,
		);
		return Common_Service_User::score($datas);
	}

	//激活用户中心的用户统计
	public static function getIncreUserAmount($params = array(), $groupBy, $order, $page, $pageSize = 20) {
		if (!is_array($params)) return false;
		$total = self::_getDao()->count(array());
		if ($page < 1) {
			$page = 1;
		}
		$page        = $pageSize * ($page - 1);
		$increAmount = self::_getDao()->getIncreUserAmount($params, $groupBy, $order, $page, $pageSize);
		return $increAmount;
	}

	//当天新增用户数
	public static function dayIncreUserAmount($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->dayIncreUserAmount($params);
	}

	/**
	 * 赠送金币
	 */
	public static function giveScoresToUser($uid = 0, $scores = 0, $scoreType = 201) {
		if (!intval($uid)) return false;
		//$userScoreInfo = User_Service_Gather::getBy(array('uid' => $uid));
		$userScoreInfo  =  User_Service_Gather::getInfoByUid($uid);
		if (empty($userScoreInfo)) return false;
		$before  = $userScoreInfo['remained_score'];
		$remined = $userScoreInfo['remained_score'] + $scores;
		$total   = $userScoreInfo['total_score'] + $scores;
		$res     = User_Service_Gather::updateBy(array(
			'remained_score' => $remined,
			'total_score'    => $total
		), array('uid' => $uid));

		User_Service_Gather::getInfoByUid($uid,true);
		$log     = self::_writeLog($uid, $before, $remined, $scoreType, 2);
		return $res && $log;
	}

	/**
	 * 用户积分变动并写日志
	 *
	 * @param $uid              用户ID
	 * @param $affectedScores   受影响的积分数
	 * @param $scoreType        改变积分的活动类型
	 * @param $groupType        类别(2为产生积分，3为消耗积分)
	 */
	public static function changeScoresAndWriteLog($uid, $affectedScores = 0, $scoreType = 0, $groupType = 3, $goodsId = 0) {
		if (!intval($uid)) return false;
		$scoreInfo     = self::getBy(array('uid' => $uid));
		$reminedScores = $scoreInfo['remained_score'] + $affectedScores;
		$totalScores   = $scoreInfo['total_score'];
		if ($affectedScores > 0) { //如果是获得积分，总积分就要添加
			$totalScores += $affectedScores;
		}
		$ret = self::_handleScoreVierifyRelated($uid, $scoreInfo['id'], $affectedScores, $reminedScores, $totalScores, $scoreType, $groupType, $goodsId);
        User_Service_Gather::getInfoByUid($uid, true);
        return $ret;
	}

	/**
	 *
	 * @param int  $uid            用户ID
	 * @param  int $sid            用户汇总信息ID
	 * @param int  $affectedScores 受影响的积分数
	 * @param int  $remainedScore  剩余积分数
	 * @param int  $totalScores    总共的积分数
	 * @param int  $scoreType      改变积分的活动类型
	 */
	private static function _handleScoreVierifyRelated($uid, $sid, $affectedScores, $remainedScore, $totalScores, $scoreType, $groupType, $goodsId) {
		if (!intval($uid) || !intval($sid)) return -1;
		Common_Service_Base::beginTransaction();
		$p            = array(
			'remained_score' => $remainedScore,
			'affected_score' => $affectedScores,
			'total_score'    => $totalScores,
		);
		$ret          = self::update($p, $sid); //更新积分信息
		$beforeScores = $remainedScore - $affectedScores; // 积分变化前
		$log          = self::_writeLog($uid, $beforeScores, $remainedScore, $scoreType, $groupType, $goodsId); //写日志
		if ($ret && $log) {
			Common_Service_Base::commit();
			return $remainedScore;
		} else {
			Gionee_Service_User::getCurUserInfo(true);
			Common_Service_Base::rollBack();
			return -2;
		}
	}


	/**
	 * 扣除冻结积分
	 * @param unknown $uid
	 * @param unknown $scores
	 */
	public  static function deduceFrozenScores($uid,$scores){
		$scoreMsg = self::getInfoByUid($uid);
		 return User_Service_Gather::update(array('frozed_score'=>$scoreMsg['frozed_score'] - $scores),$scoreMsg['id']);
	}
	/**
	 *
	 * @param unknown $params
	 *
	 * @return boolean|multitype:unknown
	 */

	//	public static function getScratch
	private static function _checkData($params = array()) {
		$fields = array(
			'uid',
			'total_score',
			'remained_score',
			'frozed_score',
			'affected_score',
			'created_time',
			'is_scratch',
			'scratch_num',
			'experience_points'
		);
		return Common::cookData($params, $fields);
	}

	static public function getInfoByUid($uid, $sync = false) {
		$rcKey = 'USER_SCORE_INFO:' . $uid;
		$ret   = Common::getCache()->get($rcKey);
		if (empty($ret) || $sync) {
			$ret = User_Service_Gather::getBy(array('uid' => $uid));
			Common::getCache()->set($rcKey, $ret, 100);
		}
		return $ret;
	}

	/**
	 *
	 * @return User_Dao_Gather
	 */
	private static function _getDao() {
		return Common::getDao("User_Dao_Gather");
	}

}