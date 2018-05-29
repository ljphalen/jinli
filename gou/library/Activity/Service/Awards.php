<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * $Id$
 * @author huangsg
 *
 */
class Activity_Service_Awards extends Common_Service_Base {


	private static $lotteryNum = 1000;

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

	public static function getAvailableAwards($cate_id){
		//可参与抽奖的奖品
		list(,$awardAll) = Activity_Service_Awards::getsBy(array('cate_id'=>$cate_id));
		$awardList = array();
		foreach ($awardAll as $key=>$val){
			if ($val['total'] > $val['winners'] && $val['probability'] != 0){
				$awardList[] = $val;
			}
		}
		if(empty($awardList)) return false;
		//对奖品数组按照中奖概率进行升序排序
		$awardList_new = self::array_sort($awardList, 'probability', 'asc');
		//根据中奖概率确定中奖区间
		foreach ($awardList_new as $key=>$val){
			if ($key == 0){
				$awardList_new[$key]['min'] = 1;
				$awardList_new[$key]['max'] = $val['probability'] * self::$lotteryNum;
			} else {
				$awardList_new[$key]['min'] = $awardList_new[$key-1]['max'] + 1;
				$awardList_new[$key]['max'] = $awardList_new[$key-1]['max'] + $val['probability'] * self::$lotteryNum;
			}
		}
		return $awardList_new;
	}

	// 递归抽奖
	public static function getLottery($awardList){
		$randomNum = rand(1, self::$lotteryNum);
		$winner['awardId'] = 0;
		foreach ($awardList as $key=>$val){
			if ($randomNum >= $val['min'] && $randomNum <= $val['max']) {
				$winner['awardId']= $val['id'];
				$winner['award_name'] = $val['award_name'];
				$winner['sort'] = intval($val['sort']);
				return $winner;
			}
		}

		if (empty($winner['awardId']) && !empty($awardList)){
			self::getLottery($awardList);
		}
	}
	/**
	 * 对二维数组进行排序,键值对应关系会被重置.
	 * @param array $array
	 * @param string $key
	 * @param string $type
	 * @return boolean|multitype:array
	 */
	public static function array_sort($array, $key, $type="asc"){
		if (empty($array) || empty($key)){
			return false;
		}

		$keyValue = $newArray = array();
		foreach ($array as $k=>$v){
			$keyValue[$k] = $v[$key];
		}

		$type == 'asc' ? asort($keyValue) : arsort($keyValue);
		reset($keyValue);
		foreach ($keyValue as $k=>$v){
			$newArray[] = $array[$k];
		}

		return $newArray;
	}

	/**
	 * 通过条件获取（单条）
	 * @param array $condition
	 * @param array $sort
	 * @return bool|mixed
	 */
	public static function getBy($condition,$sort=array()){
		return self::_getDao()->getBy($condition,$sort);
	}

	/**
	 * 通过条件获取（多条）
	 * @param array $condition
	 * @param array $sort
	 * @return bool|mixed
	 */
	public static function getsBy($condition,$sort=array()){
		$rows  =  self::_getDao()->getsBy($condition,$sort);
		$total =  self::_getDao()->count($condition);
		return array($total, $rows);
	}

	/**
	 * 获取错误的数据。奖品全部中出，但是中奖概率不为0
	 */
	public static function getWrongAward(){
		
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
		$data['probability'] = $data['probability'] / 100;
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
	 * @param integer   $id          中奖的奖品ID
	 * @param string    $imei        UID/IMEI
	 * @param array     $awards      参与抽奖的奖品数数组
	 * @param integer   $cate_id     抽奖活动
	 * @return bool|int
	 */
	public static function updateAfterWin($id, $imei, $mobile, $nickname, $awards, $cate_id){
		if (empty($id) || empty($imei) || empty($awards)) return false;
		$awardNum = count($awards);

		try {
			parent::beginTransaction();
			
			self::_getDao()->increment('winners', array('id'=>$id));	//更新中奖人数
			$winnerCode = Activity_Service_Lotteryuser::addUser(        //记录中奖用户信息
				array('award_id' => $id, 'imei' => $imei, 'phone_num' => $mobile, 'weixin' => $nickname, 'status' => 1, 'cate_id' => $cate_id, 'remark' => '')
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
	 * 获取概率总和
	 * @return float
	 */
	public static function getProbabilityCount($cate_id){
		return self::_getDao()->getProCount($cate_id);
	}
	
	/**
	 * 参数过滤
	 *
	 * @param array $data
	 * @return array
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['award_name'])) $tmp['award_name'] = $data['award_name'];
		if(isset($data['probability'])) $tmp['probability'] = $data['probability'];
		if(isset($data['total'])) $tmp['total'] = $data['total'];
		if(isset($data['winners'])) $tmp['winners'] = $data['winners'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['cate_id'])) $tmp['cate_id'] = $data['cate_id'];
		return $tmp;
	}
	
	/**
	 * @return Activity_Dao_Awards
	 */
	private static function _getDao() {
		return Common::getDao("Activity_Dao_Awards");
	}
	
	/**
	 * @return Activity_Dao_Lotteryuser
	 */
	private static function _getDaoSub() {
		return Common::getDao("Activity_Dao_Lotteryuser");
	}
}