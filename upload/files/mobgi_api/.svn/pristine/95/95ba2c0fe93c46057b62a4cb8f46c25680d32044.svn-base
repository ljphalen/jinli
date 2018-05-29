<?php
Doo::loadModel('datamodel/base/CategoryBase');

class Category extends CategoryBase{
    
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
        $result = $this->find($whereArr);
        return $result;
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
        if (isset($conditions['parentId'])){
            $where[] = "parentId = ".$conditions['parentId'];
        }
        if (isset($conditions['name']) && $conditions['name']){
            $where[] = "name = '".$conditions['name']."'";
        }
        if(isset($conditions['type'])){
            $where[] = "type = ".$conditions['type'];
        }
        if (isset($conditions['ids']) && $conditions['ids']){
            $where[] = "id in (".$conditions['ids']. ")";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    
    /**
     * 新增配置.
     * @param type $arr
     * @return boolean
     */
    public function addData($arr){
        if(!is_array($arr) || empty($arr)){
            return false;
        }
        foreach($arr as $item){
            $sql = "INSERT INTO `category` (`id`, `parentId`, `name`, `type`, `channelData`) VALUES ('".$item['id']."', '".intval($item['parentId'])."', '".addslashes($item['name'])."', '".$item['type']."','".addslashes($item['channelData'])."')  ON DUPLICATE KEY UPDATE parentId='".intval($item['parentId'])."', name='".addslashes($item['name'])."', channelData='".addslashes($item['channelData'])."';";
            Doo::db()->query($sql)->execute();
        }
        return true;
    }
    
    /**
     * 清空表数据
     * @return boolean
     */
    public function truncateData()
    {
        $sql = "delete  from  `category` where 1;";
        Doo::db()->query($sql)->execute();   
        return true;
    }
}