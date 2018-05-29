<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class StatController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Stat/pv',
        'monkeytime'    => '/Admin/Stat/monkeytime',
    );

    public $perpage = 20;

    /**
     *
     * Enter description here ...
     */
    public function pvAction() {
        $sDate = $this->getInput('sdate');
        $eDate = $this->getInput('edate');
        $quick = $this->getInput('quick');
        !$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
        !$eDate && $eDate = date('Y-m-d', strtotime("today"));

        //pv
        list($list, $lineData) = Game_Service_Stat::getPvLineData($sDate, $eDate);

        $this->assign('list', $list);
        $this->assign('lineData', json_encode($lineData));
        $this->assign('sdate', $sDate);
        $this->assign('edate', $eDate);
        $this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
        $this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
        $this->assign('monthday', date('Y-m-d', strtotime("-1 month")));
        $this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
        $this->assign('quick', $quick);
    }

    /**
     *
     * Enter description here ...
     */
    public function uvAction() {
        $sDate = $this->getInput('sdate');
        $eDate = $this->getInput('edate');
        $quick = $this->getInput('quick');
        !$sDate && $sDate = date('Y-m-d', strtotime("-8 day"));
        !$eDate && $eDate = date('Y-m-d', strtotime("today"));

        //ip
        list($list, $lineData) = Game_Service_Stat::getUvLineData($sDate, $eDate);

        $this->assign('list', $list);
        $this->assign('lineData', json_encode($lineData));
        $this->assign('sdate', $sDate);
        $this->assign('edate', $eDate);
        $this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
        $this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
        $this->assign('monthday', date('Y-m-d', strtotime("-2 month")));
        $this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
        $this->assign('quick', $quick);
    }

    public function monkeynumAction() {
        $date = $this->getInput('date');
        if (empty($date)) {
            $date = date('Ymd');
        }
        $list = Cache_Factory::getCache()->hGetAll('MON_KEY_NUM:'.$date);
        arsort($list);
        $tmp = array();
        foreach($list as $name => $num) {
            $t1 = Cache_Factory::getCache()->hGetAll('MON_KEY_TIME:'.$date.':'.$name);
            krsort($t1);
            $a1 = array_slice($t1, 0, 5, true);
            $s1 = array();
            foreach($a1 as $ak => $av) {
                $s1[] = "[{$ak}=>{$av}]";
            }
            $tmp[$name] = array(
                    'num' => $num,
                    'time' => implode(' ',$s1),
            );
        }
        $this->assign('date', $date);
        $this->assign('list', $tmp);
    }

    public function monkeytimeAction() {
        $date = $this->getInput('date');
        $name = $this->getInput('name');
        $num = Cache_Factory::getCache()->hGet('MON_KEY_NUM:'.$date, $name);
        $list = Cache_Factory::getCache()->hGetAll('MON_KEY_TIME:'.$date.':'.$name);
        krsort($list);
        $quickRspCount = 0;
        foreach($list as $duration => $count) {
            if ($duration <= 0.2) {
                $quickRspCount += $count;
            }
        }
        $this->assign('num', $num);
        $this->assign('quickRspCount', $quickRspCount);
        $this->assign('list', $list);
        $this->assign('date', $date);
        $this->assign('name', $name);
    }
}
