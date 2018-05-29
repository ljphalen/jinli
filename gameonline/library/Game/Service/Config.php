<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Lichanghua
 *
 */
class Game_Service_Config{
	
	/**
	 * 
	 * get all configs
	 */
	public static function getAllConfig() {
		$ret = self::_getDao()->getAll();
		$temp = array();
		foreach($ret as $key=>$value) {
			$temp[$value['game_key']] = $value['game_value'];
		}
		return $temp;
	}

	/**
	 * 
	 * @param unknown_type $key
	 * @return mixed
	 */
	public static function getValue($key) {
		$ret = self::_getDao()->getBy(array('game_key'=>$key));
		return $ret['game_value'];
	}

	/**
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public static function setValue($key, $value) {
		if (!$key) return false;
		return self::_getDao()->updateByKey($key, $value);
	}
	
	/**
	 * 
	 * @return game_Dao_Config
	 */
	private static function _getDao() {
		return Common::getDao("Game_Dao_Config");
	}
	
	/**
	 * 获取不同渠道【web|H5|客户端】排行数据用于展示。
	 * @param string $type [web|h5|client]
	 * @param boolean $filter 是否去掉关闭状态数据
	 */
	public static function getConfigRank($type, $filter = false){
		$allRanks = Common::getConfig('apiConfig','rankKeys');
		//获取不同渠道排行榜配置
		$configKey = $type.'_rank_config';
		$rankConfig = self::getValue($configKey);
		$config = json_decode($rankConfig, true);
		//处理配置文件跟
		$status = $sort = $data = array();
		if($config){
			foreach ($config as $key=>$value){
				$status[$key]=$value['status'];
				$sort[$key]=$value['sort'];
			}
		}
		//组装数据
		foreach ($allRanks as $key=>$value) {
			$tmp = ($status[$key]) ? $status[$key] : 0;
			//要求过滤执行下列条件
			if($filter && ($tmp != 1)) continue;
				
			$data[] = array(
					'key' => $key,
					'name' => $value,
					'sort' => ($sort[$key]) ? $sort[$key] : 0 ,
					'status' => $tmp,
			);
		}
		//排序
		$data = self::_quickSort($data);
		return $data;
	}
	
	/**
	 * 快速排序
	 * @param array $arr
	 * @return array:
	 */
	private static function _quickSort($arr){
		$length = count($arr);
		if($length <= 1) return $arr;
		$base = $arr[0]['sort'];
		$left = $right = array();
		for($i=1; $i<$length; $i++) {
			if($arr[$i]['sort'] > $base) {
				$left[] = $arr[$i];
			} else {
				$right[] = $arr[$i];
			}
		}
		$left = self::_quickSort($left);
		$right = self::_quickSort($right);
		return array_merge($left, array($arr[0]), $right);
	}
}
