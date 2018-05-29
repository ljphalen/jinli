<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Api_Gionee_Coin extends Common_Service_Base{
	
	/**
	 * 积分操作(消耗｜增加)
	 */
	public static function updateCoin($data){
		if (!is_array($data)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			$result = User_Service_CoinLog::addCoinLog(array(
					'out_uid'=>$data['out_uid'],
					'coin_type'=>$data['coin_type'],
					'coin'=> $data['coin'],
					'msg'=>$data['msg'],
					'create_time'=>Common::getTime()
			));
			if (!$result) throw new Exception("Add coin_log fail.", -202);
			
			//更新用户银币
			$ret = Gc_Service_User::updateCoin($data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$ret) throw new Exception("update user coin fail.", -203);
			
				//事务提交
			if($trans) return parent::commit();
			return true;			
		} catch (Exception $e) {
			parent::rollBack();
			Common::log(json_encode($data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 'coin_error.log');
			return false;
		}
	}
	
	/**
	 * 
	 * 冻结积分
	 */
	public static function freeze ($data) {
		if (!is_array($data)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		
		try {
			$log = Gc_Service_FreezeLog::getLogByMark($data['mark']);
			if ($log) throw new Exception("mark is exists.", -205);
			//更新用户银币
			$ret = Gc_Service_User::updateCoin( - $data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$ret) throw new Exception("update user coin fail.", -206);
			
			//更新用户冻结银币
			$ret = Gc_Service_User::updateFreezeCoin($data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$ret) throw new Exception("update user freeze coin fail.", -208);
			
			//记录日志
			$result = Gc_Service_FreezeLog::addFreezeLog(array(
					'out_uid'=>$data['out_uid'],
					'appid'=>$data['appid'],
					'coin_type'=>$data['coin_type'],
					'status'=>1,
					'mark'=>$data['mark'],
					'coin'=> $data['coin'],
					'msg'=>$data['msg'],
					'create_time'=>Common::getTime()
			));
			if (!$result) throw new Exception("Add coin_log fail.", -205);		
				
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			if($trans) parent::rollBack();
			Common::log(json_encode($data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 'coin_error.log');
			return false;
		}
	}
	
	/**
	 *
	 * 解冻积分
	 */
	public static function unfreeze ($mark, $unfreeze_type) {
		if (!$mark) return false;
	
		//开始事务
		$data = Gc_Service_FreezeLog::getLogByMark($mark);
		if(!$data) return false;
		//已解冻
		if($data['status'] == 2) return false;
		
		$trans = parent::beginTransaction();
		try {
			//更新用户冻结银币
			$info = Gc_Service_User::updateFreezeCoin(-$data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$info) throw new Exception("update user freeze coin fail.", -208);
	
			//直接消耗
			if($unfreeze_type == 1){
				// 消耗 记录日志
				$result = Gc_Service_CoinLog::addCoinLog(array(
						'out_uid'=>$data['out_uid'],
						'coin_type'=>$data['coin_type'],
						'unfreeze_type'=>$unfreeze_type,
						'coin'=> -$data['coin'],
						'msg'=>$data['msg'],
						'create_time'=>Common::getTime()
				));
				if (!$result) throw new Exception("Add coin_log fail.", -202);
			} 
			//返回给用户
			if($unfreeze_type == 2) {
				//返回给用户 更新用户银币
				$ret = Gc_Service_User::updateCoin($data['coin'], $data['out_uid'], $data['coin_type']);
				if (!$ret) throw new Exception("update user coin fail.", -206);
			}
			
			//更新冻结状态
			$freezelog = Gc_Service_FreezeLog::updateStatus(2, $data['mark']);
			if (!$freezelog) throw new Exception("update freezelog status fail.", -207);
			
			//事务提交
			if($trans) return parent::commit();
			return true;
	
		} catch (Exception $e) {
			parent::rollBack();
			Common::log(json_encode($data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 'coin_error.log');
			return false;
		}
	}
}
