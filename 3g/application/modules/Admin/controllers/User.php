<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class UserController
 * 后台用户权限
 * @author rainkid
 */
class UserController extends Admin_BaseController {

    public $actions = array(
        'listUrl'       => '/Admin/User/index',
        'addUrl'        => '/Admin/User/add',
        'addPostUrl'    => '/Admin/User/add_post',
        'editUrl'       => '/Admin/User/edit',
        'editPostUrl'   => '/Admin/User/edit_post',
        'deleteUrl'     => '/Admin/User/delete',
        'passwdUrl'     => '/Admin/User/passwd',
        'passwdPostUrl' => '/Admin/User/passwd_post',
        'logUrl'        => '/Admin/User/log',
        'editlogUrl'    => '/Admin/User/editlog',
    );

    public $viewDir = 'user';

    public $perpage = 20;

    public function indexAction() {
        Yaf_Registry::set("viewDir", 'user');
        $page    = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $where   = $this->getInput('where');
        $params  = array();

        if (!empty($where['username'])) {
            $params['username'] = $where['username'];
        }
        if (!empty($where['uid'])) {
            $params['uid'] = $where['uid'];
        }

        $this->assign('viewDir', $this->viewDir);

        if ($page) {
            list($total, $users) = Admin_Service_User::getList($page, $perpage, $params);
            list(, $groups) = Admin_Service_Group::getAllGroup();
            $groups = Common::resetKey($groups, 'groupid');
            foreach ($users as $k => $user) {
                foreach ($groups as $group) {
                    if ($user['groupid'] == $group['groupid']) {
                        $users[$k]['groupname'] = $group['name'];
                    }
                }
            }

            $json_data = array('rows' => $users, 'total' => $total);
            header("Content-type:text/json;charset=utf-8");
            echo json_encode($json_data);
            exit;
        }
    }

    public function editAction() {
        $uid = $this->getInput('uid');
        if ($this->getPost('token')) {
            $this->edit_postAction();
        } else {
            if ($uid) {
                $userInfo = Admin_Service_User::getUser(intval($uid));
                list(, $groups) = Admin_Service_Group::getAllGroup();
                $this->assign('userInfo', $userInfo);
                $this->assign('groups', $groups);

            }
        }
    }

    public function addAction() {
        if ($_POST['token']) {
            $this->add_postAction();
        } else {
            list(, $groups) = Admin_Service_Group::getAllGroup();
            $this->assign('groups', $groups);
        }

    }

    public function add_postAction() {
        $info = $this->getPost(array('username', 'password', 'r_password', 'email', 'groupid'));
        if (strlen($info['username']) < 5 || strlen($info['username']) > 16) $this->output(-1, '用户名长度5-16位之间');
        if (strlen($info['password']) < 5 || strlen($info['password']) > 16) $this->output(-1, '用户密码长度5-16位之间.');
        if ($info['password'] !== $info['r_password']) $this->output(-1, '两次密码输入不一致.');
        if ($info['email'] == '') $this->output(-1, '用户EMAIL必填.');
        if (Admin_Service_User::getUserByName($info['username'])) $this->output(-1, '用户名已经存在.');
        if (Admin_Service_User::getUserByEmail($info['email'])) $this->output(-1, '邮件地址已经存在.');
        $info['registerip'] = Util_Http::getClientIp();
        $result             = Admin_Service_User::addUser($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    public function edit_postAction() {
        $info = $this->getPost(array('uid', 'groupid', 'password', 'r_password'));
        if ($info['password'] == '' && $info['r_password'] == '') {
            // 当用户密码没有输入时，更新用户信息
            $info = array_filter($info, function ($v) {
                if ($v != '') {
                    return true;
                }
            });
        } else {
            //当用户密码输入时，对用户信息进行验证
            //if ($info['password'] == '') $this->output(-1, '密码不能为空.');
            if (strlen($info['password']) < 5 || strlen($info['password']) > 16) $this->output(-1, '用户密码长度5-16位之间');
            if ($info['password'] !== $info['r_password']) $this->output(-1, '两次密码输入不一致');
        }
        $ret = Admin_Service_User::updateUser($info, intval($info['uid']));
        if (!$ret) $this->output(-1, '更新用户失败');
        $this->output(0, '更新用户成功.');
    }

    public function passwdAction() {
        if (!$this->userInfo) Common::redirect("/Admin/Login/index");
    }

    public function passwd_postAction() {
        $adminInfo = $this->userInfo;
        if (!$adminInfo['uid']) $this->output(-1, '登录超时,请重新登录后操作');
        $info = $this->getPost(array('current_password', 'password', 'r_password'));
        $ret  = Admin_Service_User::checkUser($adminInfo['username'], $info['current_password']);
        if (Common::isError($ret)) $this->output(-1, $ret['msg']);
        $info['uid'] = $adminInfo['uid'];
        if (strlen($info['password']) < 5 || strlen($info['password']) > 16) $this->output(-1, '用户密码长度5-16位之间');
        if ($info['password'] !== $info['r_password']) $this->output(-1, '两次密码输入不一致');
        $result = Admin_Service_User::updateUser($info, intval($info['uid']));
        if (!$result) $this->output(-1, '编辑失败');
        $this->output(0, '操作成功');
    }

    public function deleteAction() {
        $uid  = $this->getInput('id');
        $info = Admin_Service_User::getUser($uid);
        if ($info && $info['groupid'] == 0) $this->output(-1, '此用户无法删除');
        if ($uid < 1) $this->output(-1, '参数错误');
        $result = Admin_Service_User::deleteUser($uid);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    public function editlogAction() {
        $id   = $this->getInput('id');
        $info = Admin_Service_Log::get($id);
        $this->assign('info', $info);
    }

    public function logAction() {
        $get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
        $togrid = !empty($get['togrid']) ? true : false;
        if ($togrid) {

            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'create_time';
            $order          = !empty($get['order']) ? $get['order'] : 'desc';
            $orderBy[$sort] = $order;
            $where          = array();
            foreach ($_POST['filter'] as $k => $v) {
                $where[$k] = $v;
            }

            if (!isset($orderBy['id'])) {
                $orderBy['id'] = 'desc';
            }


            list($total, $list) = Admin_Service_Log::getList($page, $offset, $where, $orderBy);
            foreach ($list as $k => $v) {
                $list[$k]['create_time'] = date('y/m/d H:i', $v['create_time']);
                list($c, $a, $msg) = json_decode($v['message'], true);
                $list[$k]['action'] = $c . '_' . $a;
                $list[$k]['msg']    = Common::jsonEncode($msg);
            }
            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo json_encode($ret);
            exit;
        }
    }

}
