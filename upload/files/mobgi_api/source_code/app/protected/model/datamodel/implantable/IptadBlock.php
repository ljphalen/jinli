<?php
Doo::loadModel('datamodel/base/IptadBlockBase');

class IptadBlock extends IptadBlockBase{
    
    /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
    }
    
    /**
     * 新增、更新应用广告类型
     * @param type $id
     * @param type $data
     * @return type
     */
    public function upd_block($id, $data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->blockkey = $data['blockkey'];
        $this->blockname = $data['blockname'];
        $this->appkey = $data['appkey'];
        $this->inapp = $data['inapp'];
        $this->source_type = $data['source_type'];
        $this->status = $data['status'];
        $this->oprater = $currentUser['username'];
        $this->updatetime = time();
        
        if(empty($id)){
            $this->createtime = time();
            return $this->insert();
        }else{
            $this->update();
            return $id;
        }
    }
    
    /**
     * 获取广告配置
     * @param type $appkey
     * @return type
     */
    public function getBlocksByAppkey($appkey){
        $where=array('select'=>'*','where'=>'del = 1 and appkey="'.$appkey.'"','asArray' => TRUE);
        $lists=$this->find($where);
        //加个序号展示
        if(!empty($lists)){
            $fromIndex =1;
            foreach($lists as $key=>$item){
                $lists[$key]['index'] = $fromIndex;
                $fromIndex ++;
            }
        }
        return $lists;
    }
    /**
     * 获取广告配置
     * @param type $appkey
     * @return type
     */
    public function getInappBlocksByAppkey($appkey){
        $where=array('select'=>'*','where'=>'del = 1 and appkey="'.$appkey.'" and inapp=0 and status=1','asArray' => TRUE);
        $lists=$this->find($where);
        return $lists;
    }
    /**
     * 获取广告配置
     * @param type $blockkey
     * @return type
     */
    public function getBlockByBlockkey($blockkey){
        $where=array('select'=>'*','where'=>'del = 1 and blockkey="'.$blockkey.'"','asArray' => TRUE);
        $lists=$this->find($where);
        return $lists[0];
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        return $result;
    }
    
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return "del=1";
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        $where[] = 'del = 1';
        if (isset($conditions['id']) && $conditions['id']){
            $where[] = "id = '".$conditions['id']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    
    /**
     * 更新应用状态
     * @param type $data
     */
    public function updateBlockState($blockkey, $status){
        $currentUser = Doo::session()->__get('admininfo');
        $this->updatetime = time();
        $this->oprater = $currentUser['username'];
        $this->status = $status;
        $this->blockkey = $blockkey;
        $this->update();
        return $blockkey;
    }
    
     /**
     * 软删除客户名
     * @param type $publishid
     */
    public function softDelBlock($blockkey){
        $this->updatetime=time();
        $this->blockkey=$blockkey;
        $this->del=0;
        return $this->update();
    }
    
    /**
     * 
     * @param type $appkey
     * @return type
     */
    public function getBlockNumInfoStr($appkey){
        $sql = 'select count(status) as num, status from iptad_block where del = 1 and inapp=0 and appkey="'. $appkey. '" group by status ';
        $result = Doo::db()->query($sql)->fetchAll();
        $returnStr = '';
        if(!empty($result)){
            foreach($result as $key=>$item){
                $return[$item['status']] = $item['num'];
            }
            $returnStr.='<font color="blue"><b>'.  intval($return[1]).'</b></font> 个广告类型<u>开启</u><br />';
            $returnStr.='<font color="blue"><b>'.  intval($return[0]).'</b></font> 个广告类型<u>关闭</u><br />';
        }else{
            $returnStr.='<font color="blue"><b>0</b></font> 个广告类型<u>开启</u><br />';
            $returnStr.='<font color="blue"><b>0</b></font> 个广告类型<u>关闭</u><br />';
        }
        return $returnStr;
    }
    
    /**
     * 广告类型:新增,修改,删除时删除对应应用的block key  "BLOCK_" .$appkey;
     * @return boolean
     */
    public function del_blockcache($appkey){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('IMPLANTABLE_REDIS_CACHE_DEFAULT');
        // 删除Redis
        $key = "BLOCK_".$appkey;
        $redis->delete($key);
        return true;
    }
    
    
}