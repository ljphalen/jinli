<?php
/**
 * 开发者财务信息
 *
 * @author Intril.leng
 */
Doo::loadModel('AppModel');
class Financials extends AppModel {
    
    private $_financialsModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_financialsModel = Doo::loadModel("datamodel/AdFinancial", TRUE);
    }
    
    /**
     * 查询开发者列表
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        // 当存在条件时。组合条件
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_financialsModel->find($whereArr);
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
        $result = $this->_financialsModel->getOne($whereArr);
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
        $result = $this->_financialsModel->count($whereArr);
        return $result;
    }
    
    /**
     * 添加/修改开发者帐户信息
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->_financialsModel->cred_name = $data['cred_name'];
        $this->_financialsModel->bank = $data['bank'];
        $this->_financialsModel->sub_branch = $data['sub_branch'];
        $this->_financialsModel->bank_account = $data['bank_account'];
        $this->_financialsModel->cred_type = $data['cred_type'];
        $this->_financialsModel->cred_num = $data['cred_num'];
        $this->_financialsModel->cred_pic = $data['cred_pic'];
        $this->_financialsModel->ftype = $data['ftype'];
        $this->_financialsModel->updatedate = time();
        $this->_financialsModel->dev_id = $data['dev_id'];
        $this->_financialsModel->operator = $currentUser['username'];
        if(empty($id)){
            $this->_financialsModel->createdate = time();
            return $this->_financialsModel->insert();
        } else {
            $this->_financialsModel->f_id = $id;
            return $this->_financialsModel->update();
        }
    }
    
    /**
     * 删除开发者帐号信息
     * @param int $devId
     * @return boolean
     */
    public function del($devId){
        $this->_financialsModel->devid = $devId;
        $this->_financialsModel->delete();
        return true;
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
        if (isset($conditions['f_id']) && $conditions['f_id']){
            $where[] = "f_id = ".$conditions['f_id'];
        }
        if (isset($conditions['cred_name']) && $conditions['cred_name']){
            $where[] = "cred_name LIKE '%".$conditions['cred_name']."%'";
        }
        if (isset($conditions['bank']) && $conditions['bank']){
            $where[] = "bank = '".$conditions['bank']."'";
        }
        if (isset($conditions['sub_branch']) && $conditions['sub_branch']){
            $where[] = "sub_branch = '".$conditions['sub_branch']."'";
        }
        if(isset($conditions['bank_account']) && $conditions['bank_account']){
            $where[] = "bank_account = ".$conditions['bank_account'];
        }
        if(isset($conditions['cred_type']) && $conditions['cred_type']){
            $where[] = "cred_type = '".$conditions['cred_type']."'";
        }
        if(isset($conditions['cred_num']) && $conditions['cred_num']){
            $where[] = "cred_num = '".$conditions['cred_num']."'";
        }
        if(isset($conditions['ftype']) && $conditions['ftype']){
            $where[] = "ftype = ".$conditions['ftype'];
        }
        if(isset($conditions['devid']) && $conditions['devid']){
            $where[] = "dev_id = ".$conditions['devid'];
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
