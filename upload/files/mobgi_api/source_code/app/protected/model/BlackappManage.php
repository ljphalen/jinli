<?php
/**
 * Article数据库
 *
 * @author ot.tang
 */
Doo::loadModel('AppModel');
class BlackappManage extends AppModel {

    private $_BlackappModel;
    private $_redisDB = "CACHE_REDIS_CHEAT_APP";

    public function __construct($properties = null) {
        parent::__construct($properties); 
        $this->_BlackappModel = Doo::loadModel("datamodel/Blackapp",TRUE);
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
        $whereArr['where'] = $this->conditions($conditions,$this->_BlackappModel->_fields);
        $result = $this->_BlackappModel->find($whereArr);
        return $result;
    }
    
    /**
     * 在redis查找记录
     * Enter description here ...
     */
    public function find_inredis($string = "*"){
    	$keys = $this->getkeys($this->_redisDB,$string);
		$result = array();
		$id = 0;
		foreach($keys as $value)
		{
			$result[] = array("id"=>$id,"appkey"=>$value,"created"=>0,"updated"=>0);
			++$id;
		}
		return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_BlackappModel->_fields);
        $result = $this->_BlackappModel->getOne($whereArr);
        return $result;
    }
    
    /**
     * 返回记录数
     * @param type $conditions
     * @return 数据的数量
     */
    public function records($conditions = NULL){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_BlackappModel->_fields);
        $result = $this->_BlackappModel->count($whereArr);
        return $result;
    }
    /**
     * 删除应用
     * @param array $where
     * @return Model exist
     */
    public function del($where,$exist=NULL){//$_adPositionModel = Doo::loadModel("datamodel/AdPos", TRUE);
        if (isset($where['id']) && $where['id']){
            $this->_BlackappModel->id = $where['id'];
        }
        return $this->_BlackappModel->delete();
    }
    
    public function del_fromredis($APPKEY)
    {
    	$this->deleter($APPKEY,$this->_redisDB);
    }
    
    public function delall_fromredis()
    {
    	$this->flushDB($this->_redisDB);
    }
    
    public function add($value = NULL)
    {
    	if($value !== NULL)
    	{
    		$this->_BlackappModel->appkey = $value;
    		$this->_BlackappModel->created = time();
    		$this->_BlackappModel->updated = time();
    		$this->_BlackappModel->insert();
    		$this->set($value," ",$this->_redisDB); 
    	}
    }
}