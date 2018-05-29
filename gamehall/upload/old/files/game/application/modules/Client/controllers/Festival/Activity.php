<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 道具兑换活动
 * @author luojiapeng
 *
 */
class Festival_ActivityController extends Client_BaseController{

    const  ONE_PAGE = 1;
    const  PAGE_LIMIT = 20;

    const  PRIZE_NOT_CAN_EXCHANGE_STATUS = 1;
    const  PRIZE_CAN_EXCHANGE_STATUS = 2 ;
    const  PRIZE_EXCHAGED_STATUS = 3;

    public function indexAction(){
        $info =  $this->getInput(array('puuid','imei','uname','sp'));
        $info['imei'] = Common::parseSp($info['sp'], 'imei');
        $userInfo = Account_Service_User::getUserInfo(array('uuid'=>$info['puuid']));
        $online = Account_Service_User::checkOnline($info['puuid'], $info['imei'], 'uuid');
        //活动信息
        $festivalInfo = Festival_Service_BaseInfo::getEffectFestivalInfo();
        $festivalId =  $festivalInfo[Festival_Service_BaseInfo::FIELD_ID];

        $ClientVersionKey = $festivalInfo[Festival_Service_BaseInfo::FIELD_CLIENT_VSERSIONS];
        $limitClientVersion = Common_Service_Version::getClientVersion($ClientVersionKey);
        $prizeInfo = $this->getPrizeInfo ( $festivalId, $online, $info['puuid']);
        $propsTotal = 0;
        if($online){
            $propsTotal = $this->getMyPropsTotal ( $info['puuid'], $festivalId );
        }

        $propsGroup = $this->getPropGroupList ( $festivalId );
        $propGropList = $this->makePropGroupList($propsGroup, $info['puuid']);

        $prizeRank = $this->getPrizeRank ( $festivalId , self::ONE_PAGE, self::PAGE_LIMIT);
        if(count($prizeRank) < 5 ){
            $prizeRank = array();
        }

        $configImgs = json_decode($festivalInfo['config'], true);
        $webRoot = Common::getWebRoot();
        $clientGameId = (ENV == 'product')? '117':'66';
        $gameInfo = Resource_Service_GameData::getBasicInfo($clientGameId);

        $this->assign('limitClientVersion', $limitClientVersion);
        $this->assign('festivalInfo', $festivalInfo);
        $this->assign('configImgs', $configImgs);
        $this->assign('webRoot', $webRoot);
        $this->assign('prizeInfo',$prizeInfo);
        $this->assign('online', $online);
        $this->assign('uuid', $info['puuid']);
        $this->assign('nickName', $userInfo['nickname']);
        $this->assign('propsTotal', $propsTotal);
        $this->assign('propGropList', $propGropList);
        $this->assign('prizeRank', $prizeRank);
        $this->assign('clientGameId', $clientGameId);
        $this->assign('gameInfo', $gameInfo);
        $servicePhone = Game_Service_Config::getValue('game_service_tel');
        $serviceQq = Game_Service_Config::getValue('game_service_qq');
        $this->assign('servicePhone', $servicePhone);
        $this->assign('serviceQq', $serviceQq);
        $this->assign('source', $this->getSource());
    }

    public function prizeRuleInfoAction(){
        $festivalId =  $this->getInput('festivalId');
        $params =  array(Festival_Service_Prizes::FIELD_FESTIVAL_ID=>$festivalId);
        $prizesInfo = Festival_Service_Prizes::getsBy($params, array('sort' => 'ASC'));
        $festivalInfo = $this->getFestivalInfoById($festivalId);
        $data = array();
        foreach ($prizesInfo as $item){
            $conditionTitle = $this->getPirzeConditionTitle($item['condition']);
            $data[]=array(
                'prizeIcon'=> $item['icon'],
                'name'=>$item['name'],
                'total'=> $item['total'],
                'condition' => implode('+', $conditionTitle)
            );
        }
        $this->assign('festivalInfo', $festivalInfo);
        $this->assign('prizesInfo', $data);
        $this->assign('subTitle', '兑奖规则');
    }

    private function getPirzeConditionTitle($condition){
        $propIds = explode(',', $condition);
        $titles = array();
        foreach ($propIds as $propId){
            $propData = $this->getPropById($propId);
            $titles[] = $propData['name'];
        }
        return $titles;
    }

    private function getFestivalInfoById($festivalId){
        $festivalInfo  = Festival_Service_BaseInfo::getBy(array(Festival_Service_BaseInfo::FIELD_ID=>$festivalId));
        return $festivalInfo;
    }

    private function getPropById($id){
        $propData = Festival_Service_Props::getBy(array(Festival_Service_Props::FIELD_ID=>$id));
        return $propData;
    }

    private function getPrizeInfoById($prizeId){
        $params =  array(Festival_Service_Prizes::FIELD_ID=>$prizeId);
        $prizeInfo = Festival_Service_Prizes::getBy($params);
        return $prizeInfo;
    }

    public function prizeRankAction(){
        $info =  $this->getInput(array('festivalId' , 'page'));

        if(intval($info['page']) < 1){
            $info['page'] = self::ONE_PAGE;
        }
        $festivalInfo = $this->getFestivalInfoById($info['festivalId']);
        //奖品领取排行榜
        $prizeRank = $this->getPrizeRank($info['festivalId'] , $info['page'], 100);
        $this->assign('prizeRank', $prizeRank);
        $this->assign('festivalInfo', $festivalInfo);
        $this->assign('subTitle', '奖品榜');
    }

    public function exchangedAction(){
        $info =  $this->getInput(array('festivalId' , 'prizeId', 'puuid'));

        $festivalInfo = $this->getFestivalInfoById($info['festivalId']);
        $params[Festival_Service_PrizeExchanges::FIELD_FESTIVAL_ID] = $info['festivalId'];
        $params[Festival_Service_PrizeExchanges::FIELD_PRIZE_ID] = $info['prizeId'];
        $params[Festival_Service_PrizeExchanges::FIELD_UUID] = $info['puuid'];
        $exchangeInfo = Festival_Service_PrizeExchanges::getBy($params);
        $prizeInfo = $this->getPrizeInfoById($exchangeInfo['prize_id']);

        $servicePhone = Game_Service_Config::getValue('game_service_tel');

        $this->assign('festivalInfo', $festivalInfo);
        $this->assign('servicePhone', $servicePhone);
        $this->assign('prizeInfo', $prizeInfo);
        $this->assign('prizeInfo', $prizeInfo);
        $this->assign('exchangeInfo', $exchangeInfo);
        $this->assign('subTitle', '奖品兑换信息');
    }

    public function clickAction(){
        exit();
    }

    public function gameListAction(){
        $info =  $this->getInput(array('prizeId', 'festivalId','groupPropId'));
        $festivalInfo = Festival_Service_BaseInfo::getEffectFestivalInfo();
        $festivalId = $info['festivalId'];

        $params[Festival_Service_PropGroup::FIELD_FESTIVAL_ID] = $festivalId;
        $params[Festival_Service_PropGroup::FIELD_ID] = $info['groupPropId'];
        $propGroupInfo = Festival_Service_PropGroup::getBy($params);

        $propIdsList = $propGroupInfo[Festival_Service_PropGroup::FIELD_PROP_IDS];
        $gameIdsList = $propGroupInfo[Festival_Service_PropGroup::FIELD_GAME_IDS];

        $propInfoList = $this->getPropInfoByPropIds($festivalId, $propIdsList);
        $gameInfoList = $this->getGameInfoByGameIds($festivalId, $gameIdsList);

        $this->assign('festivalInfo', $festivalInfo);
        $this->assign('propGroupInfo', $propGroupInfo);
        $this->assign('propInfoList', $propInfoList);
        $this->assign('gameInfoList', $gameInfoList);
        $this->assign('subTitle', '游戏列表');
    }

    private function getGameInfoByGameIds($festivalId, $gameIdsList) {
        $gameIdList = explode(',', $gameIdsList);
        $gameInfoList = array();
        foreach ($gameIdList as $gameId){
            $gameInfo = Resource_Service_GameData::getBasicInfo($gameId);
            $gameInfoList[] =$gameInfo;
        }
        return $gameInfoList;
    }

    private function getPropInfoByPropIds($festivalId, $propIdsList) {
        $propIdList = explode(',', $propIdsList);
        $propInfoList = array();
        foreach ($propIdList as $val){
            $propInfo = Festival_Service_Props::getBy(array(Festival_Service_Props::FIELD_ID=>$val,
                Festival_Service_Props::FIELD_FESTIVAL_ID=>$festivalId
            ));
            $propInfoList[] = array('propName'=>$propInfo[Festival_Service_Props::FIELD_NAME],
                'propImg'=>$propInfo[Festival_Service_Props::FIELD_IMG]
            );
        }
        return $propInfoList;
    }

    public function exchangePrizeAction(){
        $info =  $this->getInput(array('festivalId' , 'prizeId', 'puuid'));
        if(!$info['puuid'] || !$info['festivalId'] || !$info['prizeId']){
            exit;
        }
        $festivalInfo = $this->getFestivalInfoById($info['festivalId']);
        $prizeInfo = $this->getPrizeInfoById($info['prizeId']);
        if($prizeInfo[Festival_Service_Prizes::FIELD_PRIZE_TYPE] != Festival_Service_Prizes::SEND_ENTITY){
            exit;
        }
        $webRoot = Common::getWebRoot();
        $this->assign('webRoot', $webRoot);
        $servicePhone = Game_Service_Config::getValue('game_service_tel');
        $this->assign('uuid', $info['puuid']);
        $this->assign('festivalInfo', $festivalInfo);
        $this->assign('servicePhone', $servicePhone);
        $this->assign('prizeInfo', $prizeInfo);
        $this->assign('subTitle', '奖品兑换');

    }

    public function exchangePrizePostAction(){
        $info =  $this->getInput(array('festivalId' ,
            'prizeId',
            'puuid',
            'uname',
            'prizeType',
            'contact',
            'phone',
            'address',
            'sp'));
        $msg = array('接收的参数' => $info);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);

        $this->checkRequesParam ( $info );


        $sp = common::parseSp($info['sp']);
        $info['imei'] = $sp['imei'];
        $currentClientVersion = $sp['game_ver'];

        $data['status'] = 0;
        $festivalInfo = $this->getFestivalInfoById($info['festivalId']);
        if(!$festivalInfo){
            $this->output(1, '活动已过期', $data);
        }

        $msg = array('活动信息' => $festivalInfo);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);


        $result = Festival_Service_BaseInfo::checkClientVersion ( $currentClientVersion, $festivalInfo );
        if(!$result){
            $this->output(1, '客户端的版本过低，请升级', $data);
        }

        $online = Account_Service_User::checkOnline($info['puuid'], $info['imei'], 'uuid');
        //$online = true;
        if(!$online){
            $this->output(1, '用户未登录', $data);
        }

        //是否兑换过此奖品
        $isExchagedPirze =  $this->isExchangedPrize($info['festivalId'], $info['prizeId'], $info['puuid'], $online);
        if($isExchagedPirze){
            $this->output(1, '用户兑换过此奖品', $data);
        }
        $msg = array('是否兑换过奖品' => $isExchagedPirze);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);

        //奖品的
        $params[Festival_Service_Prizes::FIELD_FESTIVAL_ID] = $info['festivalId'];
        $params[Festival_Service_Prizes::FIELD_ID] = $info['prizeId'];
        $prizeInfo = Festival_Service_Prizes::getBy($params);
        $prizeNum = intval($prizeInfo[Festival_Service_Prizes::FIELD_TOTAL]);
        if(!$prizeNum){
            $this->output(1, '奖品数量不足', $data);
        }
        $msg = array('奖品数量' => $prizeNum);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);

        $hadPrizeTotal = $this->gethadExchangePrizeNum($info['festivalId'], $info['prizeId']);
        if($hadPrizeTotal >= $prizeNum){
            $this->output(1, '库存奖品数量不足', $data);
        }
        $msg = array('奖品已经兑换数量' => $hadPrizeTotal);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);

        //是否符合兑换
        $isCanExchangePrize =$this->isCanExchagePrize($info['festivalId'], $info['prizeId'], $info['puuid'], $online);
        if(!$isCanExchangePrize){
            $this->output(1, '不符合兑换物品的条件', $data);
        }
        $msg = array('是否符合兑换' => $isCanExchangePrize);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);

        //奖品条件
        $prizeCondition = $this->getPrizeCondition ( $prizeInfo );
        $prizeType = intval($info['prizeType']);


        if($prizeType == Festival_Service_Prizes::SEND_ENTITY){
            $result = $this->exchageEntityPrize($info, $prizeCondition);
        }elseif ($prizeType == Festival_Service_Prizes::SEND_TICKET){
            $result = $this->exchangeTicketPrize ( $info, $prizeInfo);
        }elseif($prizeType == Festival_Service_Prizes::SEND_POINT){
            $result = $this->exchagePointPrize($info, $prizeInfo);
        }
        if($result){
            $data['status'] = 1;
            $this->output(0, '兑换成功', $data);
        }else{
            $this->output(1, '兑换失败', $data);
        }

    }

    private function exchagePointPrize($info, $prizeInfo){
        if(!is_array($prizeInfo)){
            return false;
        }

        $data['uuid'] = $info['puuid'];
        $data['gain_type'] = 8;
        $data['gain_sub_type'] = 1;
        $data['points'] = $prizeInfo[Festival_Service_Prizes::FIELD_DENOMINATION];
        $data['create_time'] = Common::getTime();
        $data['update_time'] = Common::getTime();
        $data['status'] = 1 ;
        Point_Service_User::gainPoint($data);
        $prizeCondition = $this->getPrizeCondition($prizeInfo);
        return $this->exchageEntityPrize($info, $prizeCondition);

    }

    private function getPrizeCondition($prizeInfo) {
        //奖品条件
        $prizeCondition = $prizeInfo[Festival_Service_Prizes::FIELD_CONDITION];
        $prizeConditionArr = explode(',', $prizeCondition);
        return $prizeConditionArr;
    }

    private function checkRequesParam($info) {
        $data['status'] = 0;
        if(!$info['festivalId'] || !$info['prizeId'] || !intval($info['prizeType']) ){
            $this->output(1, '非法请求1', $data);
        }
        if(!$info['puuid'] || !$info['uname'] ){
            $this->output(1, '非法请求2', $data);
        }
        if(!$info['sp']){
            $this->output(1, '非法请求3', $data);
        }
        if($info['prizeType'] == Festival_Service_Prizes::SEND_ENTITY){
            if(!trim($info['contact'])){
                $this->output(1, '收货人不能为空', $data);
            }
            if(!trim($info['phone'])){
                $this->output(1, '联系电话不能为空', $data);
            }
            if(!trim($info['address'])){
                $this->output(1, '收货地址不能为空', $data);
            }
        }
    }

    private function exchageEntityPrize($info, $prizeCondition){
        if(!is_array($prizeCondition)){
            return false;
        }
        try {
            $time = Common::getTime();
            Common_Service_Base::beginTransaction();
            $result = $this->savePrizeExchage ( $info, $time );
            if (!$result){
                Common_Service_Base::rollBack();
                return false;
            }

            foreach ($prizeCondition as $propId){
                $result = $this->savaPropsConsume ( $info, $time, $propId);
                if (!$result){
                    Common_Service_Base::rollBack();
                    return false;
                }
                $result = $this->updatePropTotals ( $info,  $propId);
                if (!$result){
                    Common_Service_Base::rollBack();
                    return false;
                }
            }
            Common_Service_Base::commit();
            return true;
        }catch (Exception $e) {
            Common_Service_Base::rollBack();
            return false;
        }

    }

    private function updatePropTotals($info, $propId) {
        $queryParams = array(Festival_Service_PropsTotals::FIELD_UUID=>$info['puuid'],
            Festival_Service_PropsTotals::FIELD_FESTIVAL_ID=>$info['festivalId'],
            Festival_Service_PropsTotals::FIELD_PROP_ID=>$propId);
        $queryReult  = Festival_Service_PropsTotals::getBy($queryParams);
        if(!$queryReult){
            return false;
        }
        $data[Festival_Service_PropsTotals::FIELD_COSUME_TOTAL] = $queryReult[Festival_Service_PropsTotals::FIELD_COSUME_TOTAL] + 1;
        $data[Festival_Service_PropsTotals::FIELD_REMAIN_TOTAL] = $queryReult[Festival_Service_PropsTotals::FIELD_REMAIN_TOTAL] - 1;
        $result = Festival_Service_PropsTotals::updateBy($data, $queryParams);
        return $result;
    }


    private function savaPropsConsume($info, $time, $propId) {
        $data[Festival_Service_PropsConsume::FIELD_UUID] = $info['puuid'];
        $data[Festival_Service_PropsConsume::FIELD_FESTIVAL_ID] = $info['festivalId'];
        $data[Festival_Service_PropsConsume::FIELD_PRIZE_ID] = $info['prizeId'];
        $data[Festival_Service_PropsConsume::FIELD_PROP_ID] = $propId;
        $data[Festival_Service_PropsConsume::FIELD_CONSUME_NUM] = 1;
        $data[Festival_Service_PropsConsume::FIELD_CREATE_TIME] = $time;
        $result = Festival_Service_PropsConsume::insert($data);
        return $result;
    }

    private function savePrizeExchage($info, $time) {
        $data[Festival_Service_PrizeExchanges::FIELD_UUID] = $info['puuid'];
        $data[Festival_Service_PrizeExchanges::FIELD_FESTIVAL_ID] = $info['festivalId'];
        $data[Festival_Service_PrizeExchanges::FIELD_PRIZE_ID] = $info['prizeId'];
        $data[Festival_Service_PrizeExchanges::FIELD_CONTACT] = $info['contact']?$info['contact']:'';
        $data[Festival_Service_PrizeExchanges::FIELD_ADDRESS] = $info['address']?$info['address']:'';
        $data[Festival_Service_PrizeExchanges::FIELD_PHONE] = $info['phone']?$info['phone']:'';
        $data[Festival_Service_PrizeExchanges::FIELD_STATUS] = ($info['prizeType']==1)?0:1;
        $data[Festival_Service_PrizeExchanges::FIELD_CREATE_TIME] = $time;
        $result = Festival_Service_PrizeExchanges::insert($data);
        return $result;
    }

    private function exchangeTicketPrize($info, $prizeInfo) {
        if(!is_array($prizeInfo)){
            return false;
        }
        $sendData = array(
            'uuid'=>$info['puuid'],
            'type'=> 8,
            'task_id'=>$info['festivalId'],
            'section_start'=>$prizeInfo[Festival_Service_Prizes::FIELD_START_TIME],
            'section_end'=>$prizeInfo[Festival_Service_Prizes::FIELD_END_TIME],
            'denomination'=>$prizeInfo[Festival_Service_Prizes::FIELD_DENOMINATION],
            'desc'=>'活动赠送',
        );
        $exchange = new Util_Activity_Context(new Util_Activity_TicketSend($sendData));
        $exchange->sendTictket();

        $prizeCondition = $this->getPrizeCondition($prizeInfo);
        return  $this->exchageEntityPrize($info, $prizeCondition);
    }

    private function getPrizeRank($festivalId, $page ,$pageLimit) {
        //奖品兑换榜
        $params['festival_id'] = $festivalId;
        $orderBy = array(Festival_Service_PrizeExchanges::FIELD_CREATE_TIME=>'DESC'
        );
        list(, $result) = Festival_Service_PrizeExchanges::getList($page,
            $pageLimit,
            $params,
            $orderBy);
        $prizeRank = array();
        foreach ($result as $val){
            $userInfo = Account_Service_User::getUserInfo(array('uuid'=>$val[Festival_Service_PrizeExchanges::FIELD_UUID]));
            $prizeInfo = Festival_Service_Prizes::getBy(array(Festival_Service_Prizes::FIELD_FESTIVAL_ID=>$festivalId,
                Festival_Service_Prizes::FIELD_ID=>$val[Festival_Service_PrizeExchanges::FIELD_PRIZE_ID]
            ));
            $condition = $this->getPrizeCondition($prizeInfo);
            $prizeRank[] = array('nickname'=> $userInfo['nickname'],
                'prizeName'=>$prizeInfo[Festival_Service_Prizes::FIELD_NAME],
                'consumeNum'=>count($condition)
            );
        }
        return $prizeRank;
    }

    private function makePropGroupList($propsGroup, $uuid){
        if(!is_array($propsGroup)){
            return array();
        }
        $propsGroupList = array();
        foreach ($propsGroup as $props){
            $propList = $this->getPropList($props[Festival_Service_PropGroup::FIELD_FESTIVAL_ID],
                $props[Festival_Service_PropGroup::FIELD_PROP_IDS],
                $uuid);

            $propGroupNum = 0;
            foreach ($propList as $val){
                $propGroupNum += $val['propNum'];
            }
            $propsGroupList[] = array('propGroupId'=>$props[Festival_Service_PropGroup::FIELD_ID],
                'propGroupname' => $props[Festival_Service_PropGroup::FIELD_NAME],
                'propGroupimg'=> $props[Festival_Service_PropGroup::FIELD_IMG],
                'propList'   =>$propList,
                'propGroupNum'=>$propGroupNum

            );
        }
        return $propsGroupList;
    }

    private function getPropList($festivalId, $propIdsStr, $uuid) {
        $propIds = explode(',', $propIdsStr);
        $propList = array();
        foreach ($propIds as $propId){
            $queryParams   = array(Festival_Service_Props::FIELD_FESTIVAL_ID=>$festivalId,
                Festival_Service_Props::FIELD_ID=>$propId);
            $propReulst  = Festival_Service_Props::getBy($queryParams);
            $myPropNum = $this->getEventPropNumByUser($uuid, $festivalId, $propId);

            $propList[] = array('propId'=>$propReulst[Festival_Service_Props::FIELD_ID],
                'propName'=>$propReulst[Festival_Service_Props::FIELD_NAME],
                'propImg'=>$propReulst[Festival_Service_Props::FIELD_IMG],
                'propGrayImg'=>$propReulst[Festival_Service_Props::FIELD_GRAY_IMG],
                'propNum'=>$myPropNum
            );
        }
        return $propList;
    }

    private function getEventPropNumByUser($uuid, $festivalId, $propId) {
        if(!$uuid || !$festivalId || !$propId){
            return 0;
        }
        //获取我的道具
        $queryParams = array(Festival_Service_PropsTotals::FIELD_UUID=>$uuid,
            Festival_Service_PropsTotals::FIELD_FESTIVAL_ID=>$festivalId,
            Festival_Service_PropsTotals::FIELD_PROP_ID=>$propId
        );
        $queryReult  = Festival_Service_PropsTotals::getBy($queryParams);
        $grantPropNum = $queryReult[Festival_Service_PropsTotals::FIELD_GRANT_TOTAL];
        $consumePropNum = $queryReult[Festival_Service_PropsTotals::FIELD_COSUME_TOTAL];
        $remainPropsTotal = $grantPropNum - $consumePropNum;
        return intval($remainPropsTotal);
    }

    private function getPropGroupList($festivalId) {
        $propGroupList = array();
        $queryParams   = array(Festival_Service_PropGroup::FIELD_FESTIVAL_ID=>$festivalId);
        $propGroupList  = Festival_Service_PropGroup::getsBy($queryParams);
        return $propGroupList;
    }

    private function getMyPropsTotal($uuid, $festivalId) {
        //获取我的道具
        $queryParams = array(Festival_Service_PropsTotals::FIELD_UUID=>$uuid,
            Festival_Service_PropsTotals::FIELD_FESTIVAL_ID=>$festivalId);
        $queryReult  = Festival_Service_PropsTotals::getsBy($queryParams);
        $grantPropTotal = 0;
        $consumePropTotal = 0 ;
        foreach ($queryReult as $val){
            $grantPropTotal += $val[Festival_Service_PropsTotals::FIELD_GRANT_TOTAL];
            $consumePropTotal += $val[Festival_Service_PropsTotals::FIELD_COSUME_TOTAL];
        }
        $remainPropsTotal = $grantPropTotal - $consumePropTotal;

        return intval($remainPropsTotal);
    }



    private function getPrizeInfo($festivalId, $online, $uuid) {
        $prizeInfo = array();
        //奖品信息
        $params[Festival_Service_Prizes::FIELD_FESTIVAL_ID] = $festivalId;
        list(, $prizeList) = Festival_Service_Prizes::getList(self::ONE_PAGE, 9 , $params, array('sort' => 'ASC'));
        foreach ($prizeList as $prize){
            $prizeId = $prize[Festival_Service_Prizes::FIELD_ID];
            //已经获取的奖品的数量
            $hadExchangePrizeNum = $this->gethadExchangePrizeNum($festivalId, $prizeId);
            $remainExchangePrizeNum = $prize[Festival_Service_Prizes::FIELD_TOTAL] - $hadExchangePrizeNum;

            $exchangeStatus = 1;
            $isExchagedPirze = false;
            if($online){
                //是否兑换过
                $isExchagedPirze =  $this->isExchangedPrize($festivalId, $prizeId, $uuid, $online);
                //是否能兑换
                $isCanExchagePrize = $this->isCanExchagePrize($festivalId, $prizeId, $uuid, $online);
                if($isExchagedPirze){
                    $exchangeStatus = self::PRIZE_EXCHAGED_STATUS;
                }elseif($isCanExchagePrize && $remainExchangePrizeNum > 0 ){
                    $exchangeStatus = self::PRIZE_CAN_EXCHANGE_STATUS;
                }else{
                    $exchangeStatus = self::PRIZE_NOT_CAN_EXCHANGE_STATUS;
                }
            }
            $prizeCondition = $this->getPrizeCondition($prize);
            $needExchagePropNum = count($prizeCondition);
            $propsTitle = $this->getPirzeConditionTitle($prize['condition']);
            $prizeInfo[] = array('prizeId'    => $prizeId,
                'prizeName'  => $prize[Festival_Service_Prizes::FIELD_NAME],
                'prizeImg'   => $prize[Festival_Service_Prizes::FIELD_IMG],
                'prizeIcon' => $prize[Festival_Service_Prizes::FIELD_ICON],
                'remainExchangePrizeNum' => $remainExchangePrizeNum,
                'exchangeStatus' => $exchangeStatus,
                'prizeType'=>$prize[Festival_Service_Prizes::FIELD_PRIZE_TYPE],
                'needExchagePropNum'=>$needExchagePropNum,
                'isExchagedPirze'=>$isExchagedPirze,
                'propsTitle' => implode(',', $propsTitle),
            );
        }

        return $prizeInfo;
    }

    private function isCanExchagePrize( $festivalId , $prizeId, $uuid , $online) {
        if( !$festivalId || !$uuid || !$prizeId || !$online){
            return false;
        }

        $msg = array('进入方法'=>__METHOD__, '参数'=>func_get_args());
        Util_Log::info(__CLASS__, 'exchage.log', $msg);
        //获取奖品兑换所需要的道具ID
        $queryParams = array(Festival_Service_Prizes::FIELD_FESTIVAL_ID=>$festivalId,
            Festival_Service_Prizes::FIELD_ID=>$prizeId);
        $prizeInfo = Festival_Service_Prizes::getBy($queryParams);
        $prizeCondition = $this->getPrizeCondition($prizeInfo);
        $prizeCondition =  array_count_values($prizeCondition);

        $msg = array('进入方法'=>__METHOD__, '奖品条件'=>$prizeCondition);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);
        $flag = 1;
        $prizeNum = 0;
        foreach ($prizeCondition as $key=>$val){
            //获取我的道具
            $prizeTotalParams = array(
                Festival_Service_PropsTotals::FIELD_FESTIVAL_ID=>$festivalId,
                Festival_Service_PropsTotals::FIELD_PROP_ID=>$key,
                Festival_Service_PropsTotals::FIELD_UUID=>$uuid,
                Festival_Service_PropsTotals::FIELD_REMAIN_TOTAL=>array('>=', $val)
            );
            $result  = Festival_Service_PropsTotals::getBy($prizeTotalParams);
            if(!$result){
                $flag = 0;
                break;
            }
            $prizeNum++ ;
        }

        $msg = array('进入方法'=>__METHOD__, 'flag'=>$flag, 'prizeNum'=>$prizeNum);
        Util_Log::info(__CLASS__, 'exchage.log', $msg);
        if($flag && $prizeNum == count($prizeCondition)){
            return true;
        }
        return false;
    }

    private function isExchangedPrize( $festivalId , $prizeId, $uuid , $online) {
        if( !$festivalId || !$uuid || !$prizeId || !$online){
            return false;
        }
        //获取我的兑换道具
        $queryParams = array(
            Festival_Service_PrizeExchanges::FIELD_FESTIVAL_ID=>$festivalId,
            Festival_Service_PrizeExchanges::FIELD_PRIZE_ID=>$prizeId,
            Festival_Service_PrizeExchanges::FIELD_UUID=>$uuid
        );
        $result  = Festival_Service_PrizeExchanges::getBy($queryParams);
        if($result){
            return true;
        }
        return false;
    }

    private function gethadExchangePrizeNum( $festivalId , $prizeId) {
        if( !$festivalId ){
            return array();
        }
        //获取我的道具
        $queryParams = array(
            Festival_Service_PrizeExchanges::FIELD_FESTIVAL_ID=>$festivalId,
            Festival_Service_PrizeExchanges::FIELD_PRIZE_ID=>$prizeId
        );
        $prizeList  = Festival_Service_PrizeExchanges::getsBy($queryParams);
        $prizeTotal = 0;
        foreach ($prizeList as $val){
            $prizeTotal++;
        }

        return $prizeTotal;
    }

    /**
     * 统计跳转
     */
    public function tjAction(){
        $url = html_entity_decode(html_entity_decode($this->getInput('_url')));
        if (strpos($url, '?') === false) {
            $url = $url.'?t_bi='.$this->getSource();
        } else {
            $url = $url.'&t_bi='.$this->getSource();
        }
        $this->redirect($url);
    }
}