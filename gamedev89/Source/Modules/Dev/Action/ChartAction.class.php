<?php
/**
 * 
 * 统计报表
 * @author shuhai
 *
 */
class ChartAction extends BaseAction{
	
	/*
	 * 普通数据
	 */
	function index() {
		
		$app_id = $this->_post('app_id');
		$start_time = $this->_post('start_time')?strtotime($this->_post('start_time')):mktime(0,0,0,date('m')-1);
		$end_time =  $this->_post('end_time')?strtotime($this->_post('end_time')):mktime(0,0,0,date('m'),date('d')+1);
			
		
		
		$d_apps = D("Apps");
		$info = $d_apps->where(array('id' => $app_id, "author_id"=>$this->uid))->find();
		//var_dump($info,$app_id);exit;
		if (!empty($info))
		{
			//appid 转变为 gameID
			$game_id = helper('Sync')->getGameId($app_id);
			if ($game_id)
			{
				$url = "http://bi.gionee.com/gameGSPThird_GN.action";
				$data_str = 'type=third&user=thirdGameGSP&password=gamegsp123&APPID='.$game_id;
				if ($start_time > 0)
				{
					$data_str .= "&startDay=".Date('Ymd',$start_time);
				}
				if ($end_time > 0 )
				{
					$data_str .= "&endDay=".Date('Ymd',$end_time);
				}
				$res = $this->curlpost($url,$data_str);
				//var_dump($res,$url,$data_str);exit;
				$resArr = json_decode($res,true);
				$comminfo = $resArr['abData'];
				$this->assign('comminfo',$comminfo);
			}
		}
		$applist = $d_apps->where(array("author_id"=>$this->uid))->select();
		$this->assign('applist',$applist);
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		$this->display();
	}
	
	/*
	 * 联运数据
	 */
	function union() {
		$app_id = $this->_post('app_id');
		$start_time = $this->_post('start_time')?strtotime($this->_post('start_time')):mktime(0,0,0,date('m')-1);
		$end_time =  $this->_post('end_time')?strtotime($this->_post('end_time')):mktime(0,0,0,date('m'),date('d')+1);
		
		$d_union = D("Union");
		$info = $d_union->where(array('id' => $app_id, "author_id"=>$this->uid))->find();
		if (!empty($info))
		{
			
			$url = "http://bi.gionee.com/gameGSPThird.action";
			$data = "type=third&user=thirdGameGSP&password=gamegsp123&startDay=20130101&endDay=20140301&APIKey=C2EE6664B5FD4063A3EF573C97A90C2C";
			$data_str = 'type=third&user=thirdGameGSP&password=gamegsp123&APIKey='.$info['api_key'];
			if ($start_time > 0)
			{
				$data_str .= "&startDay=".Date('Ymd',$start_time);
			}
			if ($end_time > 0 )
			{
				$data_str .= "&endDay=".Date('Ymd',$end_time);
			}
			$res = $this->curlpost($url,$data_str);
			$resArr = json_decode($res,true);
			$unioninfo = $resArr['aaData'];
			$this->assign('unioninfo',$unioninfo);
		}
		$applist = D("Union")->where(array("author_id"=>$this->uid,'status' => UnionModel::ALLOW))->select();
		$this->assign('applist',$applist);
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		$this->display();
	}
	
	
	
	/*
	 * Post请求
	 */
	function curlpost($url,$data){
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		$return = curl_exec ( $ch );
		curl_close ( $ch );
		return $return;
	}
}