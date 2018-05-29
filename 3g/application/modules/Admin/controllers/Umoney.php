<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class UmoneyController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Umoney/index',
        'orderUrl' => '/Admin/Umoney/order',
        'logUrl'   => '/Admin/Umoney/log',
        'apiUrl'   => '/Admin/Umoney/api',
    );

    public function  indexAction() {
        $postData = $this->getInput(array('uid', 'username', 'sortColumn', 'sortType', 'page'));
        $page     = max($postData['page'], 1);
        $where    = array();
        if (!empty($postData['uid'])) {
            $where['uid'] = $postData['uid'];
        }
        if (!empty($postData['username'])) {
            $user         = Gionee_Service_User::getUserByName($postData['username']);
            $where['uid'] = $user['id'];
        }
        $sort = array();
        if (!empty($postData['sortColumn']) && !empty($postData['sortType'])) {
            $sort = array($postData['sortColumn'] => $postData['sortType']);
        }
        list($total, $dataList) = User_Service_UMoney::getList($page, $this->pageSize, $where, $sort);
        $this->assign('dataList', $dataList);
        $this->assign('params', $postData);

        $columns = array('total_money' => '总金额', 'remained_money' => '剩余金额', 'affected_money' => '变动金额');
        $this->assign('columns', $columns);
        $this->assign('sort', array('DESC', 'ASC'));
        unset($postData['page']);
        $searchParams = http_build_query($postData);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?{$searchParams}&"));
    }

    public function orderAction() {
        $postData = $this->getInput(array(
            "order_sn",
            "username",
            "order_status",
            "pay_status",
            "sdate",
            "edate",
            "page"
        ));
        !$postData['sdate'] && $postData['sdate'] = date("Y-m-d", strtotime("-7 day"));
        !$postData['edate'] && $postData['edate'] = date("Y-m-d", strtotime("now"));
        $page              = max($postData['page'], 1);
        $where             = array();
        $where['add_time'] = array(
            array(">=", strtotime($postData['sdate'])),
            array("<=", strtotime($postData['edate'] . " 23:59:59"))
        );
        if (!empty($postData['order_sn'])) {
            $where['order_sn'] = $postData['order_sn'];
        }
        if (!empty($postData['pay_status'])) {
            $where['pay_status'] = $postData['pay_status'];
        }
        if (!empty($postData['order_status'])) {
            $where['order_status'] = $postData['order_status'];
        }
        if (!empty($postData['username'])) {
            $user         = Gionee_Service_User::getUserByName($postData['username']);
            $where['uid'] = $user['id'];
        }

        $config = Common::getConfig('moneyConfig');
        list($total, $dataList) = User_Service_UMoneyOrder::getList($page, $this->pageSize, $where, array("id" => 'DESC'));
        $this->assign('dataList', $dataList);
        $this->assign('params', $postData);
        $this->assign('payStatus', $config['payStatus']);
        $this->assign('orderStatus', $config['orderStatus']);
        $this->assign('channel', $config['payChannel']);
        $this->assign('orderTypes', $config['orderType']);
        unset($postData['page']);
        $searchParams = http_build_query($postData);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['orderUrl'] . "?{$searchParams}&"));
    }

    public function logAction() {
        $postData = $this->getInput(array("order_sn", "username", "sdate", "edate", "page"));
        !$postData['sdate'] && $postData['sdate'] = date("Y-m-d", strtotime("-7 day"));
        !$postData['edate'] && $postData['edate'] = date("Y-m-d", strtotime("now"));
        $page              = max($postData['page'], 1);
        $where             = array();
        $where['add_time'] = array(
            array(">=", strtotime($postData['sdate'])),
            array("<=", strtotime($postData['edate'] . " 23:59:59"))
        );
        if (!empty($postData['order_sn'])) {
            $where['order_sn'] = $postData['order_sn'];
        }
        if (!empty($postData['username'])) {
            $user         = Gionee_Service_User::getUserByName($postData['username']);
            $where['uid'] = $user['id'];
        }
        list($total, $dataList) = User_Service_UMoneyLog::getList($page, $this->pageSize, $where, array("id" => 'DESC'));
        $this->assign('dataList', $dataList);
        $this->assign('params', $postData);
        unset($postData['page']);
        $searchParams = http_build_query($postData);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['logUrl'] . "?{$searchParams}&"));
    }

    public function apiAction() {
        $postData = $this->getInput(array('page', 'sdate', 'edate', 'order_sn'));
        !$postData['sdate'] && $postData['sdate'] = date("Y-m-d", strtotime("-7 day"));
        !$postData['edate'] && $postData['edate'] = date("Y-m-d", strtotime("now"));
        $page              = max($postData['page'], 1);
        $where             = array();
        $where['add_time'] = array(
            array(">=", strtotime($postData['sdate'])),
            array("<=", strtotime($postData['edate'] . " 23:59:59"))
        );
        if (!empty($postData['order_sn'])) {
            $where['order_sn'] = $postData['order_sn'];
        }
        $apiType = Common::getConfig('moneyConfig', 'apiTypes');
        list($total, $dataList) = User_Service_UMoneyApiLog::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
        foreach ($dataList as $k => $v) {
            $dataList[$k]['type']     = $apiType[$v['type']];
            $dataList[$k]['add_time'] = date("Y-m-d H:i:s", $v['add_time']);
        }
        $this->assign('dataList', $dataList);
        $searchParams = http_build_query($postData);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['apiUrl'] . "?{$searchParams}&"));
        $this->assign('params', $postData);
    }
}