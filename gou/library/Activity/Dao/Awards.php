<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Activity_Dao_Awards extends Common_Dao_Base {
	
	protected $_name = 'gou_lottery_awards';
	protected $_primary = 'id';

	/**
	 * 获取概率总和
	 * @return string
	 */
	public function getProCount($cate_id){
		$sql = sprintf('SELECT SUM(probability) FROM %s WHERE cate_id=%s',$this->getTableName(), $cate_id);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 更新奖品概率
	 * @param float $averageNum
	 * @return mixed
	 */
	public function updateProbability($averageNum, $cate_id){
		$sql = "UPDATE ".$this->_name." SET 
				probability=probability+".$averageNum."
				WHERE total>winners
					AND probability<>0
				AND cate_id=" . $cate_id;
		return Db_Adapter_Pdo::execute($sql);
	}
	
	/**
	 * 将奖品全部中出的数据的中奖概率设置为0
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public function updateProbabilitySub($id){
		$sql = "UPDATE ".$this->_name." SET probability=0 
				WHERE ".$this->_primary."=" . $id;
		return Db_Adapter_Pdo::execute($sql);
	}
	
	/**
	 * 将计算平均概率的余数加到中奖概率最大的奖品上
	 * @param integer $id
	 * @param float $remainder
	 * @return Ambigous <boolean, number>
	 */
	public function updateProRemainder($id, $remainder){
		$sql = "UPDATE ".$this->_name." SET probability=probability+".$remainder." 
				WHERE ".$this->_primary."=" . $id;
		return Db_Adapter_Pdo::execute($sql);
	}
	
}