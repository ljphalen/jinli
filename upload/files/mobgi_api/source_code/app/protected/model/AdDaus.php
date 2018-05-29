<?php
/**
 * Description of Statis
 *
 * @author Administrator
 */
Doo::loadModel ( 'AppModel' );
class AdDaus extends AppModel {
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
    public function findAll($whereArr = NULL) {
        $AdDauModel = Doo::loadModel ( "datamodel/AdDau", TRUE );
        $where = array(
                'asArray' => true,
                'where' => $whereArr['where'],
                'select' => 'date, num',
        );
        $result = $AdDauModel->find($where);
        return $result;
    }
}