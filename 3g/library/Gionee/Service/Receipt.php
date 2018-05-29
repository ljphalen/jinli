<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Receipt{
	
	public static function  add($params){
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	public static function getList($page,$pageSize,$where,$order){
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList(($page-1)*$pageSize,$pageSize,$where,$order));
	}
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function edit($params,$id){
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	
	public static function editBy($params,$where=array()){
		if(!is_array($where))  return false;
		$data = self::_checkData($params);
		return self::_getDao()->updateBy($data, $where);
	}
	public static function getBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	public static function getsBy($params,$order=array()){
		if(!is_array($params))return  false;
		return self::_getDao()->getsBy($params,$order);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}
	
	
	/**
	 * 写当月数据到统计表中
	 */
	public static function addReceiptData(){
		$date = date('Y-m',time());
		$sdate = $date.'-01';
		$edate = $date.'-'.date('t',time());
		$arrDate  = explode($date, '-');
		list($total,$urlIds) = Gionee_Service_ParterUrl::getUrlIdList(1,2147483647 , array());
		$data = Gionee_Service_Business::getBussinessDetailInfo($urlIds, $sdate, $edate);
		foreach ($data as $k=>$v){
			if(!empty($v['clicks'])){
				$pv = 0;
				foreach ($v['clicks'] as $m=>$n){
					$pv +=$n;
				}
				$Totaldays = 30;
				if($v['price_type'] == 3){
					$Totaldays = Common::getMonthLastDay($arrDate[0], $arrDate[1]);
				}
				$money= self::getIncomeByType($v['price_type'], $pv, $v['price'],$Totaldays,count($v['clicks']));
				$logData = Gionee_Service_Receipt::getBy(array('pid'=>$v['pid'],'bid'=>$v['bid'],'date'=>$date));
				if(!empty($logData)){
					Gionee_Service_Receipt::edit(array('money'=>$money,'real_money'=>$money, 'pv'=>$pv,'model'=>$v['model']), $logData['id']);
				}else{
					Gionee_Service_Receipt::add(
							array(
									'pid'=>$v['pid'],
									'bid'	=>$v['bid'],
									'price'=>$v['price'],
									'price_type'=>$v['price_type'],
									'pv'	=>$pv,
									'money'=>$data[$k]['totalMoney'],
									'model'=>$v['model'],
									'real_money'=>$data[$k]['totalMoney'],
									'date'	=>$date,
							)
					);
				}
			}
		}
	}
	
	
	/**
	 * 把前一天的收入状况汇总
	 */
	public static function writeIncomeDataToDb(){
		$date = date('Y-m-d',strtotime('-1 day'));
		$month = substr($date, 0,7);
		list($total,$urlIds) = Gionee_Service_ParterUrl::getUrlIdList(1,1000 , array());
		$data = Gionee_Service_Business::getBussinessDetailInfo($urlIds, $date, $date);
		foreach ($data as $k=>$v){
			if(!empty($v['clicks'])){ //如果点击量存在时才计算收入
				$money = 0;
				$pv = 0;
				foreach ($v['clicks'] as $m=>$n){
					$pv = $n;// 前一天的点击量
				}
				$startDate = date('Y-m-d',$v['start_time']);
				$endDate = date('Y-m-d',$v['end_time']);
				 if(empty($v['status']) || !($startDate<=$date && $date<=$endDate)){ //如果已过期或关闭
					return;
				}
				$Totaldays = 30;//默认30天
				if ($v['price_type'] == 3){//如果是按月结算时
					$arrDate = explode('-', $date);
					$Totaldays = Common::getMonthLastDay($arrDate[0], $arrDate[1]);
				}
				$money= self::getIncomeByType($v['price_type'], $pv, $v['price'],$Totaldays);
				$logData = Gionee_Service_Receipt::getBy(array('pid'=>$v['pid'],'bid'=>$v['bid'],'date'=>$month));
				if(!empty($logData)){
					if(date('Y-m-d',$logData['update_at']) == date('Y-m-d',time())){ //如果当天已经更新过，而不再更新
						return ;
					}
					$realMoney 			= $logData['real_money']+$money;
					$countedMoney 	= $logData['money'] + $money;
					$countedPv 			= $logData['pv']+$pv;
					Gionee_Service_Receipt::edit(array('pv'=>$countedPv,'money'=>$countedMoney,'real_money'=>$realMoney,'update_at'=>time()), $logData['id']);
				}else{
					$params = array(
								'pid'=>$v['pid'],
								'bid'	=>$v['bid'],
								'price'=>$v['price'],
								'price_type'=>$v['price_type'],
								'pv'	=>$pv,
								'money'=>$money,
								'model'=>$v['model'],
								'real_money'=>$money,
								'date'	=>$month,
								'update_at'=>time(),
						);
					Gionee_Service_Receipt::add($params);
				}
			}
		}
	}
	
	/**
	 * 根据计价类型得到每天点击收入
	 *
	 */
	public static  function getIncomeByType($type,$clickNum,$price,$Totaldays = 30,$day=1){
		$sum = 0;
		switch ($type){
			case 1:{ //按PV点击计算
				$sum = bcmul(intval($clickNum), $price,2);
				break;
			}
			
			case 2:{ //按UV点击计算
				$sum =  bcmul(intval($clickNum), $price,2);
				break;
			}
			case 3:{//按月计价
				$sum = $day*(bcdiv($price, $Totaldays,2));
				break;
			}
			default:break;
		}
		return money_format($sum,2);
	}
	
	
	private  static function _checkData($params){
		$temp = array();
		if(isset($params['date']))						$temp['date'] = $params['date'];
		if(isset($params['pid']))							$temp['pid'] = $params['pid'];
		if(isset($params['bid']))							$temp['bid']	 = $params['bid'];
		if(isset($params['pv']))							$temp['pv']	 = $params['pv'];
		if(isset($params['money']))					$temp['money']	 = $params['money'];
		if(isset($params['real_money']))			$temp['real_money']	 = $params['real_money'];
		if(isset($params['reason']))					$temp['reason']	 = $params['reason'];
		if(isset($params['check_status']))			$temp['check_status']	 = $params['check_status'];
		if(isset($params['receipt_status']))		$temp['receipt_status']	 = $params['receipt_status'];
		if(isset($params['confirm_status']))		$temp['confirm_status'] = $params['confirm_status'];
		if(isset($params['model']))					$temp['model']			 = $params['model'];
		if(isset($params['price']))						$temp['price']				=$params['price'];
		if(isset($params['price_type']))				$temp['price_type'] = $params['price_type'];
 		if(isset($params['edit_time']))				$temp['edit_time'] = $params['edit_time'];
		if(isset($params['edit_user']))				$temp['edit_user'] = $params['edit_user'];
		if(isset($params['model']))					$temp['model']		 = $params['model'];
		if(isset($params['update_at']))				$temp['update_at'] = $params['update_at'];
		return $temp;
	}
	
	/**
	 * 
	 * @return Gionee_Dao_Receipt
	 */
	private static  function _getDao(){
		return Common::getDao('Gionee_Dao_Receipt');
	}
}