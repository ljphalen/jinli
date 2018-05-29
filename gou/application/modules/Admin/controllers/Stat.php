<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class StatController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Stat/pv',
		'uvUrl' => '/Admin/Stat/uv',
		'clickUrl' => '/Admin/Stat/click',
        'thirdpartUrl'=>'/admin/stat/thirdpart',
        'exportUrl'=>'/admin/stat/export',
        'pieUrl'=>'/admin/stat/pie',
        'areaPieUrl'=>'/admin/stat/areaPie',
        'orderUrl'=>'/admin/stat/order',
        'syncOrderUrl'=>'/admin/stat/syncOrder',
	);
	
	public $perpage = 20;
    public $versions = array(1=>'H5版', 2=>'预装版', 3=>'渠道版',4=>'穷购物', 5=>'APP版', 6=>'IOS版');
	/**
	 * 
	 * Enter description here ...
	 */
	public function pvAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$quick = $this->getInput('quick');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
		
		//pv
		list($list, $lineData) = Gou_Service_Stat::getPvLineData($sDate, $eDate);

//        var_export($list);
//        var_export($lineData);

//        print_r($lineData);
		$this->assign('list', array_reverse($list));
		$this->assign('lineData', json_encode($lineData));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
		$this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
		$this->assign('monthday', date('Y-m-d', strtotime("-1 month")));
		$this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
		$this->assign('quick', $quick);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uvAction() {
		$sDate = $this->getInput('sdate');
		$eDate = $this->getInput('edate');
		$quick = $this->getInput('quick');
		!$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
		!$eDate && $eDate = date('Y-m-d', strtotime("today"));
	
		//ip
		list($list, $lineData) = Gou_Service_Stat::getUvLineData($sDate, $eDate);
	
		$this->assign('list', array_reverse($list));
		$this->assign('lineData', json_encode($lineData));
		$this->assign('sdate', $sDate);
		$this->assign('edate', $eDate);
		$this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
		$this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
		$this->assign('monthday', date('Y-m-d', strtotime("-1 month")));
		$this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
		$this->assign('quick', $quick);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function clickAction() {
		
		/**
		 *
		 * 分成渠道：1
		 * 购物大厅—广告管理:2
		 * 购物大厅—渠道管理:3
		 * 购物大厅—专题管理:4
		 * 购物大厅—导购管理:5
		 * 购物大厅—消息通知:6
		 * 购物大厅—商品管理:7
		 * 货到付款—广告管理:8
		 * 货到付款—导购管理:9
		 * 专区——分类管理：10
		 * 专区——广告管理：11
		 * 资源管理：12
		 * 饭饭主题：27
		 * 知物：28
		 * 教程：29
		 * 热门活动：33
		 * 教程：29
		 * 教程：29
		 */
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('start_time', 'end_time', 'type_id', 'item_id', 'export'));
		if ($page < 1) $page = 1;
		
		!$params['start_time'] && $params['start_time'] = date('Y-m-d', strtotime("-8 day"));
		!$params['end_time'] && $params['end_time'] = date('Y-m-d', strtotime("today"));
		
		$search = array();
		if ($params['start_time']) $search['start_time'] = $params['start_time'];
		if ($params['end_time']) $search['end_time'] = $params['end_time'];
		if ($params['type_id']) $search['type_id'] = $params['type_id'];
		if ($params['item_id']) $search['item_id'] = $params['item_id'];
	
		list($sum, $total, $list) = Gou_Service_ClickStat::search($page, $this->perpage, $search);
		
		$url = $this->actions['clickUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		
		$this->assign('list', $list);
		$this->assign('search', $search);
		$this->assign('sum', $sum);
	}


    public function thirdpartAction() {
        $search = $this->getInput(array('start_time', 'end_time', 'version_id', 'module_id', 'channel_id', 'item_id', 'export'));

        if (!$search['start_time']) $search['start_time'] = date('Y-m-d', strtotime("-1 day"));
        if (!$search['end_time']) $search['end_time'] = date('Y-m-d', strtotime("-1 day"));

        $params = array();
        if ($search['version_id']) $params['version_id'] = $search['version_id'];
        if ($search['module_id']) $params['module_id'] = $search['module_id'];
        if ($search['channel_id']) $params['channel_id'] = $search['channel_id'];
        if ($search['item_id']) $params['item_id'] = $search['item_id'];

        list(, $modules) = Gou_Service_ChannelModule::getAll();
        $this->assign('modules', Common::resetKey($modules, 'id'));

        list(, $channels) = Gou_Service_ChannelName::getAll();
        $this->assign('channels', Common::resetKey($channels, 'id'));

        $start_time = strtotime($search['start_time']);
        $dateline = $end_time = strtotime($search['end_time']);

        $result = array();
        do {
            $params['dateline'] = date('Y-m-d', $dateline);
            $ret = Stat_Service_Log::getsByParams($params);
            $result = array_merge($result, $ret);
            $dateline -= 86400;
        } while($dateline >= $start_time);

        if ($search['export']) $this->_export($result);

        $this->assign('versions', $this->versions);
        $this->assign('result', $result);
        $this->assign('search', $search);
    }

    public function orderAction() {
        $search = $this->getInput(array('start_time', 'end_time', 'version_id', 'module_id', 'channel_id', 'item_id', 'export'));

        if (!$search['start_time']) $search['start_time'] = date('Y-m-d', strtotime("-1 day"));
        if (!$search['end_time']) $search['end_time'] = date('Y-m-d', strtotime("-1 day"));

        $params = array();
        if ($search['version_id']) $params['version_id'] = $search['version_id'];
        if ($search['module_id']) $params['module_id'] = $search['module_id'];
        if ($search['channel_id']) $params['channel_id'] = $search['channel_id'];
        if ($search['item_id']) $params['item_id'] = $search['item_id'];

        list(, $modules) = Gou_Service_ChannelModule::getAll();
        $this->assign('modules', Common::resetKey($modules, 'id'));

        list(, $channels) = Gou_Service_ChannelName::getAll();
        $this->assign('channels', Common::resetKey($channels, 'id'));

        $start_time = strtotime($search['start_time']);
        $dateline = $end_time = strtotime($search['end_time']);

        $result = $orders = array();
        do {
            $params['dateline'] = date('Y-m-d', $dateline);
            $ret = Stat_Service_Log::getsGroupByChannelCode($params);
            list(, $oret) = Stat_Service_Order::getsBy(array('dateline'=>$params['dateline']));
            $orders = array_merge($orders, $oret);
            $result = array_merge($result, $ret);
            $dateline -= 86400;
        } while($dateline >= $start_time);

        $orders_fmt = array();
        foreach ($orders as $value) {
            $key = sprintf("%s_%s", $value['dateline'], $value['channel_code']);
            $orders_fmt[$key] = $value;
        }

        foreach($result as $key=>$value) {
            $okey = sprintf("%s_%s", $value['dateline'], $value['channel_code']);
            $order = $orders_fmt[$okey];
            $result[$key]['order_total'] = $order['order_total'];
            $result[$key]['price_total'] = $order['price_total'];
            $result[$key]['price_slit'] = $order['price_slit'];
            $result[$key]['sure_order_total'] = $order['sure_order_total'];
            $result[$key]['sure_price_total'] = $order['sure_price_total'];
        }

        if ($search['export']) $this->_export($result);

        $this->assign('versions', $this->versions);
        $this->assign('result', $result);
        $this->assign('search', $search);
    }

    /**
     * @param $result
     */
    private function _export($result) {
        $file = realpath(Common::getConfig('siteConfig', 'dataPath')) .'/cache/export.csv';
        $header = array('时间', '版本','模块', '渠道', '标题', '内容PV', '内容UV', 'IMEI', '下单量', '下单金额', '确认订单量', '确认金额', '佣金');

        list(, $modules) = Gou_Service_ChannelModule::getAll();
        $modules = Common::resetKey($modules,'id');

        list(, $channels) = Gou_Service_ChannelName::getAll();
        $channels = Common::resetKey($channels, 'id');

        $header = sprintf("%s\r\n", implode(",", $header));
        $header = mb_convert_encoding($header, 'gb2312', 'UTF-8');

        Util_File::write($file, $header);
        $rs_str = "";
        foreach($result as $value) {
            $rs_array = array(
                $value['dateline'],
                $this->versions[$value['version_id']],
                $value['module_id'] ? $modules[$value['module_id']]['name'] : "-",
                $value['channel_id'] ? $channels[$value['channel_id']]['name'] : "-",
                $value['item_id'] ? $value['item_id']."-".$value['name'] : "-",
                $value['pv'],
                $value['uv'],
                $value['imei'],
                $value['order_total'],
                $value['price_total'],
                $value['sure_order_total'],
                $value['sure_price_total'],
                $value['price_slit'],
            );

            $rs_str .= sprintf("%s\r\n", implode(",", $rs_array));
        }
        $rs_str = mb_convert_encoding($rs_str, 'gb2312', 'UTF-8');
        Util_File::write($file, $rs_str, Util_File::APPEND_WRITE);
    }

    /**
     *
     */
    public function exportAction() {
        $file = realpath(Common::getConfig('siteConfig', 'dataPath')) .'/cache/export.csv';
        Util_DownFile::downloadFile($file, date('Y-m-d'));
    }

    /**
     *
     */
    public function plotAction() {

        $y = array(0=>'', 1=>'H5版',2=>'预装版',3=>'渠道版',4=>'穷购物',5=>'APP版',6=>'IOS版');
        $x = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);

        $search = $this->getInput(array('start_time', 'end_time'));

        if (!$search['start_time']) $search['start_time'] = date('Y-m-d', strtotime('today'));
        if (!$search['end_time']) $search['end_time'] = date('Y-m-d', strtotime('today'));

        $start_time = strtotime($search['start_time']);
        $dateline = $end_time = strtotime($search['end_time']);
        $result = array();
        do {
            $params['dateline'] = date('Y-m-d', $dateline);
            list(, $ret) = Stat_Service_Log::getsBy(array('dateline'=>$params['dateline']), array('id'=>'DESC'));

            $result = array_merge($result, $ret);
            $dateline -= 86400;
        } while($dateline >= $start_time);

        $lineData = array();
        foreach ($result as $log) {
            $xi = sprintf("%.2f",date('i', $log['create_time']) / 60);
            $xh = sprintf("%.d",date('H', $log['create_time']));
            $xv = $xh+$xi;
            $yv = rand(($log['version_id']*100 - 50), ($log['version_id']*100 + 50)) / 100;
            array_push($lineData, array($xv, $yv));
        }
        $this->assign('y', json_encode(array_keys($y)));
        $this->assign('x', json_encode($x));
        $this->assign('ydata', json_encode($y));
        $this->assign('search', $search);
        $this->assign('lineData', str_replace('"',"" ,json_encode($lineData, true)));
    }

    /**
     *
     */
    public function pieAction() {
        $search = $this->getInput(array('version_id', 'start_time', 'end_time'));

        if (!$search['start_time']) $search['start_time'] = date('Y-m-d', strtotime('today'));
        if (!$search['end_time']) $search['end_time'] = date('Y-m-d', strtotime('today'));

        $start_time = strtotime($search['start_time']);
        $dateline = $end_time = strtotime($search['end_time']);
        if ($search['version_id']) $params['version_id'] = $search['version_id'];

        $result = array();
        $total = 0;
        do {
            $params['dateline'] = date('Y-m-d', $dateline);
            $ret = Stat_Service_Log::getByHost($params);
            foreach ($ret as $value) {
                $ret = explode(".", $value['host']);
                $len = count($ret);
                $rkey = sprintf("*.%s.%s", $ret[$len - 2], $ret[$len -1]);

                $result[$rkey] += $value['total'];
                $total += $value['total'];
            }
            $dateline -= 86400;
        } while($dateline >= $start_time);

        $output = array();
        foreach ($result as $key=>$value) {
            array_push($output, array($key, sprintf("-%.3f-", ($value/$total)*100)));
        }

        $this->assign('search', $search);
        $this->assign('lineData', str_replace(array('"-', '-"'),"" ,json_encode($output, true)));
    }

    /**
     *
     */
    public function areaPieAction() {
        $search = $this->getInput(array('version_id', 'start_time', 'end_time'));

        if (!$search['start_time']) $search['start_time'] = date('Y-m-d', strtotime('today'));
        if (!$search['end_time']) $search['end_time'] = date('Y-m-d', strtotime('today'));

        $start_time = strtotime($search['start_time']);
        $dateline = $end_time = strtotime($search['end_time']);
        if ($search['version_id']) $params['version_id'] = $search['version_id'];

        $result = array();
        $total = 0;
        do {
            $params['dateline'] = date('Y-m-d', $dateline);
            $ret = Stat_Service_Log::getByProvince($params);
            $ret = Common::resetKey($ret, 'province');
            foreach ($ret as $key=>$value) {
                if (!$key) $key='其它';
                if ($key=="-") $key="未知";
                $result[$key] += $value['total'];
                $total += $value['total'];
            }
            $dateline -= 86400;
        } while($dateline >= $start_time);

        $output = array();
        foreach ($result as $key=>$value) {
            array_push($output, array($key, sprintf("-%.3f-", ($value/$total)*100)));
        }

        $this->assign('search', $search);
        $this->assign('lineData', str_replace(array('"-', '-"', "'"), "" ,json_encode($output, true)));
    }

    /**
     *
     */
    public function syncOrderAction() {
        $cache = Common::getCache();
        $stat = $cache->get("mmb_order_sync");
        if ($stat == "doing") {
            $this->output(0, '同步正在进行中,请稍后');
        } else if ($stat== "done") {
            $cache->set("mmb_order_sync", "do");
            $this->output(0, '上次同步已经完成，本次同步请求已经发送，请等待.');
        } else if($stat == "do"){
            $this->output(0, '同步请求已经发送，等待处理中.');
        }
        $cache->set("mmb_order_sync", "do");
        $this->output(0, '同步请求发送成功，数据生成需要20分钟左右.');
    }

}
