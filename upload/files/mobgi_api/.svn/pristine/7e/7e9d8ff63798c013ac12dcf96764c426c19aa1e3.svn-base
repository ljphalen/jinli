<?php
/**
 * 广告位模型
 *
 * @author Stephen.Feng
 */
Doo::loadModel('AppModel');
class AdPosition extends AppModel {

    private $_adPositionModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_adPositionModel = Doo::loadModel("datamodel/AdPos", TRUE);
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
        $result = $this->_adPositionModel->find($whereArr);
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
        $result = $this->_adPositionModel->getOne($whereArr);
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
        $result = $this->_adPositionModel->count($whereArr);
        return $result;
    }
    
    /**
     * 添加应用
     * @param type $data
     */
    public function upd($id = NULL, $data){
        if (isset($data['parent_ids'])){
            unset($data['parent_ids']);
        } 
        $_adPositionModel = Doo::loadModel("datamodel/AdPos", TRUE);
        $_adPositionModel->parent_id = $data['parent_id'];
        $_adPositionModel->pos_key = $data['pos_key'];
        $_adPositionModel->pos_name = $data['pos_name'];
        $_adPositionModel->ad_type = $data['ad_type'];
        $_adPositionModel->ad_subtype = $data['ad_subtype'];
        $_adPositionModel->updated = time();
        if(empty($id)){
            $_adPositionModel->created = time();
            $_adPositionModel->insert();
            return $_adPositionModel->lastInsertId();
        }else{
            $_adPositionModel->id = $id;
            $_adPositionModel->update();
            return $id;
        }
    }
    
    /**
     * 删除广告位
     * @param int $appId
     * @return boolean
     */
    public function del($where){
        $_adPositionModel = Doo::loadModel("datamodel/AdPos", TRUE);
        if (isset($where['id']) && $where['id']){
            $_adPositionModel->id = $where['id'];
        }
        if (isset($where['parent_id']) && $where['parent_id']){
            $_adPositionModel->parent_id = $where['parent_id'];
        }
        return $_adPositionModel->delete();
    }
    /**
     * 更改广告位状态
     * @param int $appId
     * @return boolean
     */
    public function setPosState($pos_key,$state=0){
        if(empty($pos_key)){
            return false;
        }
        $_adPositionModel = Doo::loadModel("datamodel/AdDeverPos", TRUE);
        $pos=Doo::loadModel("datamodel/AdDeverPos",TRUE);
        $posInfo = $pos->getPosByDeverposkey($pos_key);
        $_adPositionModel->id = $posInfo['id'];
        $_adPositionModel->state=$state;
        $_adPositionModel->dever_pos_key=$pos_key;
        $result = $_adPositionModel->update();
         
        $appModel = Doo::loadModel('Apps', TRUE);
        $appInfo = $appModel->findOne(array('app_id'=>$posInfo['app_id']));
        if($appInfo['ischeck'] == 1){
           $app_name = $appInfo['app_name'];
           $this->data['session'] = Doo::session()->__get("admininfo");
           $by_email_name = $this->data['session']['e_name'];
           $stateStr = $state == 1?"开启":"关闭";
           $mailtemplate=Doo::conf()->mailtemplate;
           $subject=sprintf($mailtemplate["appposstatechange"]["title"],$app_name,$posInfo['dever_pos_name'],$by_email_name, $stateStr);
           $body = $mailtemplate["appposstatechange"]["body"];
           $this->sendEmail($mailtemplate["appposstatechange"]["tomailers"], $subject, $body);
        }
         return $result;
    }
    
    public function sendEmail($email, $subject, $body) {
        Doo::loadClass("mailer/SendMail");
        $mailconfig = Doo::conf()->mailconfig;
        $email = new SendMail($email, $subject, $body, $mailconfig);
        return $email->send();
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
        if (isset($conditions['parent_id'])){
            $where[] = "parent_id = ".$conditions['parent_id'];
        }
        if (isset($conditions['pos_key']) && $conditions['pos_key']){
            $where[] = "pos_key = '".$conditions['pos_key']."'";
        }
        if (isset($conditions['pos_name']) && $conditions['pos_name']){
            $where[] = "pos_name LIKE '%".$conditions['pos_name']."%'";
        }
        if(isset($conditions['ad_type'])){
            $where[] = "ad_type = ".$conditions['ad_type'];
        }
        if(isset($conditions['ad_subtype'])){
            $where[] = "ad_subtype = ".$conditions['ad_subtype'];
        }
        if(isset($conditions['screated']) && $conditions['screated']){
            $where[] = "created >= '".$conditions['screated']."'";
        }
        if(isset($conditions['ecreated']) && $conditions['ecreated']){
            $where[] = "created < '".$conditions['ecreated']."'";
        }
        if(isset($conditions['supdated']) && $conditions['supdated']){
            $where[] = "updated >= '".$conditions['updated']."'";
        }
        if(isset($conditions['eupdated']) && $conditions['eupdated']){
            $where[] = "updated < '".$conditions['eupdated']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}