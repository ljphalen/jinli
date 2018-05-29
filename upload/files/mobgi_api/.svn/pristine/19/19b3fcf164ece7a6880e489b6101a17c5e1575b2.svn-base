<?php
/**
 * Admin模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class Admin extends AppModel {

    private $_adminModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        // 设置数据库连接
        Doo::db()->reconnect('admin');
        $this->_adminModel = Doo::loadModel("datamodel/Admins", TRUE);
    }

    /**
     * 查询多条记录
     * @param type $conditions
     * @return type
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = 'adminid, username, password, realname, e_name, role_id, from_unixtime(date_create) as date_create, from_unixtime(date_update) as date_update, from_unixtime(date_login) as date_login';
        // 当存在条件时。组合条件
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_adminModel->find($whereArr);
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
        $result = $this->_adminModel->getOne($whereArr);
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
        $result = $this->_adminModel->count($whereArr);
        return $result;
    }

    /**
     * 添加/修改管理员
     * @param type $data
     */
    public function upd($adminid,$data){
        //$this->_adminModel->username = $data['username'];
        if ($data['password']){
            $this->_adminModel->password = md5(md5($data['password']));
        }
        $this->_adminModel->realname = $data['realname'];
        $this->_adminModel->username = $data['username'];
        $this->_adminModel->e_name = $data['e_name'];
        $this->_adminModel->role_id= $data['role_id'];
        $this->_adminModel->date_update = time();
        if(isset($data['lock_num'])){
            $this->_adminModel->lock_num= $data['lock_num'];
        }
        if(empty($adminid)){
            $this->_adminModel->date_create = time();
            return $this->_adminModel->insert();
        }{
            $this->_adminModel->adminid = $adminid;
            return $this->_adminModel->update();
        }
    }

    public function updPwd ($adminid,$post){
        $this->_adminModel->password = md5(md5(md5($post['password'])));
        $this->_adminModel->adminid = $adminid;
        $this->_adminModel->update();
    }

    /**
     * 删除管理员
     * @param int $adminId
     * @return boolean
     */
    public function del($data){
        if (isset($data['adminid']) && $data['adminid']){
            $this->_adminModel->adminid = $data['adminid'];
        }
        if (isset($data['role_id']) && $data['role_id']){
            $this->_adminModel->role_id = $data['role_id'];
        }
        return $this->_adminModel->delete();
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
        if (isset($conditions['adminid']) && $conditions['adminid']){
            $where[] = "adminid = ".$conditions['adminid'];
        }
        if (isset($conditions['username']) && $conditions['username']){
            $where[] = "username LIKE '%".$conditions['username']."%'";
        }
        if (isset($conditions['password']) && $conditions['password']){
            $where[] = "password = '".$conditions['password']."'";
        }
        if (isset($conditions['realname']) && $conditions['realname']){
            $where[] = "realname LIKE '%".$conditions['realname']."%'";
        }
        if(isset($conditions['e_name']) && $conditions['e_name']){
            $where[] = "e_name LIKE '%".$conditions['e_name']."%'";
        }
        if(isset($conditions['role_id']) && $conditions['role_id']){
            $where[] = "role_id = ".$conditions['role_id'];
        }
        if(isset($conditions['screate']) && $conditions['screate']){
            $where[] = "date_create >= '".$conditions['screate']."'";
        }
        if(isset($conditions['ecreate']) && $conditions['ecreate']){
            $where[] = "date_create < '".$conditions['ecreate']."'";
        }
        if(isset($conditions['supdate']) && $conditions['supdate']){
            $where[] = "date_update >= '".$conditions['supdate']."'";
        }
        if(isset($conditions['eupdate']) && $conditions['eupdate']){
            $where[] = "date_update < '".$conditions['eupdate']."'";
        }
        if(isset($conditions['slogin']) && $conditions['slogin']){
            $where[] = "date_login >= '".$conditions['slogin']."'";
        }
        if(isset($conditions['elogin']) && $conditions['elogin']){
            $where[] = "date_login < '".$conditions['elogin']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}
