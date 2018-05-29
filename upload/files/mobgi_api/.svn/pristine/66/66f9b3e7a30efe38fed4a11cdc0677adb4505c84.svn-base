<?php

/**
 * Description of ProductController
 *
 * @author Stephen.Feng
 */
Doo::loadController("AppDooController");

class ReproductController extends AppDooController {
    
   private   $mVideoClickTypeName = array('video_inner_install_manage' => '立即下载',
                                                                               'video_toward_market'=>'跳转市场应用',
                                                                               'video_inner_url_open'=>'跳转系统默认浏览器',
                                                                               'video_out_url_open'=>'跳转自建浏览器',
                                                                               'video_ad_list_open'=>'打开列表广告',
                                                                               'video_user_action_value'=>'自定义动作',
   );

    private $prodcutmodel;

    public function __construct(){
        parent::__construct();
    }
    public function add() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get=$this->get;
        $adsproduct = Doo::loadModel("datamodel/AdsProductInfo", TRUE);
        $this->data["product"]=$adsproduct->view(array("id"=>$get["id"]));

        $adProductAcountingModel = Doo::loadModel("datamodel/AdProductAcounting", TRUE);
        $this->data['ad_product_acounting'] = $adProductAcountingModel->getRecentlyAccounting($get["id"]);
        $platform = $this->data['product']['platform'];
        $this->data["myproduct"] = $this->mangerBelongProduct($platform);
                
        //$this->data["ad_add_pop"] ="/pop/add";
        //$this->data["ad_edit_pop"] ="/pop/showad";
        $this->data["ad_limit_item"] =Doo::conf()->AD_LIMIT_STAT_ITEM;
        $this->myrender("reproduct/add", $this->data);
    }
    public function autoProductinfo(){
        
        $product_name=$this->post["product_name"];
        $platform=$this->post["platform"];
        $updated=  intval($this->post["updated"]);
       // $product_name=$this->get["product_name"];
        $pic=Doo::loadModel("datamodel/AdPic",TRUE);
        $picinfo=$pic->getAdPicByProductName($product_name,$updated,$platform);
        $iconinfo=$pic->getAdIconByProductName($product_name,$platform);
        $apk=Doo::loadModel("datamodel/AdApk",TRUE);
        $apkinfo=$apk->getAdApkByProductName($product_name,$platform);
        
        $text=Doo::loadModel("datamodel/AdText",TRUE);
        $textinfo=$text->getAdTextByProductName($product_name,$updated,$platform);
        
        $html=Doo::loadModel("datamodel/AdHtml",TRUE);
        $htmlinfo=$html->getAdHtmlByProductName($product_name,$updated,$platform);
        
/*         $video=Doo::loadModel("datamodel/AdVideo",TRUE);
        $videoinfo=$video->getAdVideoByProductName($product_name,$updated,$platform); */
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $productinfo = $product->getOne(array("where"=>"product_name='".$product_name."'","asArray"=>TRUE));
        $result=array("apk"=>$apkinfo,"pic"=>$picinfo,"icon"=>$iconinfo,"text"=>$textinfo,"html"=>$htmlinfo,"video"=>false,"product"=>$productinfo);
//        echo '{"apk":false,"pic":[{"id":"470","ad_product_id":"100","product_name":"oppo\u624b\u673a","pic_name":"oppo-\u7ad6\u5c4f-\u5168\u5c4f-\u65cb\u8f6c","pic_url":"http:\/\/dl2.gxpan.cn\/ad\/qt\/pp\/oppo\/1310\/mg2\/01_2_L.jpg","ad_type":"0","ad_subtype":"1","resolution":"480*800","screen_ratio":"1","color":null,"focus":null,"xuetou":null,"platform":"0","ischeck":"1","checker":"fengfangqian","owner":"fengfangqian","check_msg":"\u7cfb\u7edf\u8f6c\u5316","creattime":"1382083653","updatetime":"1382083653"},{"id":"469","ad_product_id":"100","product_name":"oppo\u624b\u673a","pic_name":"oppo-\u7ad6\u5c4f-\u534a\u5c4f-\u65cb\u8f6c","pic_url":"http:\/\/dl2.gxpan.cn\/ad\/qt\/pp\/oppo\/1310\/mg2\/01_2_M.jpg","ad_type":"0","ad_subtype":"1","resolution":"360*600","screen_ratio":"0.8","color":null,"focus":null,"xuetou":null,"platform":"0","ischeck":"1","checker":"fengfangqian","owner":"fengfangqian","check_msg":"\u7cfb\u7edf\u8f6c\u5316","creattime":"1382083629","updatetime":"1382083629"}],"icon":{"id":"34","ad_product_id":"100","product_name":"oppo\u624b\u673a","pic_name":"oppo\u624b\u673a-icon-oppo\u624b\u673a","pic_url":"http:\/\/dl2.gxpan.cn\/ad\/qt\/pp\/oppo\/1310\/mg\/00.jpg","ad_type":"10","ad_subtype":"10","resolution":"","screen_ratio":"1","color":null,"focus":null,"xuetou":null,"platform":"0","ischeck":"1","checker":"fengfangqian","owner":"fengfangqian","check_msg":"\u7cfb\u7edf\u8f6c\u5316","creattime":"1381977495","updatetime":"1382084441"},"text":[],"product":{"id":"100","product_name":"oppo\u624b\u673a","product_icon":"http:\/\/dl2.gxpan.cn\/ad\/qt\/pp\/oppo\/1310\/mg\/00.jpg","product_desc":"oppo\u624b\u673a","product_url":"http:\/\/126.am\/Dn0uF1","net_type":"1","click_type_object":"{\"direct_url\":{\"direct_url_open\":\"1\"}}","product_version":"","product_package":"","platform":"1","appkey":"0685bd9184jfhq22","created":"1381977495","oprator":null,"updated":"1382084441"}}';
        $this->showData($result);
    }
    
    public function autoProductForVedio(){
    
        $product_name=$this->post["product_name"];
        $platform=$this->post["platform"];
        $updated=  intval($this->post["updated"]);
        $video=Doo::loadModel("datamodel/AdIncentiveVideo",TRUE);
        $videoinfo=$video->getAdIncentiveVideoByProductName($product_name,$updated,$platform);
        $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $productinfo = $product->getOne(array("where"=>"product_name='".$product_name."'","asArray"=>TRUE));
        $result=array("video"=>$videoinfo,"product"=>$productinfo);
        $this->showData($result);
    }
    
    public function autoPlatform()
    {
        $platform = $this->post['platform'];
        $platform = $_POST['platform'];
//        $platform = 1;
//        $platform = 0;
//        var_dump($platform);
        $this->data["myproduct"] = $this->mangerBelongProduct($platform);
        foreach($this->data['myproduct'] as $key=>$item)
        {
            $platform_config_arr = array(0=>'(T)', 1=>"(A)", 2=>"(I)");
            $this->data['myproduct'][$key]['platform_product_name']= $platform_config_arr[$item['platform']] . $this->data['myproduct'][$key]['product_name'];
        }
        $this->showData($this->data["myproduct"]);
    }
    
    public function lists() {//产品列表
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $productmodel=Doo::loadModel("datamodel/AdsProductInfo",TRUE);
        $keyword = $this->get["keyword"];
        $platform = $this->get['platform'];
        $this->data['result']['keyword'] =    $this->get["keyword"];
        $this->data['result']['platform'] =    $this->get["platform"];
        $url = "/reproduct/lists?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        if ($platform){
            $url .= "platform=".$platform.'&';
        }
        $total = $productmodel->getCount($keyword, $platform);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $productList = $productmodel->productlist($keyword, $limit, $platform);
        $productList = $this->fillVideoClickType($productList);
        

        $this->data["products"] = $productList;
        $this->myrender("reproduct/list", $this->data);
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


    public function product_check() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->add();
            die();
        }
        $id=$this->get["id"];
        $adsproduct = Doo::loadModel("datamodel/AdsProductInfo", TRUE);
        $this->data["product"]=$adsproduct->view(array("id"=>$id));
        $this->data["myproduct"] = $this->mangerBelongProduct();
        $this->myrender("reproduct/product_check", $this->data);
    }
    
    public function upd() {
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post=$this->post;
//        var_dump($post);die;
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            $post["ischeck"]=1;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }else{
            $post["ischeck"]=0;//编辑重新审核
        }
        
     
        
        $post["owner"]=$this->data['session']["username"];
        
        $productmodel=Doo::loadModel("datamodel/AdsProductInfo",TRUE);
        $product_id=$productmodel->saveResource2product($post);
        $limit = Doo::loadModel("datamodel/AdsProductLimit", TRUE);
        $stat_limit = stripslashes($post["stat_limit"]);
        $stat_plan = stripslashes($post["stat_plan"]);
        $limit->upd($stat_limit, $stat_plan, $product_id); //保存导量信息
        
        //保存最近三个月的结算方式与价格(由于月份是动态变化的,所以需要先拼出post上来的key的值,再根据key的值获取post的数据.
        $nextMonth = date('Ym', strtotime("+1 month"));
        $thisMonth = date('Ym');
        $lastMonth = date('Ym', strtotime("-1 month"));
        $account_way_nextmonth_key = 'account_way_select_'. $this->getMonth($nextMonth);
        $account_way_thismonth_key = 'account_way_select_'. $this->getMonth($thisMonth);
        $account_way_lastmonth_key = 'account_way_select_'. $this->getMonth($lastMonth);
        //默认CPA,,核算方式1:CPM 2:CPC 3:CPA 4:CPI,5:CPD,6:CPS
        $account_way_nextmonth_value = empty($post[$account_way_nextmonth_key])?'3':$post[$account_way_nextmonth_key];
        $account_way_thismonth_value = empty($post[$account_way_thismonth_key])?'3':$post[$account_way_thismonth_key];
        $account_way_lastmonth_value = empty($post[$account_way_lastmonth_key])?'3':$post[$account_way_lastmonth_key];
        $account_price_nextmonth_key = 'account_price_'. $this->getMonth($nextMonth);
        $account_price_thismonth_key = 'account_price_'. $this->getMonth($thisMonth);
        $account_price_lastmonth_key = 'account_price_'. $this->getMonth($lastMonth);
        $account_price_nextmonth_value = (empty($post[$account_price_nextmonth_key])?'3.00':$post[$account_price_nextmonth_key]) * 100.0;
        $account_price_thismonth_value = (empty($post[$account_price_thismonth_key])?'3.00':$post[$account_price_thismonth_key]) * 100.0;
        $account_price_lastmonth_value = (empty($post[$account_price_lastmonth_key])?'3.00':$post[$account_price_lastmonth_key]) * 100.0;
        $adProductAcountingModel = Doo::loadModel("datamodel/AdProductAcounting", TRUE);
        $result_nextmonth = $adProductAcountingModel->saveAccounting($product_id, $nextMonth, $account_way_nextmonth_value, $account_price_nextmonth_value);
        $result_thismonth = $adProductAcountingModel->saveAccounting($product_id, $thisMonth, $account_way_thismonth_value, $account_price_thismonth_value);
        $result_lastmonth = $adProductAcountingModel->saveAccounting($product_id, $lastMonth, $account_way_lastmonth_value, $account_price_lastmonth_value);
        if(!$result_nextmonth || !$result_thismonth || !$result_lastmonth){
            $this->redirect("/reproduct/lists","保存最近三个月的结算方式失败");
        }
        
      
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => '产品列表', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $product_id);
        if($this->checkPermission(RESOURCE, RESOURCE_CHECK)){
            try{
                $productmodel->check_save($post);
            }  catch (Exception $e){
                $this->redirect("/reproduct/lists","更新至业务表失败");
            }
        }
        $this->redirect("/reproduct/lists","更新成功");
    }
    public function check_save(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_CHECK)) {
            $this->displayNoPermission();
        }else{
            $post=$this->post;
            $post["checker"]=$this->data['session']["username"];
            $post["check_msg"]="总理特批";
        }
        # END 检查权限
        $post["owner"]=$this->data['session']["username"];
        $product=Doo::loadModel("datamodel/AdsProductInfo",TRUE);
        try{
            $product->saveResource2product($post);//先保存修改的信息
            $product->check_save($post);
        }catch (Exception $e){
            $this->redirect("/resource/checklist","更新失败");
        }
        $this->redirect("/resource/checklist","更新成功");
    }
//    public function addel(){
//        # START 检查权限
//        if (!$this->checkPermission(RESOURCE, RESOURCE_EDIT)) {
//            $this->displayNoPermission();
//        }
//        # END 检查权限
//        $get=$this->get;
//        $adid=$get["ad_info_id"];
//        $rid=$get["rid"];
//        $type=$get["type"];
//        $ads=Doo::loadModel("datamodel/AdsInfo",TRUE);
//        $ads->id=$adid;
//        $ads->delete();
//        if($type==1){
//            $pic=Doo::loadModel("datamodel/AdPic",TRUE);
//            $pic->id=$rid;
//            $pic->delete();
//        }else if($type==2){
//            $text=Doo::loadModel("datamodel/AdText",TRUE);
//            $text->id=$rid;
//            $text->delete();
//        }
//    }
//    public function updateads(){
//        $get=$this->get;
//        $ads=Doo::loadModel("datamodel/AdsInfo",TRUE);
//        try {
//            $ads->updteads($get);
//        } catch (Exception $exc) {
//            $this->showMsg("更新失败");
//        }
//        $this->showMsg("更新成功");
//    }
    public function get_resource(){
        # START 检查权限
        if (!$this->checkPermission(RESOURCE, RESOURCE_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $id=$this->post["id"];
        $rtype=$this->post["rtype"];
        $picinfo=false;
        if($rtype==1){//图片素材库
            $pic=Doo::loadModel("datamodel/AdPic",TRUE);
            $picinfo[0]=$pic->getAdpic($id);
        }
        $textinfo=false;
        if($rtype==2){//文案素材库
            $text=Doo::loadModel("datamodel/AdText",TRUE);
            $textinfo[0]=$text->getAdtext($id);
        }
        $htmlinfo=false;
        if($rtype==4){//网页素材库
            $html=Doo::loadModel("datamodel/AdHtml",TRUE);
            $htmlinfo[0]=$html->getAdhtml($id);
        }
        
        $videoinfo=false;
        if($rtype == 6){//视频素材库
            $video=Doo::loadModel("datamodel/AdIncentiveVideo",TRUE);
            $videoinfo[0]=$video->getAdIncentivevideo($id);
        }
        $apkinfo=false;
        $iconinfo=false;
        $productinfo=false;
        $result=array("apk"=>$apkinfo,"pic"=>$picinfo,"html"=>$htmlinfo,"video"=>$videoinfo,"icon"=>$iconinfo,"text"=>$textinfo,"product"=>$productinfo);
        $this->showData($result);
    }
    
    /**
     * 把年月转成月如:201409 201410这样的字符串转成9 10
     * @param type $year_month
     * @return type
     */
    public function getMonth($year_month){
        $month = substr($year_month, 4);
        return intval($month);
    }
    
}

?>
