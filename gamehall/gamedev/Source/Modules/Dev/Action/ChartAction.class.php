<?php
/**
 * 统计报表
 * @author shuhai
 */
class ChartAction extends BaseAction
{
	protected $api_url, $api_gn_url;

	function _before()
	{
		$this->api_url    = "http://bi.gionee.com/gameGSPThird.action";
		$this->api_gn_url = "http://bi.gionee.com/gameGSPThird_GN.action";
	}

	/*
	 * 普通数据
	 */
	function index()
	{
		$app_id = $this->_post('app_id', 'intval', 0);
		$start_time = $this->_post('start_time')?strtotime($this->_post('start_time')):mktime(0,0,0,date('m')-1);
		$end_time =  $this->_post('end_time')?strtotime($this->_post('end_time')):mktime(0,0,0,date('m'),date('d')+1);

		if($end_time < $start_time)
			$this->error("结束时间不能早于开始时间，请重新选择时间");

		$d_apps = D("Apps");
		$info = $d_apps->where(array('id' => $app_id, "author_id"=>$this->uid))->find();
		if (!empty($info))
		{
			//appid 转变为 gameID
			$game_id = helper('Sync')->getGameId($app_id);
			if ($game_id)
			{
				$data_str = 'type=third&user=thirdGameGSP&password=gamegsp123&APPID='.$game_id;
				if ($start_time > 0)
					$data_str .= "&startDay=".Date('Ymd',$start_time);
				if ($end_time > 0 )
					$data_str .= "&endDay=".Date('Ymd',$end_time);

				$res = $this->curlpost($this->api_gn_url, $data_str);
				$resArr = json_decode($res, true);
				if(empty($res) || $resArr === false )
					$resArr["errorCode"] = -1;

				if(APP_DEBUG || !empty($resArr["errorCode"]))
				{
					Log::write("ChartCURLError:".$this->api_gn_url, Log::EMERG);
					Log::write("ChartCURLError:".$data_str, Log::EMERG);
					Log::write("ChartCURLError:".$res, Log::EMERG);
					$this->assign('errorInfo', $this->getError($resArr["errorCode"]));
				}

				$comminfo = $resArr['abData'];
				
				//#2714 开发者平台-数据统计，查询出来的数据，时间不是倒序排列的
				$data = array();
				foreach ($comminfo as $row)
					$data[$row["day"]] = $row;
				krsort($data);
				
				$this->assign('comminfo', $data);
			}
		}

		//应用缓存到今天24点
		$applist = $d_apps->where(array("author_id"=>$this->uid))->cache(true, time()-strtotime(date("Y-m-d")) )->select();
		$this->assign('applist',$applist);
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		
		$this->export('index');
		$this->display();
	}

	/*
	 * 联运数据
	 */
	function _union()
	{
		$app_id = $this->_post('app_id', 'intval', 0);
		$start_time = $this->_post('start_time')?strtotime($this->_post('start_time')):mktime(0,0,0,date('m')-1);
		$end_time =  $this->_post('end_time')?strtotime($this->_post('end_time')):mktime(0,0,0,date('m'),date('d')+1);

		if($end_time < $start_time)
			$this->error("结束时间不能早于开始时间，请重新选择时间");

		$d_union = D("Union");
		$info = $d_union->where(array('id' => $app_id, "author_id"=>$this->uid))->find();
		if (!empty($info))
		{
			$data_str = 'type=third&user=thirdGameGSP&password=gamegsp123';
			if ($start_time > 0)
				$data_str .= "&startDay=".Date('Ymd',$start_time);
			if ($end_time > 0 )
				$data_str .= "&endDay=".Date('Ymd',$end_time);
			if(APP_DEBUG)
				$data_str .= "&APIKey=C432B8AC4C424E0998C5BE137D863884";
			else
				$data_str .= '&APIKey='.$info['api_key'];

			$res = $this->curlpost($this->api_url, $data_str);
			$resArr = json_decode($res,true);
			if(empty($res) || $resArr === false )
				$resArr["errorCode"] = -1;

			if(APP_DEBUG || !empty($resArr["errorCode"]))
			{
				Log::write("ChartCURLError:".$this->api_url, Log::EMERG);
				Log::write("ChartCURLError:".$data_str, Log::EMERG);
				Log::write("ChartCURLError:".$res, Log::EMERG);
				$this->assign('errorInfo', $this->getError($resArr["errorCode"]));
			}

			$unioninfo = $resArr['aaData'];

			//#2714 开发者平台-数据统计，查询出来的数据，时间不是倒序排列的
			$data = array();
			foreach ($unioninfo as $row)
				$data[$row["day"]] = $row;
			krsort($data);
				
			$this->assign('unioninfo', $data);
		}
		$applist = D("Union")->where(array("author_id"=>$this->uid,'status' => UnionModel::ALLOW))->cache(true, time()-strtotime(date("Y-m-d")) )->select();
		$this->assign('applist',$applist);
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
	}

	function union()
	{
		$this->_union();
		
		$this->export('union');
		$this->display();
	}

	function union_dau()
	{
		$this->_union();
		
		$this->export('union_dau');
		$this->display();
	}
	
	function export($tpl)
	{
		$export = $this->_post('export', 'strval', "");
		if(!empty($export))
		{
			$html = $this->fetch($tpl);
			preg_match('@<table class="tab">([\w\W]*?)</table>@is', $html, $table);
			
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
            header("Content-Disposition: inline; filename=\"" . $tpl . ".xls\"");

            printf('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><body><table>%s</table></body></html>', $table[1]);
			exit;
		}
	}

	/*
	 * Post请求
	 */
	function curlpost($url,$data)
	{
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 60);
		$return = curl_exec ( $ch );
		curl_close ( $ch );

		return $return;
	}

	function getError($error_code)
	{
		$array = array();
		$array["-1"]	=	"请求超时，请稍后重试";
		$array["00000000"]	=	"请求链接异常";
		$array["00000001"]	=	"请求用户名或者密码错误";
		$array["00000002"]	=	"请求参数开始日期或者结束日期格式错误";
		$array["00000003"]	=	"请求中开始日期大于结束日期";
		$array["00000004"]	=	"APIKey对应的游戏不存在";

		$error = isset($array[$error_code]) ? $array[$error_code] : "未知错误，请重试";

		return $error;
	}

}