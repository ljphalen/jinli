<?php
/**
 * Article数据库
 *
 * @author ot.tang
 */
Doo::loadModel('AppModel');
class LogManage extends AppModel {

    private $_LogModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_LogModel = Doo::loadModel("datamodel/Log",TRUE);
    }
    
    /**
     * 查询应用列表-多条记录
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        // 当存在条件时。组合条件
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->conditions($conditions,$this->_LogModel->_fields);
        $result = $this->_LogModel->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_LogModel->_fields);
        $result = $this->_LogModel->getOne($whereArr);
        return $result;
    }
    
    /**
     * 返回记录数
     * @param type $conditions
     * @return 数据的数量
     */
    public function records($conditions = NULL){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_LogModel->_fields);
        $result = $this->_LogModel->count($whereArr);
        return $result;
    }
}