<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Client_Manager_Subject {

    const INFO = 'info';
    const GAMES = 'games';
    const ITEMS = 'items';
    
    public static function getSubject($subjectId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SUBJECT, Util_CacheKey::SUBJECT_INFO);
        $args = array($subjectId, $userId);
        $recommendList = Util_CacheKey::getCache($api, $args);
        return $recommendList;
    }
    
    public static function updateSubject($subjectId, $userId, $subject) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SUBJECT, Util_CacheKey::SUBJECT_INFO);
        $args = array($subjectId, $userId);
        Util_CacheKey::updateCache($api, $args, $subject, 86400);
    }
    
    public static function deleteSubject($subjectId, $userId) {
        $api = Util_CacheKey::getApi(Util_CacheKey::SUBJECT, Util_CacheKey::SUBJECT_INFO);
        $args = array($subjectId, $userId);
        Util_CacheKey::deleteCache($api, $args);
    }

    public static function loadSubject($subjectId, $userId) {
        //从数据库同步数据到缓存
        $subject = array();
		$info = Client_Service_Subject::getSubject($subjectId);
		if($info) {
		    $subject[self::INFO] = $info;
		    $games = Client_Service_SubjectGames::getSubjectAllGames($subjectId);
		    $itemGames = array();
		    foreach ($games as $game) {
		        $itemGames[$game['item_id']][] = $game;
		    }
		    if($info['sub_type'] == Client_Service_Subject::SUBTYPE_CUSTOM) {
		        $itemList = array();
		        $itemGameList = array();
		        $items = Client_Service_SubjectItems::getsBySubId($subjectId);
		        $index=0;		        
		        foreach ($items as $item) {
		            $itemList[] = $item;
		            $itemGameList[$index++] = $itemGames[$item['item_id']];
		        }
		        $subject[self::ITEMS] = $itemList;
		    }else{
		        $itemGameList = $itemGames[0];
		    }
		    $subject[self::GAMES] = $itemGameList;
		}
        self::updateSubject($subjectId, $userId, $subject);
        return $subject;
    }
    
    public static function saveSubject2DB($subjectInfo) {
        $subject = $subjectInfo[self::INFO];
        $items = $subjectInfo[self::ITEMS];
        $games = $subjectInfo[self::GAMES];
        return Client_Service_Subject::saveSubject($subject, $items, $games);
    }
    
}
