<?php
/**
 * 站内信
 *
 * @author Intril.Leng
 */

Doo::loadController("AppDooController");
class SiteMsgController extends AppDooController {

    public function index(){
        # START 检查权限
        if (!$this->checkPermission(WEB, WEB_MSG_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        // 选择模板
        $url = "index?";
        $MsgsModel = Doo::loadModel('Msgs', TRUE);
        // 分页
        $total = $MsgsModel->records($whereArr);
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $whereArr['desc'] = "createdate";
        $this->data['result'] = $MsgsModel->findAll($whereArr);
        if ($this->data['result']){
            foreach ($this->data['result'] as $key => $info){
                $this->data['result'][$key]['senddate'] = date('Y-m-d H:i:s', $info['senddate']);
                $this->data['result'][$key]['createdate'] = date('Y-m-d H:i:s', $info['createdate']);
            }
        }
        $this->data['current_time'] = date("Y-m-d H:i:s", (time() + 1800));
        $this->myrender('sitemsg/index', $this->data);
    }

    public function edit(){
        # START 检查权限
        if (!$this->checkPermission(WEB, WEB_MSG_SEND)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        $MsgsModel = Doo::loadModel('Msgs', TRUE);
        if (isset($get['id']) && $get['id'] && is_numeric($get['id'])) {// 编辑
            $whereArr = array('id' => $get['id']);
            $this->data['result'] = $MsgsModel->findOne($whereArr);
            $this->data['title'] = '修改';
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_3');
            $devId = $redis->get("WWW_MOBGI_SITEMSG_".$get['id']);
            $this->data['senddate'] = date("Y-m-d", $this->data['result']['senddate']);
            $this->data['hour'] = date("H", $this->data['result']['senddate']);
            $this->data['second'] = date("i", $this->data['result']['senddate']);
        }else{
            $this->data['result'] = array(
                'id' => '', 'dev_id' => '', 'title' => '', 'msg' => "", 'senddate' => '',
            );
            $this->data['title'] = '添加';
            $devId = $get["dev_id"];
        }
        // 取开发者列表
        $devIdArr = explode(",", $devId);
        $developerModel = Doo::loadModel('Developer', TRUE);
        //$developer = Doo::db()->fetchAll("SELECT * FROM mobgi_www.users");
        $developer=$developerModel->findAll(array("ischeck"=>1));
        $this->data['right'] = $this->data['left'] = array();
        foreach ($developer as $key => $dev){
            if (in_array($dev['dev_id'], $devIdArr)){
                $this->data['right'][$dev['dev_id']] = $dev['email'];
            }else{
                $this->data['left'][$dev['dev_id']] = $dev['email'];
            }
        }
        $this->data['right'] = json_encode($this->data['right']);
        $this->data['left'] = json_encode($this->data['left']);
        // 选择模板
        $this->myrender('sitemsg/detail', $this->data);
    }

    public function save(){
        # START 检查权限
        if (!$this->checkPermission(WEB, WEB_MSG_SEND)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        if (empty($post['title'])){
            $this->redirect("javascript:history.go(-1)","请填写标题");
        }
        if (!empty($post['hour'])){
            if (!is_numeric($post['hour']) || $post['hour'] > 23 ){
                $this->redirect("javascript:history.go(-1)","小时必须是小于24的数字");
            }
        }else{
            $post['hour'] = "00";
        }
        if (!empty($post['second'])){
            if (!is_numeric($post['second']) || $post['second'] > 59) {
                $this->redirect("javascript:history.go(-1)","分钟必须是小于60的数字");
            }
        }else{
            $post['second'] = "00";
        }
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_3');

        // 存DB
        $post['senddate'] = $post['senddate']. " " . $post['hour'] .':'.$post['second'].":00";
        if ($post['senddate'] <= date('Y-m-d H:i:s', (time()-1800))){ // 必须大于当前时间前半小时
            $this->redirect("javascript:history.go(-1)","发送时间必须大于当前时间");
        }
        $MsgsModel = Doo::loadModel('Msgs', TRUE);
        $id = $MsgsModel->upd ( $post ['id'], $post );
        $redis->set("WWW_MOBGI_SITEMSG_".$id, $post['dev_id']);

        $this->userLogs(array('msg' => json_encode($post), 'title' => '站内信'), $post['id']);
        $this->redirect ( '../SiteMsg/index' );
    }

    public function show(){
        $id = $this->post['id'];
        if (!$id) {
            echo json_encode(array('retCode' => -1, 'msg' => '请传入ID'));
        }
        $MsgModel = Doo::loadModel('Msgs', TRUE);
        $data = $MsgModel->findOne(array('id' => $id));
        echo json_encode(array('retCode' => 1, 'msg' => $data));
    }

    public function delete(){
        # START 检查权限
        if (!$this->checkPermission(WEB, WEB_MSG_SEND)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $MsgsModel = Doo::loadModel('Msgs', TRUE);
        $id = $this->get['id'];
        $msg = $MsgsModel->findOne(array('id' => $id));
        // 检查是否已发送
        if ($msg['senddate'] < time()){
            $this->redirect("javascript:history.go(-1)","站内信已发送出去，禁止删除");
        }
        if ($id){
            $MsgsModel->del($id);
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_3');
            $redis->delete("WWW_MOBGI_SITEMSG_".$id);
            $this->userLogs(array('msg' => json_encode($msg), 'title' => '站内信', 'action' => 'delete'));
        }
        $this->redirect('../SiteMsg/index',"删除成功");
    }
}