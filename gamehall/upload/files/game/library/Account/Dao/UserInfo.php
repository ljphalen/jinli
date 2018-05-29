<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Account_Dao_UserInfo
 * @author fanch
 *
*/
class Account_Dao_UserInfo extends Common_Dao_Base{
	protected $_name = 'game_user_info';
	protected $_primary = 'id';
	
	/**
	 * 减少用户积分专用方法
	 * @param int $points
	 * @param array $params
	 * @return 
	 */
	public function subtractPoints($points, $params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$where .= " AND `points` >= $points";
		$sql = sprintf('UPDATE %s SET `points`= points-%d WHERE %s', $this->getTableName(), $points, $where);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 增加用户积分专用方法
	 * @param int $points
	 * @param array $params
	 * @return
	 */
	public function addPoints($points, $params){
		$where = Db_Adapter_Pdo::sqlWhere($params);
		$sql = sprintf('UPDATE %s SET `points`= points+%d WHERE %s', $this->getTableName(), $points, $where);
		return Db_Adapter_Pdo::execute($sql, array(), false);
	}
	
	/**
	 * 获取指定日期生日的用户
	 * @param int $start
	 * @param int $limit
	 * @param string today
	 * @return 
	 */
	public function getListByBirthday($start = 0, $limit = 10, $today){
		$where =" DATE_FORMAT(birthday, '%m-%d') = '$today' ";
		$sql = sprintf('SELECT * FROM %s WHERE %s LIMIT %d,%d', $this->getTableName(), $where, intval($start), intval($limit));
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 * 统计指定日期生日的用户数量
	 * @param string today
	 * @return
	 */
	public function getCountByBirthday($today){
		$where =" DATE_FORMAT(birthday, '%m-%d') = '$today' ";
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->getTableName(), $where);
		return Db_Adapter_Pdo::fetchCloum($sql, 0);
	}
	
	/**
	 * 取vip排行榜
	 */
	public function getVipRankList() {
		$sql = sprintf('SELECT `uuid`, `nickname`, `avatar`, `vip`, `vip_mon_expr`, `vip_act_expr`, `vip_rank` FROM %s WHERE `vip_rank`>0 order by `vip_rank` LIMIT 100', $this->getTableName());
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**更新排行榜*/
	public function updateVipRank() {
	    $sql = "CREATE TABLE IF NOT EXISTS `tmp_vip_rank` (`uuid` varchar(50) NOT NULL,`rank` int(10) DEFAULT '0',`expr` decimal(36,2) DEFAULT '0.00',`vip` tinyint(1) DEFAULT '0',KEY `uuid` (`uuid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	    $ret = Db_Adapter_Pdo::execute($sql);
	    if(! $ret) {
	        return 1;
	    }
	    
	    $sql = "TRUNCATE TABLE `tmp_vip_rank`;";
	    $ret = Db_Adapter_Pdo::execute($sql);
	    if(! $ret) {
	        return 2;
	    }
	    
	    $sql = "insert into tmp_vip_rank(`uuid`, `rank`, `expr`) (SELECT `uuid`,(@rank:= @rank + 1) as rank, (`vip_mon_expr`+`vip_act_expr`) as expr FROM game_user_info, (SELECT @rank:= 0) tt ORDER BY expr desc, id asc);";
	    $ret = Db_Adapter_Pdo::execute($sql);
	    if(! $ret) {
	        return 3;
	    }
	    
	    $sql = "update tmp_vip_rank set `vip`=1;";
	    $ret = Db_Adapter_Pdo::execute($sql);
	    if(! $ret) {
	        return 4;
	    }
	    
	    $vipExpr = array_reverse(User_Config_Vip::$vipExpr, true);
	    foreach ($vipExpr as $vip => $expr) {
            $sql = "update tmp_vip_rank set `vip`=".$vip." where `vip`=1 and `expr`>=".$expr.";";
    	    $ret = Db_Adapter_Pdo::execute($sql);
    	    if(! $ret) {
    	        return 5;
    	    }
	    }
	    
	    $sql = "UPDATE game_user_info, tmp_vip_rank SET game_user_info.vip_rank = tmp_vip_rank.rank WHERE tmp_vip_rank.uuid = game_user_info.uuid;";
	    $ret = Db_Adapter_Pdo::execute($sql);
	    if(! $ret) {
	        return 6;
	    }
	    
	    $sql = "UPDATE game_user_info, tmp_vip_rank SET game_user_info.vip = tmp_vip_rank.vip WHERE tmp_vip_rank.uuid = game_user_info.uuid and game_user_info.vip<tmp_vip_rank.vip;";
	    $ret = Db_Adapter_Pdo::execute($sql);
	    if(! $ret) {
	        return 7;
	    }
	    
	    return 0;
	}
	
}