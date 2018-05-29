<?php
/**
 * 应用中心模型
 *
 * @author Intril.Leng
 */

Doo::loadController("AppDooController");
class DeveloperController extends AppDooController {

    /**
     * Developer模型对象
     * @var Object
     */
    private $_developerModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_developerModel = Doo::loadModel('Developer', TRUE);
    }

    /**
     * 显示开发者列表，查询结果显示
     */
    public function index() {
        # START 检查权限
        if (!$this->checkPermission(DEVELOP, DEVELOP_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/developer/index?";
        $sqlBlock = "1=1";
        if (isset($params['user_name']) && $params['user_name']) {
            $sqlBlock .= " AND (user_name like '%".$params['user_name']."%' or email like '%".$params['user_name']."%' or mobile like '%".$params['user_name']."%')";
            $url .= "user_name=".$params['user_name']."&";
        }else{
            $params['user_name'] = '';
        }
        if(isset($params["ischeck"]) && $params["ischeck"]=="0"){
//            $sqlBlock .= " AND ischeck=0 and user_name!='' and mobile!='' and qq!='' and address!=''";
            $sqlBlock .= " AND ischeck=0 ";
            $url .= "ischeck=".$params['ischeck']."&";
        }else{
            $sqlBlock .= " AND ischeck=1";
            $params['ischeck'] = '';
        }
//        $sqlBlock.=" AND isactive=1";
        // 分页
        $total = $this->_developerModel->records($sqlBlock);
        $pager = $this->page($url, $total);
//        $sqlBlock .= " order by isactive desc, user_name asc, createdate desc LIMIT ".$pager->limit;
        $this->data['result'] = $this->_developerModel->findDeveloper($sqlBlock, $pager->limit);
        $this->data['params'] = $params;
        // 选择模板
        if(isset($params["ischeck"])  && $params["ischeck"]=="0"){
            $this->data["content"]="developer/list_check";
        }else{
            $this->data["content"]="developer/list";
        }
        $this->view()->render("ledou/index", $this->data);
    }
    //根据开发者显示其所属的app
    public function dev_app(){
        # START 检查权限 
        if (!$this->checkPermission(DEVELOP, DEVELOP_APP)){
            $this->displayNoPermission();
        } 
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        $appModel = Doo::loadModel('Apps', TRUE);
        if (isset($get['dev_id']) && $get['dev_id'] && is_numeric($get['dev_id'])){
            $appmodel = Doo::loadModel('Apps', TRUE);
            $this->data["result"]=$appmodel->getAppinfoBydevid($get["dev_id"]);
            $this->data['dev_id']=$get['dev_id'] ;
        }else{
            $this->data['result']=array();
        }
        // 选择模板
        $this->myrender("developer/app", $this->data);
    }
    /**
     * 设定应用的核算信息
     */
    public function app_edit() {
        # START 检查权限 
        if (!$this->checkPermission(DEVELOP, DEVELOP_APP)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        if (isset($get['app_id']) && $get['app_id'] && is_numeric($get['app_id'])) {
            $ad_app=Doo::loadModel('datamodel/AdApp', TRUE);
            #$this->data['result'] = Doo::db()->query("select a.app_id as appid,a.dev_id as devid,a.app_name as appname,a.acounting_method,a.denominated,a.income_rate,b.* from ad_app a LEFT JOIN app_stat b on a.dev_id=b.dev_id and a.app_id=b.app_id where a.app_id=".$get["app_id"])->fetch();
            $this->data['result']=$ad_app->getone(array('select' => '*', 'where' => 'app_id="'.$get["app_id"].'"', 'asArray' => TRUE));
        }
        // 选择模板
        $this->myrender("developer/app_edit", $this->data);
    }
    public function app_hesuan_save(){
        $post=$this->post;
        try{
//            $app_stat=Doo::loadModel('datamodel/AppStat', TRUE);
//            $app_stat->income=$post["income"];
//            $app_stat->income_after=$post["income_after"];
//            $app_stat->app_id=$post["app_id"];
//            $app_stat->dev_id=$post["dev_id"];
//            #$app=$app_stat->find(array('select' => '*', 'where' => 'app_id="'.$post["app_id"].'"', 'asArray' => TRUE));
//            $app=Doo::db()->query("select * from app_stat where app_id=".$post["app_id"])->fetch();
//            if(empty($app)){
//                $app_stat->createdate=time();
//                $app_stat->insert();
//            }else{
//                $app_stat->updatedate=time();
//                $app_stat->update();
//            }
            $adapp=Doo::loadModel('datamodel/AdApp', TRUE);
            $adapp->acounting_method=$post["acounting_method"];
            $adapp->denominated=$post["denominated"];
            $adapp->income_rate=$post["income_rate"];
            $adapp->updatedate=time();
            $adapp->app_id=$post["app_id"];
            $adapp->update(); 
            
            $this->redirect('../developer/dev_app?dev_id='.$post["dev_id"],"修改成功");
        } catch (Exception $exc) {
            $this->redirect('../developer/app_edit?dev_id='.$post["dev_id"],"修改失败");
        }
    }


    /**
     * 修改，添加开发者
     */
    public function edit() {
        # START 检查权限
        if (!$this->checkPermission(DEVELOP, DEVELOP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        if (isset($get['dev_id']) && $get['dev_id'] && is_numeric($get['dev_id'])) {
            $whereArr = array('dev_id' => $get['dev_id'],'app_id' => $get['app_id']);
            $this->data['result'] = $this->_developerModel->findOne($whereArr);
            $this->data['title'] = '修改';
        }else{
            $this->data['result'] = array(
                'email' => '', 'password' => '', 'user_name' => '', 'qq' => '', 'mobile' => '', 'dev_id' => '',
            );
            $this->data['title'] = '添加';
        }

        // 选择模板
        $this->data["content"]="developer/detail";
        $this->myrender("developer/detail", $this->data);
    }
    
    /**
     * 保存
     */
    public function save () {
        # START 检查权限
        if (!$this->checkPermission(DEVELOP, DEVELOP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if (!$post['email']){
            $this->redirect("javascript:history.go(-1)","请填写E_mail");
        }
        if (!$post['qq']){
            $this->redirect("javascript:history.go(-1)","请填写qq");
        }
        $mode= "/^([a-z0-9+_]|\-|\.)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,6}$/i";
        if ( ! (preg_match($mode,$post['email']))  ){
            $this->redirect("javascript:history.go(-1)","请填写正确的Email格式");
        }
        if (!$post['dev_id']){
            if (!$post['password'] || strlen($post['password']) < 6) {
                $this->redirect("javascript:history.go(-1)","请填写密码或密码长度少于6位字符");
            }
            $checkEmail = $this->_developerModel->records(array('email' => $post['email']));
            if ($checkEmail > 0){
                $this->redirect("javascript:history.go(-1)","此Email已存在，请用其他Email");
            }
            $checkUser = $this->_developerModel->records(array('user_name' => $post['user_name']));
            if ($checkUser > 0){
                $this->redirect("javascript:history.go(-1)","此用户名已存在，请用其他用户名");
            }
            $checkMobile = $this->_developerModel->records(array('mobile' => $post['mobile']));
            if ($checkMobile > 0){
                $this->redirect("javascript:history.go(-1)","此手机号已存在，请用其他手机号");
            }
        }else{
            $checkUser = $this->_developerModel->records("user_name ='".$post['user_name']."' and dev_id != ".$post['dev_id']);
            if ($checkUser > 0){
                $this->redirect("javascript:history.go(-1)","此用户名已存在，请用其他用户名");
            }
            $checkMobile = $this->_developerModel->records("mobile ='".$post['mobile']."' and dev_id != ".$post['dev_id']);
            if ($checkMobile > 0){
                $this->redirect("javascript:history.go(-1)","此手机号已存在，请用其他手机号");
            }
        }
        if (empty($post['user_name'])){
            $this->redirect("javascript:history.go(-1)","请填写用户名");
        }
        if (strlen($post['mobile']) != 11){
            $this->redirect("javascript:history.go(-1)","手机号格式不对");
        }
        $insertId = $this->_developerModel->upd($post['dev_id'], $post);
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '开发者列表', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $post['dev_id']);
        $this->redirect('../financials/edit?dev_id='.$insertId);
    }

    /**
     * 删除开发者
     */
    public function delete() {
        # START 检查权限
        if (!$this->checkPermission(DEVELOP, DEVELOP_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $id = $this->get['dev_id'];
        if ($id){
            $appModel = Doo::loadModel('Apps', TRUE);
            $checkHasApp = $appModel->findOne(array('dev_id' => $id));
            if (!empty($checkHasApp)){
                $this->redirect('../developer/index', '删除失败，请先删掉该开发者下面的应用');
            }
            $userInfo = $this->_developerModel->findOne(array('dev_id' => $id));
            $financialModel = Doo::loadModel('Financials', TRUE);
            $fInfo = $financialModel->findOne(array('devid' => $id));
            if (isset($fInfo['f_id']) && $fInfo['f_id']){
                $financialModel->del($userInfo['dev_id']);
                // 删掉上传的文件
                Doo::loadCore('helper/DooFile');
                $dooUpload = new DooFile();
                if (file_exists(UPLOAD_PATH.$fInfo['cred_pic'])){
                    $dooUpload->delete(UPLOAD_PATH.$fInfo['cred_pic']);
                }
            }
            $this->userLogs(array('msg' => json_encode($userInfo), 'title' => '开发者列表', 'action' => 'delete'));
            //Doo::db()->query("DELETE FROM mobgi_www.users WHERE email ='".$userInfo['email']."'");
            $DeveloperModel = Doo::loadModel('Developer', true);
            $DeveloperModel->del($id);
            
        }
        $this->redirect('../developer/index');
    }

    /**
     * 获取开发者列表控件
     * @return type
     */
    public function retDeveloperList(){
        $get = $this->get;
        $where = array();
        if (isset($get['dev_id']) && $get['dev_id']){
            $where['dev_id'] = $get['dev_id'];
        }
        $where["ischeck"]=1;
        $developer = $this->_developerModel->findAll($where);
        if (empty($developer)){
            return array();
        }
        $result = array();
        foreach($developer as $key => $value){
            $result[$key] = array('id' => $value['dev_id'], 'name' => $value['user_name']);
        }
        if (!isset($get['callbackparam']) || !$get['callbackparam']){
            die("error");
        }
        $jsonp = $get['callbackparam'];
        echo $jsonp.'('.json_encode($result).')';
    }
    /*
     * 查看待审核开发者信息
     */
    public function showCheckDevloper(){
        $dev_id=$this->get["dev_id"];
        # START 检查权限
        if (!$this->checkPermission(DEVELOP, DEVELOP_CHECK)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        if (isset($get['dev_id']) && $get['dev_id'] && is_numeric($get['dev_id'])) {
            $whereArr = array('dev_id' => $get['dev_id'],'app_id' => $get['app_id']);
            $this->data['result'] = $this->_developerModel->findOne($whereArr);
            
            
            $whereArr = array('devid' => $get['dev_id']);
            $financialsModel=Doo::loadModel('Financials', TRUE);
            $this->data['financials'] = $financialsModel->findOne($whereArr);
            $cred_pic=  split(";",$this->data['financials']['cred_pic']);
            $this->data['financials']['cred_pic']=$cred_pic[0];
            $this->data['financials']['cred_pic1']=$cred_pic[1];
            $this->data['upload_path'] ="http://www.mobgi.com".'/misc'.$this->data['financials']['cred_pic'];
            $this->data['upload_path1'] ="http://www.mobgi.com".'/misc'.$this->data['financials']['cred_pic1'];
            $this->data['financials'] = $this->removeAllXss($this->data['financials']);
        }else{
            $this->data['result'] = array(
                'email' => '', 'password' => '', 'user_name' => '', 'qq' => '', 'mobile' => '', 'dev_id' => '',
            );
        }

        // 选择模板
        $this->data["content"]="developer/check";
        $this->myrender("developer/check", $this->data);
    }
    /*
     * 审查开发者
     */
    public function CheckDevloper(){
        # START 检查权限
        if (!$this->checkPermission(DEVELOP, DEVELOP_CHECK)){
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        
        if($this->_developerModel->check_deverloper($post["dev_id"],$post["ispass"],$post["msg"])){
            $mailtemplate=Doo::conf()->mailtemplate;
            
            if($post["ispass"]==1){
                $financialsModel=Doo::loadModel('Financials', TRUE);
                $whereArr = array('devid' => $post["dev_id"]);
                $financials=$financialsModel->findOne($whereArr);
                $subject=$mailtemplate["userpass"]["title"];
                $body=sprintf($mailtemplate["userpass"]["body"],$financials["cred_name"],$financials["cred_num"]);
            }else{
                $whereArr = array('dev_id' => $post["dev_id"]);
                $devloper=$this->_developerModel->findOne($whereArr);
                $subject=$mailtemplate["userdenied"]["title"];
                $body=sprintf($mailtemplate["userdenied"]["body"],$devloper["check_msg"]);
            }
            $this->sendEmail($post["email"], $subject, $body);
            $MsgModel = Doo::loadModel('Msgs', TRUE);
            $MsgModel->sendLetter($post["dev_id"],$subject,$body);
            die(json_encode(array("result"=>0,"msg"=>"操作成功")));
        }else{
            die(json_encode(array("result"=>-1,"msg"=>"操作失败")));
        }
    }
}