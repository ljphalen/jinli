<?php
 /**
 * 数据接口
 * @package application
 * @since 1.0.0 (2013-03-22)
 * @version 1.0.0 (2013-03-22)
 * @author jun <huanghaijun@mykj.com>
 */
 
 class task{
 	private $db = null;
 	public function __construct($db){
 		$this->db=$db;
 	}
 	
 	
 	/**
 	 * 更新渠道列表缓存
 	 */
 	public function update_company($channels,$channeltype=1){
 		$filename = SROOT.'/data/cache/company/channel'.$channeltype.CACHE_FILE_EXT;
 		$data = json_encode($channels);
 		return $this->write($filename, $data);
 	}
 	
 	
 	/* public function update_subcompany($channels){
 		$filename = SROOT.'/data/cache/company/subchannel'.CACHE_FILE_EXT;
 		$data = json_encode($channels);
 		return $this->write($filename, $data);
 	} */
 	
 	/**
 	 * 更新子渠道列表缓存
 	 */
 	public function update_subcompany($channels){
 		$filename = SROOT.'/data/cache/company/subchannel/';
 		
 		foreach($channels as $k=>$v){
 			if(empty($v))continue;
 			$t_filename = $filename.$k.CACHE_FILE_EXT;
 			$data = json_encode($v);
 			if(!$this->write($t_filename, $data)){
 				return false;
 			}
 		}
 		return true;
 	}
 	
 	/**
 	 * 取渠道列表
 	 */
 	public function get_company($channeltype=1){
 		$filename = SROOT.'/data/cache/company/channel'.$channeltype.CACHE_FILE_EXT;
 		$data = json_decode(@file_get_contents($filename),true);
 		return $data;
 	}
 	
 	
 	/**
 	 * 更新报表缓存
 	 */
 	public function update_report($date1,$date2,$fileid,$bymonth=0){
 		$filename = SROOT.'/data/cache/reportall/'.$fileid.CACHE_FILE_EXT;
 		$subfilename = SROOT.'/data/cache/reportsub/'.$fileid.CACHE_FILE_EXT;
 		$reportuser = array();
 		$reportorder = array();
 		$gameids = array(0=>0);
 		$clientids = array();
 		$sub_clientids = array();
 		
 		$companys1 = $this->get_company(1);
 		$companys2 = $this->get_company(2);
 		$companys = $companys1+$companys2;

 		
 		$reportuser = $this->get_reportuserList($date1, $date2);
 	
 		
 		//生成每个用户的报表数据
 		$t_reportuser = array();
 		$t_sub_reportuser = array();
 		foreach($reportuser as $k=>$v){
 			//$t_reportuser[$v['clientid']][$v['clientidsub']][$v['gameid']]=array('registerusers'=>$v['registerusers']);
 			//$t_reportuser[$v['clientid']][$v['clientidsub']][0]['registerusers']+=$v['registerusers'];
 			
 			$t_reportuser[$v['clientid']][$v['gameid']]['registerusers']+=$v['registerusers'];
 			$t_reportuser[$v['clientid']][0]['registerusers']+=$v['registerusers'];
 			
 			$t_reportuser[$v['clientid']][$v['gameid']]['loginusers']+=$v['loginusers'];
 			$t_reportuser[$v['clientid']][0]['loginusers']+=$v['loginusers'];
 			
 			$t_reportuser[$v['clientid']][$v['gameid']]['firstloginnum']+=$v['firstloginnum'];
 			$t_reportuser[$v['clientid']][0]['firstloginnum']+=$v['firstloginnum'];
 			
 			$t_reportuser[$v['clientid']][$v['gameid']]['firstpaymentusernum']+=$v['firstpaymentusernum'];
 			$t_reportuser[$v['clientid']][0]['firstpaymentusernum']+=$v['firstpaymentusernum'];
 			
 			$t_reportuser[$v['clientid']][$v['gameid']]['firstpaymenttime']+=$v['firstpaymenttime'];
 			$t_reportuser[$v['clientid']][0]['firstpaymenttime']+=$v['firstpaymenttime'];
 			
 			$t_reportuser[$v['clientid']][$v['gameid']]['firstpaymentmoney']+=$v['firstpaymentmoney'];
 			$t_reportuser[$v['clientid']][0]['firstpaymentmoney']+=$v['firstpaymentmoney'];
 			
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][$v['gameid']]['registerusers']+=$v['registerusers'];
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][0]['registerusers']+=$v['registerusers'];
 			
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][$v['gameid']]['loginusers']+=$v['loginusers'];
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][0]['loginusers']+=$v['loginusers'];
 			
 		 	$t_sub_reportuser[$v['clientid']][$v['clientidsub']][$v['gameid']]['firstloginnum']+=$v['firstloginnum'];
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][0]['firstloginnum']+=$v['firstloginnum'];
 			
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][$v['gameid']]['firstpaymentusernum']+=$v['firstpaymentusernum'];
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][0]['firstpaymentusernum']+=$v['firstpaymentusernum'];
 			
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][$v['gameid']]['firstpaymenttime']+=$v['firstpaymenttime'];
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][0]['firstpaymenttime']+=$v['firstpaymenttime'];
 			
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][$v['gameid']]['firstpaymentmoney']+=$v['firstpaymentmoney'];
 			$t_sub_reportuser[$v['clientid']][$v['clientidsub']][0]['firstpaymentmoney']+=$v['firstpaymentmoney'];
 			 
 			$gameids[$v['gameid']] = $v['gameid'];
 			$clientids[$v['clientid']] = $v['clientid'];
 			$sub_clientids[$v['clientid']][$v['clientidsub']] = $v['clientidsub'];
 		}
 		
 		//print_r($t_sub_reportuser);exit;
 		
 		//如果是月报，则将月活跃登录用户数，月用户平均游戏时长合并过来
 		if($bymonth){
 			$reportuser_m = $this->get_reportuserCurM($date2);
 			//print_r($reportuser_m);
 			
 			$t_reportuser_m = array();
 			$t_sub_reportuser_m = array();
 			$counts = array();
 			foreach($reportuser_m as $k=>$v){
 				$t_reportuser_m[$v['clientid']][$v['gameid']]['activeusermonth']+=$v['activeusermonth'];
 				$t_reportuser_m[$v['clientid']][$v['gameid']]['avgplaygamemonth']+=$v['avgplaygamemonth'];
 				$t_sub_reportuser_m[$v['clientid']][$v['clientidsub']][$v['gameid']]['activeusermonth']+=$v['activeusermonth'];
 				$t_sub_reportuser_m[$v['clientid']][$v['clientidsub']][$v['gameid']]['avgplaygamemonth']+=$v['avgplaygamemonth'];
 				
 				//$counts[$v['clientid']][$v['gameid']]++;
 				
 				$t_reportuser_m[$v['clientid']][0]['activeusermonth']+=$v['activeusermonth'];
 				$t_reportuser_m[$v['clientid']][0]['avgplaygamemonth']+=$v['avgplaygamemonth'];
 				$t_sub_reportuser_m[$v['clientid']][$v['clientidsub']][0]['activeusermonth']+=$v['activeusermonth'];
 				$t_sub_reportuser_m[$v['clientid']][$v['clientidsub']][0]['avgplaygamemonth']+=$v['avgplaygamemonth'];
 				
 				$counts[$v['clientid']][0]++;
 				$counts[$v['clientid'].'_'.$v['clientidsub']][0]++;
 			}
 			//print_r($counts);
 			//print_r($t_reportuser_m);exit;
 			
 			foreach($t_reportuser_m as $cid=>$v){
 				//$t_reportuser_m[$cid][0]['activeusermonth'] = round($v[0]['activeusermonth']/$counts[$cid][0]);
 				$t_reportuser_m[$cid][0]['avgplaygamemonth'] = round($v[0]['avgplaygamemonth']/$counts[$cid][0]);
 				
 				foreach($v as $gid=>$v2){
 					if(isset($t_reportuser[$cid][$gid])){
 						$t_reportuser[$cid][$gid]['activeusermonth']=$t_reportuser_m[$cid][$gid]['activeusermonth'];
 						$t_reportuser[$cid][$gid]['avgplaygamemonth']=$t_reportuser_m[$cid][$gid]['avgplaygamemonth'];
 					}
 				}
 			}
 			$t_reportuser_m=null;
 			
 			foreach($t_sub_reportuser_m as $cid=>$val){
 				foreach($val as $scid=>$v){
	 				//$t_sub_reportuser_m[$cid][$scid][0]['activeusermonth'] = round($v[0]['activeusermonth']/$counts[$cid.'_'.$scid][0]);
	 				$t_sub_reportuser_m[$cid][$scid][0]['avgplaygamemonth'] = round($v[0]['avgplaygamemonth']/$counts[$cid.'_'.$scid][0]);
	 				
	 				foreach($v as $gid=>$v2){
	 					if(isset($t_sub_reportuser[$cid][$scid][$gid])){
	 						$t_sub_reportuser[$cid][$scid][$gid]['activeusermonth']=$t_sub_reportuser_m[$cid][$scid][$gid]['activeusermonth'];
	 						$t_sub_reportuser[$cid][$scid][$gid]['avgplaygamemonth']=$t_sub_reportuser_m[$cid][$scid][$gid]['avgplaygamemonth'];
	 					}
	 				}
 				}
 			}
 			//print_r($t_sub_reportuser);exit;
 			$t_sub_reportuser_m=null;
 			//print_r($t_reportuser);exit;
 		}
 		
 		
 			
 		$reportorder = $this->get_reportorderList($date1, $date2);
 			
 	
 		//去掉不列入统计的记录
 		$setting = new setting();
 		$our_paytype = $setting->get_our_paytype();
 		$channel_paytype = $setting->get_channel_paytype();
 		
/*  		if(in_array(50,$our_paytype)){
 			
 			foreach($GLOBALS['ini_ipay_paytypes'] as $k=>$v){
 				if($k==50)continue;
 				$our_paytype[]=$k;
 			}
 		}
 	
 		foreach($reportorder as $k=>$v){
 			if(!in_array($v['payway'],$our_paytype)){
 				if(!(isset($channel_paytype[$v['clientid']])&&$channel_paytype[$v['clientid']]==$v['payway'])){
 					unset($reportorder[$k]);
 				}
 			}
 		} */
 		/* var_dump($reportorder)	;
 		exit(); */
 		$t_reportorder = array();
 		$t_sub_reportorder = array();
 		foreach($reportorder as $k=>$v){
 			/* $t_reportorder[$v['clientid']][$v['clientidsub']][$v['gameid']][$v['payway']]=array(
 					'consumeorders'=>$v['consumeorders'],
 					'consumeusers'=>$v['consumeusers'],
 					'consumemoney'=>$v['consumemoney'],
 			); */
 			
 			//统计主渠道的对应的游戏数据
 			$t_reportorder[$v['clientid']][$v['gameid']]['consumeorders']+=$v['consumeorders'];
 			$t_reportorder[$v['clientid']][$v['gameid']]['consumeusers']+=$v['consumeusers'];
 			$t_reportorder[$v['clientid']][$v['gameid']]['consumemoney']+=$v['consumemoney'];
 			//分开统计移动与爱贝的消费金额
 			if( $v['payway'] == 888 || $v['payway'] == -1){
 				$t_reportorder[$v['clientid']][$v['gameid']]['consumemoneyyd']+=$v['consumemoney'];
 			}else{
 				$t_reportorder[$v['clientid']][$v['gameid']]['consumemoneyipay']+=$v['consumemoney'];
 			}
 		
 			//统计主渠道中所有游戏的数据
 			$t_reportorder[$v['clientid']][0]['consumeorders']+=$v['consumeorders'];
 			$t_reportorder[$v['clientid']][0]['consumeusers']+=$v['consumeusers'];
 			$t_reportorder[$v['clientid']][0]['consumemoney']+=$v['consumemoney'];
 			//分开统计移动与爱贝的消费金额
 			if( $v['payway'] == 888 || $v['payway'] == -1){
 				$t_reportorder[$v['clientid']][0]['consumemoneyyd']+=$v['consumemoney'];
 			}else{
 				$t_reportorder[$v['clientid']][0]['consumemoneyipay']+=$v['consumemoney'];
 			}
 		
 			//统计子渠道的对应的游戏数据
 			$t_sub_reportorder[$v['clientid']][$v['clientidsub']][$v['gameid']]['consumeorders']+=$v['consumeorders'];
 			$t_sub_reportorder[$v['clientid']][$v['clientidsub']][$v['gameid']]['consumeusers']+=$v['consumeusers'];
 			$t_sub_reportorder[$v['clientid']][$v['clientidsub']][$v['gameid']]['consumemoney']+=$v['consumemoney'];
 			//分开统计移动与爱贝的消费金额
 			if( $v['payway'] == 888 || $v['payway'] == -1){
 				$t_sub_reportorder[$v['clientid']][$v['clientidsub']][$v['gameid']]['consumemoneyyd']+=$v['consumemoney'];
 			}else{
 				$t_sub_reportorder[$v['clientid']][$v['clientidsub']][$v['gameid']]['consumemoneyipay']+=$v['consumemoney'];
 			}
 		
 			////统计子渠道的对应的所有游戏数据
 			$t_sub_reportorder[$v['clientid']][$v['clientidsub']][0]['consumeorders']+=$v['consumeorders'];
 			$t_sub_reportorder[$v['clientid']][$v['clientidsub']][0]['consumeusers']+=$v['consumeusers'];
 			$t_sub_reportorder[$v['clientid']][$v['clientidsub']][0]['consumemoney']+=$v['consumemoney'];
 			//分开统计移动与爱贝的消费金额
 			if( $v['payway'] == 888 || $v['payway'] == -1){
 				$t_sub_reportorder[$v['clientid']][$v['clientidsub']][0]['consumemoneyyd']+=$v['consumemoney'];
 			}else{
 				$t_sub_reportorder[$v['clientid']][$v['clientidsub']][0]['consumemoneyipay']+=$v['consumemoney'];
 			}
 		
 			
 			$gameids[$v['gameid']] = $v['gameid'];
 			$clientids[$v['clientid']] = $v['clientid'];
 			$sub_clientids[$v['clientid']][$v['clientidsub']] = $v['clientidsub'];
 		}
 		//var_dump($t_reportorder);
 		//print_r($t_reportorder);exit;
 			
 		//print_r($reportorder);exit;
 			
 		$reportdata = array();
 		$sub_reportdata = array();
 		//$games = $GLOBALS['ini_games'];
 	
 		foreach($clientids as $k=>$v){
 			if($v<=0)continue;
 			foreach($gameids as $gid){
 				$arr = array();
 				if(isset($t_reportuser[$v][$gid])){
 					$arr = $t_reportuser[$v][$gid];
 				}
 					
 				if(isset($t_reportorder[$v][$gid])){
 					$arr = array_merge($arr,$t_reportorder[$v][$gid]);
 				}
 					
 				if(!empty($arr)){
 					$reportdata[$v][$gid] = $arr;
 				}
 			}
 			
 			
 			foreach($sub_clientids[$v] as $subcid){
 				foreach($gameids as $gid){
 					$arr = array();
 					if(isset($t_sub_reportuser[$v][$subcid][$gid])){
 						$arr = $t_sub_reportuser[$v][$subcid][$gid];
 					}
 				
 					if(isset($t_sub_reportorder[$v][$subcid][$gid])){
 						$arr = array_merge($arr,$t_sub_reportorder[$v][$subcid][$gid]);
 					}
 				
 					if(!empty($arr)){
 						$sub_reportdata[$v][$subcid][$gid] = $arr;
 					}
 				}
 			}
 		}
 		//print_r($reportdata);exit;
 			
 
 		$data = json_encode($reportdata);		
 		if(!$this->write($filename, $data)){
 			return false;
 		}

 		foreach($sub_reportdata as $cid=>$data){
 			$path = SROOT.'/data/cache/reportsub/'.$cid;
 			if(!file_exists($path)){
 				mkdir($path,0777);
 			}
 			$subfilename = $path.'/'.$fileid.CACHE_FILE_EXT;
 			$data = json_encode($data);
 			if(!$this->write($subfilename, $data)){
 				return false;
 			}
 		}
 		
 		$reportdata = null;
 		$sub_reportdata = null;
 		
 		return true;
 	}
 	
 	
 	/**
 	 * 更新报表缓存-总计
 	 */
 	public function update_report_total($clientid,$subclientid){
 		$tmptime = strtotime('-1 day');
 		$date1 = date('Y-m-1',$tmptime);
 		$date2 = date('Y-m-d',$tmptime);
 		$filename = SROOT.'/data/cache/reportcount/'.$clientid.'_'.$subclientid.CACHE_FILE_EXT;
 		//$subfilename = SROOT.'/data/cache/reportsub/'.$fileid.CACHE_FILE_EXT;
 
 		
 		$report_total = $this->get_report_total($date1, $date2, $clientid, $subclientid);
 		
 		//print_r($report_total);
 		//如统计的是渠道商，则排除非我方支付方式及非渠道商支付方式金额
 		if($clientid>0){
	 		$reportorder = $this->get_reportorderList($GLOBALS['ini_report_startdate'], $date2, $clientid, $subclientid);
	 		$setting = new setting();
	 		$our_paytype = $setting->get_our_paytype();
	 		$channel_paytype = $setting->get_channel_paytype();
	 		if(in_array(50,$our_paytype)){
	 			foreach($GLOBALS['ini_ipay_paytypes'] as $k=>$v){
	 				if($k==50)continue;
	 				$our_paytype[]=$k;
	 			}
	 		}
	 		$counts = array();
	 		$consumemoney = 0;
	 		foreach($reportorder as $k=>$v){
	 			$flag = 1;
	 			if(!in_array($v['payway'],$our_paytype)){
	 				if(!(isset($channel_paytype[$v['clientid']])&&$channel_paytype[$v['clientid']]==$v['payway'])){
	 					//unset($reportorder[$k]);
	 					$flag = 0;
	 				}
	 			}
	 			
	 			if($flag){
	 				$consumemoney += $v['consumemoney'];
	 			}
	 		}
	 		$report_total['consumemoney'] = $consumemoney;
	 		
	 		$report = new report();
	 		//取昨天数据
	 		if($subclientid>0){
	 			$report_data = $report->get_report_sub_data(0, $clientid, $subclientid, $date2, $date2);
	 			$cid = $subclientid;
	 		}else{
	 			$report_data = $report->get_report_data(0, $clientid, $date2, $date2);
	 			$cid = $clientid;
	 		}
			$report_total['lastmoney'] = getnum($report_data[$cid]['consumemoney']);
	 		//print_r($report_total);exit;
	 		
	 		//取本月数据
	 		if($subclientid>0){
	 			$report_data = $report->get_report_sub_data(0, $clientid, $subclientid, $date1, $date2);
	 			$cid = $subclientid;
	 		}else{
	 			$report_data = $report->get_report_data(0, $clientid, $date1, $date2);
	 			$cid = $clientid;
	 		}
	 		$report_total['monthmoney'] = getnum($report_data[$cid]['consumemoney']);
 		}
 		//print_r($report_total);exit;
 		$data = json_encode($report_total);
 		if(!$this->write($filename, $data)){
 			return false;
 		}
 		
 		$reportorder=null;
 	
 		return true;
 	}
 	
 	/**
 	 * 更新日报表
 	 */
 	public function update_report_date($createdate){
 		$tmp_time = strtotime($createdate);
 		$createdate = date('Y-m-d',$tmp_time);
 			
 		$res = $this->update_report($createdate, $createdate, $createdate);
 		return $res;
 	}
 	
 	/**
 	 * 更新月报表
 	 */
 	public function update_report_month($month){
 		$tmp_time = strtotime($month);
 		$date1 = date('Y-m-1',$tmp_time);
 		
 		
 		if(date('Ym',strtotime('-1 day'))==date('Ym',$tmp_time)){
 			//如为当月且止日期为昨天
 			$date2 = date('Y-m-d',strtotime('-1 day'));
 		}else{
 			$date2 = date('Y-m-t',$tmp_time);
 		}
 		
 		$fileid = date('Y-m',$tmp_time);
 		
 		$res = $this->update_report($date1, $date2, $fileid, 1);
 		return $res;
 	}
 	
 	/**
 	 * 更新年报表
 	 */
 	public function update_report_year($year){
 		$date1 = $year.'-01-01';
 		if(date('Y')==$year){
 			//如为当年则止日期为昨天
 			$date2 = date('Y-m-d',strtotime('-1 day'));
 		}else{
 			$date2 = $year.'-12-31';
 		}
 		$fileid = $year;
 		
 		$res = $this->update_report($date1, $date2, $fileid);
 		return $res;
 	}
 	
 	
 	/**
 	 * 读取用户日报表列表信息
 	 */
 	public function get_reportuserClientList($date1,$date2,$clientid,$clientids,$channeltype,$gameid){
 		$params = array($date1,$date2,$clientid,$clientids,$channeltype,$gameid);
 		$res = $this->db->simpleCall('sp_web_reportuserClientList',$params);
 		return $res[0];
 	}
 	
 	/**
 	 * 读取订单日报表列表信息
 	 */
 	public function get_reportorderClientList($date1,$date2,$clientid,$clientids,$channeltype,$gameid){
 		$params = array($date1,$date2,$clientid,$clientids,$channeltype,$gameid);
 		$res = $this->db->simpleCall('sp_web_reportorderClientList',$params);
 		return $res[0];
 	}
 	
 	
 	/**
 	 * 读取用户日报表列表信息
 	 */
 	public function get_reportuserList($date1,$date2){
 		$params = array($date1,$date2);
 		$res = $this->db->simpleCall('sp_web_reportuserList',$params);
 		//var_dump($res);
 		return $res[0];
 	}
 	
 	/**
 	 * 读取订单日报表列表信息
 	 */
 	public function get_reportorderList($date1,$date2,$clientid=0,$subclientid=0){
 		$params = array($date1,$date2,$clientid,$subclientid);
 		$res = $this->db->simpleCall('sp_web_reportorderList',$params);
 		return $res[0];
 	}
 	
 	/**
 	 * 读取用户日报表列表月统计信息
 	 */
 	public function get_reportuserCurM($date1){
 		$params = array($date1);
 		$res = $this->db->simpleCall('sp_web_reportuserCurM',$params);
 		return $res[0];
 	}
 	
 	/**
 	 * 合并用户与消费
 	 * @param  $reportuser
 	 * @param  $reportorder
 	 */
 	private function merge_data($reportuser,$reportorder){
 		foreach($reportuser as $k=>$v){
 			if(!isset($reportorder[$k]))continue;
 			
 			$reportuser[$k]['payway'] = $reportorder[$k]['payway'];
 			$reportuser[$k]['consumeorders'] = $reportorder[$k]['consumeorders'];
 			$reportuser[$k]['consumeusers'] = $reportorder[$k]['consumeusers'];
 			$reportuser[$k]['consumemoney'] = $reportorder[$k]['consumemoney'];
 			unset($reportorder[$k]);
 		}
 		
 		$reports = array_merge($reportuser,$reportorder);
 		
 		return $reports;
 	}
 	
 	
 	/**
 	 * 统计概览信息
 	 */
 	public function get_report_total($date1,$date2,$clientid,$subclientid){
 		$params = array($date1,$date2,$clientid,$subclientid);
 		$res = $this->db->simpleCall('sp_web_reportSurvey',$params);
 		return $res[0][0];
 	}
 	
 	/**
 	 * 生成对帐单
 	 * @param  $date1
 	 * @param  $date2
 	 */
 	public function create_report($date1,$date2,$fileid){
 		$setting = new setting();
 		$channel_paytypes = $setting->get_channel_paytype();
 		$payways = $setting->get_payways();
 		$yd_channels = $setting->get_yd_channel();
 		$our_paytype = $setting->get_our_paytype();
 		$base_rate = $setting->get_base_rate();
 		
 	
 		
	 	$reportorder = $this->get_reportorderList($date1, $date2);
	 	$yearmonth = sprintf('%d年%d月',substr($fileid,0,4),substr($fileid,-2));
	 	
	 

	 	$filename_ipay = SROOT.'/data/cache/reporthtml/ipay/'.$fileid.CACHE_FILE_EXT;
	 	$filename_ours = SROOT.'/data/cache/reporthtml/ours/'.$fileid.CACHE_FILE_EXT;
	 	$filename_clients = SROOT.'/data/cache/reporthtml/clients/'.$fileid.CACHE_FILE_EXT;
	 	$filename_ydchannel = SROOT.'/data/cache/reporthtml/ydchannel/'.$fileid.CACHE_FILE_EXT;
	 	
	 	
	 	$companys1 = $this->get_company(1);
	 	$companys2 = $this->get_company(2);
	 	$companys = $companys1+$companys2;
	 	
	
	 	$t_reportorder = array();
		$t_reportorder_ipay = array();
		$t_reportorder_yd = array();
		$t_reportorder_client = array();
		$t_reportorder_ydchannel = array();
		$t_reportorder_lt = array();
	
		
		foreach($reportorder as $k=>$v){
			//统计爱贝
			//if(!in_array($v['clientid'],$GLOBALS['config']['paytocmnet']) && $v['payway']>=50&&$v['payway']<=59){
				if(!($v['payway'] == 888)){
					$t_reportorder[$v['clientid']][$v['payway']]+=$v['consumemoney'];
					$t_reportorder_ipay[$v['clientid']]+=$v['consumemoney'];
				}
			//}
			
			
			//统计移动
			/* if(in_array($v['clientid'],$GLOBALS['config']['paytocmnet'])){
				if(in_array($v['payway'],$our_paytype)){
					$t_reportorder_yd[$v['clientid']]+=$v['consumemoney'];
				}
			}elseif($v['payway']>=11&&$v['payway']<=13){
				$t_reportorder_yd[$v['clientid']]+=$v['consumemoney'];
			} */
		
			if($v['payway'] == 888){			
				$t_reportorder_yd[$v['clientid']]+=$v['consumemoney'];
			}
			
			
			//统计渠道自有
			if(isset($channel_paytypes[$v['clientid']])&&intval($channel_paytypes[$v['clientid']])==$v['payway']){
				$t_reportorder_client[$v['clientid']]+=$v['consumemoney'];
			}
			
			//统计移动自有渠道
			if(isset($companys2[$v['clientid']])){
				if($v['clientid'] == 1 && $v['payway'] == 888){
					$t_reportorder_ydchannel[$v['clientid']]+=$v['consumemoney'];
				}
			}
			
			//统计联通
			if($v['payway']==76){
				$t_reportorder_lt[$v['clientid']]+=$v['consumemoney'];
			}
		
			//$t_reportorder[$v['clientid']][0]+=$v['consumemoney'];
		}
	
			
	
		
		//爱贝计费平台对帐单
		$html = '<!DOCTYPE html><html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>爱贝计费平台对帐单</title>
</head><body><div>深圳市同楼网络科技有限公司['.$yearmonth.']爱贝计费平台对帐单</div>
		<table border="1" cellpadding="0" cellspacing="0">
  <tr>
    <td>渠道号</td>
    <td>支付渠道</td>
    <td>渠道交易费率</td>
    <td>收入总额</td>
    <td>手续用费</td>
    <td>爱贝手续费率</td>
    <td>爱贝手续费用</td>
    <td>可分配收益</td>
    <td>渠道分成比率</td>
    <td>渠道结算金额</td>
    <td>分成合计</td>
    <td>负责人</td>
    <td>备注</td>
  </tr>';
		$ipay_rate = $setting->get_ipay_rate();
		$ipay_c_moeny = array();
		//ksort($t_reportorder);
		arsort($t_reportorder_ipay);
		foreach($t_reportorder_ipay as $clientid=>$money){
			$val = $t_reportorder[$clientid];
			$num =  count($val);
			$i=0;
			$count = 0;
			$rate = isset($ipay_rate[$clientid])?$ipay_rate[$clientid]:$ipay_rate[0];
			$tr = '';
			foreach($val as $payway=>$money){
				$i++;
				
				$payname = $GLOBALS['ini_ipay_paytypes'][$payway];
				$payway_rate = $GLOBALS['ini_ipay_payway_rates'][$payway];
				$fee = round($money*$payway_rate,2);
				$ipay_pay_rate = $base_rate['ipay_pay_rate'];
				$ipay_fee = round(($money-$fee)*$ipay_pay_rate,2);
				$share_money = $money-$fee-$ipay_fee;
				$c_money = round($share_money*$rate,2);
				
				$count += $c_money;
				
				$tr.='<tr>';
				if($i==1){
					$tr.='<td rowspan="'.$num.'">'.$clientid.'</td>';
				}
				$tr.='<td>'.$payname.'</td>
				<td align="right">'.sprintf('%d',$payway_rate*100).'%</td>
				<td>'.$money.'</td>
				<td align="right">'.sprintf('%.2f',$fee).'</td>
				<td align="right">'.sprintf('%d',$ipay_pay_rate*100).'%</td>
				<td align="right">'.sprintf('%.2f',$ipay_fee).'</td>
				<td align="right">'.sprintf('%.2f',$share_money).'</td>
				<td align="right">'.sprintf('%d',$rate*100).'%</td>
				<td align="right">'.sprintf('%.2f',$c_money).'</td>';
				if($i==1){
					$tr.='<td rowspan="'.$num.'" align="right">{count}</td>';
				}
				$tr.='<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>';
			}
			$tr = str_replace('{count}',sprintf('%.2f',$c_money),$tr);
			$ipay_c_moeny[$clientid] = $c_money;
			$html .= $tr;
			
		}
		
		$html .= '</table></body></html>';
		$this->write($filename_ipay, $html);
		$t_reportorder = null;
		$html = null;
		
		
		//我方接入计费平台对帐单
		$html = '<!DOCTYPE html><html lang="zh-cn">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>游戏信息费对帐单</title>
		</head><body><div>深圳市同楼网络科技有限公司'.$yearmonth.'游戏信息费对帐单</div>
		<table border="1" cellpadding="0" cellspacing="0">
		<tr>
    <td align="center">结算周期</td>
    <td align="center">渠道号</td>
    <td align="center">公司名称</td>
    <td align="center">总消费金额</td>
    <td align="center">爱贝计费</td>
    <td align="center">移动计费</td>
    <td align="center">联通计费</td>
    
    <td align="center">联通费率（加坏帐）</td>
    <td align="center">联通运营费</td>
    <td align="center">税率</td>
    <td align="center">联通平台可分配收益</td>
    <td align="center">分成比率</td>
    <td align="center">联通计费平台分成金额</td>
    
    <td align="center">移动费率（加坏帐）</td>
    <td align="center">移动运营费</td>
    <td align="center">税率</td>
    <td align="center">移动平台可分配收益</td>
    <td align="center">分成比率</td>
    <td align="center">移动计费平台分成金额</td>
    
    <td align="center">爱贝计费平台分成金额</td>
    <td align="center">分成总金额</td>
    <td align="center">负责人</td>
    <td align="center">备注</td>
  </tr>';
		$yd_rate = $setting->get_yd_rate();
		$lt_rate = $setting->get_lt_rate();
		//ksort($t_reportorder_yd);
		//ksort($companys);
		foreach($companys1 as $clientid=>$cominfo){
			$money = isset($t_reportorder_yd[$clientid])?$t_reportorder_yd[$clientid]:0;
			$ipay_money = isset($t_reportorder_ipay[$clientid])?$t_reportorder_ipay[$clientid]:0;
			$lt_money = isset($t_reportorder_lt[$clientid])?$t_reportorder_lt[$clientid]:0;
			$count = $money + $ipay_money + $lt_money;
			$companys1[$clientid]['_count'] = $count;
		}
		uasort($companys1, array('self','compare_count'));
		//foreach($t_reportorder_yd as $clientid=>$money){
		foreach($companys1 as $clientid=>$cominfo){
			//if(!isset($companys1[$clientid]))continue;
			
			$money = isset($t_reportorder_yd[$clientid])?$t_reportorder_yd[$clientid]:0;
			$rate = isset($yd_rate[$clientid])?$yd_rate[$clientid]:$yd_rate[0];
			$tr = '';
			$agentname = isset($companys1[$clientid])?$companys1[$clientid]['name']:'';
			$ipay_money = isset($t_reportorder_ipay[$clientid])?$t_reportorder_ipay[$clientid]:0;
			
			$lt_money = isset($t_reportorder_lt[$clientid])?$t_reportorder_lt[$clientid]:0;
			$rate_lt = isset($lt_rate[$clientid])?$lt_rate[$clientid]:$lt_rate[0];
			
			//计算移动分成
			$count = $money + $ipay_money + $lt_money;
			$yd_pay_rate = $base_rate['yd_pay_rate'];
			$yd_cost = round($yd_pay_rate*$money,2);
			$yd_pay_tax_rate = $base_rate['yd_pay_tax_rate'];
			$yd_pay_tax = round(($money-$yd_cost)*$yd_pay_tax_rate,2);
			$share_money = $money-$yd_cost-$yd_pay_tax;
			$c_money = round($share_money*$rate,2);
			
			//计算联通分成
			$lt_pay_rate = $base_rate['lt_pay_rate'];
			$lt_cost = round($lt_pay_rate*$lt_money,2);
			$lt_pay_tax_rate = $base_rate['lt_pay_tax_rate'];
			$lt_pay_tax = round(($lt_money-$lt_cost)*$lt_pay_tax_rate,2);
			$lt_share_money = $lt_money-$lt_cost-$lt_pay_tax;
			$lt_c_money = round($lt_share_money*$rate_lt,2);
			
			//计算爱贝分成
			$ipay_divide = isset($ipay_c_moeny[$clientid])?$ipay_c_moeny[$clientid]:0;

	
			$tr.='<tr>';
			$tr.='<td>'.$date1.'~'.$date2.'</td>
			<td>'.$clientid.'</td>
			<td>'.$agentname.'</td>
			<td align="right">'.$count.'</td>
			<td align="right">'.$ipay_money.'</td>
			<td align="right">'.$money.'</td>
			<td align="right">'.$lt_money.'</td>
			
			<td align="right">'.sprintf('%d',$lt_pay_rate*100).'%</td>
			<td align="right">'.sprintf('%.2f',$lt_cost).'</td>
			<td align="right">'.sprintf('%d',$lt_pay_tax_rate*100).'%</td>
			<td align="right">'.sprintf('%.2f',$lt_share_money).'</td>
			<td align="right">'.sprintf('%d',$rate_lt*100).'%</td>
			<td align="right">'.sprintf('%.2f',$lt_c_money).'</td>
			
			<td align="right">'.sprintf('%d',$yd_pay_rate*100).'%</td>
			<td align="right">'.sprintf('%.2f',$yd_cost).'</td>
			<td align="right">'.sprintf('%d',$yd_pay_tax_rate*100).'%</td>
			<td align="right">'.sprintf('%.2f',$share_money).'</td>
			<td align="right">'.sprintf('%d',$rate*100).'%</td>
			<td align="right">'.sprintf('%.2f',$c_money).'</td>
			
			
			<td align="right">'.sprintf('%.2f',$ipay_divide).'</td>
			<td align="right">'.sprintf('%.2f',$c_money+$ipay_divide+$lt_c_money).'</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr>';
				
			$html .= $tr;
				
		}
		//$t_reportorder_yd = null;
		
		$html .= '</table></body></html>';
		$this->write($filename_ours, $html);
		$t_reportorder_yd = null;
		$t_reportorder_ipay = null;
		$html = null;
		
		
		//渠道自有支付平台对帐单
		$html = '<!DOCTYPE html><html lang="zh-cn">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>游戏信息费对帐单</title>
		</head><body>
		<table border="1" cellpadding="0" cellspacing="0">
		<tr>
		<td align="center">结算周期</td>
		<td align="center">渠道号</td>
	    <td align="center">接入平台</td>
	    <td align="center">支付渠道</td>
	    <td align="center">支付方式</td>
	    <td align="center">总消费金额</td>
	    <td align="center">第三方费率</td>
	    <td align="center">第三方费用</td>
	    <td align="center">可分配收益</td>
	    <td align="center">分成金额</td>
		</tr>';
		ksort($t_reportorder_client);
		foreach($t_reportorder_client as $clientid=>$money){
			$rate = isset($yd_rate[$clientid])?$yd_rate[$clientid]:$yd_rate[0];
			$tr = '';
			
			$payname = isset($payways[$channel_paytypes[$clientid]])?$payways[$channel_paytypes[$clientid]]['name']:'';
		
			$tr.='<tr>';
			$tr.='<td>'.$date1.'~'.$date2.'</td>
			<td>'.$clientid.'</td>
			<td>'.$payname.'</td>
			<td align="right">&nbsp</td>
			<td align="right">&nbsp</td>
			<td align="right">'.$money.'</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr>';
		
			$html .= $tr;
		
		}
		
		$html .= '</table></body></html>';
		$this->write($filename_clients, $html);
		$t_reportorder_client = null;
		$html = null;
		
		
		//移动自有渠道对帐单
		$html = '<!DOCTYPE html><html lang="zh-cn">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>游戏信息费对帐单</title>
		</head><body><div>深圳市同楼网络科技有限公司'.$yearmonth.'游戏信息费对帐单</div>
		<table border="1" cellpadding="0" cellspacing="0">
		<tr>
		<td align="center">结算周期</td>
		<td align="center">渠道号</td>
		<td align="center">公司名称</td>
		<td align="center">总消费金额</td>
		<td align="center">费率</td>
		<td align="center">移动运营费</td>
		<td align="center">应收帐款（我方）</td>
		</tr>';
		$yd_channel_rate = $setting->get_yd_channel();
		//ksort($t_reportorder_ydchannel);
		//ksort($companys);
		//foreach($yd_channels as $clientid=>$rate){
		foreach($companys2 as $clientid=>$cominfo){
			$companys2[$clientid]['_count'] = isset($t_reportorder_ydchannel[$clientid])?$t_reportorder_ydchannel[$clientid]:0;
		}
		uasort($companys2, array('self','compare_count'));
		foreach($companys2 as $clientid=>$cominfo){
			//if(!isset($companys2[$clientid]))continue;
			$rate = isset($yd_channel_rate[$clientid])?$yd_channel_rate[$clientid]:$yd_channel_rate[0];
			//$money = isset($t_reportorder_ydchannel[$clientid])?$t_reportorder_ydchannel[$clientid]:0;
			$money = $cominfo['_count'];//isset($t_reportorder_yd[$clientid])?$t_reportorder_yd[$clientid]:0;
			$tr = '';
			$agentname = isset($companys2[$clientid])?$companys2[$clientid]['name']:'';
			$yd_cost = round($rate*$money,2);
			$share_money = $money-$yd_cost;
		
		
			$tr.='<tr>';
			$tr.='<td>'.$date1.'~'.$date2.'</td>
			<td>'.$clientid.'</td>
			<td>'.$agentname.'</td>
			<td align="right">'.$money.'</td>
			<td align="right">'.sprintf('%d',$rate*100).'%</td>
			<td align="right">'.sprintf('%.2f',$yd_cost).'</td>
			<td align="right">'.sprintf('%.2f',$share_money).'</td>
			</tr>';
		
			$html .= $tr;
		
		}
		
		$html .= '</table></body></html>';
		$this->write($filename_ydchannel, $html);
		$t_reportorder_ydchannel = null;
		$html = null;
		return true;
		
 	}
 	
 	/**
 	 * 记录最近渠道商更新过的时间
 	 */
 	public function update_company_time(){
 		$filename = SROOT.'/data/cache/company/modtime'.CACHE_FILE_EXT;
 		file_put_contents($filename, time());
 		return true;
 	}
 	
 	/**
 	 * 更新渠道商缓存
 	 */
 	public function update_company_cache(){
 		$filename = SROOT.'/data/cache/company/modtime'.CACHE_FILE_EXT;
 		file_put_contents($filename, 946656000);
 		$company = new company($this->db);
 		$company_list = $company->getCompanyList(-1, 0, 0, 999999999);
 		$company_list = $company_list[0];
 		$channel1=array();
 		$channel2=array();
 		$subchannel=array();
 		foreach($company_list as $k=>$v){
 			if($v['clientid']==0)continue;
 				
 			if($v['clientids']==0){
 				if($v['channeltype']==1){
 					$channel1[$v['clientid']]=$v;
 				}else{
 					$channel2[$v['clientid']]=$v;
 				}
 			}
 			$subchannel[$v['clientid']][$v['clientids']]=$v;
 		}
 			
 		$this->update_company($channel1,'1');
 		$this->update_company($channel2,'2');
 			
 		$this->update_subcompany($subchannel);
 			
 		$channel1=null;
 		$channel2=null;
 		$subchannel=null;
 		return true;
 	}
 	
 
 	
 	private function write($filename, $data) {
 		if($fp = @fopen($filename, 'w+')){
 			fwrite($fp, $data);
 			fclose($fp);
 			return true;
 		}
 		return false;
 	}
 	
 	private function compare_count($a,$b){
 		return $a['_count']>$b['_count']?-1:1;
 	}
 
 }
?> 