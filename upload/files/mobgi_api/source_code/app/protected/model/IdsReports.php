<?php
/**
 * Description of Statis
 *
 * @author Administrator
 */
Doo::loadModel ( 'AppModel' );
class IdsReports extends AppModel {

    private $_fieldArr = array(
            'date','pid',
            'SUM(pay) as pay',
            'SUM(income) as income',
            'SUM(register) as register'
    );

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
    public function findByDate($dateArr = NULL){
        $IdsReportModel = Doo::loadModel('datamodel/IdsReport', TRUE);
        $whereSql = "`date` >= '".$dateArr['startDate']."' AND `date` <='".$dateArr['endDate']."'";
        $where = array(
                'asArray' => TRUE,
                'select' => "SUM(register) as register, date, pid",
                'where' => $whereSql,
                'asc' => 'date',
                'groupby' => 'date',
        );
        $result = $IdsReportModel->find($where);
        if (empty($result)) {
        	return array();
        }
        $return  = array();
        foreach ($result as $key => $row) {
            $return[$row['date']] = $row;
        }
        return $return;
    }

    public function findByPidAndDate($whereArr = NULL, $ishour = false){
    	$IdsReportModel = Doo::loadModel('datamodel/IdsReport', TRUE);
        $whereSql = "`date` >= '".$whereArr['startDate']."' AND `date` <='".$whereArr['endDate']."' and pid = '".$whereArr['pid']."'";
        $where = array(
                'asArray' => TRUE,
                'select' => implode(',', $this->_fieldArr),
                'where' => $whereSql,
                'asc' => 'date',
                'groupby' => 'date',
        );
        $result = $IdsReportModel->find($where);
        if (empty($result)) {
        	return array();
        }
        $return  = array();
        if ($ishour == true) {
            $return = $result[0];
            $return['date'] = $return['date']." 00:00:00";
        }else{
            foreach ($result as $key => $row) {
                $return[$row['date']] = $row;
            }
        }
        return $return;
    }
}