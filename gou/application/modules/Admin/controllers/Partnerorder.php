<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author huangsg
 *
 */
class PartnerorderController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl'		=>'/admin/Partnerorder/index',
		'exportUrl'		=>'/admin/Partnerorder/export',
		'downloadUrl'	=>'/admin/Partnerorder/download',
	);
	
	public $perpage = 20;
	public $cate_id = 1;
	
	public function indexAction(){
		//取消自动载入视图
		Yaf_Dispatcher::getInstance()->disableView();
		$this->initView();

		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;

		$params = $this->getInput(array('channel', 'stime', 'etime', 'order_id', 'channel_code'));
		$search = array();
		if($params['channel']) $search['channel'] = $params['channel'];
		if($params['stime']) $search['order_time'] = array('>=', strtotime($params['stime']));
		if($params['etime']) $search['order_time'] = array('<=', strtotime($params['etime']));
		if($params['stime'] && $params['etime']) {
			$search['order_time'] = array(
				array('>=', strtotime($params['stime'])),
				array('<=', strtotime($params['etime']))
			);
		}
		if($params['order_id']) $search['order_id'] = $params['order_id'];
		if($params['channel_code']) $search['channel_code'] = $params['channel_code'];

		$channels = Gou_Service_PartnerOrder::partnerChannels();
		$this->assign('channels', $channels);

		list($total, $list) = Gou_Service_PartnerOrder::getList($page, $perpage, $search);

		$this->assign('params', $params);
		$this->assign('list', $list);
		$url = $this->actions['indexUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();

		//根据标识符来载入不同的视图
		if($params['channel'])
			echo $this->render(strtolower($params['channel']));
		else
			echo $this->render('index');
	}
	
	/**
	 * 导入第三方订单到本地数据库
	 * 无效方法
	 */
	public function exportAction(){
		$cateID = $this->getPost('cateID');
		if (empty($cateID))
			$this->output(-1, '请选择订单API类型.');

		$apiInfo = Gou_Service_PartnerAPI::getAPIByCateId($cateID);

		if (empty($apiInfo))
			$this->output(-1, '接口数据不存在.');

		$startTime = $this->getPost('start_time');
		$end_time = $this->getPost('end_time');
		if(empty($startTime) || empty($end_time))
			$this->output(-1, '请完整设置时间区间.');

		if (strtotime($startTime)> strtotime($end_time))
			$this->output(-1, '开始时间不能晚于结束时间.');

		//获取接口订单数据到数据库
		$params['startTime'] = str_replace(array('-', ':', ' '), '', $startTime) . '00';
		$params['endTime'] = str_replace(array('-', ':', ' '), '', $end_time) . '59';
		$params['status'] = 4;

		foreach($apiInfo as $item){
			$orderDataFromAPI = Gou_Service_PartnerOrder::getOrderFromAPI($item, $params);
		}

		if ($orderDataFromAPI)
			$this->output(0,'操作成功.');
		else
			$this->output(-1,'操作失败，请重试.');
	}

	/**
	 * 根据条件下载第三方订单
	 */
	public function downloadAction(){
		$params = $this->getInput(array('channel', 'stime', 'etime', 'order_id', 'channel_code'));

		$search = array();
		if($params['channel']) $search['channel'] = $params['channel'];
		if($params['stime']) $search['order_time'] = array('>=', strtotime($params['stime']));
		if($params['etime']) $search['order_time'] = array('<=', strtotime($params['etime']));
		if($params['stime'] && $params['etime']) {
			$search['order_time'] = array(
				array('>=', strtotime($params['stime'])),
				array('<=', strtotime($params['etime']))
			);
		}
		if($params['order_id']) $search['order_id'] = $params['order_id'];
		if($params['channel_code']) $search['channel_code'] = $params['channel_code'];

		$downMethod = '_'.ucfirst($search['channel']).'Down';

		if(method_exists(__CLASS__, $downMethod)){
			Util_DownFile::outputFile($this->$downMethod($search), date('Y-m-d') . '.csv');
		}else{
			Util_DownFile::outputFile($this->_DefaultDown($search), date('Y-m-d') . '.csv');
		}
	}

	/**
	 *  默认所有第三方订单的下载列表格式化
	 * @param array $search
	 * @return string
	 */
	private function _DefaultDown($search){
		$channels = Gou_Service_PartnerOrder::partnerChannels();
		$rs = Gou_Service_PartnerOrder::getDownloadList($search);

		$file_content = "";
		$file_content .= "\"订单渠道\",";
		$file_content .= "\"订单号\",";
		$file_content .= "\"渠道号\",";
		$file_content .= "\"订单金额\",";
		$file_content .= "\"券金额/佣金\",";
		$file_content .= "\"订单状态\",";
		$file_content .= "\"下单时间\",";
		$file_content .= "\r\n";

		if (!empty($rs)){
			foreach ($rs as $key=>$val){
				$file_content .= "\"" . (isset($channels[$val['channel']])?$channels[$val['channel']]['title']:'-') . "\",";
				$file_content .= "\"" . $val['order_id'] . "\",";
				$file_content .= "\"" . $val['channel_code'] . "\",";
				$file_content .= "\"" . $val['order_amount'] . "\",";
				$file_content .= "\"" . $val['ticket_amount'] . "\",";
				$file_content .= "\"" . $val['order_status'] . "\",";
				$file_content .= "\"" . date('Y-m-d H:i:s', $val['order_time']) . "\",";
				$file_content .= "\r\n";
			}
		}
		return mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
	}

	/**
	 *  大众点评下载列表格式化
	 * @param array $search
	 * @return string
	 */
	private function _DzdpDown($search){
		$channels = Gou_Service_PartnerOrder::partnerChannels();
		$rs = Gou_Service_PartnerOrder::getDownloadList($search);

		$file_content = "";
		$file_content .= "\"订单渠道\",";
		$file_content .= "\"订单号\",";
		$file_content .= "\"渠道号\",";
		$file_content .= "\"订单金额\",";
		$file_content .= "\"券金额\",";
		$file_content .= "\"订单状态\",";
		$file_content .= "\"下单时间\",";
		$file_content .= "\r\n";

		if (!empty($rs)){
			foreach ($rs as $key=>$val){
				$file_content .= "\"" . (isset($channels[$val['channel']])?$channels[$val['channel']]['title']:'-') . "\",";
				$file_content .= "\"" . $val['order_id'] . "\",";
				$file_content .= "\"" . $val['channel_code'] . "\",";
				$file_content .= "\"" . $val['order_amount'] . "\",";
				$file_content .= "\"" . $val['ticket_amount'] . "\",";
				$file_content .= "\"" . $val['order_status'] . "\",";
				$file_content .= "\"" . date('Y-m-d H:i:s', $val['order_time']) . "\",";
				$file_content .= "\r\n";
			}
		}
		return mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
	}

	/**
	 * 蜜芽宝贝下载列表格式化
	 * @param array $search
	 * @return string
	 */
	private function _MybbDown($search){
		$channels = Gou_Service_PartnerOrder::partnerChannels();
		$rs = Gou_Service_PartnerOrder::getDownloadList($search);

		$file_content = "";
		$file_content .= "\"订单渠道\",";
		$file_content .= "\"订单号\",";
		$file_content .= "\"渠道号\",";
		$file_content .= "\"支付金额\",";
		$file_content .= "\"分成金额\",";
		$file_content .= "\"佣金比例\",";
		$file_content .= "\"商品分类\",";
		$file_content .= "\"订单状态\",";
		$file_content .= "\"下单时间\",";
		$file_content .= "\r\n";

		if (!empty($rs)){
			foreach ($rs as $key=>$val){
				$data = json_decode($val['data'], true);

				$file_content .= "\"" . (isset($channels[$val['channel']])?$channels[$val['channel']]['title']:'-') . "\",";
				$file_content .= "\"" . $val['order_id'] . "\",";
				$file_content .= "\"" . $val['channel_code'] . "\",";
				$file_content .= "\"" . $val['order_amount'] . "\",";
				$file_content .= "\"" . $val['ticket_amount'] . "\",";
				$file_content .= "\"" . $data['comm_rate'] . "\",";
				$file_content .= "\"" . implode("; ", $data['cate']) . "\",";
				$file_content .= "\"" . $val['order_status'] . "\",";
				$file_content .= "\"" . date('Y-m-d H:i:s', $val['order_time']) . "\",";
				$file_content .= "\r\n";
			}
		}
		return mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
	}

	/**
	 * 苏宁下载列表格式化
	 * @param array $search
	 * @return string
	 */
	private function _SnDown($search){
		$channels = Gou_Service_PartnerOrder::partnerChannels();
		$rs = Gou_Service_PartnerOrder::getDownloadList($search);

		$file_content = "";
		$file_content .= "\"订单渠道\",";
		$file_content .= "\"订单号\",";
		$file_content .= "\"反馈标签\",";
		$file_content .= "\"订单金额\",";
		$file_content .= "\"实付金额\",";
		$file_content .= "\"预付佣金\",";
		$file_content .= "\"应付佣金\",";
		$file_content .= "\"购买数量\",";
		$file_content .= "\"商品名称\",";
		$file_content .= "\"一级目录\",";
		$file_content .= "\"订单状态\",";
		$file_content .= "\"下单时间\",";
		$file_content .= "\r\n";

		if (!empty($rs)){
			foreach ($rs as $key=>$val){
				$data = json_decode($val['data'], true);

				$file_content .= "\"" . (isset($channels[$val['channel']])?$channels[$val['channel']]['title']:'-') . "\",";
				$file_content .= "\"" . $val['order_id'] . "\",";
				$file_content .= "\"" . $val['channel_code'] . "\",";
				$file_content .= "\"" . $val['order_amount'] . "\",";
				$file_content .= "\"" . $data['payAmountTrue'] . "\",";
				$file_content .= "\"" . $val['ticket_amount'] . "\",";
				$file_content .= "\"" . $data['prePayCommission'] . "\",";
				$file_content .= "\"" . implode("; ", $data['saleNum']) . "\",";
				$file_content .= "\"" . implode("; ", $data['productName']) . "\",";
				$file_content .= "\"" . implode("; ", $data['cate']) . "\",";
				$file_content .= "\"" . $val['order_status'] . "\",";
				$file_content .= "\"" . date('Y-m-d H:i:s', $val['order_time']) . "\",";
				$file_content .= "\r\n";
			}
		}
		return mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
	}
}
