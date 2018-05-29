<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author ljp
 *
*/
class Api_XunSearch_Search {
	
	
	/**
	 * 获取配置信息
	 * @return array
	 */
	public static function getSearchConfig(){
		$config = Common::getConfig('searchConfig');
		return $config;
	}
	
	
	
	/**
	 * 获取查询数据
	 */
	public static function getSearchList($page, $limit, $keyword){
		if(!$keyword){
			return  false;
		}
		
		$config = Api_XunSearch_Search::getSearchConfig();
		$url = $config['searchUrl'].'api/index/search';
		//发送请求
    	$http =  new Util_Http_Curl($url);
    	$data['keyword']= $keyword;
    	$data['page']= $page;
    	$data['limit']= $limit;
    	$result = $http->post($data);
    	return $result;
	}
	
	
	public static function addIndex($data){
		if(!is_array($data)){
			return  false;
		}
		//检查合法
		$ret = self::checkGameInfo($data);
		if(!$ret) return false;
		
		$config = Api_XunSearch_Search::getSearchConfig();
		$url = $config['searchUrl'].'api/index/addIndex';
		
		$rsa = new Util_Rsa();
		$sign =  $rsa->encrypt($config['sign'], Common::getConfig("siteConfig", "rsaPemFile"));
		
		//发送请求
		$http =  new Util_Http_Curl($url);
		$data = array(
				'gameId' => $data['gameId'],
				'gameName' => $data['gameName'],
				'resume' => $data['resume'],
				'label' => $data['label'],
				'create_time' => time(),
				'sign'=>$sign
		);
		$result = $http->post($data);
		return $result;
		
		
	}
	
	public static function updateIndex($data){
		if(!is_array($data)){
			return  false;
		}
		//检查合法
		$ret = self::checkGameInfo($data);
		if(!$ret) return false;

		$config = Api_XunSearch_Search::getSearchConfig();
		$url = $config['searchUrl'].'api/index/updateIndex';
		
		$rsa = new Util_Rsa();
		$sign =  $rsa->encrypt($config['sign'], Common::getConfig("siteConfig", "rsaPemFile"));
		
		//发送请求
		$http =  new Util_Http_Curl($url);
		$data = array(
				'gameId' => $data['gameId'],
				'gameName' => $data['gameName'],
				'resume' => $data['resume'],
				'label' => $data['label'],
				'create_time' => time(),
				'sign'=>$sign
		);
		$result = $http->post($data);
		return $result;
	
	
	}
	
	public static function deleteIndex($gameId){
		if(!$gameId){
			return  false;
		}
	
		$config = Api_XunSearch_Search::getSearchConfig();
		$url = $config['searchUrl'].'api/index/deleteIndex';
		
		$rsa = new Util_Rsa();
		$sign =  $rsa->encrypt($config['sign'], Common::getConfig("siteConfig", "rsaPemFile"));
		
		//发送请求
		$http =  new Util_Http_Curl($url);
		$data = array(
				'gameId' => $gameId,
				'sign'   => $sign
		);
		$result = $http->post($data);
		return $result;
	
	
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
	
	
	
	
	
	
	
}