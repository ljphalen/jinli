<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LocalstatController extends Admin_BaseController {

    public $columnType = array(
        'column'       => '栏目访问总和',
        'column_page'  => '-栏目上滑加载',
        'column_last'  => '-栏目下滑加载',
        'detail_total' => '内容访问总和',
        'detail'       => '-列表内容访问',
        'detail_rec'   => '-推荐内容访问',
        'detail_h5'    => '-H5内容访问',
        'detail_card'  => '-卡片内容访问',
        'to'           => '查看原文',
        'back_index'   => '返回首页',
        //'more_index'  => '更多热点',
        'ad_pos'       => '广告点击',

    );

    public $searchType = array(
        'navnews_pv' => 'PV',
        'navnews_uv' => 'UV',
    );

    /**
     * 二级页面统计
     */
    public function subpageAction() {
        $postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'column_type', 'group', 'export'));
        !$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
        !$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
        $page                    = max($postData['page'], 1);
        $postData['group']       = $postData['group'] ? $postData['group'] : 'news';
        $postData['column_type'] = $postData['column_type'] ? $postData['column_type'] : 'column';
        $postData['search_type'] = $postData['search_type'] ? $postData['search_type'] : 'navnews_pv';
        $sdate                   = date('Ymd', strtotime($postData['sdate']));
        $edate                   = date('Ymd', strtotime($postData['edate']));
        $result                  = array();
        $dateList                = $ret = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        //每个频道切换PV/UV
        $columnList = Nav_Service_NewsDB::getColumnDao()->getsBy(array('group' => $postData['group']), array('id' => 'ASC'));
        $columnData = $where = $temp = array();
        foreach ($columnList as $k => $v) {
            $columnData['id'][]           = $v['id'];
            $columnData['name'][$v['id']] = $v['title'];
            foreach ($dateList as $s => $t) {
                $ret[0][$t]['column_data']        = 0;
                $ret[$v['id']][$t]['column_data'] = 0;
                $ret[$v['id']]['name']            = $v['title'];
            }
        }

        if ($postData['column_type'] == 'detail_total') {
            $vers = array(
                'detail'      => '列表内容访问',
                'detail_rec'  => '推荐内容访问',
                'detail_h5'   => 'H5内容访问',
                'detail_card' => '卡片内容访问',
            );

            $where['ver']  = array('IN', array_keys($vers));
            $where['date'] = array(array('>=', $sdate), array('<=', $edate));
            $where['type'] = $postData['search_type'];

            $datas = Gionee_Service_Log::getListByWhere($where);
            $ret   = array();
            foreach ($datas as $val) {
                $ret[$val['key']][$val['date']][$val['ver']] = $val['val'];
            }

            if ($postData['export']) {
                $_data = array(
                    'dateList'   => $dateList,
                    'list'       => $ret,
                    'vers'       => $vers,
                    'columnData' => $columnData,
                );

                $fn = $this->columnType[$postData['column_type']];
                $this->_exportTotal($_data, "二级页面{$fn}统计数据", $sdate, $edate);
                exit();
            }

            $this->assign('vers', $vers);
            $this->assign('columnData', $columnData);
            $this->assign('dateList', $dateList);
            $this->assign('list', $ret);
            $this->assign('params', $postData);
            $this->assign('columnType', $this->columnType);
            $this->assign('searchType', $this->searchType);
            $this->display('sumtotal');
            exit;
        } elseif ($postData['column_type'] == 'column') {
            $vers = array(
                'column'      => '栏目列表访问',
                'column_page' => '上滑加载',
                'column_last' => '下滑加载',
            );

            $where['ver']  = array('IN', array_keys($vers));
            $where['date'] = array(array('>=', $sdate), array('<=', $edate));
            $where['type'] = $postData['search_type'];

            $datas = Gionee_Service_Log::getListByWhere($where);
            $ret   = array();
            foreach ($datas as $val) {
                $ret[$val['key']][$val['date']][$val['ver']] = $val['val'];
            }

            if ($postData['export']) {
                $_data = array(
                    'dateList'   => $dateList,
                    'list'       => $ret,
                    'vers'       => $vers,
                    'columnData' => $columnData,
                );
                $fn    = $this->columnType[$postData['column_type']];
                $this->_exportTotal($_data, "二级页面{$fn}统计数据", $sdate, $edate);
                exit();
            }

            $this->assign('vers', $vers);
            $this->assign('columnData', $columnData);
            $this->assign('dateList', $dateList);
            $this->assign('list', $ret);
            $this->assign('params', $postData);
            $this->assign('columnType', $this->columnType);
            $this->assign('searchType', $this->searchType);
            $this->display('sumtotal');
            exit;
        } else {
            //首页的PV/UV值
            $navNewsPvUv = Gionee_Service_Log::getPvUvStatByKey($postData['search_type'], 'index', $sdate, $edate);

            $ret[0]['name'] = '首页总次数';
            if (!empty($postData['search_type'])) {
                $where['type'] = $postData['search_type'];
            }

            if ($postData['column_type'] == 'ad_pos') {
                $posArr = Nav_Service_NewsAd::getNewsPos();
                $ret    = array();
                foreach ($posArr as $k => $name) {
                    $ret[$k]['name'] = $name;
                }
                $where['type'] = $where['type'] == 'navnews_pv' ? 'pv' : 'uv';

                $posIds       = array_keys($posArr);
                $where['key'] = array('IN', $posIds);
            } else {
                $where['key'] = array('IN', $columnData['id']);
            }
            $where['date'] = array(array('>=', $sdate), array('<=', $edate));
            $datas         = $this->_getData($where, $postData['column_type']);
            foreach ($datas as $k => $v) {
                foreach ($dateList as $s => $t) {
                    $ret[0][$t]['column_data']  = $navNewsPvUv[date('Y-m-d', strtotime($t))] ? $navNewsPvUv[date('Y-m-d', strtotime($t))] : 0;
                    $ret[$k][$t]['column_data'] = $v[$t][$postData['column_type']] ? $v[$t][$postData['column_type']] : 0;
                }
            }
            if ($postData['column_type'] == 'ad_pos') {
                unset($ret[0]);
            }
            if ($postData['export']) {
                $this->_export('subpage', $ret, "二级页面{$this->searchType[$postData['search_type']]}统计数据", $sdate, $edate);
                exit();
            }


        }


        $this->assign('dateList', $dateList);
        $this->assign('list', $ret);
        $this->assign('params', $postData);
        $this->assign('columnType', $this->columnType);
        $this->assign('searchType', $this->searchType);
    }

    private function _getData($where, $columnType) {
        $moreData = array();
        if ($columnType == 'column' && stristr($where['type'], 'pv')) {
            $moreData     = $this->_moreClickPvData($where['date'], $where['type']);
            $where['ver'] = array('IN', array('column', 'back_index'));
        } else {
            $where['ver'] = $columnType;
        }
        $rawData    = Gionee_Service_Log::getsBy($where, array('date' => 'DESC'));
        $formatData = $result = array();
        foreach ($rawData as $k => $v) {
            $formatData[$v['key']][$v['date']][$v['ver']] = $v['val'];
        }
        $i = 0;
        foreach ($formatData as $m => $n) {
            foreach ($n as $s => $t) {
                if ($columnType == 'column') {
                    $temp = $t['column'];
                    if ($t['column'] >= $t['back_index']) {
                        $temp = $t['column'] - $t['back_index'];
                    }
                    $more = 0;
                    if ($i == 0) {
                        $more = $moreData[$s] ? $moreData[$s] : 0;
                    }
                    $result[$m][$s][$columnType] = $temp - $more;//头条时才扣掉从"看更多"的点击点
                } else {
                    $result[$m][$s][$columnType] = $t[$columnType] ? $t[$columnType] : 0;
                }
            }
            $i++;
        }
        return $result;
    }

    private function _moreClickPvData($date, $searchType) {
        $where         = array();
        $expData       = explode('_', $searchType);
        $where['date'] = $date;
        $where['type'] = 'localnav_' . $expData[1];
        $where['key']  = 'view_more';
        $data          = Gionee_Service_Log::getsBy($where);
        $temp          = array();
        foreach ($data as $k => $v) {
            $temp[$v['date']] = $v['val'];
        }
        return $temp;
    }

    /**
     * 频管管理页统计
     */
    public function channelAction() {

    }

    /**
     * 详情页统计
     */
    public function detailAction() {


    }


    private function _export($type, $data, $title, $sdate, $edate) {
        ini_set('memory_limit', '1024M');
        header('Content-Type: application/vnd.ms-excel;charset=GB2312');
        $filename = $title . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
        $out = fopen('php://output', 'w');
        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        $titles = $dateList = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }
        $titles = $dateList;
        array_unshift($titles, '名称');
        array_push($titles, '总计');
        fputcsv($out, $titles);
        foreach ($data as $m => $n) {
            $sum    = 0;
            $temp   = array();
            $temp[] = $n['name'];
            foreach ($dateList as $k => $v) {
                $temp[] = $n[$v]['column_data'];
                $sum += $n[$v]['column_data'];
            }
            $temp[] = $sum;
            fputcsv($out, $temp);
        }
    }

    private function _exportTotal($data, $title, $sdate, $edate) {
        ini_set('memory_limit', '1024M');
        header('Content-Type: application/vnd.ms-excel;charset=GB2312');
        $filename = $title . '-' . $sdate . '-' . $edate . '.csv';
        header('Content-Type: text/csv');
        $filename = iconv('utf8', 'gb2312', $filename);
        header('Content-Disposition: attachment;filename=' . $filename);
        $out = fopen('php://output', 'w');
        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        $dateList = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $_data = $data;

        $vers        = $_data['vers'];
        $columnData  = $_data['columnData'];
        $tempHeaders = array('名称', '');
        foreach ($_data['dateList'] as $v) {
            $tempHeaders[] = date('Y-m-d', strtotime($v));
        }
        $tempHeaders[] = '总计';
        fputcsv($out, $tempHeaders);
        $total = array();

        foreach ($columnData['name'] as $id => $name) {
            foreach ($vers as $k => $txt) {
                $sumTotal = 0;
                $_t       = array($name, $txt);
                foreach ($dateList as $d) {
                    $num = intval($_data['list'][$id][$d][$k]);
                    $sumTotal += $num;
                    $total[$d][] = $num;
                    $_t[]        = $num;
                }
                $_t[] = $sumTotal;
                fputcsv($out, $_t);
            }
            $_t = array($name, '总计');
            $st = 0;
            foreach ($dateList as $d) {
                $num = intval(array_sum($_data['list'][$id][$d]));
                $st += $num;
                $_t[] = $num;
            }
            $_t[] = $st;
            fputcsv($out, $_t);
        }

        $footer = array('所有', '总计');
        $n      = 0;
        foreach ($dateList as $d) {
            $sd = array_sum($total[$d]);
            $n += $sd;
            $footer[] = $sd;
        }
        $footer[] = $n;

        fputcsv($out, $footer);
    }


    public function statallAction() {
        $vers = array(
            'all_h5nav'    => 'H5导航',
            'all_localnav' => '本地化',
            'all_nav_news' => '新闻二级页面',
            'all_nav_fun'  => '段子二级页面',
            'all_nav_pic'  => '美图二级页面',
        );

        $postData = $this->getInput(array('sdate', 'edate', 'page', 'search_type', 'export', 'is_reload'));
        !$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
        !$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
        $sdate    = date('Ymd', strtotime($postData['sdate']));
        $edate    = date('Ymd', strtotime($postData['edate']));
        $dateList = $ret = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $tmpDate    = date('Ymd', $i);
            $dateList[] = $tmpDate;

            if ($postData['is_reload']) {
                $dataType = $postData['search_type'] == 'uv' ? 1 : 0;
                foreach ($vers as $key => $name) {
                    $out = Gionee_Service_LocalNavList::run_all_stat_data($tmpDate, $key, $dataType);
                    echo $name . ':' . Common::jsonEncode($out) . "\n";
                }
            }

        }

        $searchType = array(
            'pv' => 'PV',
            'uv' => 'UV',
        );


        $where          = array();
        $where['ver']   = 'stat_all_data';
        $where['key']   = array_keys($vers);
        $where['stime'] = strtotime($sdate);
        $where['etime'] = strtotime($edate);
        $where['type']  = empty($postData['search_type']) ? 'pv' : $postData['search_type'];

        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']] = $val['val'];
        }

        if ($postData['export']) {
            $_data = array(
                'dateList' => $dateList,
                'list'     => $ret,
                'vers'     => $vers,
            );
            $this->_exportStatTotal($_data, "内容总点击报表", $sdate, $edate);
            exit();
        }

        $this->assign('vers', $vers);
        $this->assign('dateList', $dateList);
        $this->assign('list', $ret);
        $this->assign('params', $postData);
        $this->assign('searchType', $searchType);
    }


    private function _exportStatTotal($data, $title, $sdate, $edate) {
        ini_set('memory_limit', '1024M');
        header('Content-Type: application/vnd.ms-excel;charset=GB2312');
        $filename = $title . '-' . $sdate . '-' . $edate . '.csv';
        header('Content-Type: text/csv');
        $filename = iconv('utf8', 'gb2312', $filename);
        header('Content-Disposition: attachment;filename=' . $filename);
        $out = fopen('php://output', 'w');
        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        $dateList = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $_data = $data;

        $vers        = $_data['vers'];
        $tempHeaders = array('名称');
        foreach ($_data['dateList'] as $v) {
            $tempHeaders[] = date('Y-m-d', strtotime($v));
        }
        $tempHeaders[] = '总计';
        fputcsv($out, $tempHeaders);
        $total = array();

        foreach ($vers as $k => $txt) {
            $sumTotal = 0;
            $_t       = array($txt);
            foreach ($dateList as $d) {
                $num = intval($_data['list'][$k][$d]);
                $sumTotal += $num;
                $total[$d][] = $num;
                $_t[]        = $num;
            }
            $_t[] = $sumTotal;
            fputcsv($out, $_t);
        }


        $footer = array('总计');
        $n      = 0;
        foreach ($dateList as $d) {
            $sd = array_sum($total[$d]);
            $n += $sd;
            $footer[] = $sd;
        }
        $footer[] = $n;

        fputcsv($out, $footer);
    }
}