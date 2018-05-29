<?php
/**
 * 安装提醒模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class InstallReminds extends AppModel {

    private $_installRemindsModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_installRemindsModel = Doo::loadModel("datamodel/AdInstatllRemind", TRUE);
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
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_installRemindsModel->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_installRemindsModel->getOne($whereArr);
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
        $result = $this->_installRemindsModel->count($whereArr);
        return $result;
    }


    /**
     * 添加应用
     * @param type $data
     */
    public function upd($id = NULL, $data){
        ini_set("magic_quotes_gpc","Off");//关闭自动转义
        $this->_installRemindsModel->config = $data['config'];//更新一次不容易,双保险
        $this->_installRemindsModel->update_date = time();
        if(empty($id)){
            $this->_installRemindsModel->create_date = time();
            return $this->_installRemindsModel->insert();
        }else{
            $this->_installRemindsModel->id = $id;
            $this->_installRemindsModel->update();
        }
    }
    
    /**
     * 删除应用
     * @param int $appId
     * @return boolean
     */
    public function del($appId){
        $this->_installRemindsModel->id = $id;
        return $this->_installRemindsModel->delete();
    }
    
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return "1=1";
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        if (isset($conditions['id']) && $conditions['id']){
            $where[] = "id = ".$conditions['id'];
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

?>
