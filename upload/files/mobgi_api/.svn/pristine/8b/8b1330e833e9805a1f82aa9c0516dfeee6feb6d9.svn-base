<?php
/**
 * 角色模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class Role extends AppModel {
    
    private $_roleModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        // 设置数据库连接
        Doo::db()->reconnect('admin');
        $this->_roleModel = Doo::loadModel("datamodel/Roles", TRUE);
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
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_roleModel->find($whereArr);
        return $result;
    }
    
    /**
     * 查询
     * @param type $conditions
     */
    public function view($conditions){
        $whereSql="1=1";
        if (isset($conditions['role_id']) && $conditions['role_id']){
            $whereSql.=" and rl.id = ".$conditions['role_id'];
        }
        if (isset($conditions['product_name']) && $conditions['product_name']){
            $whereSql.=" and product_name LIKE '%".$conditions['product_name']."%'";
        }
        //获取角色组下面的帐号数量．
        $sqlForRoleNum = 'select role_id, count(adminid) as num from admins group by role_id';
        $roleNumResult = Doo::db()->query($sqlForRoleNum)->fetchAll();
        $roleNumArr = array();
        foreach($roleNumResult as $item){
            $roleNumArr[$item['role_id']] = $item['num'];
        }
        //获取角色组下面的有权限的产品名
        $sqlForRoleProductName = 'select rp.role_id, d.product_name, d.platform from roles2products rp left join mobgi_api.ad_product_info d on rp.product_id=d.id';
        $roleProductNameResult = Doo::db()->query($sqlForRoleProductName)->fetchAll();
        $roleProductNameArr = array();
        $roleProductNameShortArr = array();
        foreach($roleProductNameResult as $item){
            $tmpProduct = '';
            $tmpProduct .= $item['product_name'];
            if($item['platform'] == 0)
            {
                $tmpProduct.='(通用)';
            }else if($item['platform'] == 1)
            {
                $tmpProduct.='(Android)';
            }else if($item['platform'] == 2)
            {
                $tmpProduct.='(IOS)';
            }
            $roleProductNameArr[$item['role_id']][] = $tmpProduct;
            if(count($roleProductNameShortArr[$item['role_id']]) <2){
                $roleProductNameShortArr[$item['role_id']][] = $tmpProduct;
            }
        }
        
        $sql = "SELECT rl.*, COUNT(ad.adminid) AS num,c.`product_id`,c.`operator`,d.`product_name`, d.`platform`
                FROM roles rl 
                LEFT JOIN admins ad 
                    ON ad.role_id = rl.id LEFT JOIN roles2products c ON rl.id=c.`role_id` LEFT JOIN mobgi_api.`ad_product_info` d ON d.`id`=c.`product_id` where $whereSql
                GROUP BY rl.id 
                ORDER BY id ASC";
        $result = Doo::db()->query($sql)->fetchAll();
        $role = array();
        if (empty($result)) {
            return $role;
        }
        foreach ($result as $key =>$value){
            $role[$key]['id'] = $value['id'];
            $role[$key]['createdate'] = date("Y-m-d H:i:s",$value['createdate']);
            $role[$key]['operator'] = $value['operator'];
            $role[$key]['lastupdate'] = date("Y-m-d H:i:s",$value['lastupdate']);
            $role[$key]['title'] = $value['title'];
            $role[$key]['num'] = $roleNumArr[$role[$key]['id']];
            if(count($roleProductNameArr[$role[$key]['id']]) > 2 ){
                $roleProductNameShortArr[$role[$key]['id']][] = "......";
            }
            $role[$key]['short_product_names'] = implode($roleProductNameShortArr[$role[$key]['id']], '<br>');
            $role[$key]['product_names'] = implode($roleProductNameArr[$role[$key]['id']], '，　');
            if (CAN_ROLE_REMOVE_ROLE) {
                $role[$key]['removable'] = 1;
            } else {
                $role[$key]['removable'] = 0;
            }
        }
        return $role;
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
        $result = $this->_roleModel->getOne($whereArr);
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
        $result = $this->_roleModel->count($whereArr);
        return $result;
    }
    
    /**
     * 添加/修改角色
     * @param type $data
     */
    public function upd($id, $data){
        $this->_roleModel->title = $data['title'];
        $this->_roleModel->lastupdate = time();
        if(empty($id)){
            $this->_roleModel->createdate = time();
            return $this->_roleModel->insert();
        }{
            $this->_roleModel->id = $id;
            $this->_roleModel->update();
            return $id;
        }
    }
    
    /**
     * 删除开发者
     * @param int $devId
     * @return boolean
     */
    public function del($roleId){
        $this->_roleModel->id = $roleId;
        return $this->_roleModel->delete();
    }
    /**
     * 
     * @param type $conditions
     * @return string|\SQLblock     /
     */
    public function recordsr2p($conditions){
        $sql="SELECT COUNT(*) as num FROM roles a LEFT JOIN roles2products b ON a.`id`=b.`role_id` LEFT JOIN	mobgi_api.`ad_product_info` c ON c.`id`=b.`product_id` WHERE c.product_name='".$conditions["product_name"]."' AND a.id=".$conditions["role_id"];
        $records=Doo::db()->fetchRow($sql);
        return $records;
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
        if (isset($conditions['title']) && $conditions['title']){
            $where[] = "title LIKE '%".$conditions['title']."%'";
        }
        if(isset($conditions['screatedate']) && $conditions['screatedate']){
            $where[] = "createdate >= '".$conditions['screatedate']."'";
        }
        if(isset($conditions['ecreatedate']) && $conditions['ecreatedate']){
            $where[] = "createdate < '".$conditions['ecreatedate']."'";
        }
        if(isset($conditions['slastupdate']) && $conditions['slastupdate']){
            $where[] = "lastupdate >= '".$conditions['slastupdate']."'";
        }
        if(isset($conditions['elastupdate']) && $conditions['elastupdate']){
            $where[] = "lastupdate < '".$conditions['elastupdate']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}