<?php
header("Content-type:text/html;charset=utf-8");
include 'config.php';

// 初始化
$sync = new sync ( $config );

// 获取Redis里面的DevId
$cacheData = $sync->getCacheData ();
// 获取后台数据
$dbData = $sync->selectFromBackend ($cacheData);
// 发送短消息
$sync->sendMsg ( $dbData );
class sync {
    /**
     * 日志
     * @var unknown
     */
    private $log;

    /**
     * backend 数据库
     * @var unknown
     */
    private $backend;

    /**
     * mobgi_www数据库
     * @var unknown
     */
    private $www;

    /**
     * Redis
     * @var unknown
     */
    private $redis = null;

    /**
     * 初始化数据
     * @param unknown $config
     */
    public function __construct($config) {
        $this->log = $config ['log'];
        $this->backend = $config ['backend_mysql'];
        $this->www = $config ['www_mysql'];
        if ($this->redis == null){
            $this->redis = new Redis ();
            $connected = $this->redis->connect ( $config ['redis'] [0], $config ['redis'] [1] );
            if (! $connected) {
                file_put_contents ( $this->log, "connect redis fail\r\n", FILE_APPEND );
                return false;
            }
        }
    }

    /**
     * 发送短消息
     * @param unknown $cacheData
     * @param unknown $dbData
     */
    public function sendMsg( $dbData ) {
        if (empty($dbData)){
            file_put_contents ( $this->log, "mysql has no msg send\r\n", FILE_APPEND );
            return false;
        }
        // connect backend mysql
        $www = mysql_connect ( $this->www [0], $this->www [1], $this->www [2], $this->www [3] );
        if (! $www) {
            file_put_contents ( $this->log, "connect www_mysql mysql fail\r\n", FILE_APPEND );
            return false;
        }
        mysql_query("set names utf8");

        foreach ( $dbData as $devId => $msgInfo ) {
            $devIdArr = explode(",", $msgInfo["dev_id"]);
            $values = '';
            foreach ( $devIdArr as $devId ) {
                $values .= "(" . $devId . ", '" . $msgInfo ['msg'] . "', '" . $msgInfo ['title'] . "', 0, 0, '" . date('Y-m-d H:i:s', $msgInfo['senddate']) . "'),";
            }
            try {
                mysql_query ( "INSERT INTO ".$this->www [3].".msg (`dev_id`, `msg`, `title`, `isread`, `type`, `senddate`) VALUES " . trim ( $values, "," ) );
                $this->redis->delete ( 'WWW_MOBGI_SITEMSG_' . $msgInfo ['id'] );
                echo "send msg success";
            } catch ( Exception $e ) {
                file_put_contents ( $this->log, "insert to www_mobgi.msg fail\r\n", FILE_APPEND );
            }
        }
    }

    /**
     * 从Redis里面取出所有没有发送的短信息
     *
     * @param unknown $log
     * @param unknown $config
     * @return boolean multitype:multitype:
     */
    public function getCacheData() {
        
        $keyArr = $this->redis->keys ( "WWW_MOBGI_SITEMSG*" );
        if (empty ( $keyArr )) {
            return false;
        }
        $return = array ();
        foreach ( $keyArr as $key ) {
            $msgId = str_replace("WWW_MOBGI_SITEMSG_", "", $key);
            $return [$msgId] = $this->redis->get ( $key );
        }
        return $return;
    }

    /**
     * 从Backend的DB里面取出MSG信息
     *
     * @param unknown $cacheData
     * @param unknown $config
     * @param unknown $log
     * @return boolean multitype:multitype:
     */
    public function selectFromBackend($cacheData) {
        if (empty($cacheData)){
            file_put_contents ( $this->log, "no msg has send\r\n", FILE_APPEND );
            return false;
        }
        // connect backend mysql
        $backend = mysql_connect ( $this->backend [0], $this->backend [1], $this->backend [2], $this->backend [3] );
        if (! $backend) {
            file_put_contents ( $this->log, "connect backend mysql fail\r\n", FILE_APPEND );
            return false;
        }
        mysql_query("set names utf8");
        //print_r($cacheData);
        $idArr = array_keys($cacheData);
        //print_r($idArr);
        $msgInfo = mysql_query ( "SELECT * FROM ".$this->backend [3].".msg WHERE id in (" . implode(',', $idArr) . ") and senddate<=UNIX_TIMESTAMP(NOW())" );
        if (! $msgInfo ) {
            file_put_contents ( $this->log, "has no data in backend msg table\r\n", FILE_APPEND );
            return false;
        }
        $return = array ();
        
        while ( $row = mysql_fetch_assoc ( $msgInfo ) ) {
            //print_r($row);
            //print_r($cacheData[$row['id']]);
            $row["dev_id"]=$cacheData[$row['id']];
            $return[]=$row;
            //$return [$cacheData[$row['id']]] = $row;
        }
        mysql_close ( $backend );
//        print_r($return);
//        die;
        return $return;
    }
}