<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-5-15 11:36:06
 * $Id: ImplantableController.php 62100 2015-5-15 11:36:06Z hunter.fang $
 */

Doo::loadController("AppDooController");

class ImplantableController extends AppDooController {
    
    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
    }
    
    
    /**
     * 应用列表
     */
    public function applist(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_APP_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/implantable/applist?";
        if (isset($params['appname']) && $params['appname']) {
            $whereArr['appname'] = $params['appname'];
            $url .= "appname=".$params['appname']."&";
        }else{
            $params['appname'] = '';
        }
        
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        
        // 分页
        $total = $this->appModel->records($whereArr);
        
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->appModel->findAll($whereArr);
        
        $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        foreach($this->data['result'] as $k=>$a){
            $this->data['result'][$k]['blocknuminfo'] = $this->blockModel->getBlockNumInfoStr($a['appkey']);
        }
        $this->data['params'] = $params;
        $this->myrender('implantable/applist', $this->data);
    }
    
    /**
     * 新增应用
     */
    public function appedit(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $appid = $this->get["id"];
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        $this->data["app"] = $this->appModel->view($appid);
        
        $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        $this->data['blocks'] = $this->blockModel->getBlocksByAppkey($this->data['app']['appkey']);
        $this->myrender('implantable/appedit', $this->data);
    }
    
    /**
     * 保存应用
     */
    public function appsave()
    {
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;


        if (empty($post['appname'])){
            $this->redirect("javascript:history.go(-1)","请填写应用名称");
        }
        
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        
        if (!$post['id']){ //新建
            $checkAppName = $this->appModel->records(array('full_appname' => $post['appname']));
            if ($checkAppName > 0){
                $this->redirect("javascript:history.go(-1)","应用名称已存在，请用其它应用名");
            }
        }
//        var_dump($post);die;
        $id=$this->appModel->upd($post['id'], $post);//更新应用信息
        if(!empty($post["blockkey"]) && is_array($post["blockkey"])){
            $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
            foreach ($post["blockkey"] as $k=>$v){
                $data['blockid'] = $post["blockid"][$k];
                $data["appkey"] = $post['appkey'];
                $data["inapp"]=$post["inapp"][$k];
                $data["blockkey"]=$post["blockkey"][$k];
                $data["blockname"]=$post["blockname"][$k];
                $data["source_type"]=$post["source_type"][$k];
                $data["status"]=$post["status"][$k];
                $this->blockModel->upd_block($post["blockid"][$k],$data);
            }
            //广告类型:新增,修改,删除时删除对应应用的block key  "BLOCK_" .$appkey;
            $this->blockModel->del_blockcache($post['appkey']);
        }
        
        //更新,删除应用时删除对应应用APPKEY的key redis中的值
        $this->appModel->del_appcache_by_appkey($post['appkey']);

        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '应用列表', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $post['id']);
        
        if($post['id']){
            $this->redirect("/implantable/applist","更新成功！");
        }else{
            $this->redirect("/implantable/applist","添加成功！");
        }
    }
    
    /**
     * 设置应用开启/关闭状态
     * @return type
     */
    public function setAppState(){
        # START 检查权限
        $get = $this->get;
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        if (empty($get['appid'])  && !in_array($get['state'], array("0", "1"))){
            echo json_encode(array('code' => 1, 'msg' => '参数错误'));
            return;
        }
        //逻辑校验
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        $appinfo = $this->appModel->findOne(array('id'=>$get['appid']));
        if($appinfo['state'] == $get['state']){
            echo json_encode(array('code' => 0, 'msg' => '状态未变更,请刷新页面!'));
            return;
        }

        //更新应用信息
        $this->appModel->updateAppState($appinfo['appkey'], $get['state']);
        
        //更新,删除应用时删除对应应用APPKEY的key redis中的值
        $this->appModel->del_appcache_by_appkey($appinfo['appkey']);
        
//        //关闭这个应用下的所有广告类型
//        if($get['state'] == 0){
//            $this->appModel->setBlockOffAndSourceOffByAppkey($appinfo['appkey']);
//        }
        
        //设置邮件
        $appname = $appinfo['appname'];
        $by_email_name = $this->data['session']['e_name'];
        $state = $get['state'] == 1?"开启":"关闭";
        $mailtemplate=Doo::conf()->mailtemplate;
        $subject=sprintf($mailtemplate["appstatechange"]["title"],$appname,$by_email_name, $state);
        $body = $mailtemplate["appstatechange"]["body"];
        $result = $this->sendEmail($mailtemplate["appstatechange"]['tomailers'], $subject, $body);
        echo json_encode(array('code' => 0, 'msg' => '操作成功'));
    }
    
    /**
     * 软删除广告管理
     */
    public function appdelete(){
        # START 检查权限
        $get = $this->get;
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        //逻辑校验
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        $appinfo = $this->appModel->findOne(array('id'=>$get['id']));
        $result = $this->appModel->softDelApp($appinfo['appkey']);
        
        //更新,删除应用时删除对应应用APPKEY的key redis中的值
        $this->appModel->del_appcache_by_appkey($appinfo['appkey']);
        
        if($result){
            $this->redirect("javascript:history.go(-1)","删除成功！");
        }else{
            $this->redirect("javascript:history.go(-1)","删除失败！");
        }
    }
    
    
    /**
     * 软删除广告管理
     */
    public function blockdelete(){
        # START 检查权限
        $get = $this->get;
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        //逻辑校验
        $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        $blockinfo = $this->blockModel->findOne(array('id'=>$get['id']));
        $result = $this->blockModel->softDelBlock($blockinfo['blockkey']);
        
        //广告类型:新增,修改,删除时删除对应应用的block key  "BLOCK_" .$appkey;
        $this->blockModel->del_blockcache($blockinfo['appkey']);
        
        //软删除广告位下的所有素材
        $sourceModel = Doo::loadModel("datamodel/implantable/IptadSource", TRUE);
        $sourceModel->delSourceByBlockkey($blockinfo['blockkey']);
        
        if($result){
            $this->redirect("javascript:history.go(-1)","删除成功！");
        }else{
            $this->redirect("javascript:history.go(-1)","删除失败！");
        }
    }
    
    public function setBlockState()
    {
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        if (empty($get['blockid'])  && !in_array($get['status'], array("0", "1"))){
            echo json_encode(array('code' => 1, 'msg' => '参数错误'));
            return;
        }
        //逻辑校验
        $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        $blockinfo = $this->blockModel->findOne(array('id'=>$get['blockid']));
        if($blockinfo['status'] == $get['status']){
            echo json_encode(array('code' => 0, 'msg' => '状态未变更,请刷新页面!'));
            return;
        }

        //更新应用信息
        $this->blockModel->updateBlockState($blockinfo['blockkey'], $get['status']);
        
        if($get['status'] == 0){
            //软删除广告位下的所有素材
            $sourceModel = Doo::loadModel("datamodel/implantable/IptadSource", TRUE);
            $sourceModel->setSourceOffByBlockkey($blockinfo['blockkey']);
        }
        
        //广告类型:新增,修改,删除时删除对应应用的block key  "BLOCK_" .$appkey;
        $this->blockModel->del_blockcache($blockinfo['appkey']);
        echo json_encode(array('code' => 0, 'msg' => '操作成功'));
    }
    
    /**
     * 客户列表
     */
    public function publishlist(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PUBLICSH_VIEW)) {
            $this->displayNoPermission();
        }
        $this->publishModel = Doo::loadModel("datamodel/implantable/IptadPublish", TRUE);
        $keyword = $this->get["keyword"];
        $url = "/implantable/publishlist?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        $total = $this->publishModel->getCount($keyword);
        
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $this->data["publishs"] = $this->publishModel->publishlist($keyword,$limit);
        $this->data["keyword"]=$keyword;
        $this->myrender("implantable/publishlist", $this->data);
    }
    
    /**
     * 新增客户
     */
    public function publishadd(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PUBLICSH_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $this->myrender("implantable/publishadd", $this->data);
    }
    
    /**
     * 保存客户信息
     */
    public function publishsave(){
        
         # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PUBLICSH_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $post = $_POST;
        //参数校验        
        $compay = $this->removeAllXss($post['compay']);
        if($compay != $post['compay']){
            $this->redirect("javascript:history.go(-1)","公司名称参数错误！");
        }
        
        if(empty($compay)){
            $this->redirect("javascript:history.go(-1)","公司名称不能为空！");
        }
        
        if(empty($post['tel'][1]) && empty($post['conact'][1])){
            unset($post['tel'][1]);
            unset($post['conact'][1]);
        }
        
        $publishid = intval($post['id']);
        $conact= json_encode($this->removeAllXss($post['conact']));
        $tel= json_encode($this->removeAllXss($post['tel']));
        $address = $this->removeAllXss($post['address']);
        
        $this->publishModel = Doo::loadModel("datamodel/implantable/IptadPublish", TRUE);
        
        if(empty($publishid)){
            $result = $this->publishModel->addPublish($compay, $conact, $tel,  $address);
            if($result){
                $this->redirect("/implantable/publishlist","添加成功！");
            }else{
                $this->redirect("/implantable/publishlist","添加失败！");
            }
        }else{
            $result = $this->publishModel->updatePublish($publishid, $compay, $conact, $tel,  $address);
            if($result){
                $this->redirect("/implantable/publishlist","更新成功！");
            }else{
                $this->redirect("/implantable/publishlist","更新失败！");
            }
        }
    }
    
    /**
     * 删除客户
     */
    public function publishdel(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PUBLICSH_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $this->publishModel = Doo::loadModel("datamodel/implantable/IptadPublish", TRUE);
        
        $publishId = intval($this->get["publishid"]);
        if(empty($publishId)){
            $this->redirect("javascript:history.go(-1)","参数错误！");
        }
        $result = $this->publishModel->softDelPublish($publishId);
        if($result){
            $this->redirect("javascript:history.go(-1)","删除成功！");
        }else{
            $this->redirect("javascript:history.go(-1)","删除失败！");
        }
    }
    
    /**
     * 编辑客户信息
     */
    public function publishedit(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PUBLICSH_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $publishId = intval($this->get["publishid"]);
        if(empty($publishId)){
            $this->redirect("javascript:history.go(-1)","参数错误！");
        }
        
        $this->publishModel = Doo::loadModel("datamodel/implantable/IptadPublish", TRUE);
        
        $publishInfo = $this->publishModel->getPublishByid($publishId);
        
        if(!empty($publishInfo)){
            $publishInfo['conact'] = json_decode($publishInfo['conact']);
            $publishInfo['tel'] = json_decode($publishInfo['tel']);
        }
        
        $publishInfo['contact_num'] = max(array(count($publishInfo['conact']), count($publishInfo['tel'])));
        $this->data['publish'] = $publishInfo;
        $this->myrender("implantable/publishadd", $this->data);
    }
    
    /**
     * 公司列表JSON数据
     */
    public function autoPublish()
    {
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PUBLICSH_VIEW)) {
            $this->displayNoPermission();
        }
        #
        $this->publishModel = Doo::loadModel("datamodel/implantable/IptadPublish", TRUE);
        $publishsTmp = $this->publishModel->publishlist();
        $publishs = array();
        if($publishsTmp){
            foreach($publishsTmp as $publish){
                $tmp = array();
                $tmp['id'] = $publish['id'];
                $tmp['compay'] = $publish['compay'];
                $publishs[] = $tmp;
            }
        }
        $this->showData($publishs);
    }
    
    /**
     * 产品列表
     */
    public function productlist(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PRODUCT_VIEW)) {
            $this->displayNoPermission();
        }
        $keyword = $this->get["keyword"];
        $url = "/implantable/productlist?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        
        $this->prodcutmodel = Doo::loadModel("datamodel/implantable/IptadProduct", TRUE);
        $total = $this->prodcutmodel->getCount($keyword);
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $this->data["products"] = $this->prodcutmodel->productlist($keyword, $limit);
        $this->data["keyword"]=$keyword;
        
        //获取每个产品的广告类型
        if(!empty($this->data['products'])){
            //获取广告素材
            $sourceModel = Doo::loadModel("datamodel/implantable/IptadSource", TRUE);
            //获取应用及应用的广告类型
            $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
            $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
            
            foreach($this->data["products"] as $key=>$product){
                $sourceArr = array();
                $sources = $sourceModel->getSourcesByPid($product['pid']);
                if(!empty($sources)){
                    foreach($sources as $source){
                        //只展示开启的
                        if($source['status'] == 1){
                            $tmpSource = array();
                            $blockInfo = $this->blockModel->getBlockByBlockkey($source['blockkey']);
                            $tmpSource['blockname'] = $blockInfo['blockname'];
                            $appInfo = $this->appModel->findOne(array('appkey'=>$blockInfo['appkey']));
                            $tmpSource['appname'] = $appInfo['appname'];
                            $sourceArr[] = $tmpSource;
                        }
                    }
                }
                $this->data["products"][$key]['sources'] = $sourceArr;
            }
        }
        
        $this->myrender("implantable/productlist", $this->data);
        exit;
    }
    
    /**
     * 新增产品
     */
    public function productadd(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        #
        
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        $applist = $this->appModel->findAll();
        $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        
        $blockData = array();
        if(!empty($applist)){
            foreach($applist as $appItem){
                $tmp = array();
                $tmpBlockData = $this->blockModel->getBlocksByAppkey($appItem['appkey']);
                $tmp['blocks'] = array();
                if(!empty($tmpBlockData)){
                    foreach($tmpBlockData as $blockItem){
                        $tmpBlock = array();
                        $tmpBlock['blockkey'] = $blockItem['blockkey'];
                        $tmpBlock['blockname'] = $blockItem['blockname'];
                        $tmpBlock['inapp'] = $blockItem['inapp'];
                        $tmpBlock['source_type'] = $blockItem['source_type'];
                        $tmp['blocks'][] = $tmpBlock;
                    }
                }
                $tmp['appname'] = $appItem['appname'];
                $blockData[$appItem['appkey']]= $tmp;
            }
        }
        $blockJsonData = json_encode($blockData, true);
        $this->data['blockJsonData']= $blockJsonData;
        
        $this->myrender("implantable/productadd", $this->data);
    }
    
    /**
     * 编辑产品名称
     */
    public function productedit() {
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $productid = $this->get["productid"];
        //获取产品信息
        $this->prodcutmodel = Doo::loadModel("datamodel/implantable/IptadProduct", TRUE);
        $this->data["products"] = $this->prodcutmodel->view($productid);
        //获取订单信息
        $orderModel = Doo::loadModel("datamodel/implantable/IptadOrder", TRUE);
        $this->data['orders'] = $orderModel->getOrdersByPid($productid);
        
        //获取广告素材
        $sourceModel = Doo::loadModel("datamodel/implantable/IptadSource", TRUE);
        $this->data['sources'] = $sourceModel->getSourcesByPid($productid);

        //获取应用及应用的广告类型
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        $applist = $this->appModel->findAll();
        $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        
        $blockData = array();
        if(!empty($applist)){
            foreach($applist as $appItem){
                $tmp = array();
                $tmpBlockData = $this->blockModel->getBlocksByAppkey($appItem['appkey']);
                $tmp['blocks'] = array();
                if(!empty($tmpBlockData)){
                    foreach($tmpBlockData as $blockItem){
                        $tmpBlock = array();
                        $tmpBlock['blockkey'] = $blockItem['blockkey'];
                        $tmpBlock['blockname'] = $blockItem['blockname'];
                        $tmpBlock['inapp'] = $blockItem['inapp'];
                        $tmpBlock['source_type'] = $blockItem['source_type'];
                        $tmp['blocks'][] = $tmpBlock;
                    }
                }
                $tmp['appname'] = $appItem['appname'];
                $blockData[$appItem['appkey']]= $tmp;
            }
        }
        $blockJsonData = json_encode($blockData, true);
        $this->data['blockJsonData']= $blockJsonData;
        
        $this->myrender("implantable/productadd", $this->data);
    }
    
     /**
     * 订单弹出框
     */
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
        $this->myRenderWithoutTemplate("implantable/order_pop",$this->data);
    }
    
    /**
     * 广告类型弹出框
     */
    public function showBlockPop(){
        //默认没开始、结束时间时初始化开始、结束时间
        $param = array();
        $param['index'] = $this->post['index'];
        $param['blockkey'] = $this->post['blockkey'];
        $param['blockname'] = $this->post['blockname'];
        $param['inapp'] = $this->post['inapp'];
        $param['source_type'] = $this->post['source_type'];
        $param['status'] = $this->post['status'];
        $this->data = $param;
        $this->myRenderWithoutTemplate("implantable/block_pop",$this->data);
    }
    
    /**
     * 广告内容弹出框
     */
    public function showSourcePop(){
        $this->appModel = Doo::loadModel("datamodel/implantable/IptadApp", TRUE);
        $applist = $this->appModel->findAll();
        $this->blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        
        $blockData = array();
        if(!empty($applist)){
            foreach($applist as $appItem){
                $tmp = array();
                $tmpBlockData = $this->blockModel->getBlocksByAppkey($appItem['appkey']);
                $tmp['blocks'] = array();
                if(!empty($tmpBlockData)){
                    foreach($tmpBlockData as $blockItem){
                        $tmpBlock = array();
                        $tmpBlock['blockkey'] = $blockItem['blockkey'];
                        $tmpBlock['blockname'] = $blockItem['blockname'];
                        $tmpBlock['inapp'] = $blockItem['inapp'];
                        $tmpBlock['source_type'] = $blockItem['source_type'];
                        $tmp['blocks'][] = $tmpBlock;
                    }
                }
                $tmp['appname'] = $appItem['appname'];
                $blockData[$appItem['appkey']]= $tmp;
            }
        }
        
        $blockJsonData = json_encode($blockData, true);
        
        $param = array();
        $param['blockJsonData'] = $blockJsonData;
        $param['appkey'] = $this->post['appkey'];
        $param['blockkey'] = $this->post['blockkey'];
//        $param['index'] = $this->post['index'];
//        $param['blockkey'] = $this->post['blockkey'];
//        $param['blockname'] = $this->post['blockname'];
//        $param['inapp'] = $this->post['inapp'];
//        $param['source_type'] = $this->post['source_type'];
//        $param['status'] = $this->post['status'];
        $this->data = $param;
        $this->myRenderWithoutTemplate("implantable/source_pop",$this->data);
    }
    
    
    /**
     * 生成订单号
     */
    public function createBlockKey(){
        Doo::loadPlugin("function");
        $orderkey=createappkey();
        die(json_encode(array("result"=>0,"key"=>$orderkey)));
    }
    
    /**
     * 保存产品信息
     */
    public function productsave(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $product_id = $this->post["product_id"];
        $pname = $this->post["pname"];
        $platform = $this->post["platform"];
        $publishid = $this->post["publishid"];
        $product=Doo::loadModel("datamodel/implantable/IptadProduct",TRUE);
                
        $pname = trim($pname);
        if(empty($pname))
        {
            $this->redirect("javascript:history.go(-1)","操作失败,产品名不能为空");
        }
        
        if(empty($product_id)){
            $existProductName=$product->getOne(array("select"=>"*","where"=>"product_name='$pname' and platform='$platform' ","asArray"=>true));
            if($existProductName){
                $this->redirect("javascript:history.go(-1)","操作失败,该平台的产品名已存在");
            }
        }
        
        $product->platform=$platform;
        $product->product_name=$pname;
        $product->publishid=$publishid;
        
        $session=Doo::session()->__get("admininfo");
        $product->oprator=$session["username"];
        $product->updated=time();
        try{
            if(empty($product_id)){
                $product->created=time();
                $product_id=$product->insert();
            }else{
                $product->pid=$product_id;
                $product->update();
            }
            
            //保存广告订单
            if(!empty($this->post["orderid"]) && is_array($this->post["orderid"])){
                $orderModel = Doo::loadModel("datamodel/implantable/IptadOrder", TRUE);
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
            
            //保存广告类型素材
            if(!empty($this->post["blockkey"]) && is_array($this->post["blockkey"])){
                $sourceModel = Doo::loadModel("datamodel/implantable/IptadSource", TRUE);
                foreach ($this->post["blockkey"] as $k=>$v){
                    $data["pid"]=$product_id;
                    $data["sid"]=$this->post["sid"][$k];
                    $data["blockkey"]=$this->post["blockkey"][$k];
                    $data["image_url"]=$this->post["pic_url"][$k];;
                    $data["text"]=$this->post["source_text"][$k];
                    $data["status"]=  $this->post["status"][$k];
                    $data["target_url"]=$this->post["target_url"][$k];
                    $sourceModel->upd_source($data["sid"],$data);
                }
                //素材:新增,修改,删除操作删除对应的redis key  "SOURCE_".  implode("_",$id); "SOURCE_".$id;
                $sourceModel->del_sourcecache();
            }
            //产品:修改,删除产品时删除对应的产品id key "PRODUCT_".$pid;
            $product->del_productcache($product_id);
            
            $this->redirect("/implantable/productlist","操作成功");
        }  catch (Exception $e){
            $this->redirect("javascript:history.go(-1)","操作失败");
        }
    }
    
   /**
    * 上传图片文件
    */
    public function pic_upload() {
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PRODUCT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $filepath = $this->uploadFile("pic/", "source_pic");
        if($filepath==-2){
            die(json_encode(array("code"=>-2,"msg"=>'只允许上传图片文件')));
        }
        die(json_encode(array("code"=>0,"pic_url"=>$filepath)));
    }
    
    /**
     * 渠道限制列表
     */
    public function blacklist(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_BLACKLIST_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $this->myrender("implantable/blacklist", $this->data);
    }
    
    /**
     * 获取渠道黑名单列表，输出json数据。
     */
    public function getBlacklist(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_BLACKLIST_VIEW)) {
            $this->displayNoPermission();
        }
        
        $return = array();
        $return['channel'] = array();
        $channelsArr = array();
        $blacklistModel = Doo::loadModel("datamodel/implantable/IptadBlacklist", TRUE);
        $black_list = $blacklistModel->getChannelBlacklist();
        if(!empty($black_list)){
            foreach($black_list as $black_list_item){
                if($black_list_item['type'] == 'channel'){
                    $channelArr = $blacklistModel->getChannelById(json_decode($black_list_item['value']));
                    foreach($channelArr as $key=>$value){
                        $tmpChannel = array();
                        $tmpChannel['id'] = $key;
                        $tmpChannel['name'] = $value;
                        $channelsArr[]=$tmpChannel;
                    }
                }
            }
        }
        $return['channel'] = $channelsArr;
        echo json_encode($return, true);
    }
    
    /**
     * 新增黑名单
     */
    public function blacklistEdit(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_BLACKLIST_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $channel_id = array();
        $channel = array();
        $blacklistModel = Doo::loadModel("datamodel/implantable/IptadBlacklist", TRUE);
        $black_list = $blacklistModel->getChannelBlacklist();
        if(!empty($black_list)){
            foreach($black_list as $black_list_item){
                if($black_list_item['type'] == 'channel'){
                    $channel_id = json_decode($black_list_item['value']);
                    if(!empty($channel_id)){
                        $channel = $blacklistModel->getChannelById($channel_id);
                    }
                }
            }
        }
        $this->data['channel']= $channel;
        $this->myrender("implantable/blacklistEdit", $this->data);
    }
    
    /**
     * 新增黑名单
     */
    public function blacklistSave(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_BLACKLIST_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        //屏蔽的应用，存储成json格式
        $channel_id_arr = array();
        if(!empty($post['channel_id'])){
            $channel_id_arr = $post['channel_id'];
        }
        $channel_id = json_encode($channel_id_arr);
        $type = "channel";
        $blacklistModel = Doo::loadModel("datamodel/implantable/IptadBlacklist", TRUE);
        $channel_db = $blacklistModel->getBlacklistByType($type);
        $value = $channel_id;
        $session=Doo::session()->__get("admininfo");
        $operator=$session["username"];
        if(empty($channel_db)){
            $blacklistModel->addBlacklist($type, $value, $operator);
        }else{
            $id = $channel_db['id'];
            $blacklistModel->updateBlacklist($id, $type, $value, $operator);
        }
        $blacklistInfo = $blacklistModel->getBlacklistByType($type);
        $blacklistModel->del_blacklistcache($type, $blacklistInfo['platform']);
        $this->redirect("/implantable/blacklist","保存成功！");
    }
    //配置项列表
    public function configlist(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_CONFIG_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $appname = $this->get['appname'];
        $config=Doo::loadModel("datamodel/implantable/IptadConfig",TRUE);
        $configinfo=$config->getConfigList('', '', '', $appname);
        $plan=Doo::loadModel("datamodel/implantable/IptadPlan",TRUE);
        foreach ($configinfo as $k=>$v){
            $configinfo[$k]["plan"]=$plan->getPlanProductByConfigid($v["id"]);
        }
        $this->data['result']= $configinfo;
        $this->data["appname"]=$appname;
        $this->myrender("implantable/configlist", $this->data);
    }
    //删除配置信息
    public function configDel(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get=$this->get;
        if(empty($get["id"])){
            $this->redirect("/implantable/configlist","参数错误！");
        }
        $config=Doo::loadModel("datamodel/implantable/IptadConfig",TRUE);
        try{
            $config->delConfig($get["id"]);
            $this->redirect("/implantable/configlist","删除成功！");
        }  catch (Exception $e){
            $this->redirect("/implantable/configlist","删除失败！");
        }
    }
    //新建配置项
    public function configAdd(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["mobile_make"]= array_unique(Doo::conf()->INAPP_MOBILE_MAKE);
        $this->data["carrieroperator"]= Doo::conf()->INAPP_CARRIEROPERATOR;
        $this->data["network_environment"]= Doo::conf()->INAPP_NETWORK_ENVIRONMENT;
//        $this->data["geographical_position"]= Doo::conf()->INAPP_GEOGRAPHICAL_POSITION;
        $this->data["geographical_positions"]= Doo::conf()->INAPP_GEOGRAPHICAL_POSITIONS;
        $app=Doo::loadModel("datamodel/implantable/IptadApp",TRUE);
        $this->data["applist"]=$app->findAll();
        $this->myrender("implantable/addconfig", $this->data);
    }
    public function configSave(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $config=Doo::loadModel("datamodel/implantable/IptadConfig",TRUE);
        try{
            $post["del"]=1;
            $configid=$config->addconfig($post);
        }  catch (Exception $e){
            $this->redirect("/implantable/configAdd","添加配置信息失败！");
        }
        $this->redirect("/implantable/configlist","添加成功！");
    }
    /**
     * 编辑配置项
     */
    public function editconfig()
    {
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
         # END 检查权限
        
        $config_id = intval($_GET['configid']);
        $config=Doo::loadModel("datamodel/implantable/IptadConfig",TRUE);
        $configInfo = $config->getConfig($config_id);
        $limit = Doo::loadModel("datamodel/implantable/IptadLimit",TRUE);
        $limitInfo = $limit->getLimitByConfigid($config_id);
        //把数据里面存的json数据转成数组.
        if(!empty($limitInfo['make'])){
            $limitInfo['make'] = json_decode($limitInfo['make']);
        }
        if(!empty($limitInfo['carrier'])){
            $limitInfo['carrier'] = json_decode($limitInfo['carrier']);
        }
        if(!empty($limitInfo['net_type'])){
            $limitInfo['net_type'] = json_decode($limitInfo['net_type']);
        }
        if(!empty($limitInfo['loc'])){
            $limitInfo['loc'] = json_decode($limitInfo['loc']);
        }
        
        //多个英文键值的配置处理。
        //$this->process_mobile_make_multikey($limitInfo['make']);
        //$this->process_net_enviroment_multikey($limitInfo['net_type']);
        
        $app=Doo::loadModel("datamodel/implantable/IptadApp",TRUE);
        $this->data["applist"]=$app->findAll();
        $this->data["mobile_make"]= array_unique(Doo::conf()->INAPP_MOBILE_MAKE);
        $this->data["carrieroperator"]= Doo::conf()->INAPP_CARRIEROPERATOR;
        $this->data["network_environment"]= Doo::conf()->INAPP_NETWORK_ENVIRONMENT;
        $this->data["geographical_position"]= Doo::conf()->INAPP_GEOGRAPHICAL_POSITION;
        $this->data["geographical_positions"]= Doo::conf()->INAPP_GEOGRAPHICAL_POSITIONS;
        $this->data["configInfo"] = $configInfo;
        $this->data["limitInfo"] = $limitInfo;
        
        $this->myrender("implantable/editconfig", $this->data);      
    }
    function planveiw(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PLAN_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get= $this->get;
        //搜索时间　
        if (isset($this->get['screatedate']) && $this->get['screatedate']) {
            $get["sdate"] = strtotime($this->get['screatedate']);
        }
        if (isset($this->get['ecreatedate']) && $this->get['ecreatedate']) {
            $get["edate"] = strtotime($this->get['ecreatedate']." 23:59:59");
        }
        $plan=Doo::loadModel("datamodel/implantable/IptadPlan",TRUE);
        $planlist=$plan->getConfigList($get["config_id"],$get["sdate"],$get["edate"]);
        $app=Doo::loadModel("datamodel/implantable/IptadApp",TRUE);
        $source=Doo::loadModel("datamodel/implantable/IptadSource",TRUE);
        $block=Doo::loadModel("datamodel/implantable/IptadBlock",TRUE);
        foreach ($planlist as $k =>$v){
            $planlist[$k]["blocktype"]=$block->getInappBlocksByAppkey($v["appkey"]);
            $blockinfo=  json_decode($v["block"]);
            $blockArr=array();
            foreach($blockinfo as $x=>$y){
                $blockArr[]=$source->sourceMapProduct($y);
            }
            $planlist[$k]["block"]=$blockArr;
        }
        
        $this->data["result"]=$planlist;
        $this->data["config_id"]=$get["config_id"];
        $this->myrender("implantable/planlist", $this->data);
    }
    
     function planedit_view(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $plan_id=isset($this->get["plan_id"])?$this->get["plan_id"]:0;
        $config_id=$this->get["config_id"];
        if(empty($config_id)){
            $this->redirect("/implantable/planlist","参数错误！");
        }
        $config=Doo::loadModel("datamodel/implantable/IptadConfig",True);
        $configapp=$config->getConfigApp($config_id);
        $plan=Doo::loadModel("datamodel/implantable/IptadPlan",True);
        $source=Doo::loadModel("datamodel/implantable/IptadSource",True);
        
        $result=$plan->getPlan($plan_id);
        $block=Doo::loadModel("datamodel/implantable/IptadBlock",True);
        $blockinfo=$block->getInappBlocksByAppkey($configapp["appkey"]);
        if(!empty($result)){
            $result["blockarr"]= json_decode($result["block"]);
            if(is_array($result["blockarr"])){
                foreach($result["blockarr"] as $k=>$v){
                    $result["sourcePname"][]=$source->sourceMapProduct($v);
                }
            }
        }
        
        $productlist=$source->getSourcesMapPid();
        $this->data["configapp"]=$configapp;
        $this->data["block"]=$blockinfo;
        $this->data["plan"]=$result;
        $this->data["product"]=$productlist;
        $this->data["stime"]=date("Y-m-d 00:00:00",time());
        $this->data["etime"]=date("Y-m-d 23:59:59",time());
        $this->data["config_id"]=$this->get["config_id"];
        $this->myrender("implantable/planadd",$this->data);
     }
    function plan_save(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post=$this->post;
        $plan=Doo::loadModel("datamodel/implantable/IptadPlan",True);
        $x=  json_decode($post["block"],true);
        if(!is_array($x)){
            $post["block"]=  json_encode(explode(",",$post["block"]));
        }
        
        try{
            $plan->updatePlan($post);
        }  catch (Exception $e){
            $this->redirect("/implantable/planveiw?config_id=".$post["config_id"],"操作失败！");
        }
        $this->redirect("/implantable/planveiw?config_id=".$post["config_id"],"操作成功！");
    }
    public function plan_del(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        $planid=$this->get["plan_id"];
        $configid=$this->get["config_id"];
        if(empty($planid)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        $plan=Doo::loadModel("datamodel/implantable/IptadPlan",True);
        try{
            $plan->planDel($planid);
        }  catch (Exception $e){
            $this->redirect("/implantable/planveiw?config_id=".$configid,"删除失败！");
        }
        $this->redirect("/implantable/planveiw?config_id=".$configid,"删除成功！");
    }
    public function plan_set_state(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $planid=$this->get["plan_id"];
        if(empty($planid)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        try{
            $plan=Doo::loadModel("datamodel/implantable/IptadPlan",True);
            //$whereArr["id"]=$planid;
            $state=$this->get["state"];
            //$whereArr["state"]=$state;
            //$whereArr["oprator"]=$this->data['session']["username"];
            $result=$plan->setPlanState($planid,$state);
            //删除redis里面缓存的RTB配置
            //$this->del_redis_rtb_config();
            
            //记录后台管理员操作日志
//            $referer = $_SERVER['HTTP_REFERER'];
//            $title = 'RTB导量计划状态变更';
//            $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
//            $type = $file_pre;
//            $snapsot_url = save_referer_page($referer, $file_pre);
//            $this->userLogs(array('msg' => json_encode(array('plan_id'=>$planid)), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer, 'action'=>'update'));
        }  catch (Exception $e){
           $this->showMsg("操作失败");
        }
        $this->showSucess("操作成功");
    }
    /*
     * 实时获取导量计划信息
     */
    public function plan_get_state(){
        # START 检查权限
        if (!$this->checkPermission(IMPLANTABLE, IMPLANTABLE_PLAN_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $config_id=$this->get["config_id"];
        if(empty($config_id)){
            $this->showMsg("config_id 为空");
        }
        try{
            $plan=Doo::loadModel("datamodel/implantable/IptadPlan",True);
            $result=$plan->findAll(array("config_id"=>$config_id));
            $this->showMsg($result,0);
        }  catch (Exception $e){
            $this->showMsg("拉取数据失败");
        }
    }
}

