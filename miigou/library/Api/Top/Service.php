<?php
if (! defined ( 'BASE_PATH' )) 	exit ( 'Access Denied!' );
/**
 * 淘宝开放平台接口
 *
 * @author rainkid
 * 参考文档：http://my.open.taobao.com/apidoc/index.htm
 */
class Api_Top_Service extends Api_Top_Base {
	protected static $instance = null;
	protected $expireTime = 300;
	protected $taobaokeNick = '';
	protected $isDaily = false;
	protected $pid = '';
	protected $specialCacheExpiredTimeMethods = array ( // 单独定义某些方法的缓存时间
			'getLimitPromotionRate' => 10 
	);
	
	/**
	 * 构造方法声明为private，防止直接创建对象
	 */
	public function __construct() {
		$this->appkey = Common::getConfig ( 'apiConfig', 'taobao_top_key' );
		$this->secretKey = Common::getConfig ( 'apiConfig', 'taobao_top_secret' );
		$this->gatewayUrl = Common::getConfig ( 'apiConfig', 'taobao_top_api_url' );
		$this->containerUrl = Common::getConfig ( 'apiConfig', 'taobao_top_api_container_url' );
		$this->taobaokeNick = Common::getConfig ( 'apiConfig', 'taobao_taobaoke_nick' );
		$this->isDaily = (stripos ( $this->gatewayUrl, 'daily.taobao.net' ) !== false);
		$this->expireTime = Common::getConfig ( 'apiConfig', 'taobao_top_cache_expire_time' );
		$this->pid = Common::getConfig('apiConfig', 'taobao_taobaoke_pid');
	}
	
	/**
	 * 过滤url
	 * @param string $url        	
	 */
	protected function filterUrl($url) {
		if ($this->isDaily) {
			$url = str_replace ( 'taobao.com/', 'daily.taobao.net/', $url );
		}
		return parent::filterUrl ( $url );
	}
	
	public function curl($url, $postFields = null) {
		if ($this->isDaily) {
			$url = str_replace ( 'taobao.com/', 'daily.taobao.net/', $url );
		}
		return parent::curl ( $url, $postFields );
	}
	
	/**
	 * 得到单个商品信息
	 *
	 * @param number $numIid  	商品数字ID
	 * @return array
	 */
	public function getItemInfoDetail($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Api_Top_Base::getTaokeItemInfo()
	 */
	public function getTaokeItemInfo(array $params) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Api_Top_Base::getTaokeMobileItemInfo()
	 */
	public function getTbkItemInfo(array $params) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 根据查询条件查询淘宝客的商品
	 * 'mall_info'=>false, 'saller_info'=>false
	 * 
	 * @param array $params  	* taobao.taobaoke.items.get 参数
	 * @return array
	 */
	public function findTaobaokes(array $params) {
		if (! array_key_exists ( 'keyword', $params ) && ! array_key_exists ( 'cid', $params ))
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 淘宝客推广商品查询
	 * 'mall_info'=>false, 'saller_info'=>false
	 *
	 * @param array $params  	* taobao.taobaoke.items.get 参数
	 * @return array
	 */
	public function taobaoTbkItemsGet(array $params) {
		if (! array_key_exists ( 'keyword', $params ) && ! array_key_exists ( 'cid', $params ))
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 无线淘宝客推广商品查询
	 * 'mall_info'=>false, 'saller_info'=>false
	 *
	 * @param array $params  	* taobao.taobaoke.items.get 参数
	 * @return array
	 */
	public function getTaobaokeMobileItems(array $params) {
		if (! array_key_exists ( 'keyword', $params ) && ! array_key_exists ( 'cid', $params ))
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Api_Top_Base::getTaoBaoItemCats()
	 */
	public function getTaoBaoItemCats($cid = 0) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 根据查询条件查询淘宝的商品
	 *
	 * @param array $params 	taobao.items.search 参数
	 * @return array
	 */
	public function findItems(array $params) {
		if (! array_key_exists ( 'q', $params ))
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取淘宝的商品评价列表
	 *
	 * @param $pageSize 每页显示的条数，允许值：5、10、20、40        	
	 */
	public function getTraderatesDetail($numIid, $page = 1, $pageSize = 20) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取卖家店铺的基本信息
	 *
	 * @param string $nick  	卖家昵称
	 * @return array
	 */
	public function getMallInfo($nick) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取卖家店铺的基本信息
	 *
	 * @param string $nick  	卖家昵称
	 * @return array
	 */
	public function getShopInfo($nick) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取前台展示的店铺类目
	 *
	 * @return array
	 */
	public function getShopCatsList() {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 淘宝客店铺搜索
	 *
	 * @return array
	 */
	public function getTaobaokeShops() {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取单个用户信息
	 *
	 * @param string $nick  	昵称
	 * @return array
	 */
	public function getUserInfo($nick) {
		if (strpos ( $nick, '*' ) !== false)
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取卖家信息
	 *
	 * @param string $nick  	昵称
	 * @return array
	 */
	public function getSellerInfo($nick) {
		if (strpos ( $nick, '*' ) !== false)
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取单个用户信息
	 *
	 * @param string $nick
	 *        	昵称
	 * @return array
	 */
	public function getUserInfos(array $nicks) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 得到单个商品信息
	 *
	 * @param number $numIid
	 *        	商品数字ID
	 * @return array
	 */
	public function getItemInfo($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 得到多个商品信息
	 *
	 * @param number $numIid  	商品数字ID
	 * @return array
	 */
	public function getItemInfos($numIids) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取单个运费模板
	 *
	 * @param string $nick 	昵称
	 * @param int $postageId 	邮费模板id
	 * @return array
	 */
	public function getPostage($nick, $postageId) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获得淘宝商品运费模板信息
	 *
	 * @param 卖家nick $nick        	
	 * @param postage_id $postage_id        	
	 * @author zhangyin
	 */
	public function getDeliveryTemplate($nick, $postage_id) {
		if (! $nick || ! is_string ( $nick )) {
			return false;
		}
		if (is_numeric ( $postage_id ) == false) {
			return false;
		}
		
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取单个商品的所有 SKU
	 *
	 * @param number $numIid 	商品的数字IID
	 * @return array$response = Util_Http::post ( $url, $fields);
	 */
	public function getItemSkus($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取淘宝的商品评价列表
	 *
	 * @param $pageSize 每页显示的条数，允许值：5、10、20、40        	
	 */
	public function getTraderates($numIid, $page = 1, $pageSize = 20) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取多件商品的淘宝客信息
	 *
	 * @param array $numIids 	淘宝客商品数字id串
	 */
	public function getTaobaokes(array $params) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 无线淘客商品转换
	 *
	 * @param array $params  	num_iids 必填
	 * @return array
	 */
	public function taobaokeMobileItemsConvert(array $params) {
		if (! array_key_exists ( 'num_iids', $params ))
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取淘宝的商品评价总数
	 */
	public function getTraderateNum($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取买家交易记录
	 */
	public function getTradelogs($numIid, $bidPage = 1, $pageSize = 15, $userId = null) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 抓取某个商品页面内容
	 *
	 * @param number $numIid        	
	 */
	public function getItemPage($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取买家交易记录总数
	 */
	public function getTradelogNum($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 同时获取多个用户的VIP等级
	 *
	 * @see http://baike.corp.taobao.com/index.php/%E8%8E%B7%E5%8F%96VIP%E7%94%A8%E6%88%B7%E7%AD%89%E7%BA%A7%E6%8E%A5%E5%8F%A3%EF%BC%88WEB%EF%BC%89
	 * @param array $userIds        	
	 * @return array
	 */
	public function getUserVipLevels(array $userIds) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取单个用户的VIP等级
	 *
	 * @param number $userId        	
	 */
	public function getUserVipLevel($userId) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取限时折扣信息
	 *
	 * @param number $numIid        	
	 * @param string $nick        	
	 */
	public function getLimitPromotionRate($numIid, $nick = null) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取淘宝VIP会员的打折幅度
	 *
	 * @param number $numIid        	
	 * @return array array("VIP 金　卡"， "VIP 白金卡"， "VIP 钻石卡")
	 */
	public function getVipDiscountRegion($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 物流流转信息查询
	 *
	 * @param number $tid  	淘宝交易号
	 * @param string $sellerNick 	卖家昵称
	 * @return array
	 */
	public function getLogisticsTrace($tid, $sellerNick) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取某件商品的淘宝客的链接地址
	 */
	public function getTaobaokeClickUrl($numIid) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取某件商品的淘宝客信息
	 *
	 * @param string $numIid  	淘宝客商品数字id串
	 */
	public function getTaobaoke(array $params) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 淘宝客店铺转换
	 *
	 * @param array $params  	sids  seller_nicks 两者必其一 
	 * @return array
	 */
	public function TaobaokeShopsConvert(array $params) {
		if (! array_key_exists ( 'sids', $params ) && ! array_key_exists ('seller_nicks', $params ))
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 商品关联推荐 
	 *
	 * @param array $params  	 relate_type 必填
	 * @return array
	 */
	public function taobaokeItemsRelate(array $params) {
		if (! array_key_exists ( 'relate_type', $params ))
			return array ();
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Api_Top_Base::getTaokeReport()
	 */
	public function getTaokeReport(array $params) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * Sku名字对应关系
	 *
	 * @param string $propsName        	
	 */
	public function getSkuPropsNameMap($propsName) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 获取单笔交易的部分信息
	 *
	 * @param $tid 交易编号        	
	 */
	public function getTradeInfo($tid, $topSession = null) {
		$args = func_get_args ();
		return $this->cacheFactory ( __FUNCTION__, $args );
	}
	
	/**
	 * 缓存工厂
	 *
	 * @param string $functionName 	方法名
	 * @param array $arguments  	func_get_args()
	 */
	protected function cacheFactory($functionName, $arguments, $obj = 'parent') {
		$cacheKey = __CLASS__ . '.' . $functionName . '.' . md5 ( serialize ( $arguments ) );
		try {
			$result = Common::getCache ()->get ( $cacheKey );
		} catch ( Exception $e ) {
			$result = null;
		}
		
		if (! $result) {
			$result = call_user_func_array ( array (
					$obj,
					$functionName 
			), $arguments );
			
			$expireTime = isset ( $this->specialCacheExpiredTimeMethods [$functionName] ) ? $this->specialCacheExpiredTimeMethods [$functionName] : $this->expireTime;
			try {
				Common::getCache ()->set ( $cacheKey, $result, $expireTime );
			} catch ( Exception $e ) {
			}
		}
		return $result;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Api_Top_Client::logCommunicationError()
	 */
	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt) {
		$logData = array (
				date("Y-m-d H:i:s"),
				$apiName,
				$this->appkey,
				PHP_OS,
				$this->sdkVersion,
				$requestUrl,
				$errorCode,
				str_replace ("\n", "", $responseTxt) 
		);
		Common::log($logData, 'api_error.log');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Api_Top_Client::logBizError()
	 */
	protected function logBizError($resp) {
		$logData = array (date ('Y-m-d H:i:s'),$resp);
		Common::log($logData, 'api_error.log');
	}
	
	/**
	 * get taobao autocode
	 * @param string $redirect_url
	 * @return string
	 */
	public function getAuthUrl($redirect_url) {
		$url = Common::getConfig('apiConfig', 'taobao_oauth_code_url');
		$fields = array(
				'response_type' => 'code',
				'client_id' => Common::getConfig('apiConfig', 'taobao_top_key'),
				'redirect_uri' => $redirect_url,
				'view' => 'wap'
		);
		return $url .'?'. http_build_query($fields);
	}
	
	/**
	 * get taobao auto token
	 * @param string $code
	 * @param string $redirect_url
	 * @return boolean
	 */
	public function getAuthToken($code, $redirect_url) {
		if (!$code || !$redirect_url) return false;
		$url = Common::getConfig('apiConfig', 'taobao_oauth_token_url');
		$postFields = array(
				'grant_type' => 'authorization_code',
				'code' => $code,
				'redirect_uri' => $redirect_url,
				'client_id' => Common::getConfig('apiConfig', 'taobao_top_key'),
				'client_secret' => Common::getConfig('apiConfig', 'taobao_top_secret'),
				'view' => 'wap',
		);
		return parent::curl ( $url, $postFields );
	}
	
	/**
	 * refresh token
	 * @param string $refresh_token
	 * @return boolean
	 */
	public function refreshToken($refresh_token) {
		if (!$refresh_token) return false;
		$url = Common::getConfig('apiConfig', 'taobao_oauth_token_url');
		$postFields = array(
				'grant_type' => 'refresh_token',
				'refresh_token' => $refresh_token,
				'client_id' => Common::getConfig('apiConfig', 'taobao_top_key'),
				'client_secret' => Common::getConfig('apiConfig', 'taobao_top_secret'),
				'view' => 'wap',
		);
		return parent::curl ( $url, $postFields );
	}
	
	/**
	 * Singleton mode, clone is not allowed.
	 */
	public function __clone() {
		trigger_error ( 'Clone is not allowed.', E_USER_ERROR );
	}
}