<?php
/**
 * Article数据库
 *
 * @author ot.tang
 */
Doo::loadModel('AppModel');
class ArticleManage extends AppModel {

    private $_ArticleModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_ArticleModel = Doo::loadModel("datamodel/Article",TRUE);
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
        $whereArr['where'] = $this->conditions($conditions,$this->_ArticleModel->_fields);
        $result = $this->_ArticleModel->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_ArticleModel->_fields);
        $result = $this->_ArticleModel->getOne($whereArr);
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
        $whereArr['where'] = $this->conditions($conditions,$this->_ArticleModel->_fields);
        $result = $this->_ArticleModel->count($whereArr);
        return $result;
    }
    
    /**
     * 添加应用
     * @param type $data
     */
    public function upd($data ,$id = NULL){
		$uploadpath = doo::conf()->MISC_BASEURL."/upload/";
        //$_ArticleModel = new Article;
        foreach($this->_ArticleModel->_fields as $value)
        {
        	if(isset($data["$value"]))
        	{
        		$this->_ArticleModel->{$value} = $data["$value"];
        	}
        }
        $_filename = str_replace($uploadpath, "", $data['icon']);
        $data['icon'] = $this->UpToCDN($data['icon'], '/picstore/'.$_filename,"publish",$_filename);
        $this->_ArticleModel->icon = $data['icon'];
        $this->_ArticleModel->updatedate = time();
        if(empty($id)){
            $this->_ArticleModel->createdate = time();
            $this->_ArticleModel->insert();
            return $this->_ArticleModel->lastInsertId();
        }else{
        	if($data['old_icon'] != 'nopic.jpg' && isset($data['icon']) && $data['icon'] != '')
        	{
        		Doo::loadCore('helper/DooFile');
        		$dooUpload = new DooFile();
        		$localuppath = str_replace(Doo::conf()->BASEURL, Doo::conf()->SITE_PATH, $uploadpath);
        		$dir_file = str_replace(Doo::conf()->cdn_path.'/picstore/', $localuppath, $data['old_icon']);
        		$re = $dooUpload->delete($dir_file);       		
        		$cdn_dir = str_replace(Doo::conf()->cdn_path, '', $data['old_icon']);
        		$this->UpToCDN($cdn_dir, $cdn_dir,'delete');
        	}
            $this->_ArticleModel->id = intval($id);
            $this->_ArticleModel->update();
            return $id;
        }
    }
    
    /**
     * 删除应用
     * @param array $where
     * @return Model exist
     */
    public function del($where,$exist){//$_adPositionModel = Doo::loadModel("datamodel/AdPos", TRUE);
    	$uploadpath = doo::conf()->MISC_BASEURL."/upload/";
        if (isset($where['id']) && $where['id']){
            $this->_ArticleModel->id = $where['id'];
            $_file = $exist['icon'];
            if($_file != '' && $_file != null)
            {
            $localuppath = str_replace(Doo::conf()->BASEURL, Doo::conf()->SITE_PATH, $uploadpath);
        	$dir_file = str_replace(Doo::conf()->cdn_path.'/picstore/', $localuppath, $_file);
            $_dirfile = $dir_file;
            Doo::loadCore('helper/DooFile');
            $dooUpload = new DooFile();
        	$dooUpload->delete($_dirfile);
        	$cdn_dir = str_replace(Doo::conf()->cdn_path, '', $_file);
        	$this->UpToCDN($cdn_dir, $cdn_dir,'delete');
            }
        }
        return $this->_ArticleModel->delete();
    }
}