<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @description 问答-统计
 * QaController
 *
 * @author Terry
 *
 */
class Qa_StatController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Qa_Stat/index',
        'downloadUrl' => '/Admin/Qa_Stat/download',
    );

    private $sDate = '';
    private $eDate = '';
    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $this->sDate = $this->getInput('sdate');
        $this->eDate = $this->getInput('edate');
        $quick = $this->getInput('quick');
        !$this->sDate && $this->sDate = date('Y-m-d', strtotime("-8 day"));
        !$this->eDate && $this->eDate = date('Y-m-d', strtotime("today"));
        $this->eDate  = date('Y-m-d', strtotime($this->eDate) + 24*60*60);
        if(strtotime($this->eDate) < strtotime($this->sDate)) $this->output(-1, '起始时间不能大于结束时间.');

        $lineData[] = $this->_realCusUvStat();
        $lineData[] = $this->_virQusStat();
        $lineData[] = $this->_realQusStat();
        $lineData[] = $this->_realQusCusUvStat();
        $lineData[] = $this->_realPassQusStat();
        $lineData[] = $this->_realPassQusCusUvStat();
        $lineData[] = $this->_virAusStat();
        $lineData[] = $this->_realAusStat();
        $lineData[] = $this->_realAusCusUvStat();
        $lineData[] = $this->_realPassAusStat();
        $lineData[] = $this->_realPassAusCusUvStat();
        $lineData[] = $this->_skeyEmptyStat();
        $lineData[] = $this->_skeyHasStat();
        $lineData[] = $this->_skeyCountStat();

//        $nickname_stat = $this->_nicknameModifyStat();
//        $avatar_stat = $this->_avatarModifyStat();

//        $this->assign('nickname_stat', $nickname_stat);
//        $this->assign('avatar_stat', $avatar_stat);
        $this->assign('lineData', json_encode($lineData));
        $this->assign('sdate', $this->sDate);
        $this->assign('edate', date('Y-m-d', strtotime($this->eDate) - 24*60*60));
        $this->assign('yesterday', date('Y-m-d', strtotime("-1 day")));
        $this->assign('weekday', date('Y-m-d', strtotime("-8 day")));
        $this->assign('monthday', date('Y-m-d', strtotime("-1 month")));
        $this->assign('threemonth', date('Y-m-d', strtotime('-3 month')));
        $this->assign('quick', $quick);
    }

    /**
     * 每日真实用户数(uv)
     */
    private function _realCusUvStat(){
        $stat = array(
            'name' => '参与用户',
            'data' => array()
        );
        $list_qus = Gou_Service_QaQus::getRealCusUvStat($this->sDate, $this->eDate);
//        array_walk($list_qus, function(&$v, $k){ $v['dateline'] = date('Y-m-d', $v['dateline']);});
        $list_qus = Common::resetKey($list_qus, 'dateline');

        $list_aus = Gou_Service_QaAus::getRealCusUvStat($this->sDate, $this->eDate);
//        array_walk($list_aus, function(&$v, $k){ $v['dateline'] = date('Y-m-d', $v['dateline']);});
        $list_aus = Common::resetKey($list_aus, 'dateline');
        $dateline_qus = array_keys($list_qus);
        $dateline_aus = array_keys($list_aus);
        $dateline = array_unique(array_merge($dateline_qus, $dateline_aus));
        sort($dateline);

        $data = array();
        foreach($dateline as $d){
            $count = 0;
            if(isset($list_qus[$d])) $count += $list_qus[$d]['stat'];
            if(isset($list_aus[$d])) $count += $list_aus[$d]['stat'];
            $data[] = array((strtotime($d) * 1000), $count);
        }
        $stat['data'] = $data;
        return $stat;
    }

    /**
     * 每日虚拟用户的问题数
     */
    private function _virQusStat(){
        $stat = array(
            'name' => '虚拟问题',
            'data' => array()
        );
        $list = Gou_Service_QaQus::getVirQusStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实用户的问题数
     */
    private function _realQusStat(){
        $stat = array(
            'name' => '提问数',
            'data' => array()
        );
        $list = Gou_Service_QaQus::getRealQusStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实问题的参与用户(uv)
     */
    private function _realQusCusUvStat(){
        $stat = array(
            'name' => '提问用户数',
            'data' => array()
        );
        $list = Gou_Service_QaQus::getRealQusCusUvStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实审核过的问题数(uv)
     */
    private function _realPassQusStat(){
        $stat = array(
            'name' => '提问通过数',
            'data' => array()
        );
        $list = Gou_Service_QaQus::getRealPassQusStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实审核过的问题的参与用户(uv)
     */
    private function _realPassQusCusUvStat(){
        $stat = array(
            'name' => '提问通过用户数',
            'data' => array()
        );
        $list = Gou_Service_QaQus::getRealPassQusCusUvStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日虚拟回答数
     */
    private function _virAusStat(){
        $stat = array(
            'name' => '虚拟回答',
            'data' => array()
        );
        $list = Gou_Service_QaAus::getVirAusStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实用户回答数
     */
    private function _realAusStat(){
        $stat = array(
            'name' => '回答数',
            'data' => array()
        );
        $list = Gou_Service_QaAus::getRealAusStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实用户回答数
     */
    private function _realAusCusUvStat(){
        $stat = array(
            'name' => '回答用户数',
            'data' => array()
        );
        $list = Gou_Service_QaAus::getRealAusCusUvStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实审核过的回答数(uv)
     */
    private function _realPassAusStat(){
        $stat = array(
            'name' => '回答通过数',
            'data' => array()
        );
        $list = Gou_Service_QaAus::getRealPassAusStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日真实审核过的回答的参与用户(uv)
     */
    private function _realPassAusCusUvStat(){
        $stat = array(
            'name' => '回答通过用户数',
            'data' => array()
        );
        $list = Gou_Service_QaAus::getRealPassAusCusUvStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 昵称修改总数
     */
    private function _nicknameModifyStat(){
        $stat = array(
            'name' => '修改昵称数',
            'stat' => 0
        );
        $stat['stat'] = User_Service_Uid::count(array('nickname' => array('!=', '')));
        return $stat;
    }

    /**
     * 头像修改总数
     */
    private function _avatarModifyStat(){
        $stat = array(
            'name' => '上传头像数',
            'stat' => 0
        );
        $stat['stat'] = User_Service_Uid::count(array('avatar' => array('!=', '')));
        return $stat;
    }

    /**
     * 每日关键词搜索结果为空的统计
     */
    private function _skeyEmptyStat(){
        $stat = array(
            'name' => '搜索数[无结果]',
            'data' => array()
        );
        $list = Gou_Service_QaSkey::getEmptyStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日关键词搜索结果不为空的统计
     */
    private function _skeyHasStat(){
        $stat = array(
            'name' => '搜索数[有结果]',
            'data' => array()
        );
        $list = Gou_Service_QaSkey::getHasStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每日关键词搜索结果统计
     */
    private function _skeyCountStat(){
        $stat = array(
            'name' => '搜索关键词',
            'data' => array()
        );
        $list = Gou_Service_QaSkey::getCountStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 下载
     * @return string
     */
    public function downloadAction(){
        $this->sDate = $this->getInput('sdate');
        $this->eDate = $this->getInput('edate');
        !$this->sDate && $this->sDate = date('Y-m-d', strtotime("-8 day"));
        !$this->eDate && $this->eDate = date('Y-m-d', strtotime("today"));
        $this->eDate = date('Y-m-d', strtotime($this->eDate) + 24*60*60);
        if(strtotime($this->eDate) < strtotime($this->sDate)) $this->output(-1, '起始时间不能大于结束时间.');

        $sDate = strtotime($this->sDate);
        $eDate = strtotime($this->eDate);

        $date = array(intval($sDate) * 1000);
        while($sDate + (24*60*60) < $eDate){
            $sDate = $sDate + (24*60*60);
            $date[] = intval($sDate) * 1000;
        }
        $date[] = intval($eDate) * 1000;

        header('Content-Encoding: none');
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="qa-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);

        //输出Excel列名信息
        $heads = array(
            '日期',
            '参与用户',
            '虚拟问题',
            '提问数',
            '提问用户数',
            '提问通过数',
            '提问通过用户数',
            '虚拟回答',
            '回答数',
            '回答用户数',
            '回答通过数',
            '回答通过用户数',
            '搜索数[无结果]',
            '搜索数[有结果]',
            '搜索关键词'
        );
        foreach ($heads as $key => $title) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$key] = iconv('utf-8', 'gbk', $title);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        $lineData[] = $this->_reset($this->_realCusUvStat());
        $lineData[] = $this->_reset($this->_virQusStat());
        $lineData[] = $this->_reset($this->_realQusStat());
        $lineData[] = $this->_reset($this->_realQusCusUvStat());
        $lineData[] = $this->_reset($this->_realPassQusStat());
        $lineData[] = $this->_reset($this->_realPassQusCusUvStat());
        $lineData[] = $this->_reset($this->_virAusStat());
        $lineData[] = $this->_reset($this->_realAusStat());
        $lineData[] = $this->_reset($this->_realAusCusUvStat());
        $lineData[] = $this->_reset($this->_realPassAusStat());
        $lineData[] = $this->_reset($this->_realPassAusCusUvStat());
        $lineData[] = $this->_reset($this->_skeyEmptyStat());
        $lineData[] = $this->_reset($this->_skeyHasStat());
        $lineData[] = $this->_reset($this->_skeyCountStat());

        foreach($date as $d){
            $row = array(iconv('utf-8', 'gbk', date('Y-m-d', substr($d, 0, strlen($d)-3))));
            foreach($lineData as $type){
                if(array_key_exists(strval($d), $type)){
                    $row[] = iconv('utf-8', 'gbk', $type[strval($d)]);
                }else{
                    $row[] = iconv('utf-8', 'gbk', 0);
                }
            }
            fputcsv($fp, $row);
        }

        ob_flush();
        flush();
        exit;
    }

    private function _reset($stat){
        $data = $stat['data'];
        $rs = array();
        foreach($data as $item){
            $rs[strval($item[0])] = $item[1];
        }
        unset($data);
        return $rs;
    }

}
