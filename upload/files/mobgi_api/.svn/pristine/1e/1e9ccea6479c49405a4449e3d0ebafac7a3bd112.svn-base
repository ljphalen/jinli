<?php
/**
 * 应用中心模型
 *
 * @author Intril.Leng
 */

Doo::loadController("AppDooController");
class FinancialsController extends AppDooController {

    /**
     * Developer模型对象
     * @var Object
     */
    private $_financialsModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_financialsModel = Doo::loadModel('Financials', TRUE);
    }

    public function edit() {
        # START 检查权限
        if (!$this->checkPermission(DEVELOP, DEVELOP_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        
        $whereArr = array();
        if (isset($get['dev_id']) && $get['dev_id'] && is_numeric($get['dev_id'])) {
            $whereArr = array('devid' => $get['dev_id']);
            $this->data['result'] = $this->_financialsModel->findOne($whereArr);
            $cred_pic=  split(";",$this->data['result']['cred_pic']);
            $this->data['result']['cred_pic']=$cred_pic[0];
            $this->data['result']['cred_pic1']=$cred_pic[1];
            $this->data['result']["devid"]=$get["dev_id"];
            $this->data['title'] = '修改';
        }else{
            $this->data['title'] = '添加';
        }
        if (empty($this->data['result'])){
            $this->data['result'] = array(
                'f_id'=>'','ftype'=>1,'cred_name'=>'','bank'=>'','sub_branch'=>'',
                'bank_account'=>'','cred_type'=>1,'cred_num'=>'','cred_pic'=>'','cred_pic1'=>'','devid'=>$get['dev_id'],
            );
        }
        // 选择模板
        $this->data['upload_path'] ="http://www.mobgi.com".'/misc'.$this->data['result']['cred_pic'];
        $this->data['upload_path1'] ="http://www.mobgi.com".'/misc'.$this->data['result']['cred_pic1'];
        $this->data["content"]="developer/detail";
        //财务信息的展示过滤XSS
        $this->data['result'] = $this->removeAllXss($this->data['result']);
        $this->myrender("developer/financials_detail", $this->data);
    }

    /**
     * 保存财务信息
     */
    public function saveFinancial(){
        if (!$this->checkPermission(DEVELOP, DEVELOP_EDIT)) {
            $this->displayNoPermission();
        }
        $post = $this->post;
        if (!$post['cred_name']){
            $this->redirect("javascript:history.go(-1)","请填写证件姓名");
        }
        if (!$post['bank']){
            $this->redirect("javascript:history.go(-1)","请填写开户银行");
        }
        if (!$post['bank_account']){
            $this->redirect("javascript:history.go(-1)","请填写银行账号");
        }
        if (!$post['f_id']){ // 添加
            $checkBankAccount = $this->_financialsModel->records(array('bank_account' => $post['bank_account']));
            if ($checkBankAccount > 0) {
                $this->redirect("javascript:history.go(-1)","该银行卡号已存在，请用其它卡号");
            }
            $checkCredNum = $this->_financialsModel->records(array('cred_num' => $post['cred_num']));
            if ($checkCredNum > 0) {
                $this->redirect("javascript:history.go(-1)","该证件号已存在，请用其它证件号码");
            }
        }
        if (!$post['cred_num']){
            $this->redirect("javascript:history.go(-1)","请填写证件号码");
        }
        Doo::loadCore('helper/DooFile');
        $dooUpload = new DooFile();
        if (!$dooUpload->checkFileExtension("cred_pic",array('png', 'gif', 'jpeg','jpg'))){
            $this->redirect("javascript:history.go(-1)","上传文件类型必须为jpeg,gif,png,jpg图片中一种");
        }
        $cred_pic_back = $dooUpload->upload(UPLOAD_PATH,"cred_pic1", $post['cred_num']."_back");
        
        $post['cred_pic'] = $dooUpload->upload(UPLOAD_PATH,"cred_pic", $post['cred_num']).";".$cred_pic_back;
        
        if(!$post['cred_pic']){
            $this->redirect("javascript:history.go(-1)","请上传证件图片");
        }else{
            $cred_pic=split(";",$post['cred_pic']);
            if(sizeof($cred_pic)!=2){
                $this->redirect("javascript:history.go(-1)","请上传证件图片");
            }
        }
        $financialModel = Doo::loadModel('Financials', TRUE);
        $financialModel->upd($post['f_id'], $post);
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '开发者列表-财务信息', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $post['f_id']);
        $this->redirect('../developer/index');
    }
}