<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author huangsg
 *
 */
class Gou_Service_PartnerOrder extends Common_Service_Base {

	public static $start_time;
	public static $end_time;

	/**
	 * 第三方订单接口配置
	 * @param string $pctype
	 * @return array
	 */
	public static function partnerChannels($channel='') {
		$partnerChannels = array(
			'DZDP'=>array(
					'title'		=>'大众点评',
					'status'	=>1,
					'apiUrl'	=>'http://cps.dianping.com/orderQuery/jinlicps',
					'timefm'	=>'YmdHis',
					'channel'	=>'DZDP'
			),
			'MYBB'=>array(
					'title'		=>'蜜芽宝贝',
					'status'	=>1,
					'apiUrl'	=>'http://api.miyabaobei.com/webunion/gouwudating/orders',
					'timefm'	=>'Y-m-d',
					'channel'	=>'MYBB'
			),
			'SN'=>array(
					'title'		=>'苏宁',
					'status'	=>1,
					'timefm'	=>'Y-m-d H:i:s',
					'channel'	=>'SN',
					'pageSize'	=> 30
			),
		);
		return isset($partnerChannels[$channel])?$partnerChannels[$channel]:$partnerChannels;
	}

	/**
	 * 获取第三方订单列表
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @return array
	 */
	public static function getList($page = 1, $limit = 10, $params = array()){
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('order_time'=>'DESC', 'id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}


	/**
	 * 根据条件获取第三方订单下载列表
	 * @param $params
	 * @return bool|mixed
	 */
	public static function getDownloadList($params){
		$params = self::_cookData($params);
		return self::_getDao()->getsBy($params, array('order_time'=>'DESC', 'id'=>'DESC'));
	}

	/**
	 * 获取第三方订单
	 * @param string $start_time
	 * @param string $end_time
	 * @param string $channel
	 * 大众点评:DZDP
	 * 蜜芽宝贝:MYBB
	 */
	public static function getPartnerOrder($start_time, $end_time, $channel = ''){
		self::$start_time = $start_time;
		self::$end_time = $end_time;
		$configChannels = self::partnerChannels();
		if($channel && isset($configChannels[strtoupper($channel)])){
			$adapter = 'adapter'.ucfirst($channel);
			if(method_exists(__CLASS__, $adapter)){
				$rs= self::$adapter($configChannels[strtoupper($channel)]);
//				echo '<pre>'; print_r($rs);exit;
				self::insertOrderIntoDB($rs, $configChannels[strtoupper($channel)]['title']);
			}
		}else{
			foreach ($configChannels as $key=>$config) {
				$adapter = 'adapter'.ucfirst($key);
				if(method_exists(__CLASS__, $adapter)){
					$rs =self::$adapter($config);
//					echo '<pre>'; print_r($rs);
					self::insertOrderIntoDB($rs, $config['title']);
				}
			}
		}
	}

	/**
	 * 大众点评
	 * @param array $config
	 * @return array
	 */
	public static function adapterDzdp($config){
		$params['status'] = 4;
		$params['startTime'] = date($config['timefm'], strtotime(self::$start_time));
		$params['endTime'] = date($config['timefm'], strtotime(self::$end_time));
		$result = self::getResponse($config['apiUrl'], $params);
		$rs = array();
		foreach($result as $item){
			$rs[] = array(
				'order_id' 		=> $item['orderId'],
				'order_amount' 	=> $item['orderAmount'],
				'channel_code' 	=> $item['channelcode'],
				'ticket_amount' => $item['receiptAmount'],
				'order_time' 	=> strtotime($item['receiptTime']),
				'order_status' 	=> $item['receiptStatus'],
				'channel'	 	=> $config['channel'],
			);
		}
		return $rs;
	}

	/**
	 * 蜜芽宝贝
	 * @param array $config
	 * @return array
	 */
	public static function adapterMybb($config){
		$params['stime'] = date($config['timefm'], strtotime(self::$start_time));
		$params['etime'] = date($config['timefm'], strtotime(self::$end_time));
		$result = self::getResponse($config['apiUrl'], $params);
		$result = $result['orders'];
		$rs = array();
		foreach($result as $item){
			$order_amount = 0;
			$ticket_amount = 0;
			$data = array(
				'comm_rate'	=> '',
				'cate'		=> '',
			);
			foreach($item['items'] as $sitem){
				$order_amount += $sitem['item_price']*$sitem['item_count'];
				$ticket_amount += $sitem['commission'];
				$data['comm_rate'] = $sitem['comm_rate'];
				$data['cate'][] = $sitem['item_category'];
			}
			$rs[] = array(
				'order_id' 		=> $item['order_id'],
				'order_amount' 	=> $order_amount,
				'channel_code' 	=> $item['wu_source'],
				'ticket_amount' => $ticket_amount,
				'order_time' 	=> strtotime($item['order_time']),
				'order_status' 	=> $item['order_status'],
				'channel'	 	=> $config['channel'],
				'data'			=> json_encode($data, true),
			);
		}
		return $rs;
	}

	/**
	 * 苏宁
	 * @param array $config
	 * @return array
	 */
	public static function adapterSn($config){
		$stime = date($config['timefm'], strtotime(self::$start_time));
		$etime = date($config['timefm'], strtotime(self::$end_time));
		$result = json_decode(Open_Suning_Client::queryOrder($stime, $etime, 1, $config['pageSize']), true);
		$result_temp = array();
		$rs = array();

		if(isset($result['sn_responseContent']['sn_head'])){
			$result_temp = $result['sn_responseContent']['sn_body']['queryOrder'];
			for($p = 2; $p <= $result['sn_responseContent']['sn_head']['pageTotal']; $p++){
				$result = json_decode(Open_Suning_Client::queryOrder($stime, $etime, $p, $config['pageSize']), true);
				$result_temp = array_merge($result_temp, $result['sn_responseContent']['sn_body']['queryOrder']);
			}
		}

		foreach($result_temp as $item){
			$status = '';
			$total_amount = 0;
			$ticket_amount = 0;
			$order_time = '';
			$channel_code = '';
			$data = array(
				'productName' 		=> '',
				'saleNum'			=> '',
				'commissionRatio'	=> '',
				'payAmountTrue'		=> 0,
				'cate'				=> '',
				'prePayCommission'	=> 0
			);


			foreach($item['orderDetail'] as $sitem){
				$status = $sitem['orderLineStatusDesc'];
				$order_time = $sitem['orderSubmitTime'];
				$total_amount += $sitem['payAmount'];
				$channel_code = $sitem['childAccountId'];
				$data['productName'][] = $sitem['productName'];
				$data['saleNum'][] = $sitem['saleNum'];

				$settle = json_decode(Open_Suning_Client::getOrderSettle($sitem['orderLineNumber']), true);

				if(isset($settle['sn_responseContent']['sn_body']['getOrderSettle']['settlementInfo'])
					&& !empty($settle['sn_responseContent']['sn_body']['getOrderSettle']['settlementInfo'])){
					$settle = $settle['sn_responseContent']['sn_body']['getOrderSettle']['settlementInfo'][0];
					$data['commissionRatio'][] = $settle['commissionRatio'];
					$data['payAmountTrue'] += $settle['payAmount'];
					$data['cate'][] = $settle['productFirstCatalog'];
					$ticket_amount += $settle['needPayCommission'];
					$data['prePayCommission'] += $settle['prePayCommission'];
				}
			}
			$rs[] = array(
				'order_id' 		=> $item['orderCode'],
				'order_amount' 	=> $total_amount,
				'channel_code' 	=> $channel_code,
				'ticket_amount' => $ticket_amount,
				'order_time' 	=> strtotime($order_time),
				'order_status' 	=> $status,
				'channel'	 	=> $config['channel'],
				'data'			=> json_encode($data, true)
			);
		}
		return $rs;
	}

	/**
	 * 获取第三方订单接口数据
	 * @param string $url
	 * @param array $params
	 * @return array
	 */
	public static function getResponse($url, $params, $isXml = false) {
		$curl = new Util_Http_Curl($url);
		$result = $curl->get($params);
		if ($isXml) {
			$ret = Util_XML2Array::createArray($result);
		} else {
			$ret = json_decode($result, true);
		}
		return $ret;
	}

	/**
	 * 将第三方订单入库
	 * @param array $orderList	//不同的接口，字段名称可能不同
	 * @param string $channel	//第三方接口渠道名
	 * @return boolean
	 */
	public static function insertOrderIntoDB($orderList = array(), $channel = ''){
		echo PHP_EOL;
		echo "|________________$channel orders are stocking now ..._____________|";
		$order_num = 0;

		if(!empty($orderList)) {
            try {
                parent::beginTransaction();
                foreach ($orderList as $item) {
                    if (self::_getDao()->replace($item)) $order_num++;
                }
                parent::commit();
            } catch (Exception $e) {
                parent::rollBack();
            }
		}
		echo PHP_EOL;
		echo "|________________There are $order_num(s) orders saved._____________|";
	}

	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['channel'])) $tmp['channel'] = $data['channel'];
		if(isset($data['order_time'])) $tmp['order_time'] = $data['order_time'];
		if(isset($data['order_id'])) $tmp['order_id'] = $data['order_id'];
		if(isset($data['channel_code'])) $tmp['channel_code'] = $data['channel_code'];
		return $tmp;
	}

	/**
	 * @return Gou_Dao_PartnerOrder
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_PartnerOrder");
	}
}