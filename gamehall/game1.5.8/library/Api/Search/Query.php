<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author ljp
 *
*/
class Api_Search_Query {
	
	const Search_Log_Init = '1';
	const Search_Forbid_Init = '0';
	const Search_Action_Default = '0';
	const Search_Action_Self = '1';
	const Search_Action_noSelf = '2';
	const Search_From_Default = '2';
	const Search_From_Web = '1';
	const Search_From_Android = '3';
	
	/**
	 * 获取配置信息
	 * @return array
	 */
	public static function getSearchConfig(){
		$config = Common::getConfig('searchConfig');
		return $config;
	}
	
	public static function getHotSearchConnectList($keyword, $limit, $searchInit = array()) {
	    if(!$keyword){
	        return  false;
	    }
	    $config = Api_Search_Query::getSearchConfig();
	    $url = $config['searchUrl'].'gameSearchSystem/gameSuggestionSearch!search.action';
	    $http =  new Util_Http_Curl($url);
	    $data['keyword']= $keyword;
	    $data['pageSize']= $limit;
	    $data['highLight']= '1';
	    $result = $http->post($data);
	    $result = self::handleNewListToOldList(json_decode($result, true));
	    return $result;
	}
	
	/**
	 * 获取查询数据
	 */
	public static function getSearchList($page, $limit, $keyword, $searchInit = array()){
		if(!$keyword){
			return  false;
		}
		$config = Api_Search_Query::getSearchConfig();
		$url = $config['searchUrl'].'gameSearchSystem/gameSearch!search.action';
		//发送请求
    	$http =  new Util_Http_Curl($url);
    	$data['keyword']= $keyword;
    	$data['pageNum']= $page;
    	$data['pageSize']= $limit;
    	$data['log'] = self::Search_Log_Init;
    	$data['forbid'] = self::Search_Forbid_Init;
    	$data['searchAction'] = $searchInit['searchAction'] ? $searchInit['searchAction'] : self::Search_Action_Default;
    	$data['searchFrom'] = $searchInit['searchFrom'] ? $searchInit['searchFrom'] : self::Search_From_Default;
    	$data['ip'] = Common::getClientIP();
    	$data['ua'] = $searchInit['ua'] ?  $searchInit['ua'] : $_SERVER['HTTP_USER_AGENT'];
    	$data['uid'] = Util_Cookie::get('search_unit_key', false);
    	$data['account'] = $searchInit['uuid'];
    	$result = $http->post($data);
    	$result = self::handleNewListToOldList(json_decode($result, true));
    	return $result;
	}
	
	
	public static function handleSearchList($listInit, $type = '') {
	    $isGionee = $listInit['localGameList']['totalCount'] && $listInit['action'] ? TRUE : FALSE;
		if($isGionee){
	    	$return = self::handleGioneeList($listInit, $type);
	    } else {
	    	$return = self::handleBaiduList($listInit, $type);
	    }
	    if($return['gamenewlist']) $return['gamelist'] = $return['gamenewlist'];
	    return $return;
	}
	
	private static function handleGioneeList($listInit, $type) {
	    $return['from'] = 'gn';
	    $return['total'] = $listInit['localGameList']['totalCount'];
	    $return['gamelist']  = $listInit['localGameList']['list'];
	    if($type == 'h5') {
	        foreach ($return['gamelist'] as $value){
	            $game = Resource_Service_GameData::getGameAllInfo($value['id']);
	            $game['name'] = $value['name'];
	            $return['gamenewlist'][] = $game;
	        }
	    }
	    return $return;
	}
	
	private static function handleBaiduList($listInit, $type) {
	    $return['from'] = 'baidu';
	    $baiduApi = new Api_Baidu_Game();
	    if($type == 'h5') {
	        list($return['total'], $return['gamelist']) = $baiduApi->engineList($listInit['keyword'],1,$listInit['perpage']);
	    } else {
	        list($return['total'], $baiduGames) = $baiduApi->engineList($listInit['keyword'],$listInit['page'],$listInit['perpage']);
	        if($baiduGames){
	            $return['gamelist'] = Common::resetKey($baiduGames, 'id');
	            $currentVersion = Yaf_Registry::get("apkVersion");
	            $destVersion = "1.5.7";
	            if(Common::compareWithVersion($currentVersion, $destVersion)){
	                $return['resum'] = "以下内容来自互联网，可能不适配您的手机";
	            } else {
	                $return['resum'] = "百度应用的游戏可能不适配您的手机";
	            }
	        } else {
	            $return['gamelist'] = '';
	        }
	    }
	    return $return;
	}
	
	public static function checkGameInfo($data){
		if(!$data['gameId']){
			return false;
		}
		if(!$data['gameName']){
			return false;
		}
		return true;
	}
	
	private static function handleNewListToOldList($newList) {
		$returnList = array('data' =>
			array('hasNext' => $newList['pageSize']*$newList['pageNum'] >= $newList['totalCount'] ? false : true, 
					'totalCount' => $newList['totalCount'] , 
					'curPage' => $newList['pageNum'], 
					'list' => array()
			)
		);
		foreach($newList['beanList'] as $key => $value) {
			$returnList['data']['list'][] = array(
			    'id' => $value['id'], 
			    'name' => $value['name'], 
			    'resume' => $value['resume'], 
			    'label' => $value['label'], 
			    'create_time' => $value['createTime']
			);
		}
		return $returnList;
	}
	
	
	
	
}