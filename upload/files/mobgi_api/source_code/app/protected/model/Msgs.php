<?php
/**
 * 应用中心模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class Msgs extends AppModel {

    private $_msgsModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_msgsModel = Doo::loadModel("datamodel/Msg", TRUE);
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
        // 排序条件
        if (is_array($conditions) && isset($conditions['desc'])){
            $whereArr['desc'] = $conditions['desc'];
            unset($conditions['desc']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_msgsModel->find($whereArr);
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
        $result = $this->_msgsModel->getOne($whereArr);
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
        $result = $this->_msgsModel->count($whereArr);
        return $result;
    }

    /**
     * 添加应用
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $this->_msgsModel->title = $data['title'];
        $this->_msgsModel->type = 0;
        $this->_msgsModel->msg = $data['editorValue'];
        $this->_msgsModel->createdate = time();
        $this->_msgsModel->senddate = strtotime($data['senddate']);
        if(empty($id)){
            return $this->_msgsModel->insert();
        }else{
            $this->_msgsModel->id = $id;
            return $this->_msgsModel->update();
        }
    }

    /**
     * 删除应用
     * @param int $appId
     * @return boolean
     */
    public function del($id){
        $this->_msgsModel->id = $id;
        return $this->_msgsModel->delete();
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
        if (isset($conditions['dev_id']) && $conditions['dev_id']){
            $where[] = "dev_id = ".$conditions['dev_id'];
        }
        if (isset($conditions['title']) && $conditions['title']){
            $where[] = "title LIKE '%".$conditions['title']."%'";
        }
        if (isset($conditions['type']) && $conditions['type']){
            $where[] = "type = ".$conditions['type'];
        }
        if (isset($conditions['isread']) && $conditions['isread']){
            $where[] = "isread = ".$conditions['isread'];
        }
        if(isset($conditions['sreaddate']) && $conditions['sreaddate']){
            $where[] = "readdate >= '".$conditions['sreaddate']."'";
        }
        if(isset($conditions['ereaddate']) && $conditions['ereaddate']){
            $where[] = "readdate < '".$conditions['ereaddate']."'";
        }
        if(isset($conditions['ssenddate']) && $conditions['ssenddate']){
            $where[] = "senddate >= '".$conditions['ssenddate']."'";
        }
        if(isset($conditions['esenddate']) && $conditions['esenddate']){
            $where[] = "senddate < '".$conditions['esenddate']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    public function sendLetter($dev_id,$title,$msg){
        $senddate=date("Y-m-d H:i:s",time());
        $sql="insert into mobgi_www.msg set dev_id=".$dev_id.",title='".$title."',msg='".$msg."',senddate='".$senddate."'";
        try{
            Doo::db()->query($sql);
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}