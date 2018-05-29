<?php
/**
 * Description of Statis
 *
 * @author Administrator
 */
Doo::loadModel ( 'AppModel' );
class Statis extends AppModel {
    private $_fieldArr = array (
            'pid', 'gpkg', 'aid',
            'SUM(impressions) as impressions',
            'SUM(clicks) as clicks',
//             'ROUND(SUM(clicks)/SUM(impressions), 4) * 100 as clicks_rate',
            'SUM(finish_downloads) as finish_downloads',
            'SUM(start_downloads) as start_downloads',
//             'ROUND(SUM(start_downloads)/SUM(clicks), 4) * 100 as start_downloads_rate',
//             'ROUND(SUM(cancel_downloads)/SUM(clicks), 4) * 100 as cancel_downloads_rate',
            'SUM(cancel_downloads) as cancel_downloads',
//             'ROUND(SUM(finish_downloads)/SUM(start_downloads), 4) * 100 as finish_downloads_rate',
            'SUM(installed) as installed',
//             'ROUND(SUM(installed)/(SUM(start_downloads)+SUM(continue_downloads)), 4) * 100 as installed_rate',
            'SUM(first_startups) as first_startups',
            'SUM(not_first_startups) as not_first_startups',
//             'SUM(first_startups)+SUM(not_first_startups) as startups',
            'SUM(first_registers) as first_registers',
            'SUM(not_first_registers) as not_first_registers',
//             'SUM(first_registers)+SUM(not_first_registers) as registers',
//             'ROUND((SUM(first_registers)+SUM(not_first_registers))/SUM(installed), 4) * 100 as registers_rate',
//             'ROUND((SUM(first_registers)+SUM(not_first_registers))/SUM(impressions), 4) * 100 as registers_impressions'
    );
    private $_dateField = 'DATE_FORMAT(`stat_date`,"%Y-%m-%d") as date';

    private $_reqField = array('SUM(requests) as requests', 'SUM(requests_success) as requests_success');
    /**
     * 初始化时永远使用statis连接配置
     *
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct ( $properties );
    }

    /**
     * 取所有数据
     *
     * @param string $whereArr
     * @param string $date
     * @param string $groupby
     * @return number
     */
    public function findAll($whereArr = NULL, $impWhereAll = NULL, $type="day") {
        $statisModel = Doo::loadModel ( "datamodel/AdStatGameDayStatiscs", TRUE );
        // 默认为没有传条件，取所有数据
        $whereArr ['asArray'] = TRUE;
        // 当存在条件时。组合条件
        $whereArr ['select'] = implode ( ",", $this->_fieldArr );
        if (isset($whereArr['groupby']) && in_array('date', explode(',', $whereArr['groupby']))){
            $whereArr['select'] .= ','.$this->_dateField;
        } else {
           $whereArr['select'] .= ','.$whereArr['groupby'];
        }
        $result = $statisModel->find ( $whereArr );
        $totalImpress = $this->getImpressions ( $impWhereAll );
        foreach ( $result as $key => $record ) {
            $result[$key]['startups'] = $record['first_startups'] + $record['not_first_startups'];
            $result[$key]['registers'] = $record['first_registers'] + $record['not_first_registers'];
            if ($record['impressions'] == 0){
                $result[$key]['clicks_rate'] = 0;
                $result[$key['registers_impressions']] = 0;
            }else{
                $result[$key]['clicks_rate'] = round($record['clicks'] / $record['impressions'], 4) * 100;
                $result[$key['registers_impressions']] = round(($record['first_registers'] + $record['not_first_registers']) / $record['impressions'], 4) * 100;
            }
            if ($record['clicks'] == 0){
                $result[$key]['start_downloads_rate'] = 0;
                $result[$key]['cancel_downloads_rate'] = 0;
            }else{
                $result[$key]['start_downloads_rate'] = round($record['start_downloads'] / $record['clicks'], 4) * 100;
                $result[$key]['cancel_downloads_rate'] = round($record['cancel_downloads'] / $record['clicks'], 4) * 100;
            }
            if ($record['start_downloads'] == 0) {
                $result[$key]['finish_downloads_rate'] = 0;
            }else{
                $result[$key]['finish_downloads_rate'] = round($record['finish_downloads'] / $record['start_downloads'], 4) * 100;
            }
            if (($record['start_downloads'] + $record['continue_downloads']) == 0){
                $result[$key]['installed_rate'] = 0;
            }else{
                $result[$key]['installed_rate'] = round($record['installed'] / ($record['start_downloads'] + $record['continue_downloads']), 4) * 100;
            }
            if ($record['installed'] == 0) {
                $result[$key]['registers_rate'] = 0;
            }else{
                $result[$key]['registers_rate'] = round(($record['first_registers'] + $record['not_first_registers']) / $record['installed'], 4) * 100;
            }
            if (isset($totalImpress ['total_impressions'])) {
                if ($totalImpress ['total_impressions'] != 0){
                    $result [$key] ['impressions_rate'] = round($record ['impressions'] / $totalImpress ['total_impressions'], 6) * 100;
                }else{
                    $result [$key] ['impressions_rate'] = 0;
                }
            } else {
                if ($totalImpress [$record ['date']] ['total_impressions']){
                    $result [$key] ['impressions_rate'] = round($record ['impressions'] / $totalImpress [$record ['date']] ['total_impressions'], 6) * 100;
                }else{
                    $result [$key] ['impressions_rate'] = 0;
                }
            }
            foreach ($result [$key] as $field => $value) {
                $total[$field] += $value;
            }
        }
        if ($total['impressions'] != 0){
            $total['clicks_rate'] = round($total['clicks']/$total['impressions'], 4) * 100;
            $total['registers_impressions'] = round(($total['first_registers']+$total['not_first_registers'])/$total['impressions'], 4) * 100;
        }else{
            $total['clicks_rate'] = 0;
            $total['registers_impressions'] = 0;
        }
        if ($total['clicks'] != 0){
            $total['start_downloads_rate'] = round($total['start_downloads']/$total['clicks'], 4) * 100;
            $total['cancel_downloads_rate'] = round($total['cancel_downloads']/$total['clicks'], 4) * 100;
        }else{
            $total['start_downloads_rate'] = 0;
            $total['cancel_downloads_rate'] = 0;
        }
        if ($total['start_downloads'] != 0){
            $total['finish_downloads_rate'] = round($total['finish_downloads']/$total['start_downloads'], 4) * 100;
        }else{
            $total['finish_downloads_rate'] = 0;
        }
        if (($total['start_downloads']+$total['continue_downloads']) != 0){
            $total['installed_rate'] = round($total['installed']/($total['start_downloads']+$total['continue_downloads']), 4) * 100;
        }else{
            $total['installed_rate'] = 0;
        }
        if ($total['installed'] != 0){
            $total['registers_rate'] = round(($total['first_registers']+$total['not_first_registers'])/$total['installed'], 4) * 100;
        }else{
            $total['registers_rate'] = 0;
        }
        return array('result' => $result, 'total' => $total);
    }

    public function findAllByGpkg($whereArr = NULL, $impWhereAll = NULL){
        $statisModel = Doo::loadModel('datamodel/AdStatGameDayStatiscs', TRUE);
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = implode(',', $this->_fieldArr) . ', ' . implode(',', $this->_reqField);
        if (isset($whereArr['groupby']) && in_array('date', explode(',', $whereArr['groupby']))){
            $whereArr['select'] .= ','.$this->_dateField;
        }else{
            $whereArr['select'] .= ','.$whereArr['groupby'];
        }
        $result = $statisModel->find ( $whereArr );
        $totalImpress = $this->getImpressions ( $impWhereAll );
        foreach ( $result as $key => $record ) {
            if (isset($totalImpress ['total_impressions'])) {
                $result [$key] ['impressions_rate'] = round($record ['impressions'] / $totalImpress ['total_impressions'], 6) * 100;
            } else {
                $result [$key] ['impressions_rate'] = round($record ['impressions'] / $totalImpress [$record ['date']] ['total_impressions'], 6) * 100;
            }
            foreach ($result [$key] as $field => $value) {
                $total[$field] += $value;
            }
        }
        if ($total['impressions'] == 0) {
            $total['clicks_rate'] = 0;
        }else{
            $total['clicks_rate'] = round($total['clicks']/$total['impressions'], 6) * 100;
        }
        return array('result' => $result, 'total' => $total);
    }

    /**
     * 返回记录数
     *
     * @param type $conditions
     * @return type
     */
    public function records($whereArr = NULL) {
        $statisModel = Doo::loadModel ( "datamodel/AdStatGameDayStatiscs", TRUE );
        $whereArr ['asArray'] = TRUE;
        $whereArr ['select'] = 'pid';
        $result = $statisModel->find ( $whereArr );
        if (empty ( $result )) {
            return 0;
        } else {
            return count ( $result );
        }
    }

    /**
     * 总请求数，按Pid，Aid来取
     *
     * @param type $where
     * @param type $groupby
     * @return type
     */
    public function getRequest($whereArr) {
        $return = array ();
        $statisModel = Doo::loadModel ( "datamodel/AdStatGameDayStatiscs", TRUE );
        $whereArr ['asArray'] = TRUE;
        $whereArr ['select'] = 'gpkg, SUM(requests) as requests, SUM(requests_success) as requests_success';
        $flag = false;
        if (isset ( $whereArr ['groupby'] )) {
            $whereArr ['select'] .= ", " . $this->_dateField;
            $flag = true;
        }
        $result = $statisModel->find ( $whereArr );
        if (empty ( $result )) {
            return $result;
        }

        if ($flag == false) {
            return $return = $result [0];
        }
        foreach ( $result as $val ) {
            $return [$val ['date']] = $val;
        }
        return $return;
    }

    /**
     * 总展示数
     * @param unknown $whereArr
     * @return unknown|Ambigous <multitype:unknown , unknown>
     */
    public function getImpressions($whereArr){
        $return = array ();
        $statisModel = Doo::loadModel ( "datamodel/AdStatGameDayStatiscs", TRUE );
        $whereArr ['asArray'] = TRUE;
        $whereArr ['select'] = 'SUM(impressions) as total_impressions';
        $flag = false;
        if (isset ( $whereArr ['groupby'] )) {
            $whereArr ['select'] .= ", " . $this->_dateField;
            $flag = true;
        }
        $result = $statisModel->find ( $whereArr );
        if (empty ( $result )) {
            return $result;
        }

        if ($flag == false) {
            return $return = $result [0];
        }
        foreach ( $result as $val ) {
            $return [$val ['date']] = $val;
        }
        return $return;
    }
}

?>
