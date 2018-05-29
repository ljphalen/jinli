<?php
/**
 * 角色模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class UserLog extends AppModel {

    private $_userLogModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        // 设置数据库连接
        Doo::db()->reconnect('admin');
        $this->_userLogModel = Doo::loadModel("datamodel/UserLogs", TRUE);
    }

    public function addLogs($data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->_userLogModel->date = date('Y-m-d H:i:s');
        $this->_userLogModel->title = isset($data['title']) ? $data['title'] : '';
        $this->_userLogModel->action = isset($data['action']) ? $data['action'] : '';
        $this->_userLogModel->msg = isset($data['msg']) ? $data['msg'] : '';
        $this->_userLogModel->type = isset($data['type']) ? $data['type'] : '';
        $this->_userLogModel->update_url = isset($data['update_url']) ? $data['update_url'] : '';
        $this->_userLogModel->snapshot_url = isset($data['snapshot_url']) ? $data['snapshot_url'] : '';
        $this->_userLogModel->username = $currentUser['username'];
        return $this->_userLogModel->insert();
    }
}