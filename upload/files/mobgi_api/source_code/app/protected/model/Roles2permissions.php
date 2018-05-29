<?php
/**
 * Roles2permissions模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class Roles2permissions extends AppModel {
    
    private $_roles2permissionsModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        // 设置数据库连接
        Doo::db()->reconnect('admin');
        $this->_roles2permissionsModel = Doo::loadModel("datamodel/Roles2permission", TRUE);
    }
    
    /**
     * 查询多条记录
     * @param type $conditions
     * @return type
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        // 当存在条件时。组合条件
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_roles2permissionsModel->find($whereArr);
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
        $result = $this->_roles2permissionsModel->getOne($whereArr);
        return $result;
    }
    
    /**
     * 添加/修改role2
     * @param type $data
     */
    public function upd($data){
        $this->_roles2permissionsModel->role_id = $data['role_id'];
        $this->_roles2permissionsModel->group_id = $data['group_id'];
        $this->_roles2permissionsModel->mask = $data['mask'];
        $this->_roles2permissionsModel->lastupdate = time();
        if(isset($data['id']) && !empty($data['id'])){
            $this->_roles2permissionsModel->id = $data['id'];
            return $this->_roles2permissionsModel->update();
        }{
            $this->_roles2permissionsModel->createdate = time();
            return $this->_roles2permissionsModel->insert();
        }
    }
    
    /**
     * 删除role2
     * @param int $devId
     * @return boolean
     */
    public function del($roleId){
        $this->_roles2permissionsModel->role_id = $roleId;
        return $this->_roles2permissionsModel->delete();
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
        if (isset($conditions['role_id']) && $conditions['role_id']){
            $where[] = "role_id = ".$conditions['role_id'];
        }
        if(isset($conditions['group_id']) && $conditions['group_id']){
            $where[] = "group_id = ".$conditions['group_id'];
        }
        if(isset($conditions['mask']) && $conditions['mask']){
            $where[] = "mask = '".$conditions['mask']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}