<?php
/**
 * 条件管理模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class ConditionManages extends AppModel {
    
    private $_conditionManagesModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_conditionManagesModel = Doo::loadModel("datamodel/AdConditionManage", TRUE);
    } 
    
    /**
     * 查询多条记录
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        // 当存在条件时。组合条件
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_conditionManagesModel->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = '';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_conditionManagesModel->getOne($whereArr);
        return $result;
    }

    /**
     * 返回记录数
     * @param type $conditions
     * @return type
     */
    public function records($conditions = NULL){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_conditionManagesModel->count($whereArr);
        return $result;
    }
    
    /**
     * 添加/修改条件
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->_conditionManagesModel->value = $data['value'];
        if (isset($data['type'])){
            $this->_conditionManagesModel->type = $data['type'];
        }
        $this->_conditionManagesModel->name = $data['name'];
        $this->_conditionManagesModel->condition_id = $data['condition_id'];
        $this->_conditionManagesModel->data_type = $data['data_type'];
        $this->_conditionManagesModel->updatedate = time();
        $this->_conditionManagesModel->operator = $currentUser['username'];
        if(empty($id)){
            $this->_conditionManagesModel->createdate = time();
            return $this->_conditionManagesModel->insert();
        }{
            $this->_conditionManagesModel->id = $id;
            return $this->_conditionManagesModel->update();
        }
    }
    
    public function updStatus($id, $status){
        $this->_conditionManagesModel->status = $status;
        $this->_conditionManagesModel->updatedate = time();
        if ($id){
            $this->_conditionManagesModel->id = $id;
            return $this->_conditionManagesModel->update();
        }
    }


    /**
     * 删除条件
     * @param int $devId
     * @return boolean
     */
    public function del($id){
        $this->_conditionManagesModel->id = $id;
        return $this->_conditionManagesModel->delete();
    }
    
    /**
     * 级联数据
     */
    public function toSelect(){
        $result = $this->findAll(array('status' => 1));
        if (empty($result)){
            return array();
        }
        $rs = array();
        foreach($result as $key => $value){
            $rs[$value['type']][$key] = array('c_id' => $value['condition_id'], 'name' => $value['name']);
        }
        return $rs;
    }
    //根据条件id获取条件值信息;
    public function getConditionByid($conditionid){
        if(empty($conditionid)){
            return false;
        }
        $res=$this->getArray($conditionid);
        if(empty($res)){
            $res=$this->findOne(array("condition_id"=>$conditionid));
        }
        return $res;
    }
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return ;
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        if (isset($conditions['id']) && $conditions['id']){
            $where[] = "id = ".$conditions['id'];
        }
        if (isset($conditions['value']) && $conditions['value']){
            $where[] = "value LIKE '%".$conditions['value']."%'";
        }
        if (isset($conditions['type']) && $conditions['type']){
            $where[] = "type = ".$conditions['type'];
        }
        if (isset($conditions['name']) && $conditions['name']){
            $where[] = "name LIKE '%".$conditions['name']."%'";
        }
        if (isset($conditions['status']) && $conditions['status']){
            $where[] = "status = ".$conditions['status'];
        }
        if(isset($conditions['condition_id']) && $conditions['condition_id']){
            $where[] = "condition_id = '".$conditions['condition_id']."'";
        }
        if(isset($conditions['screatedate']) && $conditions['screatedate']){
            $where[] = "createdate >= '".$conditions['screatedate']."'";
        }
        if(isset($conditions['ecreatedate']) && $conditions['ecreatedate']){
            $where[] = "createdate < '".$conditions['ecreatedate']."'";
        }
        if(isset($conditions['supdatedate']) && $conditions['supdatedate']){
            $where[] = "updatedate >= '".$conditions['supdatedate']."'";
        }
        if(isset($conditions['eupdatedate']) && $conditions['eupdatedate']){
            $where[] = "updatedate < '".$conditions['eupdatedate']."'";
        }
        if(isset($conditions['operator']) && $conditions['operator']){
            $where[] = "operator LIKE '%".$conditions['operator']."%'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}