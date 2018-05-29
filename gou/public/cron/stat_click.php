<?php
include 'common.php';
/**
 *
 * Class statisticClick
 */
//  */30 0 * * 4 /usr/local/php.5.4.13/bin/php /data/www/gou/public/cron/stat_click.php

final class statisticClick{

    private $start_time;            //统计开始时间
    private $end_time;              //统计结束时间
    private $versions = array();    //统计的版本号
    private $savePath = '';         //统计结果生成的文档的保存路径
    private $collections = array(); //mongoDB中gou库中的所有表名
    private $clickEvents = array(
        'a' => array(
            'label' => '[首页汇总]',
            'list' => array(
                'a1' => '页面开启次数',
                'a2' => '从其他页面返回',
                'a3' => '页面刷新',
            ),
        ),
        'b' => array(
            'label' => '[顶部栏汇总]',
            'list' => array(
                'b1' => '搜索点击',
                'b2' => '二维码点击',
            ),
        ),
        'c' => array(
            'label' => '[Banner汇总]',
            'list' => array(
                'c1' => 'Banner1',
                'c2' => 'Banner2',
                'c3' => 'Banner3',
                'c4' => 'Banner4',
                'c5' => 'Banner5',
            ),
        ),
        'd' => array(
            'label' => '[便民服务汇总]',
            'list' => array(
                'd1' => '便民入口1',
                'd2' => '便民入口2',
                'd3' => '便民入口3',
                'd4' => '便民入口4',
                'd5' => '便民入口5',
            ),
        ),
        'e' => array(
            'label' => '[营销位汇总]',
            'list' => array(
                'e1' => '营销位1',
                'e2' => '营销位2',
                'e3' => '营销位3',
                'e4' => '营销位4',
            ),
        ),
        'f' => array(
            'label' => '[小惠精选汇总]',
            'list' => array(
                'f1' => '小惠精选1',
                'f2' => '小惠精选2',
                'f3' => '小惠精选3',
                'f4' => '小惠精选4',
                'f5' => '小惠精选5',
                'f6' => '小惠精选6',
                'f7' => '小惠精选7',
                'f8' => '小惠精选8',
                'f9' => '小惠精选9',
//                'f10' => '小惠精选10',
//                'f11' => '小惠精选11',
//                'f12' => '小惠精选12',
//                'f13' => '小惠精选13',
//                'f14' => '小惠精选14',
//                'f15' => '小惠精选15',
            ),
        ),
        'g' => array(
            'label' => '[我的关注汇总]',
            'list' => array(
                'g1' => '我的关注1',
                'g2' => '我的关注2',
                'g3' => '我的关注3',
                'g4' => '我的关注4',
                'g5' => '我的关注5',
                'g6' => '我的关注6',
                'g7' => '我的关注7',
                'g8' => '我的关注8',
                'g9' => '我的关注9',
            ),
        ),
        'h' => array(
            'label' => '[底部Tab汇总]',
            'list' => array(
                'h1' => '大厅tab',
                'h2' => '分类tab',
                'h3' => '知物tab',
                'h4' => '我的tab',
                'h5' => '活动tab',
            ),
        ),
        'i' => array(
            'label' => '[首页跳出汇总]',
            'list' => array(
                'i1' => '首页跳出',
            ),
        ),
    );


    public function __construct(){
        //统计从昨天算起的前7天
        $this->start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
        $this->end_time = strtotime(date('Y-m-d', strtotime('-1 days')));
        $this->versions = explode(',', Gou_Service_ConfigTxt::getValue('gou_stat_click_version_txt'));
        array_walk($this->versions, function(&$v){$v = trim($v);});
        $this->savePath = Common::getConfig('siteConfig', 'statisticPath');
        $this->collections = Common::getMongo()->listCollections();
    }
    /**
     * 创建文档表名
     * @param $time
     * @return string
     */
    private function _createCollectionName($time){
        return sprintf('gou_apk_statistic_%s', date('Y_m_d', $time));
    }

    /**
     * 点击计算
     * @param array $rs
     * @param array $data
     */
    private function _statistic($rs = array(), &$data){
        foreach($rs as $key => $item){
            //记录版本号
            if(!isset($data[$item['version']])) $data[$item['version']] = array();

            //记录该版本号下的区块的uv
            $item_keys = array_keys($item);
            unset($item_keys['version']);
            array_walk($item_keys, function(&$v){$v = substr($v, 0, 1);});
            $item_keys = array_unique($item_keys);
            foreach($item_keys as $type){
                $data[$item['version']]['blocks_uv'][$type]++;
            }
            unset($item_keys);

            //记录该本版号下的uv
            $data[$item['version']]['total_uv'] ++;
            foreach($item as $ikey => $val){
                if($ikey == 'version') continue;
                //记录该本版号下的各个点的pv
                $data[$item['version']]['events'][$ikey]['pv'] += $val;
                //记录该本版号下的各个点的uv
                $data[$item['version']]['events'][$ikey]['uv'] ++;
                //记录该版本下的区块的pv
                $type = substr($ikey, 0, 1);
                $data[$item['version']]['blocks_pv'][$type] += $val;
                //记录该本版号下的pv(a块和其他块分开计算总量)
                if($type == 'a'){
                    $data[$item['version']]['total_a_pv'] += $val;
                }else{
                    $data[$item['version']]['total_pv'] += $val;
                }
            }
        }

        unset($rs);
        foreach($data as &$item){
            ksort($item, SORT_STRING);
            ksort($item['events'], SORT_STRING);
        }
    }

    /**
     * 生成统计报表
     * @return string
     */
    private function _createStat($fp, $start_time, $end_time, $version){
        $conditon = array('start_time' => $start_time, 'end_time' => $end_time, 'version' => $version);
        $query_condition = array();
        if(!empty($conditon['start_time']) && !empty($conditon['end_time'])){
            //最大时间差为7天
            if(($conditon['end_time'] - $conditon['start_time']) > (86400 * 7)) return;
        }else{
            //如果不选择时间段, 默认为当天
            $conditon['start_time'] = strtotime(date('Y-m-d', Common::getTime()));
            $conditon['end_time'] = strtotime(date('Y-m-d', Common::getTime())) + 86400;
        }
        $query_condition = array('time' => array( '$gt' => $conditon['start_time'], '$lt' => $conditon['end_time']));

        $conditon['version'] = str_replace('.', '', $conditon['version']);
        $query_condition['version'] = strval($conditon['version']);

        $collections_names = array();
        for($time = $conditon['start_time']; $time < $conditon['end_time']; $time = $time + 86400){
            $collections_names[] = $this->_createCollectionName($time);
        }
        $fields = array('_id' => 0, 'uid_id' => 0, 'time' => 0);
        $perpage = 1000;
        foreach($collections_names as $col_name){
            $page = 0;
            $data = array();
            if(!in_array($col_name, $this->collections)) continue;
            $total = Common::getMongo()->count($col_name);
            do {
                $result_condition = array('start' => $page * $perpage, 'limit' => $perpage);
                $rs = Common::getMongo()->find($col_name, $query_condition, $result_condition, $fields);
                $this->_statistic($rs, $data);
                $page++;
            } while ($total > ($page * $perpage));

            $this->_csv($fp, $data, $conditon['version'], $col_name);
            unset($data);
        }
    }

    /**
     * 创建csv统计报表
     * @param object $fp
     * @param array $data
     * @param string $version
     * @param string $collection_name
     */
    private function _csv($fp, $data = array(), $version, $collection_name){
        if(!array_key_exists($version, $data)) return;

        $version_stand = implode('.', str_split($version));

        //输出Excel列名信息
        $heads = array('日期 ' . str_replace('_', '-', substr($collection_name, -10, 10)), '类型', '事件名称', '用户数', '用户占比', '点击次数', '点击占比', '版本号');
        foreach ($heads as $key => $title) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$key] = iconv('utf-8', 'gbk', $title);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);

        $clickEvents = $this->clickEvents;
        $clickEvents_a = $clickEvents['a']['list'];
        unset($clickEvents['a']);

        foreach($clickEvents_a as $key => $label){
            $row = array(
                iconv('utf-8', 'gbk', ''),
                iconv('utf-8', 'gbk', '来源'),
                iconv('utf-8', 'gbk', $label),
                iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? $data[$version]['events'][$key]['uv'] : 0),
                iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? number_format($data[$version]['events'][$key]['uv']*100/$data[$version]['total_uv'], 2) . '%' : '0.00%'),
                iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? $data[$version]['events'][$key]['pv'] : 0),
                iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? number_format($data[$version]['events'][$key]['pv']*100/$data[$version]['total_a_pv'], 2) . '%' : '0.00%'),
                iconv('utf-8', 'gbk', $version_stand),
            );
            fputcsv($fp, $row);
        }

        foreach($clickEvents as $ckey => $child){
            $row = array(
                iconv('utf-8', 'gbk', ''),
                iconv('utf-8', 'gbk', '去向'),
                iconv('utf-8', 'gbk', $child['label']),
                iconv('utf-8', 'gbk', isset($data[$version]['blocks_uv'][$ckey]) ? $data[$version]['blocks_uv'][$ckey] : 0),
                iconv('utf-8', 'gbk', isset($data[$version]['blocks_uv'][$ckey]) ? number_format($data[$version]['blocks_uv'][$ckey]*100/$data[$version]['total_uv'], 2) . '%' : '0.00%'),
                iconv('utf-8', 'gbk', isset($data[$version]['blocks_pv'][$ckey]) ? $data[$version]['blocks_pv'][$ckey] : 0),
                iconv('utf-8', 'gbk', isset($data[$version]['blocks_pv'][$ckey]) ? number_format($data[$version]['blocks_pv'][$ckey]*100/$data[$version]['total_pv'], 2) . '%' : '0.00%'),
                iconv('utf-8', 'gbk', $version_stand),
            );
            fputcsv($fp, $row);
            foreach($child['list'] as $key => $label){
                $row = array(
                    iconv('utf-8', 'gbk', ''),
                    iconv('utf-8', 'gbk', '去向'),
                    iconv('utf-8', 'gbk', $label),
                    iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? $data[$version]['events'][$key]['uv'] : 0),
                    iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? number_format($data[$version]['events'][$key]['uv']*100/$data[$version]['total_uv'], 2) . '%' : '0.00%'),
                    iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? $data[$version]['events'][$key]['pv'] : 0),
                    iconv('utf-8', 'gbk', isset($data[$version]['events'][$key]) ? number_format($data[$version]['events'][$key]['pv']*100/$data[$version]['total_pv'], 2) . '%' : '0.00%'),
                    iconv('utf-8', 'gbk', $version_stand),
                );
                fputcsv($fp, $row);
            }
        }
    }

    /**
     * 统计
     * @return bool
     */
    public function statistic(){
        if(empty($this->versions)) return false;
        $file_name = 'statistic-click-' . date('Y-m-d', Common::getTime()) . '.csv';
        if(file_exists($this->savePath . $file_name)) @unlink($this->savePath . $file_name);
        set_time_limit(0);

        foreach($this->versions as $version){
            for($start_time = $this->start_time; $start_time < $this->end_time; $start_time += 86400){
                if(!$version || !$start_time) continue;
                $fp = fopen($this->savePath . $file_name, 'ab+');
                if(!$fp) return false;
                flock($fp, LOCK_EX);
                $this->_createStat($fp, $start_time, $start_time + 86400, $version);
                fclose($fp);
            }
        }
    }

}

$statistic = new statisticClick();
$statistic->statistic();

die;