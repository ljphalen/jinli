<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 链接参数验证
 * Game_Api_Util_Link
 * @author wupeng
 */
class Game_Api_Util_Link {

    public static function isShow($link_type, $link) {
        $result = "";
        switch ($link_type) {
            case Game_Service_Util_Link::LINK_CONTENT:
                $result = self::checkValueByLinkContent($link);
                break;
            case Game_Service_Util_Link::LINK_CATEGORY:
                $result = self::checkValueByLinkCategory($link);
                break;
            case Game_Service_Util_Link::LINK_SUBJECT:
                $result = self::checkValueByLinkSubject($link);
                break;
            case Game_Service_Util_Link::LINK_ACTIVITY:
                $result = self::checkValueByLinkActivity($link);
                break;
            case Game_Service_Util_Link::LINK_URL:
                $result = self::checkValueByLinkUrl($link);
                break;
            case Game_Service_Util_Link::LINK_GIFT:
                $result = self::checkValueByLinkGift($link);
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
        return $subject && $subject['status'] && $subject['start_time'] <= Common::getTime() && $subject['end_time'] > Common::getTime();
	}

	/**活动取参数*/
	private static function checkValueByLinkActivity($link) {
        $hd = Client_Service_Hd::getHd($link);
		return $hd && $hd['status'] && $hd['start_time'] <= Common::getTime() && $hd['end_time'] > Common::getTime();
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
        return $giftInfo && $giftInfo['status'] && $giftInfo['effect_start_time'] <= Common::getTime() && $giftInfo['effect_end_time'] > Common::getTime();
	}

}
