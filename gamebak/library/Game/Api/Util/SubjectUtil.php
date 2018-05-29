<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 专题api相关
 * Game_Api_Util_SubjectUtil
 * @author wupeng
 */
class Game_Api_Util_SubjectUtil {

	/**判断专题是否可以显示到对应版本客户端*/
	public static function isSubjectShowToClient($subject, $clientVersion) {
        $show = true;
        do{
            if (! ($subject && $subject['status'] == Client_Service_Subject::SUBJECT_STATUS_OPEN)) {
                $show = false;
                break;
            }
            if($subject['sub_type'] != Client_Service_Subject::SUBTYPE_CUSTOM) {
                break;
            }
            if(! ($clientVersion && self::isSubjectCustomShowToClient($clientVersion))) {
                $show = false;
                break;
            }
        }while(false);
        return $show;
	}
	
	/**判断自定义专题是否可以显示到对应版本客户端*/
	public static function isSubjectCustomShowToClient($clientVersion) {
	    return Common::isAfterVersion($clientVersion, '1.5.8');
	}

	public static function getClientApiSubjectParamsById($subjectId) {
	    $subject = Client_Service_Subject::getSubject($subjectId);
	    return self::getClientApiSubjectParams($subject);
	}
	
	/**取客户端api专题相关部分参数*/
	public static function getClientApiSubjectParams($subject) {
	    /**
	     * 1.5.8增加了自定义专题,兼容之前的版本,自定义专题额外增加了两个字段标识自定义专题
	     * subViewType => WebView
	     * url => 自定义专题网页地址
	     */
	    $params = array();
	    if ($subject) {
            $params = array(
                'url' => '',
                'contentId' => $subject['id'],
                'viewType' => Game_Api_Util_RecommendListUtil::getViewType(Game_Service_Util_Link::LINK_SUBJECT)
            );
	        if($subject['sub_type'] == Client_Service_Subject::SUBTYPE_CUSTOM) {
	            $params['subViewType'] = 'WebView';
	            $webroot = Yaf_Application::app()->getConfig()->webroot;
	            $intersrc = 'SUBJECT'.$subject['id'];
	            $params['url'] = $webroot.Client_Service_Subject::CLIENT_URL. '?id=' . $subject['id'].'&intersrc='.$intersrc;
	            $params['source'] = 'subject'.$subject['id'];
	        }
	    }
	    return $params;
	}
	
	
}
