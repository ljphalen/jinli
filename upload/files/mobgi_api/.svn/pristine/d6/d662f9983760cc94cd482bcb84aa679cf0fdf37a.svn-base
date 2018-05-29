<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-1-15 17:17:35
 * $Id: push_weight.php 62100 2015-1-15 17:17:35Z hunter.fang $
 */
class PUSH_WEIGHT {

    private $db;

    public function __construct() {
        $this->db = $this->getDb();
    }

    private function getDb() {
        try {
            $dev_config = array('host' => '192.168.0.14', 'port' => 3306, 'dbname' => 'mobgi_api', 'user' => 'stephen', 'pass' => '123456');
            $test_config = array('host' => '10.50.10.12', 'port' => 3306, 'dbname' => 'mobgi_api', 'user' => 'eric', 'pass' => 'XqfX29pXso');
            $sanbox_config = array('host' => '127.0.0.1', 'port' => 3306, 'dbname' => 'mobgi_api', 'user' => 'root', 'pass' => '');
            $prod_config = array('host' => 'db.api.ad.ids.com', 'port' => 3306, 'dbname' => 'mobgi_api', 'user' => 'ad_system', 'pass' => 'wY7DTW6aBXV9ljG_g4sE');
            $cfg = $dev_config;
            //线上版本
            $cfg = $prod_config;
            
            $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']}";
            $dbh = new PDO($dsn, $cfg['user'], $cfg['pass']);
            $dbh->query("SET NAMES 'utf8'");
            return $dbh;
        } catch (PDOException $e) {
            file_put_contents('./db_error_' . date("Y-m-d") . '.log', $dsn . "[Connection failed: " . $e->getMessage() . "]\r\n", FILE_APPEND);
            exit;
        }
    }

    public function set_status($weightId, $state) {
        $weightId = intval($weightId);
        $state = intval($state);
        $update_sql = 'update push_weight set state = ' . $state . ' where id = ' . $weightId;
        return $this->db->query($update_sql);
    }

    public function get_weights() {
        $sql = "select * FROM push_weight WHERE 1=1 and del = 1";
        $result = $this->db->query($sql, PDO::FETCH_ASSOC);
        return $result->fetchAll();
    }

    public function do_work() {
        $weights = $this->get_weights();
        foreach ($weights as $key=>$weight) {
            $now = time();
            if ($weight['start_time'] < $now && $weight['end_time'] > $now) {
                //未生效＝》生效
                if ($weight['state'] != 1 ) {
                    $this->set_status($weight['id'], 1);
                }
            }
            else if($weight['end_time'] < $now){
                //设置过期
                if ($weight['state'] != 3) {
                    $this->set_status($weight['id'], 3);
                }
            }
        }
    }
}

$pushPlanObj = new PUSH_WEIGHT();
$pushPlanObj->do_work();
