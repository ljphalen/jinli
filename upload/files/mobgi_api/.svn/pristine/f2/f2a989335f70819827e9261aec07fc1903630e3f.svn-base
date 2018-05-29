<?php
/**
 * Description of Statis
 *
 * @author Administrator
 */
Doo::loadModel ( 'AppModel' );
class Game extends AppModel {

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
    public function findAll($whereArr = NULL, $isList = FALSE) {
        $GameModel = Doo::loadModel ( 'datamodel/Games', TRUE );
        $where = array (
                'asArray' => true,
                'select' => 'id, name'
        );
        if (!empty($whereArr['where']['appKey'])) {
            foreach ($whereArr['where']['appKey'] as $key => $value) {
                $product_key[] = $value['appkey'];
            }
            $where['where'] = "product_key in ('".implode("','", $product_key)."')";
        }
        $result = $GameModel->find ( $where );
        if ($isList) {
            return listArray ( $result, 'id', 'name' );
        }
        return $result;
    }
}