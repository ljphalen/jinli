<?php
/**
 * Description of Statis
 *
 * @author Administrator
 */
Doo::loadModel ( 'AppModel' );
class UserRetentionRateReports extends AppModel {

    private $_fieldArr = array(
            'report_date as date', 'game_id', 'channel_id',
            'SUM(new_user) as new_user',
            'SUM(first_day_retention) as first_day_retention',
            'SUM(third_day_retention) as third_day_retention',
            'SUM(seventh_day_retention) as seventh_day_retention',
            'SUM(fifteenth_day_retention) as fifteenth_day_retention',
            'SUM(thirtieth_day_retention) as thirtieth_day_retention',
            'SUM(sixtieth_day_retention) as sixtieth_day_retention',
            'ROUND(SUM(first_day_retention)/SUM(new_user) * 100, 2) as first_day_rate',
            'ROUND(SUM(third_day_retention)/SUM(new_user) * 100, 2) as third_day_rate',
            'ROUND(SUM(seventh_day_retention)/SUM(new_user) * 100, 2) as seventh_day_rate',
            'ROUND(SUM(fifteenth_day_retention)/SUM(new_user) * 100, 2) as fifteenth_day_rate',
            'ROUND(SUM(thirtieth_day_retention)/SUM(new_user) * 100, 2) as thirtieth_day_rate',
            'ROUND(SUM(sixtieth_day_retention)/SUM(new_user) * 100, 2) as sixtieth_day_rate',
    );

    private $_compareFieldArr = array(
            'report_date as date_con', 'game_id', 'channel_id as channel_id_con',
            'SUM(new_user) as new_user_con',
            'SUM(first_day_retention) as first_day_retention_con',
            'SUM(third_day_retention) as third_day_retention_con',
            'SUM(seventh_day_retention) as seventh_day_retention_con',
            'SUM(fifteenth_day_retention) as fifteenth_day_retention_con',
            'SUM(thirtieth_day_retention) as thirtieth_day_retention_con',
            'SUM(sixtieth_day_retention) as sixtieth_day_retention',
            'ROUND(SUM(first_day_retention)/SUM(new_user) * 100, 2) as first_day_rate_con',
            'ROUND(SUM(third_day_retention)/SUM(new_user) * 100, 2) as third_day_rate_con',
            'ROUND(SUM(seventh_day_retention)/SUM(new_user) * 100, 2) as seventh_day_rate_con',
            'ROUND(SUM(fifteenth_day_retention)/SUM(new_user) * 100, 2) as fifteenth_day_rate_con',
            'ROUND(SUM(thirtieth_day_retention)/SUM(new_user) * 100, 2) as thirtieth_day_rate_con',
            'ROUND(SUM(sixtieth_day_retention)/SUM(new_user) * 100, 2) as sixtieth_day_rate_con',
    );

    /**
     * 初始化时永远使用user_retention连接配置
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
    public function findAll($whereArr = NULL) {
        $whereSQL['date'] = "report_date >= '".$whereArr['date']['startDate']."' AND report_date <= '".$whereArr['date']['endDate']."'";
        $whereSQL['cid'] = "channel_id in ('".$whereArr['cid']."')";
        $whereSQL['game_id'] = "game_id = '".$whereArr['game_id']."'";
        $where = array(
                'asArray' => true,
                'select' => implode(',', $this->_fieldArr),
                'groupby' => 'date',
                'where' => implode(' AND ', $whereSQL),
        );
        $UserRetentionRateReportModel = Doo::loadModel ( "datamodel/UserRetentionRateReport", TRUE );
        $result = $UserRetentionRateReportModel->find($where);
        return $result;
    }

    public function contrastConversionRate($whereArr, $contrastWhereArr){
        $UserRetentionRateReportModel = Doo::loadModel ( "datamodel/UserRetentionRateReport", TRUE );
        $where['game_id'] = " game_id ='".$whereArr['where']['game_id']."'";
        $where['date'] = "`report_date` >= '".$whereArr['where']['date']['startDate']."' AND `report_date` <='".$whereArr['where']['date']['endDate']."'";
        $where['cid'] = "channel_id in ('".str_replace(',', "','", $whereArr['where']['channel_id'])."')";
        $whereSQL = array(
                'asArray' => true,
                'asc' => 'date',
                'where' => implode(' AND ', $where),
                'groupby' => 'date',
                'select' => implode(',', $this->_fieldArr),
        );
        $result = $UserRetentionRateReportModel->find($whereSQL);
        if ($whereArr['where']['channel_id'] == $contrastWhereArr['where']['channel_id']) {
            return $result;
        }
        // 需要对比
        $return =array();
        foreach ($result as $key => $value) {
            $return[$value['date']] = $value;
        }
        $contrastWhere['game_id'] = " game_id ='".$contrastWhereArr['where']['game_id']."'";
        $contrastWhere['date'] = "`report_date` >= '".$contrastWhereArr['where']['date']['startDate']."' AND `report_date` <='".$contrastWhereArr['where']['date']['endDate']."'";
        $contrastWhere['cid'] = "channel_id in ('".str_replace(',', "','", $contrastWhereArr['where']['channel_id'])."')";
        $contrastWhereSQL = array(
                'asArray' => true,
                'asc' => 'date_con',
                'where' => implode(' AND ', $contrastWhere),
                'groupby' => 'date_con',
                'select' => implode(',', $this->_compareFieldArr),
        );
        $contrastResult = $UserRetentionRateReportModel->find($contrastWhereSQL);
        if (empty($contrastResult)){
            return $result;
        }
        foreach ($contrastResult as $conValue) {
            if (array_key_exists($conValue['date_con'], $return)){
                $return[$conValue['date_con']]['channel_id_con'] = $conValue['channel_id_con'];
                $return[$conValue['date_con']]['new_user_con'] = $conValue['new_user_con'];
                $return[$conValue['date_con']]['first_day_retention_con'] = $conValue['first_day_retention_con'];
                $return[$conValue['date_con']]['third_day_retention_con'] = $conValue['third_day_retention_con'];
                $return[$conValue['date_con']]['seventh_day_retention_con'] = $conValue['seventh_day_retention_con'];
                $return[$conValue['date_con']]['fifteenth_day_retention_con'] = $conValue['fifteenth_day_retention_con'];
                $return[$conValue['date_con']]['thirtieth_day_retention_con'] = $conValue['thirtieth_day_retention_con'];
                $return[$conValue['date_con']]['sixtieth_day_retention'] = $conValue['sixtieth_day_retention'];
                $return[$conValue['date_con']]['first_day_rate_con'] = $conValue['first_day_rate_con'];
                $return[$conValue['date_con']]['third_day_rate_con'] = $conValue['third_day_rate_con'];
                $return[$conValue['date_con']]['seventh_day_rate_con'] = $conValue['seventh_day_rate_con'];
                $return[$conValue['date_con']]['fifteenth_day_rate_con'] = $conValue['fifteenth_day_rate_con'];
                $return[$conValue['date_con']]['thirtieth_day_rate_con'] = $conValue['thirtieth_day_rate_con'];
                $return[$conValue['date_con']]['sixtieth_day_rate_con'] = $conValue['sixtieth_day_rate_con'];
            }
        }
        return array_values($return);
    }
}