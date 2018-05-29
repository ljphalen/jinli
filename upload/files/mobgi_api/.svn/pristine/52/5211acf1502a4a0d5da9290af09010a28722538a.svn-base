<?php
/**
 * 应用中心模型
 *
 * @author Intril.Leng
 */

Doo::loadController("AppDooController");
class AppsController extends AppDooController {

    /**
     * App模型对象
     * @var Object
     */
    private $_appModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_appModel = Doo::loadModel('Apps', TRUE);
    }

    /**
     * 显示APP列表，查询结果显示
     */
    public function index() {
        # START 检查权限
        if (!$this->checkPermission(APP, APP_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/apps/index?";
        if (isset($params['dev_id']) && $params['dev_id'] != 0) {
            $whereArr['dev_id'] = $params['dev_id'];
            $url .= "dev_id=".$params['dev_id']."&";
        }else{
            $params['dev_id'] = 0;
        }
        if (isset($params['app_name']) && $params['app_name']) {
            $whereArr['app_name'] = $params['app_name'];
            $url .= "app_name=".$params['app_name']."&";
        }else{
            $params['app_name'] = '';
        }
        if (isset($params['appdate']) && $params['appdate']) {
            $whereArr['appdate'] = $params['appdate'];
            $url .= "appdate=".strtotime($params['appdate'])."&";
        }else{
            $params['appdate'] = '';
        }
        if (isset($params['state']) && in_array($params['state'], array("0", "1"))) {
            $whereArr['state'] = $params['state'];
            $url .= "state=".$params['state']."&";
        }
        if(isset($params["ischeck"])){
            $whereArr['ischeck'] = $params['ischeck'];
            $url .= "ischeck=".$params['ischeck']."&";
        }
        //搜索时间　
        if (isset($params['screatedate']) && $params['screatedate']) {
            $whereArr['screatedate'] = strtotime($params['screatedate']);
            $url .= "screatedate=".$params['screatedate']."&";
        }
        if (isset($params['ecreatedate']) && $params['ecreatedate']) {
            $whereArr['ecreatedate'] = strtotime($params['ecreatedate']);
            $url .= "ecreatedate=".$params['ecreatedate']."&";
        }
        // 分页
        $total = $this->_appModel->records($whereArr);
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
//        $whereArr['no_apk_url'] =1;//只显示上传了应用的
        $this->data['result'] = $this->_appModel->findAll($whereArr);
        $deverloper_obj=Doo::loadModel('Developer', TRUE);
        foreach($this->data['result'] as $k=>$a){
            $pos=$this->_appModel->getPosinfoCountState($a["dev_id"],$a["app_id"]);
            $this->data['result'][$k]["pos"]=$this->_appModel->getPosCountStr($pos);
            $this->data['result'][$k]["developer"]=$deverloper_obj->findOne(array("dev_id"=>$a["dev_id"]));
        }
        $appcateList = $this->appConfig();
        $appcate = array();
        foreach ($appcateList as $sub => $items){
            $appcate += $items;
        }
        $this->data['appcate'] = $appcate;
        // 取开发者列表
        $developerModel = Doo::loadModel('Developer', TRUE);
        $developer = $developerModel->findAll(array("ischeck"=>1));
        $develList = listArray($developer, 'dev_id', 'user_name', array('0' => '所有开发人员'));
        $this->data['search'] = form_select($develList, array('name' => 'dev_id', 'value' => $params['dev_id']));
        $this->data['params'] = $params;
        // 选择模板
        if(isset($params["ischeck"]) && $params["ischeck"]==2){
            $this->myrender('app/list_check', $this->data);
        }else{
            $this->myrender('app/list', $this->data);
        }
    }

    /**
     * 编辑/添加
     */
    public function edit() {
        # START 检查权限
        if (!$this->checkPermission(APP, APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        if (isset($get['app_id']) && $get['app_id'] && is_numeric($get['app_id'])) {// 编辑
            $whereArr = array('app_id' => $get['app_id']);
            $this->data['result'] = $this->_appModel->findOne($whereArr);
            $this->data['title'] = '修改';
            // 取应用版本
            $appVersionModel = Doo::loadModel('AdAppVersions', TRUE);
            $appVersion = $appVersionModel->findAll(array('appkey' => $this->data['result']['appkey']));
            if (empty($appVersion)){
                $appVersion = array( array('app_version' => '暂无版本', 'app_id' => $get['app_id']));
            }
            $this->data['versionList'] = $appVersion;
        }else{
            $this->data['result'] = array(
                'app_id' => '', 'app_name' => '', 'dev_id' => '', 'platform' => 1,
                'appcate_id' => 0, 'state' => 0, 'app_version' => '', 'app_desc' => '','income_rate'=>Doo::conf()->INCOME_RATE,
            );
            $this->data['title'] = '添加';
        }
        $this->data['result']['appcate_name'] = "请选择应用类型";
        if ($this->data['result']['appcate_id']){
            foreach ($this->appConfig() as $sub => $item){
                if (isset($item[$this->data['result']['appcate_id']])){
                    $this->data['result']['appcate_name'] = $item[$this->data['result']['appcate_id']];
                }
            }
        }
        
        //获取广告位信息
        if(!empty($this->data['result']["dev_id"])){
            $this->data['result']["pos"]=$this->_appModel->getPosinfo($this->data['result']["dev_id"],$get["app_id"]);
        }
        // 取开发者列表
        $developerModel = Doo::loadModel('Developer', TRUE);
        $developer = $developerModel->findAll();
        $develList = listArray($developer, 'dev_id', 'email');
        $this->data['select'] = form_select($develList, array('name' => 'dev_id', 'value' => $this->data['result']['dev_id']));
        $ad_pos_type=Doo::conf()->AD_POS_TYPE;
        $ad_pos_type[""]="请选择";
        $this->data['ad_pos_type']=form_select($ad_pos_type,array('name' => 'pos_key_type_tmp'));
        // 选择模板
        $this->myrender('app/detail', $this->data);
    }
 /**
     * 编辑/添加
     */
    public function appcheckedit() {
        # START 检查权限
        if (!$this->checkPermission(APP, APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        if (isset($get['app_id']) && $get['app_id'] && is_numeric($get['app_id'])) {// 编辑
            $whereArr = array('app_id' => $get['app_id']);
            $this->data['result'] = $this->_appModel->findOne($whereArr);
            $this->data['title'] = '修改';
            // 取应用版本
            $appVersionModel = Doo::loadModel('AdAppVersions', TRUE);
            $appVersion = $appVersionModel->findAll(array('appkey' => $this->data['result']['appkey']));
            if (empty($appVersion)){
                $appVersion = array( array('app_version' => '暂无版本', 'app_id' => $get['app_id']));
            }
            $this->data['versionList'] = $appVersion;
        }else{
            $this->data['result'] = array(
                'app_id' => '', 'app_name' => '', 'dev_id' => '', 'platform' => 1,
                'appcate_id' => 0, 'state' => 0, 'app_version' => '', 'app_desc' => '','income_rate'=>Doo::conf()->INCOME_RATE,
            );
            $this->data['title'] = '添加';
        }
        $this->data['result']['appcate_name'] = "请选择应用类型";
        if ($this->data['result']['appcate_id']){
            foreach ($this->appConfig() as $sub => $item){
                if (isset($item[$this->data['result']['appcate_id']])){
                    $this->data['result']['appcate_name'] = $item[$this->data['result']['appcate_id']];
                }
            }
        }
        
        //获取广告位信息
        if(!empty($this->data['result']["dev_id"])){
            $posinfo=$this->_appModel->getPosinfo($this->data['result']["dev_id"],$get["app_id"]);
            if(!empty($posinfo)){
                $acounting=Doo::conf()->ACOUNTING_METHOD;
                foreach($posinfo as $k=>$info){
                    if(empty($info["acounting_method"])){
                        $posinfo[$k]["acounting_method"]=$acounting[$info["pos_key"]][0];
                    }
                    if(empty($info["denominated"])){
                        $posinfo[$k]["denominated"]=$acounting[$info["pos_key"]][1];
                    }
                }
            }
            $this->data['result']["pos"]=$posinfo;
        }
        // 取开发者列表
        $developerModel = Doo::loadModel('Developer', TRUE);
//        $developer = $developerModel->findAll();
//        $develList = listArray($developer, 'dev_id', 'email');
//        $this->data['select'] = form_select($develList, array('name' => 'dev_id', 'value' => $this->data['result']['dev_id']));
        $developer = $developerModel->findOne(array('dev_id'=>$this->data['result']['dev_id']));
        $this->data['dev_href'] = "<a href='/developer/edit?dev_id=".$this->data['result']['dev_id']."' target='blank'>".$developer['email']."</a>";
        $ad_pos_type=Doo::conf()->AD_POS_TYPE;
        $ad_pos_type[""]="请选择";
        $this->data['ad_pos_type']=form_select($ad_pos_type,array('name' => 'pos_key_type_tmp'));
        //获取所有配置项信息
        $adconfigObj=Doo::loadModel("AdConfigs",TRUE);
        $this->data['adconfig']=$adconfigObj->findAll();
        $adconfigtagObj=Doo::loadModel("AdConfigTag",TRUE);
        $adconfigid=$adconfigtagObj->getConfigByTypeValue($this->data["result"]["appkey"]);
        foreach ($this->data['adconfig'] as $k=>$v){
            if(in_array($v["id"],$adconfigid)){
                $this->data['adconfig'][$k]["selected"]=1;
            }else{
                $this->data['adconfig'][$k]["selected"]=0;
            }
        }
        // 选择模板
        $this->myrender('app/app_check', $this->data);
    }
    /**
     * 保存
     */
    public function save () {
        # START 检查权限
        if (!$this->checkPermission(APP, APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if (empty($post['app_name'])){
            $this->redirect("javascript:history.go(-1)","请填写应用名称");
        }
        if (!$post['app_id']){ //新建
            $checkAppName = $this->_appModel->records(array('app_name' => $post['app_name']));
            if ($checkAppName > 0){
                $this->redirect("javascript:history.go(-1)","应用名称已存在，请用其它应用名");
            }
        }
        if (empty($post['dev_id'])){
            $this->redirect("javascript:history.go(-1)","请选择开发者");
        }
        Doo::loadClass("Fredis/FRedis");

        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_2');
        $redis->set($post['appkey']."_APP", $post['state']);
        $app_id=$this->_appModel->upd($post['app_id'], $post);//更新应用信息
        if(!empty($post["pos_key"]) && is_array($post["pos_key"])){
            foreach ($post["pos_key"] as $k=>$v){
                $data["dev_id"]=$post["dev_id"];
                $data["app_id"]=$app_id;
                $data["pos_key"]=$post["pos_key"][$k];
                $data["pos_name"]=$post["pos_name"][$k];
                $data["state"]=!isset($post["pos_state_".$post["pos_id"][$k]][0])?$post["pos_state".substr($post["pos_key"][$k], 0, 16)][0]:$post["pos_state_".$post["pos_id"][$k]][0];
                $data["pos_key_type"]=$post["pos_key_type"][$k];
                $data["acounting_method"]=$post["acounting_method"][$k];
                $data["denominated"]=$post["denominated"][$k];
                $data["rate"]=$post["rate"][$k];
                $this->_appModel->upd_pos($post["pos_id"][$k],$data);
            }
        }
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '应用列表', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $post['app_id']);
        $this->redirect('../apps/index');
    }
    
    /**
     * 删除应用
     */
    public function delete() {
        # START 检查权限
        if (!$this->checkPermission(APP, APP_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $id = $this->get['app_id'];
        $appInfo = $this->_appModel->findOne(array('app_id' => $id));
        // 检查应用能否被删除掉（被产品使用的应用和正在使用的中，都不能删除）
        $adConfigTag = Doo::loadModel('AdConfigTag', TRUE);
        $result = $adConfigTag->checkUsed(1, $appInfo['appkey']);
        if (!empty($result)){
            $this->redirect("/apps/index", "该应用已被使用中，请先取消此应用的使用，再删除");
        }
        if ($id){
            $this->_appModel->del($id);
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_2');
            $redis->delete($appInfo['appkey']."_APP");
            $this->userLogs(array('msg' => json_encode($appInfo), 'title' => '应用列表', 'action' => 'delete'));
            //已通过审批的应用被删除时实时通过邮件通知相关人员
            if($appInfo['ischeck']==1){
                $by_email_name = $this->data['session']['e_name'];
                $mailtemplate=Doo::conf()->mailtemplate;
                $subject=sprintf($mailtemplate["appdelete"]["title"],$appInfo['app_name'],$by_email_name);
                $body = $mailtemplate["appdelete"]["body"];
                $this->sendEmail($mailtemplate["appdelete"]["tomailers"], $subject, $body);
            }
        }
        $this->redirect('../apps/index',"删除成功");
    }

    /**
     * 打开/关闭应用　
     */
    public function setAppState() {
        # START 检查权限
        $get = $this->get;
        if (!$this->checkPermission(APP, APP_EDIT)) {
            $this->displayNoPermission();
        }
        if (empty($get['appid'])  && !in_array($get['state'], array("0", "1"))){
            echo json_encode(array('code' => 1, 'msg' => '参数错误'));
            return;
        }
        //逻辑校验
        $appinfo = $this->_appModel->findOne(array('app_id'=>$get['appid']));
        if($appinfo['ischeck']!=1){
            echo json_encode(array('code' => 1, 'msg' => '应用还未审核通过!'));
            return;
        }
        if($appinfo['state'] == $get['state']){
            echo json_encode(array('code' => 0, 'msg' => '状态未变更,请刷新页面!'));
            return;
        }
        
        //更新应用信息
        $this->_appModel->updateAppState($get['appid'], $get['state']);
        
        //设置邮件
        $app_name = $appinfo['app_name'];
        $by_email_name = $this->data['session']['e_name'];
        $state = $get['state'] == 1?"开启":"关闭";
        $mailtemplate=Doo::conf()->mailtemplate;
        $subject=sprintf($mailtemplate["appstatechange"]["title"],$app_name,$by_email_name, $state);
        $body = $mailtemplate["appstatechange"]["body"];
        $result = $this->sendEmail($mailtemplate["appstatechange"]['tomailers'], $subject, $body);
        echo json_encode(array('code' => 0, 'msg' => '操作成功'));
    }
    
    public function versionList(){
        # START 检查权限
        if (!$this->checkPermission(APP, APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $adAppVersionModel = Doo::loadModel("AdAppVersions", true);
        $this->data['result'] = $adAppVersionModel->findAll();
        // 取应用信息
        $this->data['appinfo'] = $this->_appModel->findAll();
        // 选择模板
        $this->myrender('app/app_version', $this->data);
    }

    public function versionListsave(){
        //没用了，删掉．
        return;
        # START 检查权限
        if (!$this->checkPermission(APP, APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if (empty($post['app_version'])) {
            echo json_encode(array('errCode' => 0, 'errMsg' => '版本号不能为空'));
            return false;
        }
        $adAppVersionModel = Doo::loadModel("AdAppVersions", true);
        if ($adAppVersionModel->upd($post['id'], array('app_version' => $post['app_version'], 'appkey' => $post['appkey'], 'app_name' => $post['app_name']))){
            $this->userLogs(array('msg' => json_encode($post), 'title' => '应用版本'), $post['id']);
            echo json_encode(array('errCode' => 1, 'errMsg' => '操作成功'));
        } else {
            echo json_encode(array('errCode' => 0, 'errMsg' => '操作失败'));
        }
    }

    public function versiondelete(){
        # START 检查权限
        if (!$this->checkPermission(APP, APP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $id = $this->get['id'];
        if ($id){
            $adAppVersionModel = Doo::loadModel("AdAppVersions", true);
            $adAppVersionModel->del($id);
            $this->userLogs(array('msg' => '应用版本ID：'.$id, 'title' => '应用版本', 'action' => 'delete'));
        }
        $this->redirect('../apps/versionList');
    }

    /**
     * app类型
     * @return array
     */
    private function appConfig(){
        $appType = array(
            '教育/阅读' => array(
                1 => '儿童教育',
                2 => '漫画',
                3 => '商业杂志',
                4 => '成功励志',
                5 => '言情都市',
                6 => '工具书',
                7 => '新闻时事',
                8 => '其他读物',
            ),
            '财务/效率' => array(
                9 => '金融',
                10 => '理财',
                11 => '效率',
            ),
            '娱乐/社交' => array(
                12 => '社交',
                13 => '音乐',
                14 => '视频',
                15 => '娱乐',
            ),
            '生活/工具' => array(
                16 => '健康',
                17 => '生活',
                18 => '实用工具',
                19 => '天气',
            ),
            '游戏' => array(
                20 => '休闲益智',
                21 => '经营策略',
                22 => '动作竞技',
                23 => '棋牌游戏',
                24 => '飞行射击',
                25 => '体育竞技',
                26 => '网络游戏',
                27 => '社交游戏',
                28 => '其他游戏',
            ),
            '其他类别' => array( 29 => '其他应用'),
        );
        return $appType;
    }

    /**
     * 返回AppType
     */
    public function appType(){
        $appType = $this->appConfig();
        echo json_encode($appType);
    }

    /**
     * 获取开发者应用信用控件
     */
    public function retDevelopsApp(){
        $get = $this->get;
        $where = array();
        if (isset($get['classIds']) && $get['classIds']){
            $where['dev_id'] = $get['classIds'];
        }
        if (!isset($get['callbackparam']) || !$get['callbackparam']){
            die("error");
        }
        $jsonp = $get['callbackparam'];

        $result = $this->_appModel->retDevelopsApp($where);
        if (!empty($result)){
            echo $jsonp.'('.json_encode($result).')';
        }else{
            echo $jsonp.'(null)';
        }
    }
    private function getGameConfigListByAppkey($appkey){//返回该appkey下的所有配置文件
        if(empty($appkey)){
            return false;
        }
        $gameConfigModel = Doo::loadModel('AdGameConfigs', TRUE);
        $where = array('appkey' => $appkey,"groupby"=>"config_name");
        $result = $gameConfigModel->findAll($where);
        if(!empty($result)){
            foreach($result as $k=>$r){
                $gameConfigModel = Doo::loadModel('AdGameConfigs', TRUE);
                $where = array('appkey' => $appkey,"config_name"=>$r["config_name"]);
                $l=$gameConfigModel->findAll($where);
                $channel_id=array();
                $channel_name=array();
                foreach($l as $k1=>$r1){
                    array_push($channel_id,$r1["channel_id"]);
                    $channel_info=$gameConfigModel->getChannelidInfo($r1["channel_id"]);
                    if(!empty($channel_info)){
                        if($k1==10){//只取十条
                            array_push($channel_name,"。。。。。。");
                            break;
                        }
                        array_push($channel_name,$channel_info[$r1["channel_id"]]);
                    }

                }
                $result[$k]["channel_id"]=  implode(",",$channel_id);
                $result[$k]["channel_name"]=  implode(",",$channel_name);
                //array_push($list, $l);
            }
        }
        return $result;
    }
    /**
     * 显示APP列表，查询结果显示
     */
    public function adManager() {
//        # START 检查权限
        if (!$this->checkPermission(APP, APP_LIST)) {
            $this->displayNoPermission();
        }
//        # END 检查权限
        $params = $this->get;
        if (!isset($params['appkey']) || $params['appkey'] == ''){
            $this->redirect("/apps/index", "暂时没有此应用的广告配置功能");
        }
        $where = array('appkey' => $params['appkey'],"config_name"=>$params["config_name"]);
        $gameConfigModel = Doo::loadModel('AdGameConfigs', TRUE);
        $this->data['result'] = $gameConfigModel->findOne($where);

        if (!empty($this->data['result'])){
            $this->data['result']['config_detail'] = json_decode($this->data['result']['config_detail'], TRUE);
        }
        $this->data['result']['appkey'] = $params['appkey'];
        // 选择模板
        $tempPath = Doo::conf()->SITE_PATH.'protected/view/'.Doo::conf()->TEMPLATE_PATH.'/gameConfig/'.$params['appkey'].'.html';
        if (!file_exists($tempPath)){
            $this->redirect('/apps/index','暂时没有此应用的广告配置功能');
        }

        $retArr = $gameConfigModel->findAll($where);
        $this->data['result']["channel_id"]=array();
        foreach($retArr as $val){
            $channel_info=$gameConfigModel->getChannelidInfo($val['channel_id']);
            if(!empty($channel_info)){
                array_push($this->data['result']["channel_id"],array('identifier' => $val['channel_id'],'realname' => $channel_info[$val['channel_id']]));
            }
        }
        $this->data["list"] = $this->getGameConfigListByAppkey($params['appkey']);
        $this->myrender('gameConfig/'.$params['appkey'], $this->data);
    }

    /*
     * 保存
     */
    public function adManagersave(){
        $post = $this->post;
        if (isset($post['data']['magic_box']['gold']) && $post['data']['magic_box']['gold']){
            $post['data']['magic_box']['gold'] = json_decode($post['data']['magic_box']['gold'], TRUE);
        }
        $gameConfigModel = Doo::loadModel('AdGameConfigs', TRUE);
        if (empty($post['channel_id'])){
            $this->redirect("javascript:history.go(-1)",'请选择渠道');
        }
        if(!$gameConfigModel->delapp($post)){
            $this->redirect("javascript:history.go(-1)","你所选择的渠道已经被别的配置项使用");
        }
        foreach($post['channel_id'] as $channel){
                $gameConfigModel->upd(NULL, array('appkey' => $post['appkey'], 'config_detail' => json_encode($post['data']), 'channel_id' =>$channel,'config_name'=>$post["config_name"]));
        }
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '应用配置', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), NULL);
        $this->redirect('javascript:history.go(-1)','保存成功');
    }
    public function delGameConfig(){
        $get=$this->get;
        $appkey=$get["appkey"];
        $config_name=$get["config_name"];
        $sql="delete from ad_game_config where appkey='$appkey' and config_name='$config_name'";
        Doo::db()->query($sql)->execute();
        $this->userLogs(array('msg' => json_encode($get), 'title' => '应用配置'), $appkey);
        $this->redirect("javascript:history.go(-1)",'删除成功');
    }
    //获取sdk那边的游戏信息
    public function getAppGameInfo(){
        Doo::loadClass("net/class.curl");
        $type=$this->get["type"];
        $value=$this->get["value"];
        if(empty($type) || empty($value)){
            return false;
        }
        $url = sprintf(Doo::conf()->GAME_INFO_URL,$type,trim($value, ","));
        $curl = new CURL();
        $gameinfo = $curl->get($url,0);
        $gaemeArr = json_decode($gameinfo, TRUE);
        if (isset($gaemeArr['result'])){
            die(json_encode($gaemeArr['result']));
        }else{
            die(json_encode(array()));
        }
    }
    public function createappkey(){
        Doo::loadPlugin("function");
        $appkey=createappkey();
        die(json_encode(array("error"=>0,"data"=>$appkey)));
    }
    public function createposkey(){
        $appkey=$this->get["appkey"];
        if(empty($appkey)){
            $appkey=0;
        }
        $poskey=substr(base64_encode(microtime()),0,23)."-".substr(base64_encode(($appkey)),0,8);
        $pos=Doo::loadModel("datamodel/AdDeverPos",TRUE);
        $result=$pos->getOne(array('select'=>'*', 'asArray'=>TRUE, 'where' => "dever_pos_key='".$poskey."'"));
        if(!empty($result)){
            $this->createappkey();
        }
        die(json_encode(array("result"=>0,"msg"=>$poskey)));
    }
    /*
     * 删除广告位信息
     */
    public function del_pos_key(){
        $pos_key=$this->get["pos_key"];
        $pos=Doo::loadModel("datamodel/AdDeverPos",TRUE);
        $posInfo = $pos->getPosByDeverposkey($pos_key);
        $appInfo = $this->_appModel->findOne(array('app_id' => $posInfo['app_id']));
        if($this->_appModel->del_app_pos($pos_key)){
            //若应用已通过审核,则需要邮件提醒
            if($appInfo['ischeck'] == 1){
                $app_name = $appInfo['app_name'];
                $by_email_name = $this->data['session']['e_name'];
                $mailtemplate=Doo::conf()->mailtemplate;
                $subject=sprintf($mailtemplate["appposdelete"]["title"],$app_name, $posInfo['dever_pos_name'], $by_email_name);
                $body = $mailtemplate["appposdelete"]["body"];
                $this->sendEmail($mailtemplate["appposdelete"]["tomailers"], $subject, $body);
            }
            die(json_encode(array("result"=>0,"msg"=>"删除成功")));
        }else{
            die(json_encode(array("result"=>-1,"msg"=>"删除失败")));
        }
    }
    public function ischeck(){
        $post = $this->post;
        if (empty($post['app_id'])){
            echo json_encode(array('retCode' => -1, 'data' => '请传入app_id'));die;
        }
        $adconfigtag=Doo::loadModel("AdConfigTag",TRUE);
        $post["acounting_method"]=  explode(",",trim($post["acounting_method"],","));
        $post["pos_key"]=  explode(",",trim($post["pos_key"],","));
        $post["pos_id"]=  explode(",",trim($post["pos_id"],","));
        $post["denominated"]=  explode(",",trim($post["denominated"],","));
        $post["pos_state"]=  explode(",",trim($post["pos_state"],","));
        $ad_config_id=  explode(",", $post["belong_configid"]);
        $mailtemplate=Doo::conf()->mailtemplate;
        if($post["ispass"]==1){
            $subject=$mailtemplate["apppass"]["title"];
            $body=sprintf($mailtemplate["apppass"]["body"],$post["app_name"],$post["appkey"]);
            $adconfigtag->addConfig2Tag($ad_config_id,$post["appkey"]);
        }else{
            $subject=$mailtemplate["appdenied"]["title"];
            $body=sprintf($mailtemplate["appdenied"]["body"],$post["app_name"],$post["appkey"],$post["msg"]);
            foreach($ad_config_id as $id){
                $adconfigtag->delByConfigidType($id,1,$post["appkey"]);
            }
        }
        $post["state"]=$post["ispass"]==1?1:0;
        try{
            $this->_appModel->ischeck($post);
        }  catch (Exception $e){
            echo json_encode(array('retCode' => -1, 'data' => '更新失败'));die;
        }
        
        $ret = $this->_appModel->findOne(array('app_id' => $post['app_id']));
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '应用审核', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $appkey);
        if ($ret['ischeck'] == $post['ispass']){
            $devloper=Doo::loadModel("Developer",True);
            $devloper=$devloper->findOne(array("dev_id"=>$ret["dev_id"]));
            $this->sendEmail($devloper["email"], $subject, $body);
            $MsgModel = Doo::loadModel('Msgs', TRUE);
            $MsgModel->sendLetter($post["dev_id"],$subject,$body);
            echo json_encode(array('retCode' => 1, 'data' => '更新成功'));die;
        }else{
            echo json_encode(array('retCode' => -1, 'data' => '更新失败'));die;
        }
    }
}
