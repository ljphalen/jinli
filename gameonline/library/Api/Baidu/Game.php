<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * games api
 * @author lichanghua
 *
 */
class Api_Baidu_Game extends Common_Service_Base{
	
	public $url = "http://m.baidu.com/api";
	private $params = array(
				'from'=>'1000474v',
				'token'=>'gioneegame',
				'type'=>'app',
				'class'=>'g'
			);
	
	/**
	 * 游戏列表
	 * @param 关键字 $keyword
	 * @param  页码 $page
	 * @param 每页多少条数据 $limit
	 */
	public function search($keyword, $page, $perpage) {
		$page = intval($page);
		$params = array_merge($this->params, array('rn'=>$perpage, 'pn'=>(($page -1) * $perpage)));
		$url = sprintf('%s?action=search&word=%s', $this->url, $keyword);
		$result = self::getResponse($url, $params);
		$apps = (array)$result['apps'];
		$tmp = array();
		if ($result['ret_num'] == 1) $apps['app'] = array($apps['app']);
		
		foreach($apps['app'] as $key=>$value) {
			array_push($tmp, (array) $value);
		}
		
		return array($result['disp_num'], $tmp);
	} 	

	/**
	 * 游戏内容
	 * @param 游戏ID $id 
	 */
	public  function get($id) {
		$params = array_merge($this->params, array('docid'=>$id));
		$url = sprintf('%s?action=search&%s', $this->url, http_build_query($params));
		$result = self::getResponse($url, $params);
		return (array) $result['app'];
	}
	
	/**
	 * 
	 * 游戏详情
	 * @游戏ID $id
	 * @游戏来源 $from
	 */
	public  function getInfo($id, $from) {
		$tmp = $img = array();
		if($from == 'baidu'){
			$result = $this->get($id);
			$tmp['id'] = $result['docid'];
			$tmp['name'] = strip_tags($result['sname']);
			$tmp['link'] = $result['download_url'];
			$tmp['language'] = $result['lang'];
			$tmp['package'] = $result['package'];
			$tmp['size'] = sprintf("%.2f", $result['size'] /(1024*1024));
			$tmp['version'] = $result['platform_version'];
			$tmp['img'] = $result['icon'];
			$tmp['min_resolution'] = '';
			$tmp['max_resolution'] = '';
			$tmp['category'] = $result['catename'];
			$tmp['price'] = '';
			$tmp['updatetime'] = $result['updatetime'];
			$tmp['descrip'] = $result['brief'];
			$tmp['developer'] = $result['sourcename'];
			$tmp['tgcontent'] = '';
			$tmp['apply_version'] = $result['versionname'];
			array_push($img,$result['screenshot1']);
			array_push($img,$result['screenshot2']);
			$tmp['simgs'] = $tmp['gimgs'] = $img;
		} 
		return $tmp;
	}
	
	/**
	 * 组装游戏数组
	 * @搜索关键字 $keyword
	 * @当前页数 $page
	 * @页码偏移量 $perpage
	 */
	public function engineList($keyword,$page,$perpage) {
		$this->search($keyword, $page, $perpage);
		list($total, $result) = $this->search($keyword, $page, $perpage);
		if($result){
			foreach($result as $key=>$value){
				$tmp[$key]['id'] = $value['docid'];
				$tmp[$key]['from'] = 'baidu';
				$tmp[$key]['name'] = htmlspecialchars(strip_tags($value['sname']));
				$tmp[$key]['link'] = $value['download_url'];
				$tmp[$key]['package'] = $value['package'];
				$tmp[$key]['resume'] = $value['catename'];
				$tmp[$key]['language'] = $value['lang'];
				$tmp[$key]['img'] = $value['icon'];
				$tmp[$key]['size'] = sprintf("%.2f", $value['size'] /(1024*1024));
				$tmp[$key]['version'] = $value['platform_version'];
				$tmp[$key]['min_resolution'] = '';
				$tmp[$key]['max_resolution'] = '';
				$tmp[$key]['category'] = $value['catename'];
				$tmp[$key]['price'] = $value['price'];
				$tmp[$key]['updatetime'] = $value['updatetime'];
				$tmp[$key]['descrip'] = $value['brief'];
				$tmp[$key]['apply_version'] = $result['versionname'];
			}
			return array($total, $tmp);
		} else {
			return array(0, array());
		}
	
	}
	
	/**
	 * 获取请求并处理异常
	 * @param string $url
	 * @param array $params
	 * @return array
	 */
	public static function getResponse($url, $params) {
		$curl = new Util_Http_Curl($url);
		$output = $curl->get($params);
		$result = (array) simplexml_load_string($output, null, LIBXML_NOCDATA);
		if ($result['statuscode'] !== 0) {
			Common::log($result, 'api.log');
		}
		return (array)$result['result'];
	}
	
	/*
	 * 获取游戏的分类
	 */

	public  function getCategory() {
	
		$params = array('from'=>$this->params['from'],
				        'token'=>$this->params['token'],
				         'type'=>$this->params['type']	
			);
		
		$url = sprintf('%s?action=cate&%s', $this->url, http_build_query($params));
		$result = self::getResponseCategory($url, $params);
		return (array) $result['category'];
	}
	
	/**
	 * 发送请求，获取百度分类
	 * @param unknown_type $url
	 * @param unknown_type $params
	 * @return array
	 */
	public static function getResponseCategory($url, $params) {
		$curl = new Util_Http_Curl($url);
		$output = $curl->get($params);
		$result = (array) simplexml_load_string($output, null, LIBXML_NOCDATA);
		if ($result['statuscode'] !== 0) {
			Common::log($result, 'api.log');
		}
		return (array)$result['categories'];
	}
	
}