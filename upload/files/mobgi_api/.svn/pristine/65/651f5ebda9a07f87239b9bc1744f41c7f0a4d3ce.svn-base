<?php
/**
 * 开发者模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class Developer extends AppModel {
    
    private $_developerModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_developerModel = Doo::loadModel("datamodel/AdDeveloper", TRUE);
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
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_developerModel->find($whereArr);
        return $result;
    }
    
    /**
     * 查询开发者审核列表
     * @param type $conditions
     * @return type
     */
    public function findDeveloper($conditions = null, $limit){
         $sql = 'select ad_developer.*, ad_financial.bank_account, case when ad_developer.isactive = -1 then 0 when ad_developer.isactive=1 and (ad_developer.user_name ="" or ad_financial.bank_account="") then 1 when ad_developer.isactive=1 and ad_developer.user_name !="" and ad_financial.bank_account!="" then 2 end dev_stat from ad_developer ';
         $sql .= 'left join ad_financial on ad_developer.dev_id = ad_financial.dev_id ';
         $sql .= ' where '. $conditions;
         $sql .= ' order by dev_stat desc, ad_developer.createdate desc ';
         $sql .= 'limit ' . $limit;
         $result = Doo::db()->query($sql)->fetchAll();
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
        $result = $this->_developerModel->getOne($whereArr);
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
        $result = $this->_developerModel->count($whereArr);
        return $result;
    }
    
    
    public function view($conditions = NULL){
        $developer = $this->findAll($conditions);
        $appInfo = $this->getAppsCount();
        $financialsModel=Doo::loadModel('Financials', TRUE);
        foreach($developer as $key => $value){
            $whereArr = array('devid' => $value["dev_id"]);
            $financials=$financialsModel->findOne($whereArr);
            if(empty($financials)){
                unset($developer[$key]);
                continue;
            }
            if (isset($appInfo[$value['dev_id']][1])){
                $developer[$key]['appInfo'] = "<font color='blue'><b>".$appInfo[$value['dev_id']][1]."</b></font> 个应用<u><a href='/apps/index?state=1&dev_id=".$value['dev_id']."'>开启</a></u>广告<br />";
            }else{
                $developer[$key]['appInfo'] = "<font color='blue'><b>0</b></font> 个应用<u>开启</u>广告<br />";
            }
            if(isset($appInfo[$value['dev_id']][0])){
                $developer[$key]['appInfo'] .= "<font color='blue'><b>".$appInfo[$value['dev_id']][0]."</b></font> 个应用<u><a href='/apps/index?state=0&dev_id=".$value['dev_id']."'>关闭</a></u>广告";
            }else{
                $developer[$key]['appInfo'] .= "<font color='blue'><b>0</b></font> 个应用<u>关闭</u>广告";
            }
        }
        return $developer;
    }
    
    public function getAppsCount(){
        $appsModel = Doo::loadModel("datamodel/AdApp", TRUE);
        $result = $appsModel->find(array('select' => 'count(app_id) as num, dev_id, state', 'groupby'=>"dev_id,state",'asArray' => TRUE));
        if (empty($result)){
            return array();
        }
        $opidApp = array();
        foreach($result as $value){
            $opidApp[$value['dev_id']][$value['state']] = $value['num'];
        }
        return $opidApp;
    }
    
    /**
     * 添加/修改开发者
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->_developerModel->user_name = $data['user_name'];
        if ($data['password']){
            $this->_developerModel->password = md5(md5(md5($data['password'])));
        }
        $this->_developerModel->email = $data['email'];
        $this->_developerModel->mobile = $data['mobile'];
        $this->_developerModel->qq = $data['qq'];
        $this->_developerModel->updatedate = time();
        $this->_developerModel->operator = $currentUser['username'];
        if(empty($id)){
            $this->_developerModel->createdate = time();
            $this->_developerModel->insert();
            return $this->_developerModel->lastInsertId();
        }{
            $this->_developerModel->dev_id = $id;
            $this->_developerModel->update();
            return $id;
        }
    }
    
    /**
     * 删除开发者
     * @param int $devId
     * @return boolean
     */
    public function del($devId){
        $this->_developerModel->dev_id = $devId;
        return $this->_developerModel->delete();
    }
    /**
     * 审查开发者
     * @param int $devId
     * @param int $ispass
     * @return boolean
     */
    public function check_deverloper($devId,$ispass,$msg){
        $this->_developerModel->check_msg=$msg;
        $this->_developerModel->ischeck=$ispass;
        $this->_developerModel->dev_id = $devId;
        return $this->_developerModel->update();
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
        if (isset($conditions['dev_id']) && $conditions['dev_id']){
            $where[] = "dev_id = ".$conditions['dev_id'];
        }
        if (isset($conditions['appkey']) && $conditions['appkey']){
            $where[] = "appkey = '".$conditions['appkey']."'";
        }
        if (isset($conditions['user_name']) && $conditions['user_name']){
            $where[] = "user_name LIKE '%".$conditions['user_name']."%'";
        }
        if (isset($conditions['password']) && $conditions['password']){
            $where[] = "password = '".$conditions['password']."'";
        }
        if (isset($conditions['email']) && $conditions['email']){
            $where[] = "email = '".$conditions['email']."'";
        }
        if (isset($conditions['ischeck']) && $conditions['ischeck']){
            $where[] = "ischeck = '".$conditions['ischeck']."'";
        }
        if (isset($conditions['mobile']) && $conditions['mobile']){
            $where[] = "mobile LIKE '%".$conditions['mobile']."%'";
        }
        if (isset($conditions['qq']) && $conditions['qq']){
            $where[] = "qq LIKE '%".$conditions['qq']."%'";
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