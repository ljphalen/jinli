<?php

/**
 * 素材管理
 *
 * @author Stephen.Feng
 */
Doo::loadController("AppDooController");

class ResourceController extends AppDooController {

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        
    }
	/**
	 * 获取 pic_url
	 */
    public function getpicurl(){
    	$get = $this->get;
    	if(isset($get['product_id']))
    	{
    		$search_pic = Doo::loadModel("datamodel/AdPic",TRUE);
    		$pic = $search_pic->getOne(array("select"=>"pic_url","where"=>"ad_product_id='{$get['product_id']}' and ad_subtype=10", "desc"=>'id'));
    		echo $pic->pic_url;
    		exit;
    	}else{
    		echo "";
    		exit;
    	}
    }
    /**
     * 上传图片素材
     */
    public function index() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["result"]["time"] = date("YmdHis");
        $this->data["product"] = $this->mangerBelongProduct();
        $adpic = Doo::loadModel("datamodel/AdPic", TRUE);
        $get = $this->get;
        $id = isset($get['id']) ? $get['id'] : 0;
        $result = $adpic->getAdpic($id);
        $pic_name=  explode("-", $result["pic_name"]);
        $result["beizhu"]=$pic_name[0];
        $this->data["result"] = $result;
        // 选择模板
        $this->myrender("resource/pic", $this->data);
    }

    /**
     * 上传文案素材
     */
    public function text() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["result"]["time"] = date("YmdHis");
        $this->data["product"] = $this->mangerBelongProduct();
        $adpic = Doo::loadModel("datamodel/AdText", TRUE);
        $get = $this->get;
        $id = isset($get['id']) ? $get['id'] : 0;
        $result = $adpic->getAdtext($id);
        $this->data["result"] = $result;
        // 选择模板
        $this->myrender("resource/text", $this->data);
    }

    /**
     * 上传安装包
     */
    public function apk() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["result"]["time"] = date("YmdHis");
        $this->data["product"] = $this->mangerBelongProduct();
        $adpic = Doo::loadModel("datamodel/AdApk", TRUE);
        $get = $this->get;
        $result = $adpic->getAdapk($get["id"]);
        $this->data["result"] = $result;
        // 选择模板
        $this->myrender("resource/apk", $this->data);
    }
    /**
     * 上传视频
     */
    public function video() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["result"]["time"] = date("YmdHis");
        $this->data["product"] = $this->mangerBelongProduct();
        $adpic = Doo::loadModel("datamodel/AdVideo", TRUE);
        $get = $this->get;
        $result = $adpic->getAdapk($get["id"]);
        $this->data["result"] = $result;
        // 选择模板
        $this->myrender("resource/video", $this->data);
    }
    
    /**
     * 上传激励视频
     */
    public function incentive_video() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["result"]["time"] = date("YmdHis");
        $this->data["product"] = $this->mangerBelongProduct();
        $adpic = Doo::loadModel("datamodel/AdIncentiveVideo", TRUE);
        $get = $this->get;
        $result = $adpic->getAdIncentivevideo($get["id"]);
        $this->data["result"] = $result;
        // 选择模板
        $this->myrender("resource/incentive_video", $this->data);
    }
    /**
     * 上传html
     */
    public function html() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["result"]["time"] = date("YmdHis");
        $this->data["product"] = $this->mangerBelongProduct();
        $adpic = Doo::loadModel("datamodel/AdApk", TRUE);
        $get = $this->get;
        $result = $adpic->getAdapk($get["id"]);
        $this->data["result"] = $result;
        // 选择模板
        $this->myrender("resource/html", $this->data);
    }

    /*
     * 上传图片素材
     */

    public function pic_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if(empty($post["id"])){
            $filepath = $this->uploadFile("pic/", "file_pic_name");
            if($filepath==-2){
                $this->redirect('../resource',"只允许上传图片文件");
            }
            $post['pic_url'] = $filepath;
        }
        
        $adpic = Doo::loadModel("datamodel/AdPic", TRUE);
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $post["ischeck"]=1;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }else{
            $post["ischeck"]=0;//编辑重新审核
        }
        $post["owner"]=$this->data['session']["username"];
        foreach($post['product_id'] as $product_id)
        {
            $postNew = $post;
            $postNew['product_id'] = $product_id;
            $insertId=$adpic->upd($postNew);
        }
        $this->redirect('../resource');
    }
    /*
     * 上传图片素材审核
     */

    public function pic_check_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $adpic = Doo::loadModel("datamodel/AdPic", TRUE);
        $post["checker"]=$this->data['session']["username"];
        $insertId=$adpic->upd($post);
        $this->redirect('/resource/checklist');
    }

    /*
     * 上传文案素材
     */

    public function text_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $adpic = Doo::loadModel("datamodel/AdText", TRUE);
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $post["ischeck"]=1;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }else{
            $post["ischeck"]=0;//编辑重新审核
        }
        $post["owner"]=$this->data['session']["username"];
        foreach($post['product_id'] as $product_id)
        {
            $postNew = $post;
            $postNew['product_id'] = $product_id;
            $insertId=$adpic->upd($postNew);
        }
        $this->redirect('../resource/text');
    }
    /*
     * 上传文案素材
     */

    public function text_check_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $adpic = Doo::loadModel("datamodel/AdText", TRUE);
        $post["checker"]=$this->data['session']["username"];
        $insertId = $adpic->upd($post);
        $this->redirect('/resource/checklist');
    }

    /*
     * 上传apk素材
     */

    public function apk_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $adapk = Doo::loadModel("datamodel/AdApk", TRUE);
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $post["ischeck"]=1;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }else{
            $post["ischeck"]=0;//编辑重新审核
        }
        $post["owner"]=$this->data['session']["username"];
        $id=$adapk->upd($post);
        $this->redirect('../resource/apk');
    }

    //上传apk
    public function upload_apk() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $path = "apk/";
        $rename=time() . rand(2, 100);
        $file_url = $this->uploadFile($path, "apk_file", $rename, array("apk","ipa"));
        //echo UPLOAD_PATH.$path;
        $file_name = str_replace(array(Doo::conf()->cdn_path.'/'.$path,UPLOAD_SITE.$path), "", $file_url);
        //filename用来解析,filepath用来存储
        $result = array("filename"=>$file_name,"filepath"=>$file_url);
        $this->toJSON($result,true);
        exit;
    }

    /*
     * 解析apk报信息
     */

    public function aapt() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->showMsg("权限不够");
        }
        # END 检查权限
        $post = $this->post;
        $apkfile = UPLOAD_PATH . "apk/" . $post["apk"];//判断文件存在用绝对路径。
        if (!file_exists($apkfile)) {
            return $this->showMsg("文件不存在，解析失败");
        }
        $file_type=  explode(".",$apkfile);
        $file_type=  $file_type[sizeof($file_type)-1];
        if($file_type=="apk"){
            $data = readApkInfoFromFile($apkfile, true);
        }else{
            $data=readIPAInfoFromFile($apkfile);
        }
        if (empty($data)) {
            $this->showMsg("解析失败");
        }
        $data["platform"]=$file_type;
        $this->showMsg($data, 1);
    }
    /*
     * 上传html素材
     */

    /*
     * 上传图片素材
     */

    public function html_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if(empty($post["id"]) && empty($post['html_url'])){
            $filepath = $this->uploadFile("html/", "file_name","",array("shtml","html","htm","shtm"));
            if($filepath==-2){
                $this->redirect('../resource/html',"只允许上传html文件");
            }
            $post['html_url'] = $filepath;
        }
        
        //根据id批量获取产品的名称
        $product = Doo::loadModel("datamodel/AdsProductInfo", TRUE);
        $productsinfo = $product->find(array("select" => "id,product_name", "where" => "id in(".  implode(',', $post['product_id']).")", "asArray" => true));
        $product_id_name = array();
        foreach($productsinfo as $productItem){
            $product_id_name[$productItem['id']] = $productItem['product_name'];
        }
        
        $adhtml = Doo::loadModel("datamodel/AdHtml", TRUE);
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $post["ischeck"]=1;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }else{
            $post["ischeck"]=0;//编辑重新审核
        }
        $post["owner"]=$this->data['session']["username"];
        foreach($post['product_id'] as $product_id)
        {
            $postNew = $post;
            $postNew['product_id'] = $product_id;
            $postNew['product_name'] = $product_id_name[$product_id];
            $insertId=$adhtml->upd($postNew);
        }
        $this->redirect('../resource/html');
    }
    /*
     * 上传网页素材审核
     */

    public function html_check_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $adhtml = Doo::loadModel("datamodel/AdHtml", TRUE);
        $post["checker"]=$this->data['session']["username"];
        $insertId=$adhtml->upd($post);
        $this->redirect('/resource/checklist');
    }
    /*
     * 上传video素材
     */

    public function video_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $advideo = Doo::loadModel("datamodel/AdVideo", TRUE);
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $post["ischeck"]=1;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }else{
            $post["ischeck"]=0;//编辑重新审核
        }
        $post["owner"]=$this->data['session']["username"];
        foreach($post['product_id'] as $product_id)
        {
            $postNew = $post;
            $postNew['product_id'] = $product_id;
            $insertId=$advideo->upd($postNew);
        }
        $this->redirect('../resource/video');
    }
    
    /*
     * 上传video素材
     */
    
    public function incentive_video_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $advideo = Doo::loadModel("datamodel/AdIncentiveVideo", TRUE);
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $post["ischeck"]=1;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }else{
            $post["ischeck"]=0;//编辑重新审核
        }
        $post["owner"]=$this->data['session']["username"];
        foreach($post['product_id'] as $product_id)
        {
            $postNew = $post;
            $postNew['product_id'] = $product_id;
            $insertId=$advideo->upd($postNew);
        }
        $this->redirect('../resource/incentive_video');
    }
     /*
     * video审核
     */

    public function video_check_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->displayNoPermission();
        }
        $post=$this->post;
        $video = Doo::loadModel("datamodel/AdVideo", TRUE);
        $post["checker"]=$this->data['session']["username"];
        $id=$video->upd($post);
        $this->redirect('/resource/checklist');
    }
    //上传视频
    public function upload_video() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $path = "video/";
        $rename=time() . rand(2, 100);
        $file_url = $this->uploadFile($path, "video_file", $rename, array("mp4"));
        //echo UPLOAD_PATH.$path;
        $file_name = str_replace(array(Doo::conf()->cdn_path.'/'.$path,UPLOAD_SITE.$path), "", $file_url);
        //filename用来解析,filepath用来存储
        $result = array("filename"=>$file_name,"filepath"=>$file_url);
        $this->toJSON($result,true);
        exit;
    }
    
    //上传H5
    public function upload_h5() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $path = "h5/";
        $rename=time() . rand(2, 100);
        $file_url = $this->uploadFile($path, "h5_file", $rename, array("zip","rar"));
        //echo UPLOAD_PATH.$path;
        $file_name = str_replace(array(Doo::conf()->cdn_path.'/'.$path,UPLOAD_SITE.$path), "", $file_url);
        //filename用来解析,filepath用来存储
        $result = array("filename"=>$file_name,"filepath"=>$file_url);
        $this->toJSON($result,true);
        exit;
    }
    /*
     * 素材审核列表
     */

    public function checklist() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $ischeck=array(0,2);
        if ($this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $ischeck=array(0);
        }
        $get=$this->get;
        $result = array();
        
        $where = array();
        //搜索时间　
        if (isset($get['screatedate']) && $get['screatedate']) {
            $where['screatedate'] = strtotime($get['screatedate']);
        }
        if (isset($get['ecreatedate']) && $get['ecreatedate']) {
            $where['ecreatedate'] = strtotime($get['ecreatedate']." 23:59:59");
        }
        $where['product_name'] = $get["source_name"];
        
        if($get["source"]==0 ||$get["source"]==1){
            if($get['platform'] !== ""){
                $where['platform'] = $get['platform'];
            }
            $adpic = Doo::loadModel("datamodel/AdPic", TRUE);
            $result["pic"] = $adpic->getAdpicCheck($ischeck,$where);
            $this->get_simple_platform($result["pic"]);
        }
        if($get["source"]==0 ||$get["source"]==2){
            if($get['platform'] !== ""){
                $where['platform'] = $get['platform'];
            }
            $adtext = Doo::loadModel("datamodel/AdText", TRUE);
            $result["text"] = $adtext->getAdtextCheck($ischeck,$where);
            $this->get_simple_platform($result["text"]);
        }
        if($get["source"]==0 ||$get["source"]==3){
            if($get['platform'] !== ""){
                $where['platform'] = $get['platform'];
            }
            $apk = Doo::loadModel("datamodel/AdApk", TRUE);
            $result["apk"] = $apk->getAdapkCheck($ischeck,$where);
            $this->get_simple_platform($result["apk"]);
        }
        if($get["source"]==0 ||$get["source"]==4){
            if($get['platform'] !== ""){
                $where['platform'] = $get['platform'];
            }
            $adproducts = Doo::loadModel("datamodel/AdsProductInfo", TRUE);
            $result["products"] = $adproducts->getAdsProductCheck($ischeck,$where);
            $this->get_simple_platform($result["products"]);
        }
        if($get["source"]==0 ||$get["source"]==5){
            if($get['platform'] !== ""){
                $where['platform'] = $get['platform'];
            }
            $adhtml = Doo::loadModel("datamodel/AdHtml", TRUE);
            $result["html"] = $adhtml->getAdhtmlCheck($ischeck,$where);
            $this->get_simple_platform($result["html"]);
        }
           
        if($get["source"]==0 || $get["source"]==6){
            if($get['platform'] !== ""){
                $where['platform'] = $get['platform'];
            }
            $video = Doo::loadModel("datamodel/AdIncentiveVideo", TRUE);
            $result["video"] = $video->getAdIncentivevideoCheck($ischeck,$where);
            $this->get_simple_platform($result["video"]);
        }
        
        $this->data["conditions"]=$get;
        $this->data["result"] = $result;
        $this->myrender("resource/check", $this->data);
    }

    /*
     * apk审核
     */

    public function apk_check() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->apk();
            die();
        }
        $id=$this->get["id"];
        $apk = Doo::loadModel("datamodel/AdApk", TRUE);
        $this->data["result"]=$apk->getAdapk($id);
        $this->myrender("resource/apk_check", $this->data);
    }
    /*
     * pic审核
     */

    public function pic_check() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->index();
            die();
            //$this->displayNoPermission();
        }
        $id=$this->get["id"];
        $this->data["product"] = $this->mangerBelongProduct();
        $pic = Doo::loadModel("datamodel/AdPic", TRUE);
        $picinfo=$pic->getAdpic($id);
        if(!empty($picinfo)){
            $picname= explode("-",$picinfo["pic_name"]);
            $picinfo["beizhu"]=$picname[0];
        }
        $this->data["result"]=$picinfo;
        $this->myrender("resource/pic_check", $this->data);
    }
    /*
     * text审核
     */

    public function text_check() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->text();
            die();
        }
        $id=$this->get["id"];
        $this->data["product"] = $this->mangerBelongProduct();
        $text = Doo::loadModel("datamodel/AdText", TRUE);
        $this->data["result"]=$text->getAdtext($id);
        $this->myrender("resource/text_check", $this->data);
    }
    /*
     * html审核
     */

    public function html_check() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->html();
            die();
        }
        $id=$this->get["id"];
        $this->data["product"] = $this->mangerBelongProduct();
        $html = Doo::loadModel("datamodel/AdHtml", TRUE);
        $this->data["result"]=$html->getAdhtml($id);
        $this->myrender("resource/html_check", $this->data);
    }
    /*
     * text审核
     */

    public function video_check() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->video();
            die();
        }
        $id=$this->get["id"];
        $this->data["product"] = $this->mangerBelongProduct();
        $video = Doo::loadModel("datamodel/AdVideo", TRUE);
        $this->data["result"]=$video->getAdvideo($id);
        $this->myrender("resource/video_check", $this->data);
    }
    
    /*
     * 审核
     */
    
    public function incentive_video_check() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->video();
            die();
        }
        
        $id=$this->get["id"];
        $this->data["product"] = $this->mangerBelongProduct();
        $video = Doo::loadModel("datamodel/AdIncentiveVideo", TRUE);
           
        $this->data["result"]=$video->getAdIncentivevideo($id);
        $this->myrender("resource/incentive_video_check", $this->data);
    }
    
    /*
     * video审核
     */
    
    public function incentive_video_check_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->displayNoPermission();
        }
        $post=$this->post;
        $video = Doo::loadModel("datamodel/AdIncentiveVideo", TRUE);
        $post["checker"]=$this->data['session']["username"];
        $id=$video->upd($post);
        $this->redirect('/resource/checklist');
    }
    /*
     * apk审核
     */

    public function apk_check_save() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->displayNoPermission();
        }
        $post=$this->post;
        $apk = Doo::loadModel("datamodel/AdApk", TRUE);
        $post["checker"]=$this->data['session']["username"];
        $id=$apk->upd($post);
        $this->redirect('/resource/checklist');
    }

    /**
     * 删除
     */
    public function delete() {
    	# START 检查权限
    	if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	if(isset($this->get['id']) && isset($this->get['op']))
    	{
    		$model = NULL;
    		$id = $this->get['id'];
    		$op = $this->get['op'];
    		switch($op)
    		{
    			case pic:
    				$model = Doo::loadModel("datamodel/AdPic",true);
                                $sql="update ads_info set del=-1 where r_type=1 and r_id=".$this->get["id"];
                                Doo::db()->query($sql)->execute();
    				break;
    			case text:
    				$model = Doo::loadModel("datamodel/AdText",true);
                                $sql="update ads_info set del=-1 where r_type=2 and r_id=".$this->get["id"];
                                Doo::db()->query($sql)->execute();
    				break;
    			case apk:
    				$model = Doo::loadModel("datamodel/AdApk",true);
    				break;
    			case html:
    				$model = Doo::loadModel("datamodel/AdHtml",true);
                                $sql="update ads_info set del=-1 where r_type=4 and r_id=".$this->get["id"];
                                Doo::db()->query($sql)->execute();
    				break;
    			case video:
    				$model = Doo::loadModel("datamodel/AdVideo",true);
                                $sql="update ads_info set del=-1 where r_type=5 and r_id=".$this->get["id"];
                                Doo::db()->query($sql)->execute();
    				break;
    			case incentive_video:
    				    $model = Doo::loadModel("datamodel/AdIncentiveVideo",true);
    				    $sql="update ads_info set del=-1 where r_type=6 and r_id=".$this->get["id"];
    				    Doo::db()->query($sql)->execute();
    				    break;
    			default :
    				$this->redirect('javascript:location.href=document.referrer',"错误操作参数");
    		}
                
    		$model->id = $this->get['id'];
    		$model->delete();
    		$this->redirect('javascript:location.href=document.referrer',"删除成功");
    	}else{
    		$this->redirect('javascript:location.href=document.referrer',"请输入必要参数");
    	}
    	
    }
    /*
     * 查看素材  图片
     */
    public function material_pic(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限   	
        
        $this->data["product"] = $this->mangerBelongProduct();
        $get = $this->get;
        $search_contitions = array("product_name","ad_subtype","resolution","color","focus","xuetou", 'platform');
        $search_pic = Doo::loadModel("datamodel/AdPic",TRUE);
        $sql_string = "1=1 and ischeck=1 and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") ";
        //分页原始url
        $url = "/resource/material_pic?";
        foreach($search_contitions as $value)
        {
        	$this->data['result'][$value] = "";
        	if(isset($get["$value"]) && $get["$value"] != "")
        	{
                //安桌和IOS平台需要包含通用类型的素材
                if($value == 'platform' && in_array($get[$value], array("1", "2"))){
                    $this->data['result'][$value] = $get[$value];
                    $platformStr= implode(array(0, $get[$value]), ',');
                    $sql_string .= "and {$value} in ({$platformStr}) ";
                    $url.= "$value=".$get[$value].'&';
                }else{
                    $sql_string .= "and {$value}= '{$get[$value]}' ";
                    $this->data['result'][$value] = $get[$value];
                    $url.= "$value=".$get[$value].'&';
                }
        	}
        }
        $this->data['result']['adtype'] = "";
        if(isset($get['adtype']) && $get['adtype'] != "")
        {
        	$this->data['result']['adtype'] = $get['adtype'];
        	$adtype = stripslashes($get['adtype']);
        	$sql_string .= "and resolution in {$adtype} ";
        }
        
        $this->data['edate'] = time();
        $this->data['sdate'] = time()-7*24*3600;
        if(isset($get['sdate']) && isset($get['edate']))
        {
        	$sdate = strtotime($get['sdate']." 00:00:00");
        	$edate = strtotime($get['edate']." 23:59:59");
        	$this->data['edate'] = $edate;
        	$this->data['sdate'] = $sdate;
        	$sql_string .= "and updatetime >= $sdate and updatetime <= $edate ";
            $url.= "sdate=".$get['sdate'].'&';
            $url.= "edate=".$get['edate'].'&';
        }
        //加分页
        $total = $search_pic->getcount($sql_string);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $pic=$search_pic->getList($sql_string, $limit);
//        $pic = $search_pic->find(array("where"=>$sql_string,"asArray"=>true));
        $this->data['pic'] = (count($pic)>0) ? $pic : array();
        $this->data['i'] = 0;
        $this->myrender("resource/material_pic", $this->data);
    }
    /*
     * 查看素材 文案
     */
 	public function material_text(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限   	
        $this->data["result"]["time"] = date("YmdHis");
        $get = $this->get;
        $sql_string = "1=1 and ischeck=1 and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") ";
        $search_conditions = array("product_name","type", "platform");
        //分页原始url
        $url = "/resource/material_text?";
        foreach($search_conditions as $value)
        {
        	$this->data['result'][$value] = "";
        	if(isset($get[$value]) && $get[$value] != "")
        	{
                //安桌和IOS平台需要包含通用类型的素材
                if($value == 'platform' && in_array($get[$value], array("1", "2"))){
                    $this->data['result'][$value] = $get[$value];
                    $platformStr= implode(array(0, $get[$value]), ',');
                    $sql_string .= "and {$value} in ({$platformStr}) ";
                    $url.= "$value=".$get[$value].'&';
                }else{
                    $this->data['result'][$value] = $get[$value];
                    $sql_string .= "and {$value}='{$get[$value]}' ";
                    $url.= "$value=".$get[$value].'&';
                }
        	}
        }
        $this->data['result']['key_word'] = "";
 	 	if(isset($get['key_word']) && $get['key_word'] != "")
        {
        	$this->data['result']['key_word'] = $get['key_word'];
        	$sql_string .= "and content like '%{$get['key_word']}%' ";
        }
        $this->data['sdate'] = time()-7*24*3600;
        $this->data['edate'] = time();
        if(isset($get['sdate']) && isset($get['edate']))
        {
        	$sdate = strtotime($get['sdate']." 00:00:00");
        	$edate = strtotime($get['edate']." 23:59:59");
        	$this->data['sdate'] = $sdate;
        	$this->data['edate'] = $edate;
        	$sql_string .= "and updatetime >= $sdate and updatetime <= $edate ";
            $url.= "sdate=".$get['sdate'].'&';
            $url.= "edate=".$get['edate'].'&';
        }
        
        $this->data["product"] = $this->mangerBelongProduct();
        
        $search_text = Doo::loadModel("datamodel/AdText",TRUE);
        //加分页
        $total = $search_text->getcount($sql_string);

        $page = $this->page($url, $total);
        $limit = $page->limit;
        $text=$search_text->getList($sql_string, $limit);
//        $text = $search_text->find(array("where"=>$sql_string,"asArray"=>true));
        if(count($text) > 0)
        {
        	foreach($text as $key=>$value)
        	{
        		$search_pic = Doo::loadModel("datamodel/AdPic",true);
        		$search_apk = Doo::loadModel("datamodel/AdApk",true);
                //取icon图片
        		$pic = $search_pic->getOne(array("select"=>"pic_url","where"=>"product_name='{$value['product_name']}'and ad_subtype=10 ", "desc"=>"id"));
        		$apk = $search_apk->getOne(array("select"=>"apk_url","where"=>"product_name='{$value['product_name']}'"));
        		$text[$key]['apk_url'] = $apk ? $apk->apk_url : "";
        		$text[$key]['pic_url'] = $pic ? $pic->pic_url : "";
        	}
        }
        $this->data['text'] = (count($text)>0) ? $text : array();
        $this->myrender("resource/material_text", $this->data);
    }
    /*
     * 查看素材 安装文件
     */
	public function material_apk(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限   	
        
        $this->data["product"] = $this->mangerBelongProduct();
        $search_conditions = array("product_name","package_name","apk_version", 'platform');
        $sql_string = "1=1 and ischeck=1 and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") ";
        $get = $this->get;
        foreach($search_conditions as $value)
        {
        	$this->data['result'][$value] = "";
        	if(isset($get[$value]) && $get[$value] != "")
        	{
        		$this->data['result'][$value] = $get[$value];
        		switch ($value)
        		{
        			case "product_name":
        				$sql_string .= "and {$value}='{$get[$value]}' ";
        				break;
        			case "package_name":
        				$sql_string .= "and {$value} like '%{$get[$value]}%' ";
        				break;
        			case "apk_version":
        				$sql_string .= "and {$value} like '{$get[$value]}%' ";
        				break;
                    case "platform":
        				$sql_string .= "and {$value}='{$get[$value]}' ";
        				break;
        		}
        		
        	}
        }
        $this->data['sdate'] = time()-7*24*3600;
        $this->data['edate'] = time();
        if(isset($get['sdate']) && isset($get['edate']))
        {
        	$sdate = strtotime($get['sdate']." 00:00:00");
        	$edate = strtotime($get['edate']." 23:59:59");
        	$this->data['sdate'] = $sdate;
        	$this->data['edate'] = $edate;
        	$sql_string .= "and updatetime >= {$sdate} and updatetime <= {$edate} ";
        }
        $search_apk = Doo::loadModel("datamodel/AdApk",TRUE);
        //加分页
        $total = $search_apk->getcount($sql_string);
        $url = "/resource/material_apk?";
        if ($get['platform']){
            $url .= "platform=".$get['platform'].'&';
        }
        if ($get['product_name']){
            $url .= "product_name=".$get['product_name'].'&';
        }
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $apk=$search_apk->getList($sql_string, $limit);
//        $apk = $search_apk->find(array("where"=>$sql_string,"asArray"=>true));
        $this->data['apk'] = (count($apk) > 0) ? $apk : array();
        $this->data['i'] = 0;
        $this->myrender("resource/material_apk", $this->data);
    }
    /*
     * 查看素材  图片
     */
    public function material_html(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限   	
        
        $this->data["product"] = $this->mangerBelongProduct();
        $get = $this->get;
        $search_contitions = array("product_name","ad_type","resolution", 'platform');
        $search_pic = Doo::loadModel("datamodel/AdHtml",TRUE);
        $sql_string = "1=1 and ischeck=1 and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") ";
        //分页原始url
        $url = "/resource/material_html?";
        foreach($search_contitions as $value)
        {
        	$this->data['result'][$value] = "";
        	if(isset($get["$value"]) && $get["$value"] != "")
        	{
                //安桌和IOS平台需要包含通用类型的素材
                if($value == 'platform' && in_array($get[$value], array("1", "2"))){
                    $this->data['result'][$value] = $get[$value];
                    $platformStr= implode(array(0, $get[$value]), ',');
                    $sql_string .= "and {$value} in ({$platformStr}) ";
                    $url.= "$value=".$get[$value].'&';
                }else{
                    $sql_string .= "and {$value}= '{$get[$value]}' ";
                    $this->data['result'][$value] = $get[$value];
                    $url.= "$value=".$get[$value].'&';
                }
        	}
        }
        $this->data['result']['adtype'] = "";
        if(isset($get['adtype']) && $get['adtype'] != "")
        {
        	$this->data['result']['adtype'] = $get['adtype'];
        	$adtype = stripslashes($get['adtype']);
        	$sql_string .= "and resolution in {$adtype} ";
        }
        
        $this->data['edate'] = time();
        $this->data['sdate'] = time()-7*24*3600;
        if(isset($get['sdate']) && isset($get['edate']))
        {
        	$sdate = strtotime($get['sdate']." 00:00:00");
        	$edate = strtotime($get['edate']." 23:59:59");
        	$this->data['edate'] = $edate;
        	$this->data['sdate'] = $sdate;
        	$sql_string .= "and updatetime >= $sdate and updatetime <= $edate ";
            $url.= "sdate=".$get['sdate'].'&';
            $url.= "edate=".$get['edate'].'&';
        }
        //加分页
        $total = $search_pic->getcount($sql_string);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $pic=$search_pic->getList($sql_string, $limit);
//        $pic = $search_pic->find(array("where"=>$sql_string,"asArray"=>true));
        $this->data['pic'] = (count($pic)>0) ? $pic : array();
        $this->data['i'] = 0;
        $this->myrender("resource/material_html", $this->data);
    }
   
    /*
     * 查看素材  图片
     */
    public function material_video(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
    
        $this->data["product"] = $this->mangerBelongProduct();
        $get = $this->get;
        $search_contitions = array("product_name","ad_type","resolution", 'platform');
        $search_pic = Doo::loadModel("datamodel/AdVideo",TRUE);
        $sql_string = "1=1 and ischeck=1 and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") ";
        //分页原始url
        $url = "/resource/material_video?";
        foreach($search_contitions as $value)
        {
            $this->data['result'][$value] = "";
            if(isset($get["$value"]) && $get["$value"] != "")
            {
                //安桌和IOS平台需要包含通用类型的素材
                if($value == 'platform' && in_array($get[$value], array("1", "2"))){
                    $this->data['result'][$value] = $get[$value];
                    $platformStr= implode(array(0, $get[$value]), ',');
                    $sql_string .= "and {$value} in ({$platformStr}) ";
                    $url.= "$value=".$get[$value].'&';
                }else{
                    $sql_string .= "and {$value}= '{$get[$value]}' ";
                    $this->data['result'][$value] = $get[$value];
                    $url.= "$value=".$get[$value].'&';
                }
            }
        }
        $this->data['result']['adtype'] = "";
        if(isset($get['adtype']) && $get['adtype'] != "")
        {
            $this->data['result']['adtype'] = $get['adtype'];
            $adtype = stripslashes($get['adtype']);
            $sql_string .= "and resolution in {$adtype} ";
        }
    
        $this->data['edate'] = time();
        $this->data['sdate'] = time()-7*24*3600;
        if(isset($get['sdate']) && isset($get['edate']))
        {
            $sdate = strtotime($get['sdate']." 00:00:00");
            $edate = strtotime($get['edate']." 23:59:59");
            $this->data['edate'] = $edate;
            $this->data['sdate'] = $sdate;
            $sql_string .= "and updatetime >= $sdate and updatetime <= $edate ";
            $url.= "sdate=".$get['sdate'].'&';
            $url.= "edate=".$get['edate'].'&';
        }
        //加分页
        $total = $search_pic->getcount($sql_string);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $pic=$search_pic->getList($sql_string, $limit);
        //        $pic = $search_pic->find(array("where"=>$sql_string,"asArray"=>true));
        $this->data['pic'] = (count($pic)>0) ? $pic : array();
        $this->data['i'] = 0;
        $this->myrender("resource/material_video", $this->data);
    }
    
    /*
     * 查看素材  图片
     */
    public function material_incentive_video(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限   	
        
        
        $this->data["product"] = $this->mangerBelongProduct();
        $get = $this->get;
        $search_contitions = array("product_name", 'platform');
        $search_pic = Doo::loadModel("datamodel/AdIncentiveVideo",TRUE);
        
        
        $sql_string = "1=1 and ischeck=1 and ad_product_id in(".  implode(",",Doo::session()->__get("role_productid")).") ";
        //分页原始url
        $url = "/resource/material_incentive_video?";
        foreach($search_contitions as $value)
        {
        	$this->data['result'][$value] = "";
        	if(isset($get["$value"]) && $get["$value"] != "")
        	{
                //安桌和IOS平台需要包含通用类型的素材
                if($value == 'platform' && in_array($get[$value], array("1", "2"))){
                    $this->data['result'][$value] = $get[$value];
                    $platformStr= implode(array(0, $get[$value]), ',');
                    $sql_string .= " and {$value} in ({$platformStr}) ";
                    $url.= "$value=".$get[$value].'&';
                }else{
                    $sql_string .= " and {$value}= '{$get[$value]}' ";
                    $this->data['result'][$value] = $get[$value];
                    $url.= "$value=".$get[$value].'&';
                }
        	}
        }
        
        $this->data['result']['video_name'] = "";
        if(isset($get['video_name']) && $get['video_name'] != "")
        {
        	$this->data['result']['video_name'] = $get['video_name'];
        	$video_name = trim(stripslashes($get['video_name']));
        	$sql_string .= " and video_name like '%$video_name%'";
        }
        
        $this->data['edate'] = time();
        $this->data['sdate'] = time()-7*24*3600;
        if(isset($get['sdate']) && isset($get['edate']))
        {
        	$sdate = strtotime($get['sdate']." 00:00:00");
        	$edate = strtotime($get['edate']." 23:59:59");
        	$this->data['edate'] = $edate;
        	$this->data['sdate'] = $sdate;
        	$sql_string .= " and updatetime >= $sdate and updatetime <= $edate ";
            $url.= "sdate=".$get['sdate'].'&';
            $url.= "edate=".$get['edate'].'&';
        }
        //加分页
        $total = $search_pic->getcount($sql_string);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $pic=$search_pic->getList($sql_string, $limit);
         foreach ($pic as $key=>$val){
            $size =  $this->_getFileSize($val['video_url']);
             //$result = get_headers($url, 1);
            $pic[$key]['video_size']= round($size/(1024*1024), 2).'M';
         }
//        $pic = $search_pic->find(array("where"=>$sql_string,"asArray"=>true));
        $this->data['pic'] = (count($pic)>0) ? $pic : array();
        $this->data['i'] = 0;
        $this->myrender("resource/material_incentive_video", $this->data);
    }
    
     private function  _getFileSize($videoUrl){
        $fileSize = 0;
        $temp =  explode('upload/', $videoUrl);
       if($temp[1]){
           $path = UPLOAD_PATH.$temp[1];
           $fileSize = filesize($path);
       }
         return $fileSize;
     }
    
    //为数组新增键为platform_product_name的值.即产品名称前新增平台的简称如:
    public function get_simple_platform(&$arr)
    {
        if(empty($arr))
        {
            return;
        }
        $platform_config_arr = array(0=>'(T)', 1=>"(A)", 2=>"(I)");
        foreach($arr as $key=>$item)
        {
            $arr[$key]['simple_platform']= $platform_config_arr[$item['platform']];
        }
        return;
    }
}
