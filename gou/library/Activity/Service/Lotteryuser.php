<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 中奖用户数据操作
 * $Id$
 * @author huangsg
 *
 */
class Activity_Service_Lotteryuser extends Common_Service_Base {
	

	/**
	 * 获取用户列表
	 * @param int   $page
	 * @param int   $limit
	 * @param array $params
	 * @return array
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * 获取一条数据
	 * @param $id
	 * @return bool|mixe
	 */
	public static function getUser($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加一条数据
	 * @param array $data
	 * @return integer
	 */
	public static function addUser($data) {
		if(!is_array($data)) return false;
		$data['dateline'] = Common::getTime();
		$data = self::_cookData($data);
		self::_getDao()->insert($data);
		return self::_getDao()->getLastInsertId();
	}
	
	/**
	 * 修改用户信息
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateUser($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 抽奖成功后，记录用户提交的qq和电话号码
	 * @param string  $weixin
	 * @param string  $phone_num
	 * @param string  $imei
	 * @param integer $cate_id
	 * @return bool
	 */
	public static function updateUserContact($weixin, $phone_num, $imei, $cate_id){
		parent::beginTransaction();
		try {
			$result = self::_getDao()->updateUserContact($weixin, $phone_num, $imei, $cate_id);
			parent::commit();
			return !empty($result) ? true : false;	
		} catch (Exception $e) {
			parent::rollBack();
		}
	}
	
	/**
	 * 删除用户数据。抽奖启动之后，需要屏蔽该功能
	 * @param intger $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteUser($id) {
		return self::_getDao()->delete(intval($id));
	}
	

	/**
	 * 每日上限5次抽奖，前三次免费
	 * 检查用户是否可以参与抽奖
	 * @param $uid
	 * @param $cate_id
	 * @return bool
	 */
	public static function checkUserCanJoinLottery($uid, $cate_id){
		$create_time_start = array('>',strtotime(date('Y-m-d 00:00:00')));
		$create_time_end = array('<',strtotime(date('Y-m-d 23:59:59')));
		$condition = array('uid' => $uid, 'cate_id' => $cate_id, 'create_time' => array($create_time_start, $create_time_end));
		$time = self::_getLogDao()->count($condition);
		$score_row = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
		$score  = empty($score_row['sum_score'])?0:$score_row['sum_score'];
		return array('time'=>intval($time),'score'=>$score);
	}
	
	/**
	 * 根据IMEI号码获取用户中奖的信息
	 * @param string $imei
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getUserContactInfo($imei, $cate_id){
		return self::_getDao()->getBy(array('imei'=>$imei, 'phone_num'=>array('<>','')),array('dateline'=>'desc'));
	}


	public static function getUid($system){
	    if($system) {
	        $uid = Common::getIosUid();
	    } else {
	        $uid = Common::getAndroidtUid();
	        $imei =  Common::getIMEI();
	        if(empty($uid)) return $imei;
	        self::_getDao()->updateBy(array('imei'=>$uid),array('imei'=>$imei));
	        self::_getLogDao()->updateBy(array('uid'=>$uid),array('uid'=>$imei));
	    }	
		
		return $uid;
	}

	public static function getBy($condition,$sort=array()){
		return self::_getDao()->getBy($condition,$sort);
	}
	/**
	 * 去最近中奖的10个人
	 * @return multitype:
	 */
	public static function getTheLastTop10Winner($cate_id){
		return self::_getDao()->searchBy(0, 9,"cate_id={$cate_id} AND phone_num <>''" ,array('dateline'=>'DESC'));
	}
	
	/**
	 * 参数过滤
	 *
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['award_id'])) $tmp['award_id'] = $data['award_id'];
		if(isset($data['cate_id'])) $tmp['cate_id'] = $data['cate_id'];
		if(isset($data['phone_num'])) $tmp['phone_num'] = $data['phone_num'];
		if(isset($data['qq'])) $tmp['qq'] = intval($data['qq']);
		if(isset($data['weixin'])) $tmp['weixin'] = $data['weixin'];
		if(isset($data['remark'])) $tmp['remark'] = $data['remark'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['dateline'])) $tmp['dateline'] = $data['dateline'];
		return $tmp;
	}
	
	/**
	 * @return Activity_Dao_Lotteryuser
	 */
	private static function _getDao() {
		return Common::getDao("Activity_Dao_Lotteryuser");
	}
	/**
	 * @return Activity_Dao_Lotterylog
	 */
	private static function _getLogDao() {
		return Common::getDao("Activity_Dao_Lotterylog");
	}

}