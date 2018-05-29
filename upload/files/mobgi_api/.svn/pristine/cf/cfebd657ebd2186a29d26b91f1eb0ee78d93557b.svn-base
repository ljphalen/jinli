<?php
/**
 * 应用中心模型
 *
 * @author Intril.Leng
 */

Doo::loadController("AppDooController");
class ConditionManagesController extends AppDooController {

    /**
     * App模型对象
     * @var Object
     */
    private $_conditionManageModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_conditionManageModel = Doo::loadModel('ConditionManages', TRUE);
    }

    /**
     * 显示APP列表，查询结果显示
     */
    public function index() {
        # START 检查权限
        if (!$this->checkPermission(CONDITION, CONDITION_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data['result'] = $this->_conditionManageModel->findAll();
        $this->myrender('conditionManage/list', $this->data);
    }

    public function save () {
        # START 检查权限
        if (!$this->checkPermission(CONDITION, CONDITION_EDIT)) {
            echo json_encode(array('errCode' => 0, 'errMsg' => '权限不足，请联系管理员'));
            return false;
        }
        # END 检查权限
        $post = $this->post;
        if (empty($post['name'])) {
            echo json_encode(array('errCode' => 0, 'errMsg' => '条件名不能为空'));
            return false;
        }
        if (empty($post['condition_id'])){
            echo json_encode(array('errCode' => 0, 'errMsg' => '条件ID不能为空'));
            return false;
        }
        if ($post['id']){ // 修改
            $checkValue = $this->_conditionManageModel->records("name ='".$post['name']."' and id != ".$post['id']);
            if ($checkValue > 0) {
                echo json_encode(array('errCode' => 0, 'errMsg' => '条件名已存在,请用其它条件名'));
                return false;
            }
            $checkCid = $this->_conditionManageModel->records("condition_id ='".$post['condition_id']."' and id != ".$post['id']);
            if ($checkCid > 0) {
                echo json_encode(array('errCode' => 0, 'errMsg' => '条件ID已存在,请用其它条件ID'));
                return false;
            }
        } else { // 新增
            $checkValue = $this->_conditionManageModel->records(array('name' => $post['name']));
            if ($checkValue > 0) {
                echo json_encode(array('errCode' => 0, 'errMsg' => '条件名已存在,请用其它条件名'));
                return false;
            }
            $checkCid = $this->_conditionManageModel->records(array('condition_id' => $post['condition_id']));
            if ($checkCid > 0){
                echo json_encode(array('errCode' => 0, 'errMsg' => '条件ID已存在,请用其它条件ID'));
                return false;
            }
        }
        $post['value'] = stripslashes($post['value']);
        if($this->_conditionManageModel->upd($post['id'], $post)){
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_3');
            $redis->hMset($post['condition_id']."_CON", $post);
            $this->userLogs(array('msg' => json_encode($post), 'title' => '条件管理'), $post['id']);
            echo json_encode(array('errCode' => 1, 'errMsg' => '数据操作成功'));
            return false;
        }else{
            echo json_encode(array('errCode' => 0, 'errMsg' => '数据操作失败'));
            return false;
        }
    }

    public function open(){
        # START 检查权限
        if (!$this->checkPermission(CONDITION, CONDITION_EDIT)) {
            echo json_encode(array('errCode' => 0, 'errMsg' => '权限不足，请联系管理员'));
            return false;
        }
        # END 检查权限
        $post = $this->post;
        if (empty($post['id'])){
            echo json_encode(array('errCode' => 0, 'errMsg' => '无效的条件ID'));
            return false;
        }
        if ($this->_conditionManageModel->updStatus($post['id'], $post['status'])){
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_3');
            $data = $this->_conditionManageModel->findOne(array('id' => $post['id']));
            $redis->hMset($data['condition_id']."_CON", $data);
            $this->userLogs(array('msg' => json_encode($post), 'title' => '条件管理'), $post['id']);
            echo json_encode(array('errCode' => 1, 'errMsg' => '条件状态更新成功'));
            return false;
        }  else {
            echo json_encode(array('errCode' => 0, 'errMsg' => '条件状态更新失败'));
            return false;
        }
    }

    /**
     * 删除条件
     */
    public function delete() {
        # START 检查权限
        if (!$this->checkPermission(CONDITION, CONDITION_DEL)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $id = $this->get['id'];
        if ($id){
            $data = $this->_conditionManageModel->findOne(array('id' => $id));
            $this->_conditionManageModel->del($id);
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_3');
            $redis->del($data['condition_id']."_CON");
            $this->userLogs(array('msg' => json_encode(array('id' => $id)), 'title' => '条件管理', 'action' => 'delete'));
        }
        $this->redirect('../ConditionManages/index');
    }
    /*
     * 获取conditionid获取其信息
     */
    public function getConditionInfo(){
        $condition_id=$this->get["condition_id"];
        if(empty($condition_id)){
            $this->showMsg("条件ID为空");
        }
        $condition=Doo::loadModel("ConditionManages",TRUE);
        $result=$condition->findOne(array("condition_id"=>$condition_id));
        if(empty($result)){
            $this->showMsg("no result");
        }
        $value=json_decode($result["value"],true);
        if(is_array($value)){
            $this->showMsg(array("count"=>sizeof($value),"name"=>$result["name"]),0);
        }else{
            $this->showMsg(array("count"=>1,"name"=>$result["name"]),0);
        }

    }
}