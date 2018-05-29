<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 链接参数验证
 * Game_Service_Util_Link
 * @author wupeng
 */
class Game_Service_Util_Link {

    const LINK_CONTENT = 1;
    const LINK_CATEGORY = 2;
    const LINK_SUBJECT = 3;
    const LINK_URL = 4;
    const LINK_ACTIVITY = 5;
    const LINK_GIFT = 6;

    /**链接类型*/
    public static $linkType = array(
        self::LINK_CONTENT => "内容",
        self::LINK_CATEGORY => "分类",
        self::LINK_SUBJECT => "专题",
        self::LINK_URL => "外链",
        self::LINK_ACTIVITY => "活动",
    );
    
    public static function checkLinkValue($link_type, $link) {
        $params = array();
        switch ($link_type) {
            case Game_Service_Util_Link::LINK_CONTENT:
                $params = self::getParamsByLinkContent($link);
                break;
            case Game_Service_Util_Link::LINK_CATEGORY:
                $params = self::getParamsByLinkCategory($link);
                break;
            case Game_Service_Util_Link::LINK_SUBJECT:
                $params = self::getParamsByLinkSubject($link);
                break;
            case Game_Service_Util_Link::LINK_ACTIVITY:
                $params = self::getParamsByLinkActivity($link);
                break;
            case Game_Service_Util_Link::LINK_URL:
                $params = self::getParamsByLinkUrl($link);
                break;
            case Game_Service_Util_Link::LINK_GIFT:
                $params = self::getParamsByLinkGift($link);
                break;
        }
        return $params;
    }
    
	/**
	 * 本地化首页游戏列表
	 */
	private static function getParamsByLinkContent($link) {
		$game = Resource_Service_GameData::getGameAllInfo($link);
		return $game && $game['id'];
	}
	
	/**分类取参数*/
	private static function getParamsByLinkCategory($link) {
        $category = Resource_Service_Attribute::getBy(array('id' => $link));
        return $category && $category['status'];
	}

	/**专题取参数*/
	private static function getParamsByLinkSubject($link) {
        $subject = Client_Service_Subject::getSubject($link);
        return $subject && $subject['status'];
	}

	/**活动取参数*/
	private static function getParamsByLinkActivity($link) {
        $hd = Client_Service_Hd::getHd($link);
		return $hd && $hd['status'] && $hd['start_time'] <= Common::getTime();
	}

	/**外链取参数*/
	private static function getParamsByLinkUrl($link) {
        if (! preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $link)) {
            return false;
        }
        return true;
	}

	/**礼包取参数*/
	private static function getParamsByLinkGift($link) {
        $giftInfo = Client_Service_Gift::getGift($link);
		return $giftInfo && $giftInfo['status'];
	}
	
}
