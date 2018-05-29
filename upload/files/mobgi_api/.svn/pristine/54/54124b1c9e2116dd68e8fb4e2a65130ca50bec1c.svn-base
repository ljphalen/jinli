<?php

/**
 * 后台管理
 *
 * @author Intril.leng
 */
Doo::loadController("AppDooController");

class AdminController extends AppDooController {

    /**
     * Admin模型对象
     * @var Object
     */
    private $_adminModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_adminModel = Doo::loadModel('Admin', TRUE);
    }

    public function index() {
        // START 检查权限
        if (!$this->checkPermission(ADMIN_ACCOUNT, ADMIN_ACCOUNT_LIST)) {
            $this->displayNoPermission();
        }
        // END 检查权限
        $params = $this->get;
        $whereArr = array();
        $url = "/admin/index?";
        if (isset($params['username']) && $params['username']) {
            $whereArr['username'] = $params['username'];
            $url .= "username=".$params['username']."&";
        }else{
            $params['username'] = '';
        }
        if (isset($params['realname']) && $params['realname']) {
            $whereArr['realname'] = $params['realname'];
            $url .= "realname=".$params['realname']."&";
        }else{
            $params['realname'] = '';
        }
        if (isset($params['e_name']) && $params['e_name']) {
            $whereArr['e_name'] = $params['e_name'];
            $url .= "e_name=".$params['e_name']."&";
        }else{
            $params['e_name'] = '';
        }
        if (isset($params['role_id']) && $params['role_id'] != 0) {
            $whereArr['role_id'] = $params['role_id'];
            $url .= "role_id=".$params['role_id']."&";
        }else{
            $params['role_id'] = 0;
        }
        // 分页
        $total = $this->_adminModel->records($whereArr);
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->_adminModel->findAll($whereArr);
        $roleModel = Doo::loadModel('Role', TRUE);
        $roleInfo = $roleModel->findAll();
        $this->data['role'] = listArray($roleInfo, 'id', 'title', array('0' => '所有角色'));
        $this->data['params'] = $params;
        $this->data['select'] = form_select($this->data['role'], array('name' => 'role_id', 'value' => $this->data['params']['role_id']));
        // 选择模板
        $this->myrender('admin/list', $this->data);
    }

    /**
     * 修改
     */
    public function edit() {
        // START 检查权限
        if (!$this->checkPermission(ADMIN_ACCOUNT, ADMIN_ACCOUNT_EDIT)) {
            $this->displayNoPermission();
        }
        // END 检查权限
        $get = $this->get;
        if (isset($get['adminid']) && $get['adminid'] && is_numeric($get['adminid'])) {// 编辑
            $whereArr = array('adminid' => $get['adminid']);
            $this->data['result'] = $this->_adminModel->findOne($whereArr);
            if (!$this->data['result']) {
                $this->redirect("javascript:history.go(-1)","该用户不存在或已被删除");
            }
            $this->data['title'] = '修改';
        }else{
            $this->data['result'] = array(
                'adminid' => '', 'realname' => '', 'username' => '', 'e_name' => '', 'password' => '', 'role_id' => 0,
            );
            $this->data['title'] = '添加';
        }

        $roleModel = Doo::loadModel('Role', TRUE);
        $roleInfo = $roleModel->findAll();
        $roleList = listArray($roleInfo, 'id', 'title');
        $this->data['select'] = form_select($roleList, array('name' => 'role_id', 'value' => $this->data['result']['role_id']));
        // 选择模板
        $this->myrender('admin/detail', $this->data);
    }

    public function editPwd(){
        // START 检查权限
        if (!$this->checkPermission(ADMIN_ACCOUNT, ADMIN_ACCOUNT_EDIT_PASS)) {
            $this->displayNoPermission();
        }
        // END 检查权限
        // 选择模板
        $this->myrender('admin/detailPwd', $this->data);
    }

    /**
     * 修改密码
     */
    public function savePwd(){
        // START 检查权限
        if (!$this->checkPermission(ADMIN_ACCOUNT, ADMIN_ACCOUNT_EDIT_PASS)) {
            $this->displayNoPermission();
        }
        // END 检查权限
        $post = $this->post;
        if (empty($post['password'])){
            $this->redirect("javascript:history.go(-1)","请填写旧密码");
        }
        if (empty($post['password1'])){
            $this->redirect("javascript:history.go(-1)","请填写新密码");
        }
        if (empty($post['password2'])){
            $this->redirect("javascript:history.go(-1)","请确认旧密码");
        }
        if ($post['password'] != $post['password2']){
            $this->redirect("javascript:history.go(-1)","两次新密码不一致");
        }
        $loginData = Doo::session()->__get('admininfo');
        $rs = $this->_adminModel->findOne(array('adminid'=> $loginData['adminid'],'password' => md5(md5(md5($post['password1'])))));
        if (empty($rs)){
            $this->redirect("javascript:history.go(-1)","旧密码不正确");
        }
        $this->_adminModel->updPwd($loginData['adminid'], $post);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '修改密码'), $loginData['adminid']);
        $this->redirect("../admin/editPwd");
    }

    /**
     * 保存
     */
    public function save(){
        // START 检查权限
        if (!$this->checkPermission(ADMIN_ACCOUNT, ADMIN_ACCOUNT_EDIT)) {
            $this->displayNoPermission();
        }
        // END 检查权限
        $post = $this->post;
        if (empty($post['realname'])){
            $this->redirect("javascript:history.go(-1)","请填写真实名");
        }
        if (empty($post['username'])){
            $this->redirect("javascript:history.go(-1)","请填写用户名");
        }
        if (empty($post['e_name'])){
            $this->redirect("javascript:history.go(-1)","请填写英文名");
        }
        if (!$post['adminid']){
            if (!$post['password'] || strlen($post['password']) < 8) {
                $this->redirect("javascript:history.go(-1)","请填写密码或密码长度少于8位字符");
            }
        }
        //若有设置密码，则检测密码结构
        if($post['password']){
           //检测密码结构
            $this->checkPassword($post['password']); 
        }
        
        if(isset($post['lock_open'])&&$post['lock_open']==0){
            $post['lock_num'] = 0;
        }
        $this->_adminModel->upd($post['adminid'], $post);
        //快照
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($post), 'title' => '后台帐号管理', 'type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$referer), $post['adminid']);
        $this->redirect('../admin/index');
    }

    /**
     * 删除
     */
    public function delete() {
        // START 检查权限
        if (!$this->checkPermission(ADMIN_ACCOUNT, ADMIN_ACCOUNT_DEL)) {
            $this->displayNoPermission();
        }
        // END 检查权限
        $get = $this->get;
        // 数据效验
        if (!isset($get['adminid']) || !$get['adminid'] || !is_numeric($get['adminid'])) {
            die('invalid id');
        }
        $this->_adminModel->del(array('adminid' => $get['adminid']));
        $this->userLogs(array('msg' => json_encode($get), 'title' => '后台帐号管理', 'action' => 'delete'));
        $this->redirect('../admin/index');
    }
    
    /**
     * 日志查看
     */
    public function log(){
        // START 检查权限
        if (!$this->checkPermission(ADMIN_ACCOUNT, ADMIN_ACCOUNT_LIST)) {
            $this->displayNoPermission();
        }
        // END 检查权限
        $params = $this->get;
        $whereArr = array();
        $url = "/admin/log?";
        $where = " 1 = 1";
        if (isset($params['username']) && $params['username']) {
            $where .= " AND username like '%".$params['username']."%'";
            $url .= "username=".$params['username']."&";
        }else{
            $params['username'] = '所有人';
        }
        if (isset($params['title']) && $params['title']) {
            $where .= " AND title = '".$params['title']."'";
            $url .= "title=".$params['title']."&";
        }else{
            $params['title'] = '所有功能';
        }
        if (isset($params['sdate']) && $params['sdate'] != 0) {
            $where .= " AND date >= '".$params['sdate']."'";
            $url .= "sdate=".$params['sdate']."&";
        }else{
            $where .= " AND date >='".date("Y-m-d", strtotime("-3 days"))."'";
            $params['sdate'] = date("Y-m-d", strtotime("-3 days"));
        }
        if (isset($params['edate']) && $params['edate'] != 0) {
            //结束日期需要包含今天
            $endTimeNextDay = strtotime($params['edate'])+ 86400;
            $endDateNextDay = date('Y-m-d', $endTimeNextDay);
            $where .= " AND date <= '".$endDateNextDay."'";
            $url .= "edate=".$params['edate']."&";
        }else{
            $endTimeNextDay = time() + 86400;
            $endDateNextDay = date('Y-m-d', $endTimeNextDay);
            $where .= " AND date <= '".$endDateNextDay."'";
            $params['edate'] = date("Y-m-d");
        }
        $this->data['params']['sdate'] = $params['sdate'];
        $this->data['params']['edate'] = $params['edate'];
        // 分页
        $UserLogs = Doo::loadModel("datamodel/UserLogs", TRUE);
        
        $total = $UserLogs->count(array('select'=>'*', 'asArray' => true, 'where' => $where));
        $pager = $this->page($url, $total,50);
        $this->data['result'] = $UserLogs->find(array('select'=>'*', 'asArray' => true, 'where' => $where,'limit' => $pager->limit, 'desc' => 'date'));
        $userArr = array('所有人');
        $modArr = array('所有功能');
        
        $AllLog  = $UserLogs->find(array('select' => "*", 'asArray' => true));
        foreach ($AllLog as $val){
            $userArr[$val['username']] = $val['username'];
            $modArr[$val['title']] = $val['title'];
        }
        foreach ($this->data['result'] as $key => $value){
            $this->data['result'][$key]['msg'] = str_replace("\\", "", $this->unicodeString($value['msg']));
        }
        
        $this->data['select_user'] = form_select($userArr, array('name' => 'username', 'value' => $params['username']));
        $this->data['select_mod'] = form_select($modArr, array('name' => 'title', 'value' => $params['title']));
        // 选择模板
        $this->myrender('admin/log', $this->data);
        
    }
    
    public function unicodeString($str, $encoding=null) {
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', create_function('$match', 'return mb_convert_encoding(pack("H*", $match[1]), "utf-8", "UTF-16BE");'), $str);
    }
    
    /**
     * 根据密码字符串判断密码结构 
     * @param type $pwd
     * @return string
     */
    public function checkPassword($pwd){  
        if (strlen($pwd)<8)  
        {  
            $this->redirect("javascript:history.go(-1)","请填写密码或密码长度少于8位字符");
        }  

        if(preg_match("/^\d*$/",$pwd))  
        {  
            $this->redirect("javascript:history.go(-1)","密码必须包含字母");//全数字  
        }  

        if(preg_match("/^[a-z]*$/i",$pwd))  
        {  
            $this->redirect("javascript:history.go(-1)","密码必须包含数字");//全字母
        }  

        if(!preg_match("/^[a-z\d]*$/i",$pwd))  
        {  
            $this->redirect("javascript:history.go(-1)","密码只能包含数字和字母");//有数字有字母  ";  
        }  
        return true;
    } 
}

?>
