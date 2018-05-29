<?php
/**
 * 使用方法：
 *  App::uses('FRedis', 'Vendor');
 *  1. $this->FRedis = FRedis::getSingleton(); //是持久化（默认，以前用的）
 *  2. $this->FRedis = FRedis::getCacheSingleton(); //非持久化
 *  @author eric.cai v1.0
 */
class FRedis {
	private static $_instance = null;
	private static $_CacheInstance = null;
	private static $_CacheLeaderboardInstance = null;

	public $hash = null;
	public $redis = null;
	public $connectPool = null;
	public $counts = 0;
	//含有的服务器
	var $servers = array(
		//例子，添加时时列表格式
		array(
			'host'=>'127.0.0.1',
			'port'=>'9527'
		)
	);

	private function __construct() {
		$this->hash = new Flexihash();
	}

	//singleton:则是持久化
	public static function getSingleton($cacheservername = '') {
		if ( !isset( self::$_instance ) ) {
                        Doo::loadClass("Fredis/Flexihash");                     
			self::$_instance = new FRedis;
                                
		}
		//eric 添加于2011-12-05
		self::$_instance->addServers( true ,$cacheservername);
		return self::$_instance;
	}

	/**
	 * getCacheSingleton : 非持久化实例
	 *
	 * @author eric.caiREDIS_SERVER
	 */
	public static function getCacheSingleton() { 
		if ( !isset( self::$_CacheInstance ) ) {
			//create new instance
                        Doo::loadClass("Fredis/Flexihash");
			self::$_CacheInstance = new FRedis;
                                
		}
		//eric 添加于2011-12-05
		self::$_CacheInstance->addServers( false  );
		return self::$_CacheInstance;
	}
	//根据端口不同写入redis
	public static function getSingletonPort($cacheservername='')
	{
	    $redis = new Redis();
        if(empty($cacheservername)){
            $config = Doo::conf()->CACHE_REDIS_SERVER_DEFAULT;
        }else{
            $config = Doo::conf()->$cacheservername;
        }
        try{
            $connect = $redis->connect($config['host'],$config['port']);
            
            if ($connect!== true ){
                return false;
            }
            
            if(isset($config['db']))
            {
            	$redis->select($config['db']);
            }
			//$connect -> setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
            return $redis;
        }catch (Exception $e){
            return false;
        }
//	    if ( !isset( self::$_CacheLeaderboardInstance ) ) {
//			//create new instance
//			require_once 'flexihash-0.1.9.php';
//			self::$_CacheLeaderboardInstance = new FRedis;
//		}
//		//eric 添加于2011-12-05
//		self::$_CacheLeaderboardInstance->addServers( false , true);
//		return self::$_CacheLeaderboardInstance;
	}

	public function addServers( $isPersistent=true, $cacheservername = '' ) {
		//作缓存数据是否持久化
		if (empty($cacheservername)) {
			if ( $isPersistent ) {
                $this->servers = Doo::conf()->REDIS_SERVER; //读取配置文件上的redisServer
			}else {
				$this->servers = Doo::conf()->CACHE_REDIS_SERVER_DEFAULT ; //读取配置文件上的cacheRedisServer(只当缓存并没有数据持久化)
			}
		} else {
			$this->servers = Doo::conf()->$cacheservername;
		}
		foreach ( $this->servers as $server ) {
			$node = $server['host'] . ':' . $server['port'];
			$this->$node = false;
			$targets[] = $node;
		}
		if ( !$this->hash->getAllTargets() ) {
			$this->hash->addTargets( $targets );
		}
	}
	public function getArray( $key ) {
		$data = array();
		$res_arr = $this->hGetAll( $key );
		if ( is_array( $res_arr ) && count( $res_arr ) > 0 ) {
			$data = $res_arr;
		}
		return $data;
	}

	public function setArray( $key, $value ) {
		if ( is_array( $value ) ) {
			$this->hMset( $key, $value );
		}
	}

	public function delArray( $key, array $fields=null ) {
		if ( empty( $fields ) ) {
			$fields = $this->hKeys( $key );
		}
		foreach ( $fields as $field ) {
			$this->hDel( $key, $field );
		}
	}
	public function showServer() {
		$results = $this->hash->getAllTargets();
		return isset( $results )?$results:array();
	}

    /*
     * 方便调试
     * 根据key获取nodes
     * author eric.cai
     */
    public function getNodesBykey($key) {
        return $this->hash->lookupList( $key, 1 );
    }

	/**
	 * Redis的统一调用,但保证$arguments[0]为KEY
	 *
	 * @param unknown $name
	 * @param unknown $arguments
	 * @author Eric
	 */
	function __call( $name, $arguments ) {
		if ( !isset( $name ) && !isset( $arguments[0] ) && empty( $arguments[0] ) ) {
			return false;
		}
		$nodes = $this->hash->lookupList( $arguments[0], 1 );
		foreach ( $nodes as $node ) {
			if ( !isset( $this->connectPool[$node] ) || empty( $this->connectPool[$node] ) ) {
				$server = explode( ':', $node );
				$this->connectPool[$node] = new Redis();
				$this->connectPool[$node]->connect( $server[0], $server[1] );
			}
			if ( $this->connectPool[$node] ) {
				if ( count( $arguments ) == 1 ) {
					$value = $this->connectPool[$node]->{$name}( $arguments[0] );
				}
				if ( count( $arguments ) == 2 ) {

					$value = $this->connectPool[$node]->{$name}( $arguments[0], $arguments[1] );
				}
				if ( count( $arguments ) == 3 ) {
					$value = $this->connectPool[$node]->{$name}( $arguments[0], $arguments[1], $arguments[2] );
				}
				if ( count( $arguments ) == 4 ) {
					$value = $this->connectPool[$node]->{$name}( $arguments[0], $arguments[1], $arguments[2], $arguments[3] );
				}
				if ( isset( $value ) ) {
					return $value;
				}
			}
		}
		return false;
	}
}