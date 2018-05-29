<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏列表格式化
 * @author fanch
 *
 */
class Resource_Service_GameListFormat {


    /**
     * 格式化游戏列表数据
     * 1支持游戏客户端大于等于1.5.1版本游戏列表通用格式数据的格式化操作
     * 2支持低于1.5.1网页版中js加载游戏列表中下一页中的游戏列表数据
     *
     * @param $indexKey
     * @param $gameList
     * @return array
     */
    public static function output($gameList){
        $result = array();
        $gameAllAcoupon = Resource_Service_GameListData::getGameAllAcoupon();
        foreach($gameList as  $item){
            if($gameAllAcoupon){
                $item['rewardAcoupon'][0] = $item['rewardAcoupon'][0] + $gameAllAcoupon;
            }
            $result[] = self::buildItem($item);
        }
        return $result;
    }

    /**
     * @param $item
     * @return array
     */
    private static function buildItem($item){
        $apkVer = Yaf_Registry::get("apkVersion");
        if (strnatcmp($apkVer, '1.5.1') < 0){
            return self::buildItem148($item);
        } else {
            return self::buildItem151($item);
        }
    }

    /**
     * @param $gameList
     * @return array
     */
    private static function buildItem148($item){
        $apkVer = Yaf_Registry::get("apkVersion");
        $flag = (strnatcmp($apkVer, '1.4.8') < 0) ? false :true;
        $webroot = Common::getWebRoot();
        $tjUrl = "/client/index/tj";

        $intersrc = 'CATEGORY' . $item['categoryid'] . '_GID' . $item['gameid'];
        $href = urldecode($webroot . $tjUrl . '?id=' . $item['categoryid'] . '&pc=1&intersrc=' . $intersrc );

        $result = array(
            'id' => $item['gameid'],
            'name' => $item['name'],
            'resume' => html_entity_decode($item['resume'], ENT_QUOTES),
            'size' => $item['size'].'M',
            'link' => Common::tjurl($tjUrl, $item['gameid'], $intersrc, $item['link']),
            'alink' => urldecode($webroot . $tjUrl . '?id=' . $item['gameid'] . '&intersrc=' . $intersrc ),
            'img' => urldecode($item['img']),
            'profile' => $item['name'] . ',' . $href . ',' . $item['gameid'] . ',' . $item['link'] . ',' . $item['package'] . ',' . $item['size'] . ',' . 'Android1.6,240*320-1080*1920',
        );

        if ($flag) {
            //评测链接
            $evaluationUrl = '';
            if ($item['evaluation']) {
                $evaluationUrl = ',评测,' . $webroot . '/client/evaluation/detail/?id=' . $item['evaluation'] . '&pc=3&intersrc=' . $intersrc ;
            }
            //附加属性礼包+评测处理
            $attach = array();
            if ($item['evaluation']) array_push($attach, '评');
            if ($item['gift']) array_push($attach, '礼');
            //js a 标签 data-infpage 参数数据
            $data_info = '游戏详情,' . $href . ',' . $item['gameid'];
            $result['profile'] = $item['evaluation'] ? $data_info . $evaluationUrl : $data_info;
            $result['attach'] = ($attach) ? implode(',', $attach) : '';
            $result['device'] = 0;
            $result['data-type'] = 1;
        }
        return $result;
    }


    /**
     * 格式化单条游戏数据用于输出
     * @param $item
     *
     *
     * @return array
     */
    private static function buildItem151($item) {
        $item['size'] = $item['size'] . 'M';
        $item['img'] = Resource_Service_Games::webpProcess($item['img'], $item['webp']);
        $item['attach'] = $item['gift'];
        $item['freedl'] = self::getFreedlFlag($item['freedl']);
        $item['reward'] = self::getRewardFlag($item);
        $item['score'] = self::getScoreData($item['score']);
        $item['viewType'] = 'GameDetailView';
        self::clearItem($item);
        return $item ;
    }

    /**
     * 游戏评分数据
     * @param $score
     * @return int
     */
    private static function getScoreData($score){
        if(!$score) return 0;
        $apkVer = Yaf_Registry::get("apkVersion");
        if($apkVer){
            $score = $score/2;
        }
        return $score;
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

    private static function clearItem(&$item){
        unset($item['gift']);
        unset($item['webp']);
        unset($item['rewardAcoupon']);
        unset($item['rewardGift']);
        unset($item['evaluation']);
        unset($item['categoryid']);
        unset($item['version']);
        unset($item['versionCode']);
    }
}