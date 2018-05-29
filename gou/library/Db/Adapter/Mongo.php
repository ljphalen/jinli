<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * MongoDB Class
 * @author Terry
 */
class Db_Adapter_Mongo {

	static private $mongoSource = array();	//存储MongoDB连接资源
	static private $current_DB = array();	//存储当前操作的数据库和库名
	static private $config = null;			//存储连接MongoDB和需要操作的数据库等配置信息
	static private $key = null;				//存储MongoDB资源标识
	static $retry_times = 2;				//尝试连接MongoDB的次数
	static $try_times = 0;					//已经连接的次数

	/**
	 * 构造MongoDB, 并创建连接
	 * @param string $key
	 * @param array $config
	 */
	public function __construct($key = '', $config = array()){
		self::$key = $key;
		self::$config = $config;
		$this->connect();
	}

	/**
	 * MongoDB连接
	 * @return bool|void
	 */
	public function connect(){
		if (isset(self::$mongoSource[self::$key])) return;
		if (!self::$config) return false;
		try {
			self::$try_times++;
            $account = '';
            if(self::$config['isauth']) $account = sprintf('%s:%s@', self::$config['username'], self::$config['password']);
			$host = sprintf('%s%s/%s', $account, implode(',', self::$config['hosts']), self::$config['db']);
			$options = array(
				//'readPreference'=> MongoClient::RP_SECONDARY_PREFERRED,
				//'slaveOkay'	=> true
			);
			if (isset(self::$config['replicaSet']) && self::$config['replicaSet']) $options['replicaSet'] = self::$config['replicaSet'];
			self::$mongoSource[self::$key] = new MongoClient('mongodb://'.$host, $options);

			//选数据库
			$this->selectDB();
		} catch (MongoConnectionException $e) {
			if (self::$try_times > 1) {
				error_log($e->getMessage());
			}

			// 集群环境出现异常时，需要给予重连的机会,来刷新线程池
			if (self::$try_times < self::$retry_times) return $this->connect(self::$key);
			return false;
		}
	}


	/**
	 * 选择要操作的库
	 */
	public function selectDB(){
		if (isset(self::$current_DB[self::$key]) && self::$current_DB[self::$key]['name'] == self::$config['db']) return;
		if (!isset(self::$mongoSource[self::$key])) return;
		self::$current_DB[self::$key]['db'] = self::$mongoSource[self::$key]->selectDB(self::$config['db']);
		self::$current_DB[self::$key]['name'] = self::$config['db'];
	}

	/**
	 * 判断数据库是否可用
	 * @return bool
	 */
	public function checkDBEnable() {
		return isset(self::$current_DB[self::$key])
		&& (self::$config['db'] ? (self::$current_DB[self::$key]['name'] == self::$config['db'] ? true : false) :true)
			? true : false;
	}

	/**
	 * 获取数据库
	 * @return mixed
	 */
	public function getDB() {
		return self::$current_DB[self::$key]['db'];
	}

    /**
     * 删除当前库
     * @return bool
     */
    public function drop() {
        if (!$this->checkDBEnable()) return false;
        try {
            $this->getDB()->drop();
            return true;
        } catch (MongoCursorException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 创建集合
     * @param string		$collection_name	collection名
     * @param array		    $options	collection属性
     * array(
        'capped' =>,        这个集合是否是固定大小的, 默认为false
        'size' =>,          如果是固定大小的，指定它的大小（字节）
        'max' =>,           如果是固定大小的，指定集合中最多存储多少个字段
        'autoIndexId' =>,   如果 capped 是 TRUE 你可以显式定义为 FALSE 来禁用 自增_id 特性, 默认为true
     * )
     * @return bool
     */
    public function createCollection($collection_name, $options) {
        if (!$this->checkDBEnable()) return false;
        try {
            $this->getDB()->createCollection($collection_name, $options);
            return true;
        } catch (MongoCursorException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * 删除集合
     * @param string		$collection_name	collection名
     * @return bool
     */
    public function dropCollection($collection_name) {
        if (!$this->checkDBEnable()) return false;
        try {
            $this->getDB()->selectCollection($collection_name)->drop();
            return true;
        } catch (MongoCursorException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

	/**
	 * 建立索引
	 * @param string		$collection_name	collection名
	 * @param string/array	$index
	 * 要建索引的字段:
	 * array(
	 * 	'datetime'=> 1,
	 * )
	 * 默认创建顺序索引 1:正序 ,-1:倒序
	 * @param array			$index_params
	 * array(
	 * 	'unique'=> true,		唯一索引
	 * 	'dropDups'=> true,		如果是唯一索引, 而在记录中有重复的该字段, 则删除所有重复的, 仅剩余一个
	 * 	'background' => true,	通常在创建索引的时候, 针对该表的其他操作都需要被中断, 直到索引创建完成后再进行, 如果该参数设置为true, 则无论其他操作是否正在进行, 都会创建索引.
	 * 	'timeout'=> 3600,		创建索引的超时时间
	 * 	'name'=> '',			单个或多个字段的组合索引的名称
	 * 	'safe'=> true			在创建索引后, 如果失败是否抛出异常
	 * )
	 * @return boolean
	 */
	public function createIndex($collection_name, $index, $params = array('background'=> true)) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->createIndex($index, $params);
            return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/**
	 * 删除索引
	 * @param string	$collection_name
	 * @param array		$index	array('index-name' => 1)
	 * @return boolean
	 */
	public function dropIndex($collection_name, $index) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->deleteIndex($index);
			return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/**
	 * 获取collection的索引信息
	 * @param $collection_name
	 * @return bool
	 */
	public function getIndex($collection_name) {
		if (!$this->checkDBEnable()) return false;
		return $this->getDB()->selectCollection($collection_name)->getIndexInfo();
	}


	/**
	 * 插入新数据
	 * @param string $collection_name
	 * @param array $data
	 * array(''=>, ''=>, ...)
	 * @param array $options
	 * array(
	 *	'safe' => true,	//是否返回操作结果, 默认为false
	 * 	'fsync'=> true, //是否直接插入到物理硬盘, 默认为false
	 * )
	 * @return boolean
	 */
	public function insert($collection_name, $data, $options = array()) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->insert($data, $options);
			return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}


	/**
	 * 批量插入新数据
	 * @param string $collection_name
	 * @param array $data
	 * array(array(''=>,...), array(''=>,...), ...)
	 * @param array $options
	 * array(
	 *	'safe' => true,	//是否返回操作结果, 默认为false
	 * 	'fsync'=> true, //是否直接插入到物理硬盘, 默认为false
	 * )
	 * @return boolean
	 */
	public function batchInsert($collection_name, $data, $options = array()) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->batchInsert($data, $options);
			return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/**
	 * 类似mysql replace into ,需要指定 _id
	 * @param string	$collection_name
	 * @param array $data
	 * array(''=>, ''=>, ...)
	 * @param array $options
	 * array(
	 *	'safe' => true,	//是否返回操作结果, 默认为false
	 * 	'fsync'=> true, //是否直接插入到物理硬盘, 默认为false
	 * )
	 * @return boolean
	 */
	public function save($collection_name, $data, $options = array()) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->save($data, $options);
			return true;
		}  catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}


	/**
	 * 删除记录
	 * @param string 	$collection_name
	 * @param array		$condition
	 * array(''=>, ''=>, ...,)
	 * @param array $options
	 * array(
	 *	'safe' => true,	//是否返回操作结果, 默认为false
	 * 	'fsync'=> true, //是否直接插入到物理硬盘, 默认为false
	 * 	'justOne'=> true, //是否只影响一条记录, 默认为false
	 * )
	 * @return boolean
	 */
	public function delete($collection_name, $condition, $options = array('justOne'=> true)) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->remove($condition, $options);
			return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/**
	 * 更新记录
	 * @param string	$collection_name
	 * @param array		$condition
	 * array(''=>, ''=>, ...,)
	 * @param array		$new_data
     * array(''=>, ''=>, ...,)
	 * @param array		$options
	 * array(
	 *	'safe' => true,	//是否返回操作结果, 默认为false
	 * 	'fsync'=> true, //是否直接插入到物理硬盘, 默认为false
	 * 	'upsert'=> true, //是否没有匹配数据就添加一条新的, 默认为false
	 *  'multiple' => true, //是否影响所有符合条件的记录，默认只影响一条
	 * )
	 * @return boolean
	 */
	public function update($collection_name, $condition, $new_data, $options = array('multiple'=> false)) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->update($condition, array('$set'=> $new_data), $options);
			return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}

    /**
     * ???
     * @param $collection_name
     * @param $condition
     * @param $increase
     * @return bool
     */
	public function dataIncrease($collection_name, $condition, $increase) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->update($condition, $increase);
			return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}

	}

    /**
     * ???
     * @param $collection_name
     * @param $condition
     * @param $unset_fields
     * @return bool
     */
	public function unsetFields($collection_name, $condition, $unset_fields) {
		if (!$this->checkDBEnable()) return false;
		try {
			$this->getDB()->selectCollection($collection_name)->update($condition, array('$unset'=> $unset_fields), array('multiple'=> true));
			return true;
		} catch (MongoCursorException $e) {
			error_log($e->getMessage());
			return false;
		}
	}

	/**
	 * 查询
	 * @param string $collection_name
	 * @param array $condition
	 * @param array $result_condition
     * array(
        'start' => ,
        'limit' => ,
        'sort' => ,
     * )
	 * @param array $fields
     * array(
        '_id' => 0,
        'name' => 1,
     * )
	 */
	public function find($collection_name, $query_condition, $result_condition = array(), $fields = array()) {
		if (!$this->checkDBEnable()) return false;
		$result = array();
		try {
			$cursor = $this->getDB()->selectCollection($collection_name)->find($query_condition, $fields);
			if (isset($result_condition['start'])) $cursor->skip(intval($result_condition['start']));
			if (isset($result_condition['limit']) && $result_condition['limit']) $cursor->limit(intval($result_condition['limit']));
			if (isset($result_condition['sort'])) $cursor->sort($result_condition['sort']);
			while ($cursor->hasNext()) $result[] = $cursor->getNext();
		} catch (MongoConnectionException $e) {
			error_log($e->getMessage());
			return false;
		}
		return $result;
	}


	/**
	 * 单条查询
	 * @param string $collection_name
	 * @param array $condition
	 * @param array $fields
	 * @return array|null 如果有结果就返回一个array,如果没有结果就返回NULL
	 */
	public function findOne($collection_name, $condition, $fields = array()) {
		if (!$this->checkDBEnable()) return false;
		return $this->getDB()->selectCollection($collection_name)->findOne($condition, $fields);
	}


	/**
	 * ???
	 * @param array 	$location			array('经度'=>'', '纬度'=> '') 保证顺序!
	 * @param string 	$distance_field
	 * @param float 	$max_distance		最远距离(单位:公里)
	 * @param number 	$start
	 * @param number 	$limit				限制100条
	 */
	public function aggregate($collection_name, $location, $match = '', $start = 0, $limit = 25,
							  $max_distance = 1, $distance_field = 'gps', $sort = '', $dir = '') {
		if (!$this->checkDBEnable()) return false;
		$opt = array();
		$geo = array(
			'$geoNear'=> array(
				'near'				=> $location,
				'distanceField' 	=> $distance_field,
				'distanceMultiplier'=> 6371,
				'spherical'			=> true,
				'num'				=> 1000
			)
		);

		if ($max_distance) $geo['$geoNear']['maxDistance'] = $max_distance/EARTH_RADIUS;
		if ($match) $geo['$geoNear']['query'] = $match;
		$opt[] = $geo;
		if ($sort) {
			if (is_array($sort)) {
				$opt[] = array('$sort'=> $sort);
			} else {
				$opt[] = array('$sort'=> array($sort => ($dir > 0 ? 1 : -1)));
			}
		}
		if ($start) $opt[] = array('$skip'=> intval($start));
		if ($limit) $opt[] = array('$limit' => intval($limit));

		$cl = $this->getDB()->selectCollection($collection_name);
		$result = $cl->aggregate($opt);

		if ($result['ok'] == 0) error_log($result['errmsg']);

		return $result['ok'] == 1 ? $result['result'] : '';
	}

    /**
     * ??
     * @param $collection_name
     * @param array $opt
     * @return mixed
     */
	public function aggregateByOpt($collection_name, $opt = array()) {
		$result = $this->getDB()->selectCollection($collection_name)->aggregate($opt);
		return $result;
	}

    /**
     * ??
     * @param $collection_name
     * @param $key
     * @param $initial
     * @param $reduce
     * @return mixed
     */
	public function group($collection_name, $key, $initial, $reduce) {
		$result = $this->getDB()
			->selectCollection($collection_name)
			->group(array($key=>1), $initial, $reduce);

		return $result;
	}

    /**
     * ????
     * @param $collection_name
     * @param $map
     * @param $reduce
     * @return array
     */
	public function mapReduce($collection_name, $map, $reduce) {
		$command = array(
			'mapreduce' => $collection_name,
			'map'		=> $map,
			'reduce'	=> $reduce,
			'out'		=> array('inline'=> 1),
			'verbose'	=> true
		);
		$command_info = $this->command($command);
		if ($command_info['ok'] == 0) return array();
		return $command_info['results'];
	}

	/**
	 * 命令查询
	 * @param array $command
     * array(
        ''
     * )
	 * @return array
	 */
	public function command($command) {
		if (!$this->checkDBEnable()) return false;
		return $this->getDB()->command($command);
	}

	/**
	 * 获取表数据总量
	 * @param string	$collection_name
	 * @return int
	 */
	public function count($collection_name, $query_condition = array()) {
		if (!$this->checkDBEnable()) return false;
		return $this->getDB()->selectCollection($collection_name)->count($query_condition);
	}

	/**
	 * 获取当前MongoDB资源中所有的数据库
	 * @return mixed
	 */
	public function listDBs() {
		return self::$mongoSource[self::$key]->listDBs();
	}

	/**
	 * 获取当前库的所有表collection
	 * @return array
	 */
	public function listCollections() {
		$collection_objs = $this->getDB()->listCollections();
		$collections = array();
		if($collection_objs) {
			foreach($collection_objs as $c) {
				$collections[] = $c->getName();
			}
		}

		return $collections;
	}

    /**
     * 设置是否启动慢查询
     * @param $level
     * 0: 关闭慢查询
     * 1: 记录查询时间大于$slowms的查询
     * 2: 记录所有查询
     * @param int $slowms 毫秒 最大查询时间
     */
    public function setProfilingLevel($level, $slowms = 1000){
        $this->command(array('profile' => $level, 'slowms' => $slowms));
    }

	/**
	 * 关闭mongo资源
	 */
	public function __destruct() {
		if (self::$current_DB) {
			foreach (self::$current_DB AS $db) {
				if (is_resource($db)) $db->close();
			}
		}
	}
}