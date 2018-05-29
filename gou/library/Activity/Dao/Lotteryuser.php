<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Activity_Dao_Lotteryuser extends Common_Dao_Base{
	protected $_name = 'gou_lottery_user';
	protected $_primary = 'id';
	
	/**
	 * 抽奖成功后，记录用户提交的qq和电话号码
	 * @param string $weixin
	 * @param string $phone_num
	 * @param string $imei
	 * @return Ambigous <boolean, number>
	 */
	public function updateUserContact($weixin, $phone_num, $imei, $cate_id) {
		$sql = "UPDATE ".$this->_name." SET 
				weixin='".$weixin."',
				phone_num='".$phone_num."'
				WHERE imei='".$imei."'
				AND cate_id='".$cate_id."'";
		return Db_Adapter_Pdo::execute($sql);
	}
	
}