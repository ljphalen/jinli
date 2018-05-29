<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐信息
 * Game_Api_Util_RecommendListUtil
 * @author wupeng
 */
class Game_Api_Util_RecommendListUtil {

    /**客户端显示模板*/
    public static $viewType = array(
        Game_Service_Util_Link::LINK_CONTENT => "GameDetailView",
        Game_Service_Util_Link::LINK_CATEGORY => "CategoryDetailView",
        Game_Service_Util_Link::LINK_SUBJECT => "TopicDetailView",
        Game_Service_Util_Link::LINK_ACTIVITY => "ActivityDetailView",
        Game_Service_Util_Link::LINK_URL => "Link",
        Game_Service_Util_Link::LINK_GIFT => "GiftDetailView",
    );

    public static $systemVersionList = array(
        Game_Service_RecommendNew::GREATE_SYSTEM_VERSION,
        Game_Service_RecommendNew::LESS_SYSTEM_VERSION
    );
    
    /**客户端显示模板*/
    public static function getViewType($linkType) {
        return self::$viewType[$linkType];
    }
    
    public static function getClientParamsByBanner($banner) {
        return self::getClientParams($banner['link_type'], $banner['link'], $banner['title']);
    }
    
    public static function getClientParamsByText($text) {
        return self::getClientParams($text['link_type'], $text['link'], $text['title']);
    }
    
    public static function getClientParams($link_type, $link, $title) {
        $params = array();
        switch ($link_type) {
            case Game_Service_Util_Link::LINK_CONTENT:
                $params = self::getParamsByLinkContent($link, $title);
                break;
            case Game_Service_Util_Link::LINK_CATEGORY :
                $params = self::getParamsByLinkCategory ($link, $title);
                break;
            case Game_Service_Util_Link::LINK_SUBJECT :
                $params = self::getParamsByLinkSubject ($link, $title);
                break;
            case Game_Service_Util_Link::LINK_ACTIVITY :
                $params = self::getParamsByLinkActivity($link, $title);
                break;
            case Game_Service_Util_Link::LINK_URL :
                $params = self::getParamsByLinkUrl($link, $title);
                break;
        }
        return $params;
    }
    
	/**
	 * 本地化首页游戏列表
	 */
	private static function getParamsByLinkContent($link, $title) {
		$gameid = $link;
		$game = Resource_Service_GameData::getGameAllInfo($gameid);
		$params = array ();
		if($game) {
		    $params = array(
		        'url' => '',
		        'contentId' => $gameid,
		        'gameId' => $gameid,
		        'title' => html_entity_decode($title),
		        'package' => $game['package']
		    );
		}
		return $params;
	}
	
	/**分类取参数*/
	private static function getParamsByLinkCategory($link, $title) {
        $category = Resource_Service_Attribute::getBy(array('id' => $link));
        $params = array();
        if ($category && $category['status']) {
            $params = array(
                'url' => '',
                'contentId' => $link,
                'gameId' => '',
                'title' => html_entity_decode($title)
            );
        }
        return $params;
	}

	/**专题取参数*/
	public static function getParamsByLinkSubject($link, $title) {
        $subject = Client_Service_Subject::getSubject($link);
        $params = array();
        if (! ($subject && $subject['status'])) {
            return $params;
        }
        if ($subject['start_time'] > Common::getTime() || $subject['end_time'] <= Common::getTime()) {
            return $params;
        }
        $params = array(
            'url' => '',
            'contentId' => $link,
            'gameId' => '',
            'title' => html_entity_decode($title),
        );
        $param = Game_Api_Util_SubjectUtil::getClientApiSubjectParams($subject);
        if($param['subViewType']) {
            $params['subViewType'] = $param['subViewType'];
            $params['url'] = $param['url'];
            $params['source'] = $param['source'];
        }
        return $params;
	}

	/**活动取参数*/
	private static function getParamsByLinkActivity($link, $title) {
        $hd = Client_Service_Hd::getHd($link);
        $params = array();
        if (! ($hd && $hd['status'])) {
            return $params;
        }
        if ($hd['start_time'] > Common::getTime() || $hd['end_time'] <= Common::getTime()) {
            return $params;
        }
        $params = array(
            'url' => '',
            'contentId' => $link,
            'gameId' => $hd['game_id'],
            'title' => html_entity_decode($title)
        );
		return $params;
	}

	/**外链取参数*/
	public static function getParamsByLinkUrl($link, $title, $adtype='', $pos=0) {
        $params = array();
        $anchor = $link;
        if($adtype) {//太乱了，确定是统计用的不能删除
            $tj = '';
            if (stristr($link, 'installe')) {
                $tj = '_ness';
            } elseif (stristr($link, 'rank')) {
                $tj = '_newon';
            } elseif (stristr($link, 'single')) {
                $tj = '_pcg';
            } elseif (stristr($link, 'web')) {
                $tj = '_olg';
            }
            if (strpos(html_entity_decode($link), "?")) {
                $anchor = $anchor . '&intersrc=' . $adtype . $pos . $tj;
            } else {
                $anchor = $anchor . '?intersrc=' . $adtype . $pos . $tj;
            }
        }
        $params = array(
            'url' => html_entity_decode($anchor),
            'contentId' => '',
            'gameId' => '',
            'title' => html_entity_decode($title)
        );
		return $params;
	}

	/**礼包取参数*/
	private static function getParamsByLinkGift($link, $title) {
        $giftInfo = Client_Service_Gift::getGift($link);
        $params = array();
        if ($giftInfo && $giftInfo['status']) {
            $params = array(
                'url' => '',
                'contentId' => $link,
                'gameId' => $giftInfo['game_id'],
                'title' => html_entity_decode($title)
            );
        }
		return $params;
	}
	
	public static function getParams($params) {
	    $result = array();
	    $keys = array('url'=>"", 'contentId'=>"", 'gameId'=>"", 'title'=>"", 'source' => "");
	    foreach ($keys as $key => $value) {
	        if(isset($params[$key])) {
	            $result[$key] = $params[$key];
	        }
	    }
	    if($params['subViewType']) {
	        $result['subViewType'] = $params['subViewType'];
	    }
	    return $result;	    
	}
	
}
