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
        self::LINK_CONTENT => "游戏",
        self::LINK_CATEGORY => "分类",
        self::LINK_SUBJECT => "专题",
        self::LINK_URL => "外链",
        self::LINK_ACTIVITY => "活动",
    );
    
    public static function checkLinkValue($link_type, $link) {
        $result = "";
        switch ($link_type) {
            case Game_Service_Util_Link::LINK_CONTENT:
                if(! self::checkValueByLinkContent($link)) {
                    $result = "游戏内容失效或不存在";
                }
                break;
            case Game_Service_Util_Link::LINK_CATEGORY:
                if(! self::checkValueByLinkCategory($link)) {
                    $result = "分类失效或不存在";
                }
                break;
            case Game_Service_Util_Link::LINK_SUBJECT:
                if(! self::checkValueByLinkSubject($link)) {
                    $result = "专题失效或不存在";
                }
                break;
            case Game_Service_Util_Link::LINK_ACTIVITY:
                if(! self::checkValueByLinkActivity($link)) {
                    return "活动失效或不存在";
                }
                break;
            case Game_Service_Util_Link::LINK_URL:
                if(! self::checkValueByLinkUrl($link)) {
                    $result = "URL格式错误";
                }
                break;
            case Game_Service_Util_Link::LINK_GIFT:
                if(! self::checkValueByLinkGift($link)) {
                    $result = "礼包失效或不存在";
                }
                break;
        }
        return $result;
    }
    
	/**
	 * 判断游戏
	 */
	private static function checkValueByLinkContent($link) {
	    $game = Resource_Service_Games::getResourceByGames($link);
		return $game && $game['id'];
	}
	
	/**分类*/
	private static function checkValueByLinkCategory($link) {
        $category = Resource_Service_Attribute::getBy(array('id' => $link, 'at_type' => 1));
        return $category && $category['status'];
	}

	/**专题取参数*/
	private static function checkValueByLinkSubject($link) {
	    $subject = Client_Service_Subject::getSubject($link);
	    return $subject && $subject['status'] && $subject['end_time'] > Common::getTime();
	}

	/**活动取参数*/
	private static function checkValueByLinkActivity($link) {
        $hd = Client_Service_Hd::getHd($link);
		return $hd && $hd['status'] && $hd['end_time'] > Common::getTime();
	}

	/**外链取参数*/
	private static function checkValueByLinkUrl($link) {
        if (! preg_match('/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $link)) {
            return false;
        }
        return true;
	}

	/**礼包取参数*/
	private static function checkValueByLinkGift($link) {
        $giftInfo = Client_Service_Gift::getGift($link);
        return $giftInfo && $giftInfo['status'] && 
            $giftInfo['game_status'] == Resource_Service_Games::STATE_ONLINE &&
            $giftInfo['effect_end_time'] > Common::getTime();
	}
	
}
