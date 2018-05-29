<?php
Doo::loadModel('datamodel/base/IptadSourceBase');

class IptadSource extends IptadSourceBase{
    
    /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
    }
    
    /**
     * 新增、更新应用广告素材类型
     * @param type $id
     * @param type $data
     * @return type
     */
    public function upd_source($id, $data){
        
        $this->pid = $data['pid'];
        $this->blockkey = $data['blockkey'];
        $this->image_url = $data['image_url'];
        $this->text = $data['text'];
        $this->status = $data['status'];
        $this->target_url = $data['target_url'];
        $this->updatetime = time();
        
        if(empty($id)){
            unset($this->id);
            $this->createtime = time();
            return $this->insert();
        }else{
            $this->id = $id;
            $this->update();
            return $id;
        }
    }
    
    /**
     * 根据产品获取素材
     * @param type $productid
     * @return boolean
     */
    public function getSourcesByPid($productid){
        if(empty($productid)){
            return false;
        }
        $sources=$this->find(array("where"=>'pid ='.$productid.' and del = 1 ','asArray' => TRUE)); 
        //加个序号展示
        if(!empty($sources)){
            $blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
            $appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
            $fromIndex =1;
            foreach($sources as $key=>$item){
                $sources[$key]['index'] = $fromIndex;
                $fromIndex ++;
                $blockInfo = $blockModel->getBlockByBlockkey($item['blockkey']);
                $sources[$key]['inapp']  = $blockInfo['inapp'];
                $sources[$key]['source_type']  = $blockInfo['source_type'];
                $sources[$key]['appkey']  = $blockInfo['appkey'];
                $sources[$key]['blockname']  = $blockInfo['blockname'];
                $appInfo = $appModel->findOne(array('appkey'=>$blockInfo['appkey']));
                $sources[$key]['appname']  = $appInfo['appname'];
            }
        }
        
        //列表排序“可配置素材”展示在“不可配置素材”上面，素材分别按照添加的时间先后展示，对应同种“广告类型”的素材展示在相邻位置
        $sort_inapp = array();
        $sort_source_type = array();
        $sort_updatetime = array();
        foreach($sources as $key=>$sourceItem){
            $sort_inapp[] = $sourceItem['inapp'];
            $sort_source_type[] = $sourceItem['source_type'];
            $sort_updatetime[] = $sourceItem['updatetime'];
        }
        array_multisort($sort_inapp, SORT_ASC, $sort_source_type, SORT_ASC, $sort_updatetime, SORT_ASC, $sources);
        return $sources;
    }
    /**
     * 返回所有产品的对应的素材
     * @param type $productid
     * @return boolean
     */
    public function getSourcesMapPid($pid=""){
        $product=Doo::loadModel("datamodel/implantable/IptadProduct",TRUE);
        $where["asArray"]=true;
        if(!empty($pid)){
            $where["where"]="id=".$pid;
        }
        $plists = $product->find($where);
        foreach ($plists as $k=>$v){
            $sources=$this->find(array("select"=>"blockkey,id","where"=>'pid ='.$v["pid"].' and status=1 ','asArray' => TRUE));
            $plists[$k]["source"]=  json_encode($sources);
        }
        return $plists;
    }
    public function sourceMapProduct($id){
        $res=Doo::db()->query("select * from iptad_source a left join iptad_product b on a.pid=b.pid where a.id='".$id."'")->fetch();
        return $res;
    }
    /**
     * 素材:新增,修改,删除操作删除对应的redis key  "SOURCE_".  implode("_",$id); "SOURCE_".$id;
     * @return boolean
     */
    public function del_sourcecache(){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('IMPLANTABLE_REDIS_CACHE_DEFAULT');
        // 删除Redis
        $key = "SOURCE_*";
        $redis->delete($key);
        return true;
    }
    
    //软删除指定广告位的素材
    public function delSourceByBlockkey($blockkey){
        return Doo::db()->query("update iptad_source set del=0 where blockkey='".$blockkey."'");
    }
    
    //批量关闭指定blockkey的的素材
    public function setSourceOffByBlockkey($blockkey){
        return Doo::db()->query("update iptad_source set status=0 where blockkey='".$blockkey."'");
    }
}