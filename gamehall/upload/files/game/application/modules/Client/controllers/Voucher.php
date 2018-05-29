<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class VoucherController extends Client_BaseController {

    private $perpage=10;

    /**
     * 我的A券列表
     */
    public function aTicketAction(){
        $input = $this->getInput(array('puuid', 'page', 'sp'));
        $online = $this->checkUserOnline($input);
        $page = $input['page'];
        if($page < 1) $page =1;
        $uuid = $input['puuid'];
        if($online){
            $data = $this->useAticket($uuid, $page);
        }
        $this->assign('sp', $input['sp']);
        $this->assign('uuid', $uuid);
        $this->assign('online', $online);
        $this->assign('data', $data);
    }


    public function aTicketMoreAction(){
        $input = $this->getInput(array('puuid', 'page', 'sp'));
        if(!$input['puuid'] && !$input['sp']){
            $this->output('-1', '参数非法');
        }
        $online = $this->checkUserOnline($input);
        if(!$online){
            $this->output(0,'', array('isLogin' => false));
        }

        $page = $input['page'];
        if($page < 1) $page =1;
        $uuid = $input['puuid'];
        $data = $this->useAticket($uuid, $page);
        $this->output(0,'',$data);
    }


    /**
     * 已使用过的A券详情列表
     */
    public function usedATicketAction(){
        $input = $this->getInput(array('puuid', 'page', 'sp'));
        $page = $input['page'];
        if($page < 1) $page =1;
        $uuid = $input['puuid'];
        $online = $this->checkUserOnline($input);
        if($online) {
            $data = $this->usedAticket($uuid, $page);
        }
        $this->assign('online', $online);
        $this->assign('sp', $input['sp']);
        $this->assign('uuid', $uuid);
        $this->assign('data', $data);
    }

    public function usedATicketMoreAction(){
        $input = $this->getInput(array('puuid', 'page', 'sp'));
        if(!$input['puuid'] && !$input['sp']){
            $this->output('-1', '参数非法');
        }
        $online = $this->checkUserOnline($input);
        if(!$online){
            $this->output(0,'', array('isLogin' => false));
        }
        $page = $input['page'];
        if($page < 1) $page =1;
        $uuid = $input['puuid'];
        $data = $this->usedAticket($uuid, $page);
        $this->output(0,'',$data);
    }

    /**
     * 游戏券说明页面
     */
    public function gameTicketIntroAction(){
        $gameTicket = Game_Service_Config::getValue('game_voucher_detail');
        $this->assign('gameTicket', $gameTicket);
    }

    private function buildUseableListData($data, $time){
        $list = array();
        foreach($data as $item){
            $list[] = array(
                'origin' => Client_Service_TicketTrade::getTaskInfo($item['id'], $item['send_type'], $item['sub_send_type']),
                'startDate' =>date('Y-m-d', $item['start_time']),
                'endDate' =>date('Y-m-d', $item['end_time']),
                'status' => ($item['start_time'] > $time ) ? 5 : ( ($item['balance'] && $item['balance'] != $item['denomination']) ? 4 : 3),
                'chargeMount' => number_format($item['denomination'], 2, '.', ''),
                'leftMount' => number_format($item['balance'], 2, '.', ''),
            );
        }
        return $list;
    }

    /**
     * @param $data
     * @param $time
     * @return array
     */
    private function buildUsedListData($data, $time){
        $list = array();
        foreach($data as $item){
            $list[] = array(
                'origin' => Client_Service_TicketTrade::getTaskInfo($item['id'], $item['send_type'], $item['sub_send_type']),
                'startDate' =>date('Y-m-d', $item['start_time']),
                'endDate' =>date('Y-m-d', $item['end_time']),
                'status' => ($item['end_time'] < $time ) ? 1 : 2,
                'chargeMount' => number_format($item['denomination'], 2, '.', ''),
                'leftMount' => number_format($item['denomination'], 2, '.', ''),
            );
        }
        return $list;
    }

    /**
     * @param $input
     */
    private function checkUserOnline($input) {
        $online = false;
        $sp = Common::parseSp($input['sp']);
        if ($input['puuid'] && $sp['imei']) {
            $online = Account_Service_User::checkOnline($input['puuid'], $sp['imei'], 'uuid');
        }
        return $online;
    }

    /**
     * @param $uuid
     * @param $page
     * @return array
     */
    private function useAticket($uuid, $page) {
        //更新A券可用游戏券余额
        $time = Common::getTime();
        $ticketType = Client_Service_Acoupon::TICKET_TYPE_ACOUPON;
        $balance = Client_Service_TicketTrade::getUserTicketBalance($uuid, $time, $ticketType, 0);
        //该游戏在有效期内A券的out_order_id
        $useTradeIds = Client_Service_TicketTrade::getUserTradeId($uuid, $time, $ticketType, 0);
        //组装可用A券条件
        $params = array('uuid' => $uuid, 'status' => 1, 'ticket_type' => $ticketType, 'start_time' => array('>', $time),);
        $orParams = array();
        if ($useTradeIds) {
            $orParams = array('id' => array('IN', $useTradeIds));
        }
        list($total, $data) = Client_Service_TicketTrade::getDetailList($page, $this->perpage, $params, $orParams, array('start_time' => 'ASC'));
        $hasNext = $this->hasNext($total, $page);
        $data = array('isLogin' => true, 'list' => $this->buildUseableListData($data, $time), 'hasnext' => $hasNext, 'curpage' => $page, 'balance'=> number_format($balance, 2, '.', ''));
        return $data;
    }

    /**
     * @param $uuid
     * @param $page
     * @return array
     */
    private function usedAticket($uuid, $page) {
        $time = Common::getTime();
        $ticketType = Client_Service_Acoupon::TICKET_TYPE_ACOUPON;;

        Client_Service_TicketTrade::getUserTicketBalance($uuid, $time, $ticketType, 0);

        //该游戏在有效期内不可用的游戏券的out_order_id
        $useTradeIds = Client_Service_TicketTrade::getUserTradeId($uuid, $time, $ticketType, 0, 0);

        //组装不可用游戏券条件
        $params = array('uuid' => $uuid, 'status' => 1, 'ticket_type' => $ticketType, 'end_time' => array('<', $time),);
        $orParams = array();
        if ($useTradeIds) {
            $orParams = array('id' => array('IN', $useTradeIds));
        }

        list($total, $data) = Client_Service_TicketTrade::getDetailList($page, $this->perpage, $params, $orParams, array('start_time' => 'DESC'));
        $hasNext = $this->hasNext($total, $page);

        $data = array('isLogin' => true, 'list' => $this->buildUsedListData($data, $time), 'hasnext' => $hasNext, 'curpage' => $page,);
        return $data;
    }

    private function hasNext($total, $page) {
        if(!$total) return false;
        return (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    }
}