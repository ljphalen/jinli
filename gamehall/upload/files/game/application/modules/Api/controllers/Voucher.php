<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class VoucherController extends Api_BaseController {

    private $perpage=10;

    /**
     * 游戏券列表
     */
    public function gameTicketListAction() {
        $input = $this->getInput(array('puuid', 'page', 'sp'));
        if(!$input['puuid'] && !$input['sp']){
            $this->clientOutput(array());
        }

        $this->checkUserOnline($input);

        $page = $input['page'];
        if($page < 1) $page =1;

        $uuid = $input['puuid'];
        $time = Common::getTime();
        $ticketType = Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER;
        $gameHallId = Resource_Service_Games::getGameHallId();
        $firstItem = $this->getGameHallTicket($uuid, $time, $ticketType, $gameHallId);
        $params = array(
            'uuid' => $uuid,
            'status'=>1,
            'ticket_type' => $ticketType,
            'game_id' => array('!=', $gameHallId),
        );

        $list = Client_Service_TicketTrade::getListGroupBy($page, $this->perpage, $params, 'game_id');
        if($list){
            $list = $this->buildListData($list, $uuid, $time, $ticketType);
        }
        if($page == 1 && $firstItem){
            array_unshift($list, $firstItem);
        }
        $total = Client_Service_TicketTrade::countGroupBy($params, 'game_id');
        if($firstItem) {
            $total +=1;
        }
        $hasNext = $this->hasNext($total, $page);

        $data= array(
            'list' => $list,
            'hasnext' => $hasNext,
            'curpage' => $page,
            'total' => $total
        );
        $this->response('', $data);
    }

    /**
     * 游戏券详情列表
     */
    public function gameTicketDetailAction(){
        $input = $this->getInput(array('puuid', 'gameId', 'page', 'sp'));
        if(!$input['puuid'] && !$input['sp'] && !$input['gameId']){
            $this->clientOutput(array());
        }
        $this->checkUserOnline($input);
        $page = $input['page'];
        if($page < 1) $page =1;

        //更新当前游戏可用游戏券余额
        $time = Common::getTime();
        $uuid = $input['puuid'];
        $gameId = $input['gameId'];
        $ticketType = Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER;
        $balance = Client_Service_TicketTrade::getUserTicketBalance($uuid, $time, $ticketType, $gameId);
        //该游戏在有效期内游戏券的out_order_id
        $useTradeIds = Client_Service_TicketTrade::getUserTradeId($uuid, $time, $ticketType, $gameId);
        //组装可用游戏券条件
        $params = array(
            'uuid' => $uuid,
            'status' => 1,
            'ticket_type' => $ticketType,
            'game_id' => $gameId,
            'start_time' => array('>', $time),
        );
        $orParams = array();
        if($useTradeIds){
            $orParams = array(
                'id' => array('IN', $useTradeIds)
            );
        }
        list($total, $data) = Client_Service_TicketTrade::getDetailList($page, $this->perpage, $params, $orParams, array('start_time'=>'ASC'));
        $hasNext = $this->hasNext($total, $page);
        $data = array(
            'list' => $this->buildUseableListData($data, $time),
            'hasnext' => $hasNext,
            'curpage' => $page,
            'balance' => number_format($balance, 2, '.', ''),
            'title' => $this->getGameTitle($gameId)
        );
        $this->response('', $data);
    }

    /**
     * @param $gameId
     * @return string
     */
    private function getGameTitle($gameId){
        $gameHallId = Resource_Service_Games::getGameHallId();
        if($gameId == $gameHallId){
            return '全平台通用';
        }else{
            $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
            return html_entity_decode($gameInfo['name']);
        }
    }

    /**
     * 已使用过的游戏券详情列表
     */
    public function usedGameTicketDetailAction(){
        $input = $this->getInput(array('puuid', 'gameId', 'page', 'sp'));
        if(!$input['puuid'] && !$input['sp'] && !$input['gameId']){
            $this->clientOutput(array());
        }
        $this->checkUserOnline($input);
        $page = $input['page'];
        if($page < 1) $page =1;
        //更新当前游戏可用游戏券余额
        $time = Common::getTime();
        $uuid = $input['puuid'];
        $gameId = $input['gameId'];
        $ticketType = Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER;
        $balance = Client_Service_TicketTrade::getUserTicketBalance($uuid, $time, $ticketType, $gameId);

        //该游戏在有效期内不可用的游戏券的out_order_id
        $useTradeIds = Client_Service_TicketTrade::getUserTradeId($uuid, $time, $ticketType, $gameId, 0);

        //组装不可用游戏券条件
        $params = array(
            'uuid' => $input['puuid'],
            'status' => 1,
            'ticket_type' => $ticketType,
            'game_id' => $input['gameId'],
            'end_time' => array('<', $time),
        );
        if($useTradeIds){
            $orParams = array(
                'id' => array('IN', $useTradeIds)
            );
        }

        list($total, $data) = Client_Service_TicketTrade::getDetailList($page, $this->perpage, $params, $orParams, array('start_time'=>'ASC'));
        $hasNext = $this->hasNext($total, $page);
        $data = array(
            'list' => $this->buildUsedListData($data, $time),
            'hasnext' => $hasNext,
            'curpage' => $page,
            'balance' => number_format($balance, 2, '.', ''),
            'title' => $this->getGameTitle($gameId)
        );
        $this->response('', $data);
    }

    private function buildUseableListData($data, $time){
        $list = array();
        foreach($data as $item){
            $gameInfo = Resource_Service_GameData::getBasicInfo($item['game_id']);
            $list[] = array(
                'title' => Client_Service_TicketTrade::getTaskInfo($item['id'], $item['send_type'], $item['sub_send_type']),
                'description' => html_entity_decode($gameInfo['name']) . "专用",
                'time' => date('Y-m-d', $item['start_time']) . '-' . date('Y-m-d', $item['end_time']),
                'status' => $this->getUseableStatus($item, $time),
                'gameTick' => number_format($item['denomination'], 2, '.', ''),
                'available' => $this->getUseableBlance($item, $item['balance'], $time),
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
            $gameInfo = Resource_Service_GameData::getBasicInfo($item['game_id']);
            $list[] = array(
                'title' => Client_Service_TicketTrade::getTaskInfo($item['id'], $item['send_type'], $item['sub_send_type']),
                'description' => html_entity_decode($gameInfo['name']) . "专用",
                'time' => date('Y-m-d', $item['start_time']) . '-' . date('Y-m-d', $item['end_time']),
                'status' => $this->getUsedStatus($item, $time),
                'gameTick' => number_format($item['denomination'], 2, '.', ''),
                'available' => "0.00",
            );
        }
        return $list;
    }

    /**
     * 可用A券|游戏券状态
     * @param $item
     * @param $time
     * @return string
     */
    private function getUseableStatus($item, $time){
        if($item['start_time'] >= $time) {
            return "即将生效";
        }
        return "可使用";
    }

    /**
     * 可用情况下的游戏券余额
     * @param $item
     * @param $balance
     * @param $time
     * @return string
     */
    private function getUseableBlance($item, $balance, $time){
        if($item['start_time'] >= $time) {
            return number_format($item['denomination'], 2, '.', '');
        }
        return number_format($balance, 2, '.', '');
    }

    /**
     * 不可用A券|游戏券状态
     * @param $item
     * @param $time
     * @return string
     */
    private function getUsedStatus($item, $time){
        if($item['end_time'] >= $time) {
            return "已使用";
        }
        return "已过期";
    }

    private function buildListData($data, $uuid, $time, $ticketType){
        $list = array();
        foreach($data as $item){
            $gameId = $item['game_id'];
            $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
            $balance = Client_Service_TicketTrade::getUserTicketBalance($uuid, $time, $ticketType, $gameId);
            $list[] = array(
                'id' => $item['game_id'],
                'title' => html_entity_decode($gameInfo['name']),
                'iconUrl' => $gameInfo['img'],
                'gameTick' => number_format($balance, 2, '.', ''),
            );
        }
        return $list;
    }

    /**
     * 获取全平台游戏券的总数量
     * @param $input
     */
    private function getGameHallTicket($uuid, $time, $ticketType, $gameId) {
        $hasTicketTrade = Client_Service_TicketTrade::getBy(array('uuid'=>$uuid, 'ticket_type'=>$ticketType, 'game_id'=>$gameId));
        if(!$hasTicketTrade) return array();
        $balance = Client_Service_TicketTrade::getUserTicketBalance($uuid, $time, $ticketType, $gameId);
        $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
        $data = array(
            'id' => $gameId,
            'title' => '全平台通用',
            'iconUrl' => $gameInfo['img'],
            'gameTick' => number_format($balance, 2, '.', ''),
        );
        return $data;
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
        if (!$online) {
            $this->response('0', array());
        }
    }

    private function response($code, $data){
        $result = array(
            'success' => true,
            'msg' => '',
            'sign' => 'GioneeGameHall',
        );
        if($data) $result['data'] = $data;
        if(isset($code)) $result['code'] = $code;
        $this->clientOutput($result);
    }
}