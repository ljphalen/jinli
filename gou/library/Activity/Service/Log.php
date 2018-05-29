<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * $Id$
 * @author willa
 *
 */
class Activity_Service_Log extends Common_Service_Base {
	/**
	 * 获取所有奖品数据
	 * @return array:
	 */
	public static function getAll(){
		return self::_getDao()->getAll();
	}

	/**
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @param array $sort
	 * @return array
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取单条奖品数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getAward($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * 获取奖品总数
	 * @return integer
	 */
	public static function getCount($cate_id){
		return self::_getDao()->count(array('cate_id'=>$cate_id));
	}
	
	/**
	 * 添加奖品信息
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function add($data){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		if(empty($data['create_time']))
			$data['create_time']=time();
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改奖品信息
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function update($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		$data['probability'] = $data['probability'] / 100;
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 用户中奖之后更新信息
	 * @param integer $id			中奖的奖品ID
	 * @param string  $imei			IMEI
	 * @param integer $awards    	参与抽奖的奖品数数组
	 * @param integer $cate_id  	参与抽奖的奖品数数组
	 * @return boolean
	 */
	public static function updateAfterWin($id, $imei, $awards, $cate_id){
		if (empty($id) || empty($imei) || empty($awards)) return false;
		$awardNum = count($awards);

		try {
			parent::beginTransaction();
			
			self::_getDao()->increment('winners', array('id'=>$id));	//更新中奖人数
			$winnerCode = Activity_Service_Lotteryuser::addUser(		//记录中奖用户信息
				array('award_id'=>$id, 'imei'=>$imei, 'status'=>1, 'cate_id'=>$cate_id)
			);	

			//奖品中奖概率处理
			$currentAwardInfo = self::getAward($id);
			if ($currentAwardInfo['total'] == $currentAwardInfo['winners']) {	//奖品已被全部中出，分配中奖概率
				//将奖品全部中出的数据的中奖概率设置为0
				self::_getDao()->updateProbabilitySub($id);
				
				$lastIndex = $awardNum - 1;
				if ($lastIndex > 0) {	//还有其他奖品参与抽奖
					$currentAwardPro = $currentAwardInfo['probability']*1000;		//当前概率＊1000
					$remainder = $currentAwardPro%($awardNum-1);					//求余数
					$averageNum = ($currentAwardPro - $remainder)/($awardNum-1);	//平均数
					
					//将平均概率分配到剩余的参与抽奖的奖品上
					self::_getDao()->updateProbability($averageNum/1000, $cate_id);
					
					//将余数追加到中奖概率最大的奖品上
					if (!empty($remainder)){
						self::_getDao()->updateProRemainder($awards[$lastIndex]['id'], $remainder/1000);
					}
				}
			}
			
			parent::commit();
			return $winnerCode;	//数据处理成功，返回中奖编号
			
		} catch (Exception $e) {
			parent::rollBack();
		}
	}
	
	/**
	 * 删除奖品信息
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * 参数过滤
	 *
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['award_id'])) $tmp['award_id'] = $data['award_id'];
		if(isset($data['score'])) $tmp['score'] = $data['score'];
		if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
		if(isset($data['cate_id'])) $tmp['cate_id'] = $data['cate_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 * @return Activity_Dao_Lotterylog
	 */
	private static function _getDao() {
		return Common::getDao("Activity_Dao_Lotterylog");
	}
	
	/**
	 * @return Activity_Dao_Lotteryuser
	 */
	private static function _getDaoUser() {
		return Common::getDao("Activity_Dao_Lotteryuser");
	}
}