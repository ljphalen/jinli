<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_Api_Coin extends Common_Service_Base{

	/**
	 * 验证签名
	 */
	
	public function checkSign($data, $auth_key){
		if (!is_array($data)) return false;
		Common::log($data, 'sign.log');
		$sign = $data['sign'];
		unset($data['appid']);
		unset($data['appsecret']); 
		unset($data['sign']);
		$new_sign = parent::createSign($data, $auth_key);
		if ($sign != $new_sign) return false;
		return true;
	}
	
	/**
	 * 积分操作(消耗｜增加)
	 */
	public function updateCoin($data){
		if (!is_array($data)) return false;
		try {
			//开始事务
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction faileded.", -201);
			//记录日志
			$result = User_Service_CoinLog::addCoinLog(array(
					'out_uid'=>$data['out_uid'],
					'appid'=>$data['appid'],
					'coin_type'=>$data['coin_type'],
					'coin'=> $data['coin'],
					'msg'=>$data['msg'],
					'create_time'=>Common::getTime()
			));
			if (!$result) throw new Exception("Add coin_log failed.", -202);
			
			//更新用户银币
			$ret = User_Service_User::updateCoin($data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$ret) throw new Exception("update user coin failed.", -203);
			
			//若是发放积分
			if ($data['coin'] > 0) {
				//更新应用积分
				$ret = User_Service_App::updateCoin($data['coin'], $data['appid'], $data['coin_type']);
				if (!$ret) throw new Exception('update app coin faileded');
			}
			
			//事务提交
			return parent::commit();
			
		} catch (Exception $e) {
			parent::rollBack();
			//消耗coin出错监控
			Common::log(json_encode($data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 'coin.log');
			return false;
		}
	}
	
	/**
	 * 
	 * 冻结积分
	 */
	public function freeze ($data) {
		if (!is_array($data)) return false;
		try {
			//开始事务
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction faileded.", -201);
			//更新用户银币
			$ret = User_Service_User::updateCoin(-$data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$ret) throw new Exception("update user coin failed.", -206);
			
			//更新用户冻结银币
			$info = User_Service_User::updateFreezeCoin($data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$info) throw new Exception("update user freeze coin failed.", -208);
			
			//记录日志
			$result = User_Service_FreezeLog::addFreezeLog(array(
					'out_uid'=>$data['out_uid'],
					'appid'=>$data['appid'],
					'coin_type'=>$data['coin_type'],
					'status'=>0,
					'mark'=>$data['mark'],
					'coin'=> $data['coin'],
					'msg'=>$data['msg'],
					'create_time'=>Common::getTime()
			));
			if (!$result) throw new Exception("Add coin_log failed.", -205);				
				
			//事务提交
			return parent::commit();
		} catch (Exception $e) {
			parent::rollBack();
			//冻结coin出错监控
			Common::log(json_encode($data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 'coin.log');
			return false;
		}
	}
	
	/**
	 *
	 * 解冻积分
	 */
	public function unfreeze ($data) {
		if (!is_array($data)) return false;
		try {
			//开始事务
			$trans = parent::beginTransaction();
			if (!$trans) throw new Exception("begin transaction faileded.", -201);
			//更新用户冻结银币
			$info = User_Service_User::updateFreezeCoin(-$data['coin'], $data['out_uid'], $data['coin_type']);
			if (!$info) throw new Exception("update user freeze coin failed.", -208);
	
			//判断是是直接消耗
			if($data['unfreeze_type'] == 1){
				// 消耗 记录日志
				$result = User_Service_CoinLog::addCoinLog(array(
						'out_uid'=>$data['out_uid'],
						'appid'=>$data['appid'],
						'coin_type'=>$data['coin_type'],
						'coin'=>-$data['coin'],
						'msg'=>$data['mark'],
						'create_time'=>Common::getTime()
				));
				if (!$result) throw new Exception("Add coin_log failed.", -202);
			} 
			//返回给用户
			if($data['unfreeze_type'] == 2) {
				//返回给用户 更新用户银币
				$ret = User_Service_User::updateCoin($data['coin'], $data['out_uid'], $data['coin_type']);
				if (!$ret) throw new Exception("update user coin failed.", -206);
			}
			
			//更新冻结状态
			$freezelog = User_Service_FreezeLog::updateStatus($data['unfreeze_type'], $data['mark']);
			if (!$freezelog) throw new Exception("update freezelog status failed.", -207);
			
			//事务提交
			return parent::commit();
		} catch (Exception $e) {
			parent::rollBack();
			//冻结coin出错监控
			Common::log(json_encode($data) ."; ". $e->getCode() . '; ' . $e->getMessage() . "\n", 'coin.log');
			return false;
		}
	}
}
