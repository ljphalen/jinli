<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏附加属性缓存。
 * 所有数据hash存储
 * @author fanch
 *
 */
class Resource_Service_GameExtraData extends Common_Service_Base{

    const FILL_FIRST_PAGE = 'first';
    const FILL_NEXT_PAGE = 'next';

    /**
     * 根据游戏id批量获取游戏附加属性
     * @param $gameIds
     * @return array
     */
    public static function getGamesExtraData($gameIds) {
        if(empty($gameIds)) return array();
        $extraData = array();
        $cacheExtra = Resource_Service_GameExtraCache::getExtraByIds($gameIds);
        foreach ($cacheExtra as $key => $item) {
            $extraData[$key] = self::buildItem(json_decode($item, true));
        }
        return $extraData;
    }

    /**
     * 填充游戏列表附加属性
     * @param $gameList
     * @param $gameIdField
     * @param string $action
     * @return array
     */
    public static function fillGamesExtraData(&$gameList, $gameIdField, $action=''){
        $apkVer = Yaf_Registry::get("apkVersion");
        if(strnatcmp($apkVer, '1.4.8') < 0){
            return $gameList;
        }
        $data = Common::resetKey($gameList, $gameIdField);
        $gameIds = array_keys($data);
        $gamesExtra = self::getGamesExtraData($gameIds);
        $result = self::fillFormatData($data, $gamesExtra, $apkVer,$action);
        return $result;
    }

    /**
     * @param $gamesList
     * @param $gamesExtra
     * @param $action
     * @return array
     */
    private static function fillFormatData(&$gameList, $gamesExtra, $apkVer, $action){
        if (strnatcmp($apkVer, '1.5.1') < 0){
            return self::fillFormatLt151($gameList, $gamesExtra, $action);
        }else{
            return self::fillFormatGt151($gameList, $gamesExtra);
        }
    }

    /**
     * @param $gamesList
     * @param $gamesExtra
     * @return array
     */
    private static function fillFormatGt151(&$gameList, $gamesExtra){
        $result = array();
        foreach($gameList as $key => $item){
            $gameExtra = $gamesExtra[$key];
            $item['attach'] = ($gameExtra['attach']) ? 1 : 0;
            $item['score'] =  ($gameExtra['score']) ? $gameExtra['score']/2 : 0;
            $item['freedl'] =  $gameExtra['freedl'];
            $item['reward'] =  $gameExtra['reward'];
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param $gamesList
     * @param $gamesExtra
     * @param $action
     * @return array
     */
    private static function fillFormatLt151(&$gameList, $gamesExtra, $action){
        switch($action){
            case 'first':
                return self::fillFirstPage($gameList, $gamesExtra);
                break;
            case 'next':
                return self::fillNextPage($gameList, $gamesExtra);
                break;
        }
    }

    /**
     * @param $gamesList
     * @param $gamesExtra
     * @return array
     */
    private static function fillFirstPage(&$gameList, $gamesExtra){
        $result = array();
        foreach($gameList as $key => $item){
            $gameGift = $gamesExtra[$key]['attach'];
            $item['gift_info'] = ($gameGift) ? 1 : 0;
            $gameEvaluation = $gamesExtra[$key]['evaluation'];
            $item['pc_info'] = ($gameEvaluation) ? 1 : 0;
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param $gamesList
     * @param $gamesExtra
     * @return array
     */
    private static function fillNextPage(&$gameList, $gamesExtra){
        $result = array();
        foreach($gameList as $key => $item){
            if ($item['attach']) {
                $attachArr = array();
                $gameEvaluation = $gamesExtra[$key]['evaluation'];
                if($gameEvaluation){
                    $attachArr = array('评');
                }
                $gameGift = $gamesExtra[$key]['attach'];
                if ($gameGift) {
                    array_push($attachArr, '礼');
                    $attachArr = array_unique($attachArr);
                }
                $item['gift_info'] = implode(',', $attachArr);
            }
            $result[] = $item;
        }
        return $result;
    }

    private static function buildItem($item) {
        $data=array(
            'evaluation' => $item['evaluation'],
            'attach' => self::getAttachByGame($item),
            'freedl' => self::getFreedlFlag($item['freedl']),
            'reward' => self::getRewardFlag($item),
            'score' => $item['score']
        );
        return $data ;
    }
    
    /**
     * 通过游戏ID 判断是否存在可用礼包
     * @param int $gameId
     * @return boolean
     */
    private static function getAttachByGame($item) {
        if (!intval($gameId)) return false;
        $apkVer = Yaf_Registry::get("apkVersion");
        if (strnatcmp($apkVer, '1.6.1') < 0) return $item['commonGift'];
        return $item['vipGift'];
    }

    private static function getFreedlFlag($item){
        $apkVer = Yaf_Registry::get("apkVersion");
        //版本低于1.5.3免流量不支持直接返回
        if (strnatcmp($apkVer, '1.5.3') < 0) return '';
        return $item['freedl'];
    }

    private static function getRewardFlag($item){
        $apkVer = Yaf_Registry::get("apkVersion");
        //版本低于1.5.5不支持直接返回
        if (strnatcmp($apkVer, '1.5.5') < 0) return '';

        list($ticketCount, $isSendTicket) = $item['rewardAcoupon'];
        if(strnatcmp($apkVer, '1.5.8') < 0){
            if(!$isSendTicket) {
                return '';
            }
            $rewardFlag = 'aticket';
        } else {
            $rewardFlag = self::getGameRewardMark($item['rewardGift'], $isSendTicket, $ticketCount);
        }
       return $rewardFlag;
    }

    private static function getGameRewardMark($item, $isSendTicket, $ticketCount){
        $rewardMark = array();
        if ($isSendTicket) {
            $rewardMark = array(
                'rewardTypeCount' => Client_Service_GiftActivity::ONE_PRESENT,//'1',
                'remindDes' => '安装登录游戏后，即可获得'.$ticketCount.'A券！',
                'rewardStatisId' => Client_Service_GiftActivity::ATICKET_SIGN, //'3'
            );
        }

        if(!$item){
            return $isSendTicket ? $rewardMark : '';
        }

        if($item['install'] == 1 && $isSendTicket && !$item['loginGame']){
            $rewardMark = array(
                'rewardTypeCount' => Client_Service_GiftActivity::TWO_PRESENT,//'2',
                'remindDes' => '成功安装游戏后，即有机会获得限量礼包！\n安装登录游戏后，即可获得'.$ticketCount.'A券！',
                'rewardStatisId' => Client_Service_GiftActivity::INSTALL_GAME_ATICKET_SIGN,//'5'
            );
            return $rewardMark;
        }

        if($item['install'] && !$isSendTicket && !$item['loginGame']){
            $rewardMark = array(
                'rewardTypeCount' => Client_Service_GiftActivity::ONE_PRESENT,//'1',
                'remindDes' => '成功安装游戏后，即有机会获得限量礼包！ ',
                'rewardStatisId' => Client_Service_GiftActivity::INSTALL_GAME_SIGN,//'1'
            );
            return $rewardMark;
        }

        if($item['loginGame'] && $isSendTicket && !$item['install']){
            $rewardMark = array(
                'rewardTypeCount' => Client_Service_GiftActivity::TWO_PRESENT,//'2',
                'remindDes' => '安装登录游戏后，即可获得'.$ticketCount.'A券，并有机会获得限量礼包！',
                'rewardStatisId' => Client_Service_GiftActivity::LOGIN_GAME_ATICKET_SIGN,//'6'
            );
            return $rewardMark;
        }

        if($item['loginGame'] && !$isSendTicket && !$item['install']){
            $rewardMark = array(
                'rewardTypeCount' => Client_Service_GiftActivity::ONE_PRESENT,//'1',
                'remindDes' => '安装登录游戏后，即有机会获得限量礼包！',
                'rewardStatisId' => Client_Service_GiftActivity::LOGIN_GAME_SIGN,//'2'
            );
            return $rewardMark;
        }

        if($item['loginGame'] && !$isSendTicket && $item['install']){
            $rewardMark = array(
                'rewardTypeCount' => Client_Service_GiftActivity::TWO_PRESENT,//'2',
                'remindDes' => '成功安装游戏后，即有机会获得限量礼包！\n安装登录游戏后，即有机会获得限量礼包！',
                'rewardStatisId' => Client_Service_GiftActivity::INSTALL_LOGIN_GAME_SIGN,//'4'
            );
            return $rewardMark;
        }

        if($item['loginGame'] && $isSendTicket && $item['install']){
            $rewardMark = array(
                'rewardTypeCount' => Client_Service_GiftActivity::THREE_PRESENT,//'3',
                'remindDes' => '成功安装游戏后，即有机会获得限量礼包！\n安装登录游戏后，即可获得'.$ticketCount.'A券，并有机会获得限量礼包！',
                'rewardStatisId' => Client_Service_GiftActivity::INSTALL_LOGIN_GAME_ATICKET_SIGN,//'7'
            );
            return $rewardMark;
        }
        return $rewardMark;
    }
}