<?php
/**
 * 分表分库算法类
 *
 * @author Intril
 *
 */
class MysqlShard {

    /**
     * 返回错误信息
     * Enter description here ...
     * @var unknown_type
     */
    private static $_statusCode = array(
        0=>'成功',
        1=>'初始化库名、表名不能为空',
        2=>'参数有误, 必需是数字（player_id）',
        3=>'还没创建表链接或没取得链接表的信息',
        4=>'数据切分配置有误',
    ); 

    /**
     * 计算返回分表分库的DB信息和表信息
     *
     * @param Integer $shardId 分库分表ID
     * @param string $dbName 数据库名前缀
     * @param string $tblName 表名前缀	为空时，取数据库前缀
     */
    public static function init($shardId, $dbPrefix){
        $hostInfo = self::getDataBaseId($shardId, $dbPrefix);
        $tblId = self::getTableId($shardId, $dbPrefix);
        $sdConfig =  array(
        	'host' => array(
                $hostInfo['host'],
                $hostInfo['database'],
                $hostInfo['login'],
                $hostInfo['password'],
                'mysql',
                true,
                'charset'=>$hostInfo['encoding']
            ),
        	'table' => $tblId,
        );
        return $sdConfig;
    }

    /**
     * 根据pid取MOD获取存的databases
     * @param $pid
     * @return array
     * @author eric.cai
     */
    private static function getDataBaseId($shardId, $dbPrefix) {
        // 获取分表分库配置
        $host = Doo::conf()->shardHost;
        $config = Doo::conf()->shardConfig;
		$hosts = $host[$dbPrefix];
		$databases = $config[$dbPrefix]['databases'];
        if(!is_array($databases)) {
            return array();
        }
        $dbId = self::getModByNumber($shardId,end($databases));
        foreach($hosts as $key => $value){
            list($s, $e) = explode('-', $key);
            if($dbId >= (int)$s && $dbId <= (int)$e) {
                $host = $value;
                $host['database'] = $dbPrefix. '_' .$dbId;
                return $host;
            }
        }
        return array();
    }

    /**
     * 根据pid取MOD获取存的tables
     * @param $pid
     * @return array
     * @author eric.cai
     */
    private static function getTableId($shardId, $dbPrefix) {
        $config = Doo::conf()->shardConfig;
        $tables = $config[$dbPrefix]['tables'];
        if( is_array($tables) && count($tables) == 2 ) {
            $tblId = self::getModByNumber(intval($shardId/current($tables)),end($tables));
            return $dbPrefix. '_' .$tblId;
        }
        return '';
    }

    /**
     * 返回取模的值
     *
     * @param Int $number
     * @param Int $s
     * @return Int Id
     */
    private static function getModByNumber($number,$s) {
        return intval($number % $s);
    }
}
