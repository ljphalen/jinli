<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 广东联通免流量接口文件
 * @author fanch
 * 渠道号：1200001
 * 大渠道号：120
 * 子渠道号：1
*/
class Api_Freedl_Cu_Gd {
	
	private static $partnerKey = '108'; //合作方唯一标识
	private static $partnerSecret = '53fda0e351262e1ed099ea00'; //合作方密钥
	private static $partnerChannel = '1200001'; //合作方渠道号
	private static $debug = true;
	
	/**
	 * 联通免流量 接口domain
	 * @return string
	 */
	public static function getDomain(){
 		return (ENV == 'product') ? 'http://open.17wo.cn/FreeFlow' : 'http://112.96.28.198/OpenApiTest';
		//return 'http://open.17wo.cn/FreeFlow';
	}
	
	/**
	 * 根据预先定义的一级分类ID，获取其下属的二级分类列表。
	 * @param id int 大分类id 默认游戏 2
	 * return array
	 */
	public static function getcategorylist($id=2){
		$request = array(
				'id'=>$id,
				'pKey'=> self::$partnerKey,
				'tm'=>self::getTm()				
		);
		$code = self::sign($request);
		$url = sprintf('%s/wo/content/getcategorylist/%s?pKey=%s&tm=%s&code=%s', self::getDomain(), $request['id'], $request['pKey'], $request['tm'], $code);
		$result = Util_Http::get($url, array('Content-Type' => 'application/json; charset=utf-8'));
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'getcategorylist', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}
	
	/**
	 * 资源列表查询接口
	 * @param int $id 二级分类ID
	 * @param int $page 起始页
	 * @param int $limit 每页条数
	 * @param string $platform 平台，枚举类型：android/ios/symbian/wm/windowsphone/all
	 * @param string $date 资源的更新日期,用于增量同步，接口返回该日期之后更新以及新增资源 格式：yyyy-MM-ddTHH:mm:ss
	 * @param string $tagid 用于区别资源，传入相应
	 * @return array
	 */
	public static function getcontentlist($id, $page = 1, $limit = 10, $platform = 'android', $date = '', $tagid = ''){
		$request = array(
				'id' => $id, 
				'pKey' => self::$partnerKey,
				'page' => (($page-1)*$limit). '-' . $limit, //分页，page=20-10表示跳过前20，取10个
				'tm' => self::getTm(),
				'platform' => $platform
		);
		if($date) $request['date'] = $date;
		if($tagid) $request['tagid'] = $tagid;
		$code = self::sign($request);
		$url = sprintf('%s/wo/content/getcontentlist/%s?pKey=%s&tm=%s&code=%s&platform=%s&page=%s', self::getDomain(), $request['id'], $request['pKey'], $request['tm'], $code, $request['platform'], $request['page']);
		if($date) $url.=sprintf('&date=%s', $request['date']);
		if($tagid) $url.=sprintf('&tagid=%s', $request['tagid']);
		$result = Util_Http::get($url, array('Content-Type' => 'application/json; charset=utf-8'));
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'getcontentlist', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}

	/**
	 * 资源详情获取接口
	 * @param id int 联通游戏资源contentid
	 * return array
	 */
	public static function getcontent($id){
		$request = array(
				'id' => $id,
				'pKey'=> self::$partnerKey,
				'tm'=> self::getTm()
		);
		$code = self::sign($request);
		$url = sprintf('%s/wo/content/getcontent/%s?pKey=%s&tm=%s&code=%s', self::getDomain(), $request['id'], $request['pKey'], $request['tm'], $code);
		$result = Util_Http::get($url, array('Content-Type' => 'application/json; charset=utf-8'));
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'getcontent', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}

	/**
	 * 资源下载地址获取接口
	 * @param id int 联通游戏资源contentid
	 * return array
	 */
	public static function getdownloadurl($id){
		$request = array(
				'id' => $id,
				'pKey'=> self::$partnerKey,
				'tm'=> self::getTm(),
				'channel' => self::$partnerChannel
		);
		$code = self::sign($request);
		$url = sprintf('%s/wo/content/getdownloadurl/%s?pKey=%s&tm=%s&code=%s&channel=%s', self::getDomain(), $request['id'], $request['pKey'], $request['tm'], $code, self::$partnerChannel);
		$result = Util_Http::get($url, array('Content-Type' => 'application/json; charset=utf-8'));
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'getdownloadurl', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}
	
	/**
	 * 不带时效的下载地址
	 * @param int $id 联通资源contentid
	 * @return string
	 */
	public static function getmllurl($id){
		$url = sprintf('http://wo.iuni.com.cn/d/download.aspx?cid=%s&channel=%s', $id, self::$partnerChannel);
		return $url;
	}
	
	/**
	 * 资源状态获取接口
	 * @param id int 联通游戏资源contentid
	 * return array
	 */
	public static function getstatus($id){
		$request = array(
				'id' => $id,
				'pKey'=> self::$partnerKey,
				'tm'=> self::getTm(),
		);
		$code = self::sign($request);
		$url = sprintf('%s/content/status/%s?pKey=%s&tm=%s&code=%s', self::getDomain(), $request['id'], $request['pKey'], $request['tm'], $code);
		$result = Util_Http::get($url, array('Content-Type' => 'application/json; charset=utf-8'));
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'getstatus', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}
	
	/**
	 * 新增资源内容
	 * @param array $body 资源数据文件
	 * @param int $areaFlag 专区资源标记，取值如下：0为普通资源 1为wo+快装专区资源 2为免流量专区资源.
	 * @return mixed
	 */
	public static function addcontent($data, $areaFlag=2){
		$request = array(
				'pKey'=> self::$partnerKey,
				'tm'=> self::getTm(),
			    'areaFlag' => $areaFlag
		);
		$request['code'] = self::sign($request);
		$request['data'] = $data; //添加的资源内容数据
		$url = sprintf('%s/content', self::getDomain());
		$result = Util_Http::post($url, json_encode($request), array('Content-Type' => 'application/json; charset=utf-8'));
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'addcontent', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}

	/**
	 * 修改资源内容
	 * @param int $id 合作方内容Id 即资源providerPid
	 * @param array $data 资源数据文件
	 * @return mixed
	 */
	public static function updatecontent($providerPid, $data){
		$request = array(
				'id' => $providerPid,
				'pKey'=> self::$partnerKey,
				'tm'=> self::getTm()
		);
		$request['code'] = self::sign($request);
		$request['data'] = $data; //添加的资源内容数据
		$url = sprintf('%s/cp/content/%s', self::getDomain(), $providerPid);
		$result = Util_Http::post($url, json_encode($request), array('Content-Type' => 'application/json; charset=utf-8'), 10, 'PUT');
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'updatecontent', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}
	
	/**
	 * 更新资源状态
	 * @param int $providerPid 合作方内容Id
	 * @param string $status 资源状态 valid|invalid
	 * @param int category 一级类别ID 1:软件 2:游戏
	 * @return mixed
	 */
	public static function updatestatus($providerPid, $status, $category=2 ){
		$request = array(
				'id' => $providerPid,
				'category' => $category,
				'status' => $status,
				'pKey'=> self::$partnerKey,
				'tm'=> self::getTm()
		);
		$request['code'] = self::sign($request);
		$url = sprintf('%s/cp/content/status/%s', self::getDomain(), $providerPid);
		$result = Util_Http::post($url, json_encode($request), array('Content-Type' => 'application/json; charset=utf-8'), 10, 'PUT');
		//增加请求返回日志
		if(self::$debug) Common::log(array('action'=>'updatestatus', 'response'=>$result->data), date('Y-m-d') . '_cuapi.log');
		$retJson = self::_after($result->data);
		return json_decode($retJson, true);
	}	

	
	/**
	 * code 生成方法：
	 * URL中需要参与计算的字段按降序排列，构造生成值的排列顺序。参数值按照参数的排列顺序组合在一起。
	 * MD5（排列后的字符串）。
	 * 返回url中code签名数据
	 * @param string $data
	 */
	public static function sign($para){
		krsort($para, SORT_STRING | SORT_FLAG_CASE);//按key降序
		reset($para);
		$str = $code = '';
		$partnerKey = self::$partnerSecret;
		foreach ($para as $key => $value){
			$str.= $value;
		}
		$code = md5($str . self::$partnerSecret);	
		return $code;	
	}
	
	/**
	 * 结果预处理
	 */
	private static function _after($data){
		$tmp = $data;
		if(is_null(json_decode($data))){
			//包含头信息的错误提示
			$strArr = explode('\r\n', $data);
			$tmp = $strArr[1];
		}
		return $tmp;
	}
	
	/**
	 * 获取请求的时间tm参数值
	 * @return string
	 */
	private static function getTm(){
		return date('YmdHis');
	}
}