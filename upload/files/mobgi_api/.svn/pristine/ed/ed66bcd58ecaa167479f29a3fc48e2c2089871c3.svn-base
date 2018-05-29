<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-1-15 17:17:35
 * $Id: push_plan.php 62100 2015-1-15 17:17:35Z hunter.fang $
 */
class PUSH_PLAN {

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

    public function set_status($planId, $status) {
        $planId = intval($planId);
        $status = intval($status);
        $update_sql = 'update push_plan set runstatus = ' . $status . ' where id = ' . $planId;
        return $this->db->query($update_sql);
    }

    public function get_plans() {
        $sql = "select * FROM push_plan WHERE 1=1 and del = 1";
        $result = $this->db->query($sql, PDO::FETCH_ASSOC);
        return $result->fetchAll();
    }

    public function do_work() {
        $plans = $this->get_plans();
        foreach ($plans as $key=>$plan) {
            $now = time();
            if ($plan['start_time'] < $now && $plan['end_time'] > $now) {
                //一次性导量
                if ($plan['go_method'] == 1) {
                    if ($plan['runstatus'] == 1) {
                        $this->set_status($plan['id'], 2);
                    }
                }
                //周期性导量
                else if ($plan['go_method'] == 2) {
                    $hour = date("H");
                    $min = date("i");
                    $sec = date("s");
                    $start_h = date("H",$plan["start_time"]);
                    $start_m = date("i",$plan["start_time"]);
                    $start_s = date("s",$plan["start_time"]);
                    $end_h = date("H", $plan["end_time"]);
                    $end_m = date("i", $plan["end_time"]);
                    $end_s = date("s", $plan["end_time"]);
                    $datenow = strtotime(date("Y-m-d"));
                    if ((strtotime(date("Y-m-d", $plan["start_time"])) <= $datenow) && ($datenow <= strtotime(date("Y-m-d", $plan["end_time"])))) {
                        if ($hour >= $start_h && $hour < $end_h) {
                            $this->set_status($plan['id'], 2);
                        } else if ($hour == $end_h) {
                            if ($min >= $start_m && $min < $end_m) {
                                $this->set_status($plan['id'], 2);
                            } else if ($min == $end_m) {
                                if ($start_s <= $sec && $sec <= $end_s){
                                    $this->set_status($plan['id'], 2);
                                }
                                else {
                                    $this->set_status($plan['id'], 1);
                                }
                            } else {
                                $this->set_status($plan['id'], 1);
                            }
                        } else {
                            $this->set_status($plan['id'], 1);
                        }
                    } else {
                        if ($plan["runstatus"] == 2) {
                            $this->set_status($plan['id'], 1);
                        }
                    }
                } else {
                    //数据异常
                }
            }
            //一次性导量与周期性导量，状态设置成导量完毕
            else if($plan['end_time'] < $now){
                if ($plan['runstatus'] == 1 || $plan['runstatus'] == 2) {
                    $this->set_status($plan['id'], 3);
                }
            }
        }
    }
}

$pushPlanObj = new PUSH_PLAN();
$pushPlanObj->do_work();
