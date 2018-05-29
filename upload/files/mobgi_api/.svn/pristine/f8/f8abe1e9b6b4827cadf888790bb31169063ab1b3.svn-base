<?php
Doo::loadModel('AppModel');
class ImgManages extends AppModel {

    private $_ImgModel;
    
    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_ImgModel = Doo::loadModel("datamodel/ImgManage",TRUE);
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
        $whereArr['where'] = $this->conditions($conditions,$this->_ImgModel->_fields);
        $result = $this->_ImgModel->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_ImgModel->_fields);
        $result = $this->_ImgModel->getOne($whereArr);
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
        $whereArr['where'] = $this->conditions($conditions,$this->_ImgModel->_fields);
        $result = $this->_ImgModel->count($whereArr);
        return $result;
    }
    
    /**
     * 添加应用
     * @param type $data
     */
    public function upd($data ,$id = NULL){
		$uploadpath = doo::conf()->MISC_BASEURL."/upload/";
        //$_ArticleModel = new Article;
        foreach($this->_ImgModel->_fields as $value)
        {
        	if(isset($data["$value"]))
        	{
        		$this->_ImgModel->{$value} = $data["$value"];
        	}
        }
        $_filename = str_replace($uploadpath, "", $data['url']);
        $data['url'] = $this->UpToCDN($data['url'], '/picstore/'.$_filename,"publish",$_filename);
        $this->_ImgModel->url = $data['url'];
        $this->_ImgModel->updatedate = time();
        if($id === NULL){
            $this->_ImgModel->createdate = time();
            $this->_ImgModel->insert();
            return $this->_ImgModel->lastInsertId();
        }else{
        	$where["where"] = "id=".intval($id);
        	$model = new ImgManage();
        	Doo::db()->reconnect('admin');
        	$model = $model->getOne($where);
        	$url = $model->url;
        	$file = str_replace(Doo::conf()->cdn_path, "", $url);
        	$this->UpToCDN($file, $file,"delete");
            $this->_ImgModel->id = intval($id);
            Doo::db()->reconnect('admin');
            $this->_ImgModel->update();
            return $id;
        }
    }
    
    /**
     * 删除应用
     * @param array $where
     * @return Model exist
     */
    public function del($where,$exist=NULL){//$_adPositionModel = Doo::loadModel("datamodel/AdPos", TRUE);
        if (isset($where['id']) && $where['id']){
            $this->_ImgModel->id = $where['id'];
        }
        return $this->_ImgModel->delete();
    }
}