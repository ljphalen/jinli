<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @description 砍价-统计
 * CutController
 *
 * @author Terry
 *
 */
class Cut_StatController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Cut_Stat/index',
        'downloadUrl' => '/Admin/Cut_Stat/download',
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

        $lineData[] = $this->_realCutUvStat();
        $lineData[] = $this->_realCutPvStat();

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
     * 每天砍价真实人数
     */
    private function _realCutUvStat(){
        $stat = array(
            'name' => '参与用户',
            'data' => array()
        );
        $list = Cut_Service_Game::getRealCutUvStat($this->sDate, $this->eDate);
        foreach($list as $item){
            $stat_item = array(intval(strtotime($item['dateline'])) * 1000, intval($item['stat']));
            array_push($stat['data'], $stat_item);
        }
        return $stat;
    }

    /**
     * 每天砍价次数
     */
    private function _realCutPvStat(){
        $stat = array(
            'name' => '参与次数',
            'data' => array()
        );
        $list = Cut_Service_Game::getRealCutPvStat($this->sDate, $this->eDate);
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
        header('Content-Disposition: attachment;filename="cut-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);

        //输出Excel列名信息
        $heads = array(
            '日期',
            '参与用户',
            '参与次数',
        );
        foreach ($heads as $key => $title) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$key] = iconv('utf-8', 'gbk', $title);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        $lineData[] = $this->_reset($this->_realCutUvStat());
        $lineData[] = $this->_reset($this->_realCutPvStat());

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
