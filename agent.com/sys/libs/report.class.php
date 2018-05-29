<?php
 /**
 * 报表数据类
 * @package application
 * @since 1.0.0 (2013-03-22)
 * @version 1.0.0 (2013-03-22)
 * @author jun <huanghaijun@mykj.com>
 */
 
 class report{
 	private $db = null;
 	public function __construct($db=''){
 		//连接数据库
 		$this->db=$db;
 	}
 	
 	/**
 	 * 分页取二渠道商统计列表
 	 */
 	public function get_report_list($channeltype,$gameid,$clientid,$date1,$date2,$pagesize,$page){
 		
 		$companys = $this->get_channel_list($channeltype);
 		
 		$offset = ($page-1)*$pagesize;
 		
 		$reports = $this->get_report_data($gameid, $clientid, $date1, $date2);
 		
 		var_dump($reports);
 		$t_data =  array();
 		$counts = array();
 		foreach($companys as $k=>$v){
 			if($clientid>0){
 				if($clientid!=$v['clientid'])continue;
 			}
 			$arr = array(
	 					'clientid'=>$v['clientid'],
	 					'name'=>$v['name'],
	 					'registerusers'=>getnum($reports[$k]['registerusers']),
 					    'loginusers'=>getnum($reports[$k]['loginusers']),
	 					'activeusermonth'=>getnum($reports[$k]['activeusermonth']),
	 					'avgplaygamemonth'=>formatdate(getnum($reports[$k]['avgplaygamemonth'])),
	 					'consumeorders'=>getnum($reports[$k]['consumeorders']),
	 					'consumeusers'=>getnum($reports[$k]['consumeusers']),
	 					'consumemoney'=>getnum($reports[$k]['consumemoney']),
 					    'consumemoneyipay'=>getnum($reports[$k]['consumemoneyipay']),
 					    'consumemoneyyd'=>getnum($reports[$k]['consumemoneyyd']),
	 					'firstloginnum'=>getnum($reports[$k]['firstloginnum']),
	 					'firstpaymentusernum'=>getnum($reports[$k]['firstpaymentusernum']),
	 					'firstpaymenttime'=>getnum($reports[$k]['firstpaymenttime']),
	 					'firstpaymentmoney'=>getnum($reports[$k]['firstpaymentmoney']),
	 					   
 					);
 			
 			$t_data[] = $arr;
 			
 			$counts['registerusers']+=$arr['registerusers'];
 			$counts['loginusers']+=$arr['loginusers'];
 			$counts['consumeusers']+=$arr['consumeusers'];
 			$counts['consumemoney']+=$arr['consumemoney'];
 			$counts['consumemoneyipay']+=$arr['consumemoneyipay'];
 			$counts['consumemoneyyd']+=$arr['consumemoneyyd'];
 			$counts['firstloginnum']+=$arr['firstloginnum'];
 			$counts['firstpaymentusernum']+=$arr['firstpaymentusernum'];
 			$counts['firstpaymenttime']+=$arr['firstpaymenttime'];
 			$counts['firstpaymentmoney']+=$arr['firstpaymentmoney'];
 		
 		}
 		
    
       
 		uasort($t_data, array('self','compare'));
 		$rows = count($t_data);
 		
 		//var_dump($t_data);
 		//exit();
 		
 		if($pagesize>0){
 			$t_data = array_slice($t_data, $offset, $pagesize,true);
 		}
 		
 		/* foreach($reports as $k=>$v){
 			$reports[$k]['clientid'] = $k;
 			$reports[$k]['name'] = isset($companys[$k]['name'])?$companys[$k]['name']:'';
 		} */		
 		$companys = null;
 		$result = array('rows'=>$rows,'list'=>$t_data,'counts'=>$counts);
 		return $result;
 	}
 	
 	
 	/**
 	 * 分页取某渠道商统计列表
 	 */
 	public function get_report_sub_list($gameid,$clientid,$date1,$date2,$pagesize,$page,$subclientid=-1){
 			
 		$companys = $this->get_subchannel_list($clientid);

 		$offset = ($page-1)*$pagesize;
 			
 		$reports = $this->get_report_sub_data($gameid, $clientid,-1, $date1, $date2);
 		$t_data =  array();
 		$counts = array();
 		foreach($companys as $k=>$v){
 			if($subclientid>-1){
 				if($subclientid!=$v['clientids'])continue;
 			}
 			$arr = array(
 					'clientid'=>$clientid,
 					'clientids'=>$k,
 					'name'=>$v['name'],
 					'registerusers'=>getnum($reports[$k]['registerusers']),
 					'loginusers'=>getnum($reports[$k]['loginusers']),
 					'activeusermonth'=>getnum($reports[$k]['activeusermonth']),
 					'avgplaygamemonth'=>formatdate(getnum($reports[$k]['avgplaygamemonth'])),
 					'consumeorders'=>getnum($reports[$k]['consumeorders']),
 					'consumeusers'=>getnum($reports[$k]['consumeusers']),
 					'consumemoney'=>getnum($reports[$k]['consumemoney']),
 					'consumemoneyipay'=>getnum($reports[$k]['consumemoneyipay']),
 					'consumemoneyyd'=>getnum($reports[$k]['consumemoneyyd']),
 					'firstloginnum'=>getnum($reports[$k]['firstloginnum']),
 					'firstpaymentusernum'=>getnum($reports[$k]['firstpaymentusernum']),
 					'firstpaymenttime'=>getnum($reports[$k]['firstpaymenttime']),
 					'firstpaymentmoney'=>getnum($reports[$k]['firstpaymentmoney']),
 					
 			);
 	
 			$t_data[] = $arr;
 			$counts['registerusers']+=$arr['registerusers'];
 			$counts['loginusers']+=$arr['loginusers'];
 			$counts['consumeusers']+=$arr['consumeusers'];
 			$counts['consumemoney']+=$arr['consumemoney'];
 			$counts['consumemoneyipay']+=$arr['consumemoneyipay'];
 			$counts['consumemoneyyd']+=$arr['consumemoneyyd'];
 			$counts['firstloginnum']+=$arr['firstloginnum'];
 			$counts['firstpaymentusernum']+=$arr['firstpaymentusernum'];
 			$counts['firstpaymenttime']+=$arr['firstpaymenttime'];
 			$counts['firstpaymentmoney']+=$arr['firstpaymentmoney'];
 		}
 			
 		uasort($t_data, array('self','compare'));
 			
 		$rows = count($t_data);
 			
 		if($pagesize>0){
 			$t_data = array_slice($t_data, $offset, $pagesize,true);
 		}
 		
 			
 		/* foreach($reports as $k=>$v){
 		 $reports[$k]['clientid'] = $k;
 		$reports[$k]['name'] = isset($companys[$k]['name'])?$companys[$k]['name']:'';
 		} */
 		$companys = null;
 		$result = array('rows'=>$rows,'list'=>$t_data,'counts'=>$counts);
 		return $result;
 	}
 	
 	
 	/**
 	 * 分页取日月报表-二三级总表
 	 */
 	public function get_report_count_list($reporttype, $gameid,$clientid,$date1,$date2,$pagesize,$page){
 		
 		//$companys = $this->get_subchannel_list($clientid);
 		//取渠道信息
 		$companys = $this->get_channel_list(1)+$this->get_channel_list(2);
 		$company_info = $companys[$clientid];
 		$companys = null;
 			
 		//找不到渠道信息直接输出空
 		if(empty($company_info)){
 			$result = array('rows'=>0,'list'=>array(),'counts'=>array());
 			return $result;
 		}
 		
 		$offset = ($page-1)*$pagesize;
 		
 		$t_data =  array();
 		$counts = array();
 		if($reporttype=='day'){
 			$dates = $this->get_dates($date1, $date2);
 		}else{
 			$dates = $this->get_months($date1, $date2);
 		}
 		foreach($dates as $k=>$v){
 			if($reporttype=='day'){
 				$date1 = $date2 = $v;
 			}else{
 				$date1 = $v.'-01';
 				$date2 = date('Y-m-t',strtotime($date1));
 			}
 			
 			$reports = $this->get_report_data($gameid, $clientid, $date1, $date2);
 			//var_dump($reports);
 			$arr = array(
 					'today'=>$v,
 					'clientid'=>$clientid,
 					'name'=>$company_info['name'],
 					'channeltype'=>$company_info['channeltype'],
 					'registerusers'=>getnum($reports[$clientid]['registerusers']),
 					'loginusers'=>getnum($reports[$clientid]['loginusers']),
 					'activeusermonth'=>getnum($reports[$clientid]['activeusermonth']),
 					'avgplaygamemonth'=>formatdate(getnum($reports[$clientid]['avgplaygamemonth'])),
 					'consumeorders'=>getnum($reports[$clientid]['consumeorders']),
 					'consumeusers'=>getnum($reports[$clientid]['consumeusers']),
 					'consumemoney'=>getnum($reports[$clientid]['consumemoney']),
 					'consumemoneyipay'=>getnum($reports[$clientid]['consumemoneyipay']),
 					'consumemoneyyd'=>getnum($reports[$clientid]['consumemoneyyd']),
 					'firstloginnum'=>getnum($reports[$clientid]['firstloginnum']),
 					'firstpaymentusernum'=>getnum($reports[$clientid]['firstpaymentusernum']),
 					'firstpaymenttime'=>getnum($reports[$clientid]['firstpaymenttime']),
 					'firstpaymentmoney'=>getnum($reports[$clientid]['firstpaymentmoney']),
 			);
 			
 			$t_data[] = $arr;
 			
 			$counts['registerusers']+=$arr['registerusers'];
 			$counts['loginusers']+=$arr['loginusers'];
 			$counts['consumeusers']+=$arr['consumeusers'];
 			$counts['consumemoney']+=$arr['consumemoney'];
 			$counts['consumemoneyipay']+=$arr['consumemoneyipay'];
 			$counts['consumemoneyyd']+=$arr['consumemoneyyd'];
 			$counts['firstloginnum']+=$arr['firstloginnum'];
 			$counts['firstpaymentusernum']+=$arr['firstpaymentusernum'];
 			$counts['firstpaymenttime']+=$arr['firstpaymenttime'];
 			$counts['firstpaymentmoney']+=$arr['firstpaymentmoney'];
 		}
 		//print_r($t_data);exit;
 			
 		//uasort($t_data, array('self','compare'));
 			
 		$rows = count($t_data);
 			
 		if($pagesize>0){
 			$t_data = array_slice($t_data, $offset, $pagesize,true);
 		}
 			
 		/* foreach($reports as $k=>$v){
 		 $reports[$k]['clientid'] = $k;
 		$reports[$k]['name'] = isset($companys[$k]['name'])?$companys[$k]['name']:'';
 		} */
 		$companys = null;
 		
 		$result = array('rows'=>$rows,'list'=>$t_data,'counts'=>$counts);
 		return $result;
 	}
 	
 	
 	/**
 	 * 分页取日月报表-三级
 	 */
 	public function get_report_sub_count_list($reporttype, $gameid,$clientid,$subclientid,$date1,$date2,$pagesize,$page){
 			
 		//取渠道信息
 		$companys = $this->get_subchannel_list($clientid);
 		$company_info = $companys[$subclientid];
 		$companys = null;
 		
 		//找不到渠道信息直接输出空
 		if(empty($company_info)){
 			$result = array('rows'=>0,'list'=>array(),'counts'=>array());
 			return $result;
 		}
 	
 		$offset = ($page-1)*$pagesize;
 			
 		$t_data =  array();
 		$counts = array();
 		if($reporttype=='day'){
 			$dates = $this->get_dates($date1, $date2);
 		}else{
 			$dates = $this->get_months($date1, $date2);
 		}
 		foreach($dates as $k=>$v){
 			if($reporttype=='day'){
 				$date1 = $date2 = $v;
 			}else{
 				$date1 = $v.'-01';
 				$date2 = date('Y-m-t',strtotime($date1));
 			}
 	
 			$reports = $this->get_report_sub_data($gameid, $clientid, $subclientid, $date1, $date2);
 			$arr = array(
 					'today'=>$v,
 					'clientid'=>$clientid,
 					'clientids'=>$subclientid,
 					'name'=>$company_info['name'],
 					'channeltype'=>$company_info['channeltype'],
 					'registerusers'=>getnum($reports[$subclientid]['registerusers']),
 					'loginusers'=>getnum($reports[$subclientid]['loginusers']),
 					'activeusermonth'=>getnum($reports[$subclientid]['activeusermonth']),
 					'avgplaygamemonth'=>formatdate(getnum($reports[$subclientid]['avgplaygamemonth'])),
 					'consumeorders'=>getnum($reports[$subclientid]['consumeorders']),
 					'consumeusers'=>getnum($reports[$subclientid]['consumeusers']),
 					'consumemoney'=>getnum($reports[$subclientid]['consumemoney']),
 					'consumemoneyipay'=>getnum($reports[$subclientid]['consumemoneyipay']),
 					'consumemoneyyd'=>getnum($reports[$subclientid]['consumemoneyyd']),
 					'firstloginnum'=>getnum($reports[$subclientid]['firstloginnum']),
 					'firstpaymentusernum'=>getnum($reports[$subclientid]['firstpaymentusernum']),
 					'firstpaymenttime'=>getnum($reports[$subclientid]['firstpaymenttime']),
 					'firstpaymentmoney'=>getnum($reports[$subclientid]['firstpaymentmoney'])
 			);
 	
 			$t_data[] = $arr;
 			$counts['registerusers']+=$arr['registerusers'];
 			$counts['loginusers']+=$arr['loginusers'];
 			$counts['consumeusers']+=$arr['consumeusers'];
 			$counts['consumemoney']+=$arr['consumemoney'];
 			$counts['consumemoneyipay']+=$arr['consumemoneyipay'];
 			$counts['consumemoneyyd']+=$arr['consumemoneyyd'];
 			$counts['firstloginnum']+=$arr['firstloginnum'];
 			$counts['firstpaymentusernum']+=$arr['firstpaymentusernum'];
 			$counts['firstpaymenttime']+=$arr['firstpaymenttime'];
 			$counts['firstpaymentmoney']+=$arr['firstpaymentmoney'];
 		}
 		//print_r($t_data);exit;
 	
 		//uasort($t_data, array('self','compare'));
 	
 		$rows = count($t_data);
 	
 		if($pagesize>0){
 			$t_data = array_slice($t_data, $offset, $pagesize,true);
 		}
 	
 		/* foreach($reports as $k=>$v){
 		 $reports[$k]['clientid'] = $k;
 		$reports[$k]['name'] = isset($companys[$k]['name'])?$companys[$k]['name']:'';
 		} */
 		$companys = null;
 		$result = array('rows'=>$rows,'list'=>$t_data,'counts'=>$counts);
 		return $result;
 	}
 	
 	
 	public function get_channel_list($channeltype=1){
 		$data = array();
 		$filename = SROOT.'/data/cache/company/channel'.$channeltype.CACHE_FILE_EXT;
 		if(file_exists($filename)) {
 			$data = json_decode(file_get_contents($filename),true);
 		}
 		return $data;
 	}
 	
 	public function get_subchannel_list($clientid){
 		$data = array();
 		$filename = SROOT.'/data/cache/company/subchannel/'.$clientid.CACHE_FILE_EXT;
 		if(file_exists($filename)){
 			$data = json_decode(file_get_contents($filename),true);
 		}
 		return $data;
 	}
 	
 	/**
 	 * 缓存取二级渠道汇总数据
 	 */
 	public function get_report_data($gameid,$clientid,$date1,$date2,$year=0,$month=0){
 		
 		$results = array();
 		/* if($date1==$date2 && !empty($date1)){
 			$filename = SROOT.'/data/cache/reportall/'.$date1.CACHE_FILE_EXT;
 			$data = json_decode(file_get_contents($filename),true);
 		} */
 		$dates = $this->get_cache_filename($date1, $date2);
 		$datesnum = count($dates);
 		$data = array();
 		//print_r($dates);
 		//var_dump($dates);
 		foreach($dates as $k=>$v){
 			$filename = SROOT.'/data/cache/reportall/'.$v.CACHE_FILE_EXT;
 			if(file_exists($filename)){
 				$tmp_data = json_decode(file_get_contents($filename),true);
 				if($datesnum>0){
	 				if(!empty($tmp_data)){
	 					foreach($tmp_data as $cid=>$arr1){
	 						foreach($arr1 as $gid=>$arr2){
	 							foreach($arr2 as $key=>$val){
	 								$data[$cid][$gid][$key]+=$val;
	 							}
	 						}
	 					}
	 				}
 				}else{
 					$data = $tmp_data;
 				}
 			}
 		}
 		
 		if($clientid>0){
 			if(isset($data[$clientid])){
 				$data = $data[$clientid];
 				$results[$clientid] = $data[$gameid];
 			}
 		}else{
 			foreach($data as $k=>$v){
 				$results[$k] = $v[$gameid];
 			}
 		} 	
 		//print_r($results);exit;
 		return $results;
 	}
 	
 	/**
 	 * 缓存取某二级渠道的子渠道数据
 	 */
 	public function get_report_sub_data($gameid,$clientid,$subclientid,$date1,$date2,$year=0,$month=0){
 		$results = array();
 		
 		$dates = $this->get_cache_filename($date1, $date2);
 		$datesnum = count($dates);
 		$data = array();
 		
 		foreach($dates as $k=>$v){
 			$filename = SROOT.'/data/cache/reportsub/'.$clientid.'/'.$v.CACHE_FILE_EXT;
 			if(file_exists($filename)){
 				$tmp_data = json_decode(file_get_contents($filename),true);
 				if($datesnum>0){
	 				if(!empty($tmp_data)){
	 					foreach($tmp_data as $cid=>$arr1){
	 						foreach($arr1 as $gid=>$arr2){
	 							foreach($arr2 as $key=>$val){
	 								$data[$cid][$gid][$key]+=$val;
	 							}
	 						}
	 					}
	 				}
 				}else{
 					$data = $tmp_data;
 				}
 			}
 		}
 		
 		/* if($date1==$date2 && !empty($date1)){
 			$filename = SROOT.'/data/cache/reportsub/'.$clientid.'/'.$date1.CACHE_FILE_EXT;
 			$data = json_decode(file_get_contents($filename),true);
 		} */
 		
 		if($subclientid>=0){
 			if(isset($data[$subclientid])){
 				$data = $data[$subclientid];
 				$results[$subclientid] = $data[$gameid];
 			}
 		}else{
 			foreach($data as $k=>$v){
 				$results[$k] = $v[$gameid];
 			}
 		}
 		//print_r($results);exit;
 		return $results;
 	}
 	
 	/**
 	 * 取统计概览
 	 * @param  $clientid
 	 * @param  $subclientid
 	 * @return array
 	 */
 	function get_report_total($clientid=0,$subclientid=0){
 		$filename = SROOT.'/data/cache/reportcount/'.$clientid.'_'.$subclientid.CACHE_FILE_EXT;
 		$is_old = 1;
 		if(file_exists($filename)){
 			$mtime = filemtime($filename);
 			//if(date('Ymd')==date('Ymd',$mtime)){
 			if(time()-$mtime<3600){
 				$is_old = 0;
 			}
 		}
 		
 		
 		if($is_old){
 			$task = new task($this->db);
 			$task->update_report_total($clientid,$subclientid);
 		}
 		
 		$data = array();
 		if(file_exists($filename)){
 			$data = json_decode(file_get_contents($filename),true);
 		}
 		return $data;
 	}
 	
 	/**
 	 * 取缓存文件名称
 	 */
 	public function get_cache_filename($date1,$date2){
 		$dates = $this->get_cache_filenames($date1,$date2);
 		$dates = $this->merge_year($dates);
 		return $dates;
 	}
 	
 	/**
 	 * 取缓存文件名称
 	 */
 	public function get_cache_filenames($date1,$date2){
 		$dates = array();
 		$dateint1 = strtotime($date1);
 		$dateint2 = strtotime($date2);
 		if($dateint1>$dateint2){
 			$dateint2 = $dateint1;
 		}
 			
 		if(strtotime('-1 day')<$dateint2){
 			$dateint2 = strtotime('-1 day');
 		}
 	
 		if($dateint1==$dateint2 && $dateint1>0){
 			//所选日期
 			$dates[] = date('Y-m-d',$dateint1);
 	
 		}elseif(date('Ym',$dateint1)==date('Ym',$dateint2) && date('j',$dateint1)==1 &&
 				(date('t',$dateint2)==date('j',$dateint2)
 						|| date('Ym')==date('Ym',$dateint2) && date('Y-m-d',strtotime('-1 day'))==date('Y-m-d',$dateint2))  ){
 			//某月
 			$dates[] = date('Y-m',$dateint1);
 		}elseif(date('Y',$dateint1)==date('Y',$dateint2) && date('md',$dateint1)=='0101' &&
 				(date('md',$dateint2)=='1231'
 						|| date('Ym')==date('Ym',$dateint2) && date('Y-m-d',strtotime('-1 day'))==date('Y-m-d',$dateint2))  ){
 			//某年
 			$dates[] = date('Y',$dateint1);
 	
 		}elseif(date('Y',$dateint1)<date('Y',$dateint2) && date('md',$dateint1)=='0101' &&
 				(date('md',$dateint2)=='1231'
 						|| date('Ym')==date('Ym',$dateint2) && date('Y-m-d',strtotime('-1 day'))==date('Y-m-d',$dateint2))  ){
 			//某年
 			for($i=date('Y',$dateint1);$i<=date('Y',$dateint2);$i++){
 				$dates[] = $i;
 			}
 	
 		}elseif($dateint1<$dateint2 && $dateint1>0 && $dateint2>0){
 			//日期间范围
 			if(date('Ym',$dateint1)!=date('Ym',$dateint2)){
 				$date1 = date('Y-m-d',$dateint1);
 				$date2 = date('Y-m-t',$dateint1);
 				$dates = array_merge($dates,$this->get_cache_filenames($date1, $date2));
 				$date1 = date('Y-m-1',$dateint2);
 				$date2 = date('Y-m-d',$dateint2);
 				$dates = array_merge($dates,$this->get_cache_filenames($date1, $date2));
 					
 				$dateint1 = mktime(0,0,0,date('n',$dateint1)+1,date('j',$dateint1),date('Y',$dateint1));
 				while(date('Ym',$dateint1)<date('Ym',$dateint2)){
 					$dates[] = date('Y-m',$dateint1);
 					$dateint1 = mktime(0,0,0,date('n',$dateint1)+1,date('j',$dateint1),date('Y',$dateint1));
 				}
 			}else{
 				for($i=$dateint1;$i<=$dateint2;$i+=3600*24){
 					$dates[] = date('Y-m-d',$i);
 				}
 			}
 		}
 			
 		return $dates;
 	}
 	
 	/**
 	 * 将出现的1-12月合并为一年
 	 * @param  $dates
 	 */
 	private function merge_year($dates){
 		$years = array();
 		foreach($dates as $k=>$v){
 			$years[substr($v,0,4)] = substr($v,0,4);
 		}
 		
 		foreach($years as $k=>$v){
 			$in_year = 1;
 			$months = array();
 			for($i=1;$i<=12;$i++){
 				if(!in_array(sprintf('%04d-%02d',$k,$i),$dates)){
 					$in_year = 0;
 					break;
 				}else{
 					$months[] = sprintf('%04d-%02d',$k,$i);
 				}
 			}
 			
 			if($in_year){
 				$dates = array_diff($dates, $months);
 				$dates[] = $k;
 			}
 		}
 		//print_r($dates);
 		return $dates;
 	}
 	
 	
 	/**
 	 * 取两日期间的日期
 	 */
 	private function get_dates($date1,$date2){
 		$dates = array();
 		$dateint1 = strtotime($date1);
 		$dateint2 = strtotime($date2);
 		if($dateint1>$dateint2){
 			$dateint2 = $dateint1;
 		}
 			
 		if(strtotime('-1 day')<$dateint2){
 			$dateint2 = strtotime('-1 day');
 		}
 	
 		if($dateint1==$dateint2 && $dateint1>0){
 			//所选日期
 			$dates[] = date('Y-m-d',$dateint1);
 	
 		}else{
 			for($i=$dateint1;$i<=$dateint2;$i+=3600*24){
 				$dates[] = date('Y-m-d',$i);
 			}
 		}
 		return $dates;
 	}
 	
 	/**
 	 * 取两日期间的月份
 	 */
 	private function get_months($date1,$date2){
 		$dates = array();
 		$dateint1 = strtotime($date1);
 		$dateint2 = strtotime($date2);
 		if($dateint1>$dateint2){
 			$dateint2 = $dateint1;
 		}
 	
 		if(strtotime('-1 day')<$dateint2){
 			$dateint2 = strtotime('-1 day');
 		}
 	
 		if($dateint1==$dateint2 && $dateint1>0){
 			//所选日期
 			$dates[] = date('Y-m',$dateint1);
 	
 		}else{
 			for($i=$dateint1;$i<=$dateint2;$i+=3600*24){
 				if(!in_array(date('Y-m',$i), $dates)){
 					$dates[] = date('Y-m',$i);
 				}
 			}
 		}
 		return $dates;
 	}
 	
 	/**
 	 * 按消息总额排序
 	 */
 	public function compare($a,$b){
	 	 if ($a == $b) {
	        return 0;
	    }
 			$a['consumemoney'] = getnum($a['consumemoney']);
 			$b['consumemoney'] = getnum($b['consumemoney']);
 			$a['registerusers'] = getnum($a['registerusers']);
 			$b['registerusers'] = getnum($b['registerusers']);
 			if ($a['consumemoney']>$b['consumemoney']){
 				return -1;
 			}elseif($a['consumemoney'] == $b['consumemoney']){
 				if($a['registerusers']>$b['registerusers']){
 					return -1;
 				}else{
 					return 1;
 				}
 			}else{
 				return 1;
 			}
 	
 	}
 	
 	
 	
 	/**
	 * 按条件取支付方式列表
	 * @param date $date1    起日期
	 * @param date $date2    止日期
	 * @param int $clientid    渠道号
	 * @param bigint $subclientid    子渠道号(-1时取所有)
	 */
	public function get_paywaylist($date1,$date2,$clientid,$subclientid=-1,$gameid=0){
		if((string)$subclientid==='')$subclientid=-1;
		$arr=array($date1,$date2,intval($clientid),intval($subclientid),intval($gameid));
		$res = $this->db->simpleCall("sp_web_paywaylist", $arr);
		if(empty($res[0])){
			return array();
		}
		//print_r($res[0]);
		$setting = new setting();
		$our_paytype = $setting->get_our_paytype();
		$channel_paytype = $setting->get_channel_paytype();
		//print_r($our_paytype);
		if(in_array(50,$our_paytype)){
			foreach($GLOBALS['ini_ipay_paytypes'] as $k=>$v){
				if($k==50)continue;
				$our_paytype[]=$k;
			}
		}
		foreach($res[0] as $k=>$v){
			if(!in_array($v['payway'],$our_paytype)){
				if(!(isset($channel_paytype[$clientid])&&$channel_paytype[$clientid]==$v['payway'])){
					unset($res[0][$k]);
				}
			}
		}
		
		$rtndata=array();
		foreach($res[0] as $key=>$val){
			//对酷派，应用汇的特殊处理(Ipay支付)
			if(50==$val['payway']){
				$payways = get_configs('payways_config');
				if($clientid>1 && isset($payways['50_'.$clientid])){
					$val['payway'].='_'.$clientid;
				}
			}
			
			
			//对特殊渠道处理（8170 	360助手)，非移动支付都算入cmnet支付
			if(!in_array($val['payway'],array(11,12,13)) && in_array($clientid,$GLOBALS['config']['paytocmnet'])){
				$val['payway'] = 11;
			}
			
			if(isset($rtndata[$val['payway']])){
				$rtndata[$val['payway']]['consumeorders'] += $val['consumeorders'];
				$rtndata[$val['payway']]['consumeusers'] += $val['consumeusers'];
				$rtndata[$val['payway']]['consumemoney'] += $val['consumemoney'];
			}else{
				$rtndata[$val['payway']]['payway']=$val['payway'];
				$rtndata[$val['payway']]['consumeorders'] = $val['consumeorders'];
				$rtndata[$val['payway']]['consumeusers'] = $val['consumeusers'];
				$rtndata[$val['payway']]['consumemoney'] = $val['consumemoney'];
			}
		}
		
		return $rtndata;
	}
 
}
?>