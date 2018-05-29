<?php

/**
 * Description of ProductController
 *
 * @author Stephen.Feng
 */
Doo::loadController("AppDooController");

class ProductController extends AppDooController {

    private $prodcutmodel;
    private   $mVideoClickTypeName = array('video_inner_install_manage' => '立即下载',
        'video_toward_market'=>'跳转市场应用',
        'video_inner_url_open'=>'跳转系统默认浏览器',
        'video_out_url_open'=>'跳转自建浏览器',
        'video_ad_list_open'=>'打开列表广告',
        'video_user_action_value'=>'自定义动作',
    );

    public function __construct(){
        parent::__construct();
        $this->prodcutmodel = Doo::loadModel("AdProducts", TRUE);
    }

    public function lists() {//产品列表
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $keyword = $this->get["keyword"];
        $platform=$this->get["platform"];
        $url = "/product/lists?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        $total = $this->prodcutmodel->getCount($keyword,$platform);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $this->data["products"] = $this->prodcutmodel->productlist($keyword,$platform,$limit);
        $this->data["platform"]=$platform;
        $this->data["keyword"]=$keyword;
        //print_r($this->data);
        $this->myrender("product/list", $this->data);
    }
    //弹出产品列表窗口
    public function showProductlitsPop(){
        $keyword = $this->get["keyword"];
        $platform=$this->get["platform"];
        $this->data["keyword"] = $keyword;
        $productList = $this->prodcutmodel->productlist($keyword, $platform);
        $productList = $this->fillVideoClickType($productList);
        $this->get_simple_platform($productList);
        $this->data["products"] = $productList;
        
        $this->myRenderWithoutTemplate("product/pop",$this->data);
    }
    
    private function fillVideoClickType($productList){
        foreach ($productList as $key =>$val){
            $videoClickObject = json_decode($val['json_conf'],  true);
            $videoClickObject = $videoClickObject['videoclictypeobj'];
            $videoKey =  array_keys($videoClickObject);
            $productList[$key]['video'] = '无动作';
            if($videoKey[0]){
                $productList[$key]['video'] = $this->mVideoClickTypeName[$videoKey[0]];
            }
    
        }
        return $productList;
    }
    
    public function add() {
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["ad_add_pop"] ="/pop/add";
        $this->data["ad_edit_pop"] ="/pop/showad";
        $this->data["ad_limit_item"] =Doo::conf()->AD_LIMIT_STAT_ITEM;
        $this->myrender("product/add", $this->data);
    }
    public function edit() {
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $productid = $this->get["productid"];
        $this->data["products"] = $this->prodcutmodel->view($productid);
        $adProductAcountingModel = Doo::loadModel("datamodel/AdProductAcounting", TRUE);
        $this->data['ad_product_acounting'] = $adProductAcountingModel->getRecentlyAccounting($productid);
        $this->data["ad_add_pop"] ="/pop/add";
        $this->data["ad_edit_pop"] ="/pop/showad";
        $this->data["ad_limit_item"] =Doo::conf()->AD_LIMIT_STAT_ITEM;
        //print_r($this->data);
        $this->myrender("product/detail", $this->data);
    }
    /*编辑产品名称*/
    public function reedit() {
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_MANGER_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $productid = $this->get["productid"];
        $this->data["products"] = $this->prodcutmodel->view($productid);
        
        $orderModel = Doo::loadModel("datamodel/AdOrder", TRUE);
        $this->data['orders'] = $orderModel->getOrdersByPid($productid);
        //print_r($this->data);
        $this->myrender("product/readd", $this->data);
    }

    public function del(){
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $productid = $this->get["productid"];
        $this->prodcutmodel->del($productid);
        $limit = Doo::loadModel("AdLimit", TRUE);
        $keys=array($productid."_0_adinfo",$productid."_1_adinfo",$productid."_product");
        $this->deleter($keys,"CACHE_REDIS_SERVER_1");
        $limit->del($productid);
        $this->userLogs(array('msg' => json_encode(array('product_id' => $productid)), 'title' => '产品列表', 'action' => 'delete'));
        $this->redirect("lists", "删除成功");
    }
//    public function redel(){
//        # START 检查权限
//        if (!$this->checkPermission(PRODUCT, PRODUCT_MANGER_EDIT)) {
//            $this->displayNoPermission();
//        }
//        # END 检查权限
//        $productid = $this->get["productid"];
//        $this->prodcutmodel->del($productid);
//        $limit = Doo::loadModel("AdLimit", TRUE);
//        $keys=array($productid."_0_adinfo",$productid."_1_adinfo",$productid."_product");
//        $this->deleter($keys,"CACHE_REDIS_SERVER_1");
//        $limit->del($productid);
//        $this->userLogs(array('msg' => json_encode(array('product_id' => $productid)), 'title' => '产品列表', 'action' => 'delete'));
//        $this->redirect("relist", "删除成功");
//    }
    /*
     * 删除广告
     */
    public function addel() {
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $ad_info_id = $this->get["ad_info_id"];
        $type = $this->get["type"];
        $subtype=$this->get["subtype"];
        $scrrent_type=$this->get["screen_type"];
        $ad=Doo::loadModel("Ad",TRUE);
        $ad->delad($ad_info_id,$type,$subtype,$scrrent_type="");
        $this->userLogs(array('msg' => json_encode($this->get), 'title' => '产品列表-广告内容', 'action' => 'delete'));
        $this->redirect("lists", "删除成功");
    }
    /*
     *预建广告 
     */
    public function readd(){
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_MANGER_EDIT)) {
            $this->displayNoPermission();
        }
        $this->myrender("product/readd", $this->data);
    }
    /*
     *产品列表
     */
    public function relist(){
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_MANGER_EDIT)) {
            $this->displayNoPermission();
        }
        $keyword = $this->get["keyword"];
        $platform=$this->get["platform"];
        $customer = $this->get['customer'];
        $url = "/product/relist?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        if($platform)
        {
            $url .= "platform=".$platform.'&';
        }
        if($customer)
        {
            $url .= "customer=".$customer.'&';
        }
        $total = $this->prodcutmodel->getCount($keyword, $platform, $customer);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $this->data["products"] = $this->prodcutmodel->productlist($keyword,$platform, $limit, $customer);
        $this->data["platform"]=$platform;
        $this->data["keyword"]=$keyword;
        $this->data["customer"]=$customer;
        $this->myrender("product/relist", $this->data);
    }
    
    /*预建广告保存*/
    public function reupdsave(){
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $product_id = $this->post["product_id"];
        $product_name = trim($this->post["pname"]);
        $platform = $this->post["platform"];
        $appkey=$this->post["pappkey"];
        $customer = $this->post["customer"];
        $star = $this->post['star'];
        $playering = $this->post['playering'];
        $promote_type = $this->post['promote_type'];
        
             
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        if(empty($product_name))
        {
            $this->redirect("javascript:history.go(-1)","操作失败,产品名不能为空");
        }
        
        if(empty($product_id)){
            $existProductName=$product->getOne(array("select"=>"*","where"=>"product_name='$product_name' and platform='$platform' ","asArray"=>true));
            if($existProductName){
                $this->redirect("javascript:history.go(-1)","操作失败,该平台的产品名已存在");
            }
        }
        
        if(empty($product_id)){
            $existAppkey=$product->getOne(array("select"=>"*","where"=>"appkey='$appkey'","asArray"=>true));
            if($existAppkey){
                $this->redirect("javascript:history.go(-1)","操作失败,appkey已存在");
            }
        }
        
        $product->platform=$platform;
        $product->product_name=$product_name;
        $product->publishid=$customer;
        $product->star= $star;
        $product->playering= $playering;
        $product->promote_type= $promote_type;
        
        $session=Doo::session()->__get("admininfo");
        $product->oprator=$session["username"];
        $product->updated=time();
        try{
            if(empty($product_id)){
                $product->appkey=$this->post["pappkey"];
                $product->created=time();
                $product_id=$product->insert();
            }else{
                $product->id=$product_id;
                $product->update();
            }
            
            if(!empty($this->post["orderid"]) && is_array($this->post["orderid"])){
                $orderModel = Doo::loadModel("datamodel/AdOrder", TRUE);
                foreach ($this->post["orderid"] as $k=>$v){
                    $data["pid"]=$product_id;
                    $data["oid"]=$this->post["oid"][$k];
                    $data["orderid"]=$this->post["orderid"][$k];
                    $data["agreementid"]=$this->post["agreementid"][$k];;
                    $data["pos_key_type"]=$this->post["pos_key_type"][$k];
                    $data["startdate"]=  strtotime($this->post["startdate"][$k]);
                    $data["enddate"]=   strtotime($this->post["enddate"][$k]);
                    $data["paymethod"]=$this->post["paymethod"][$k];
                    $data["price"]=$this->post["price"][$k];
                    $orderModel->upd_order($data["oid"],$data);
                }
            }

            $sql="update mobgi_ads.ad_apk set product_name='".$product_name."'  where ad_product_id=${product_id}";
            Doo::db()->query($sql);
            $sql="update mobgi_ads.ad_pic set product_name='".$product_name."'  where ad_product_id=${product_id}";
            Doo::db()->query($sql);
            $sql="update mobgi_ads.ad_text set product_name='".$product_name."'  where ad_product_id=${product_id}";
            Doo::db()->query($sql);
            $sql="update mobgi_ads.ads_product_info set product_name='".$product_name."'  where id=${product_id}";
            Doo::db()->query($sql);
            $this->redirect("/product/relist","操作成功");
        }  catch (Exception $e){
            $this->redirect("javascript:history.go(-1)","操作失败");
        }
    }
    public function upd() {
        # START 检查权限
        if (!$this->checkPermission(PRODUCT, PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $product_id = $this->post["product_id"];
        $pname = $this->post["pname"];
        $pappkey = $this->post["pappkey"];
        $picon = $this->post["picon"];
        $pdesc = $this->post["pdesc"];
        $purl = $this->post["purl"];
        $clictypeobj = stripslashes($this->post["clictypeobj"]);
        $pversion = $this->post["product_version"];
        $package = $this->post["ppackage"];
        $stat_limit = stripslashes($this->post["stat_limit"]);
        $stat_plan = stripslashes($this->post["stat_plan"]);
        $ad_info_id =empty($this->post["ad_info_id"])?"":$this->post["ad_info_id"];
        $ad_state = empty($this->post["state"])?"":$this->post["state"];
        $ad_rate = empty($this->post["rate"])?"":$this->post["rate"];
        $ad_type = empty($this->post["ad_type"])?"":$this->post["ad_type"];
        $ad_subtype = empty($this->post["ad_subtype"])?"":$this->post["ad_subtype"];
        $screen_type = empty($this->post["screen_type"])?"":$this->post["screen_type"];


        $product = Doo::loadModel("AdProducts", TRUE);
        if(empty($product_id)){
            $product_id =$product->upd($pname, $picon, $pdesc, $purl, $clictypeobj, $pversion, $package,$pappkey);
            if (empty($product_id)) {
                $this->redirect("javascript:history.go(-1)","添加失败");
            }
        }else{
            $upproduct=$product->upd($pname, $picon, $pdesc, $purl, $clictypeobj, $pversion, $package,$pappkey,$product_id);
            if (!$upproduct) {
                $this->redirect("javascript:history.go(-1)","更新失败");
            }
        }

        $limit = Doo::loadModel("AdLimit", TRUE);
        $limit->upd($stat_limit, $stat_plan, $product_id); //保存导量信息

        $ad = Doo::loadModel("Ad", TRUE);
        if(!empty($ad_info_id)){
            foreach($ad_info_id as $k=>$v){
                $ad->updAdStateRate($v,$product_id,$ad_state[$k],$ad_rate[$k],$ad_type[$k]);
                $key[]=$product_id."_".$ad_type[$k]."_adinfo";
                if($ad_typep[$k]==0){
                    $key[]=$v."_".$ad_subtype[$k]."_0"."_notembeddedinfo";
                    $key[]=$v."_".$ad_subtype[$k]."_1"."_notembeddedinfo";
                    $key[]=$v."_".$ad_subtype[$k]."_2"."_notembeddedinfo";
                }else{
                    $key[]=$v."_".$ad_subtype[$k]."_embeddedinfo";
                }
            }
            $this->deleter($key,"CACHE_REDIS_SERVER_1");//删除redis中的数据
        }
        $this->deleter($product_id."_product","CACHE_REDIS_SERVER_1");
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => '产品列表'), $product_id);
        $this->redirect("/product/lists","更新成功");
    }
    //获取包大小
    public function packageSize() {
        $url=$this->get["url"];
        $url = parse_url($url);
        if ($fp = @fsockopen($url['host'], empty($url['port']) ? 80 : $url['port'], $error)) {
            fputs($fp, "GET " . (empty($url['path']) ? '/' : $url['path']) . " HTTP/1.1\r\n");
            fputs($fp, "Host:$url[host]\r\n\r\n");
            while (!feof($fp)) {
                $tmp = fgets($fp);
                if (trim($tmp) == '') {
                    break;
                } else if (preg_match('/Content-Length:(.*)/si', $tmp, $arr)) {
                    $this->showData(array("packagesize"=>trim($arr[1])));
                }
            }
            return null;
        } else {
            return null;
        }
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
    
    public function showOrderPop(){
        //默认没开始、结束时间时初始化开始、结束时间
        if(empty($this->post['startdate'])){
            $this->post['startdate'] = date("Y-m-d");
        }
        if(empty($this->post['enddate'])){
            $this->post['enddate'] = '2020-01-01';
        }
        $param = array();
        $param['index'] = $this->post['index'];
        $param['oid'] = $this->post['oid'];
        $param['orderid'] = $this->post['orderid'];
        $param['agreementid'] = $this->post['agreementid'];
        $param['startdate'] = date("Y-m-d",strtotime($this->post['startdate']));
        $param['enddate'] = date("Y-m-d",strtotime($this->post['enddate']));
        $param['paymethod'] = $this->post['paymethod'];
        $param['price'] = $this->post['price'];
        $this->data = $param;
        $this->myRenderWithoutTemplate("product/order_pop",$this->data);
    }
}

?>
