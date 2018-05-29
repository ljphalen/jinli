<?php
/**
 * 应用版本模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class AdAppVersions extends AppModel {

    private $_adAppVersionsModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_adAppVersionsModel = Doo::loadModel("datamodel/AdAppVersion", TRUE);
    }

    /**
     * 查询应用版本列表-多条记录
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        // 当存在条件时。组合条件
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_adAppVersionsModel->find($whereArr);
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
        $result = $this->_adAppVersionsModel->getOne($whereArr);
        return $result;
    }

    /**
     * 添加应用版本
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $this->_adAppVersionsModel->app_version = $data['app_version'];
        $this->_adAppVersionsModel->appkey = $data['appkey'];
        $this->_adAppVersionsModel->app_name = $data['app_name'];
        $this->_adAppVersionsModel->updatedate = time();
        if(empty($id)){
            $this->_adAppVersionsModel->createdate = time();
            return $this->_adAppVersionsModel->insert();
        }else{
            $this->_adAppVersionsModel->id = $id;
            return $this->_adAppVersionsModel->update();
        }
    }

    /**
     * 删除应用版本
     * @param int $appId
     * @return boolean
     */
    public function del($id){
        $this->_adAppVersionsModel->id = $id;
        return $this->_adAppVersionsModel->delete();
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
        if (isset($conditions['appkey']) && $conditions['appkey']){
            $where[] = "appkey = '".$conditions['appkey']."'";
        }
        if (isset($conditions['app_version']) && $conditions['app_version']){
            $where[] = "app_version = '".$conditions['app_version']."'";
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
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}