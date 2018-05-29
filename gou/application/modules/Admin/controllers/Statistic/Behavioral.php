<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Statistic_BehavioralController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Statistic_Behavioral/index',
        'detailUrl' => '/Admin/Statistic_Behavioral/detail',
        'userUrl' => '/Admin/Statistic_Behavioral/user',
        'patchDeleteUrl' => '/Admin/Statistic_Behavioral/patchDelete',
    );

    public $perpage = 20;
    private $table = 'behavioral';

    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $param = $this->getInput(array('module', 'from_time', 'to_time', 'username', 'groupid'));

        $search = array();
        if ($param['module']) {
            $regex = new MongoRegex('/' . addcslashes(trim($param['module']), '?') . '/');
            $search['url'] = $regex;
        }
        if ($param['username']) $search['username'] = trim($param['username']);
        if ($param['groupid']) $search['groupid'] = $param['groupid'];
        if ($param['from_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']));
        if ($param['to_time']) $search['time'] = array('$lte'=>strtotime($param['to_time']));
        if ($param['from_time'] && $param['to_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']), '$lte'=>strtotime($param['to_time']));
        if(!$search['time']) $search['time'] = array('$gte'=>strtotime('-2 days'));

        $perpage = $this->perpage;
        $start = $perpage * ($page ? $page - 1 : 0);
        $logs = Common::getMongo()->find($this->table, $search, array('start' => $start, 'limit' => $perpage, 'sort' => array('time' => -1)));
        $total = Common::getMongo()->count($this->table, $search);

        list(, $groups) = Admin_Service_Group::getAllGroup();
        $groups = Common::resetKey($groups, 'groupid');

        $this->assign('logs', $logs);
        $this->assign('groups', $groups);
        $this->assign('param', $param);

        $url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('total', $total);
    }

    /**
     *
     */
    public function userAction() {
        $uid = intval($this->getInput('uid'));
        if(!$uid) $this->output(-1, '操作失败');

        $page = intval($this->getInput('page'));
        $param = $this->getInput(array('module', 'from_time', 'to_time'));

        $search = array();
        if ($uid) $search['uid'] = strval($uid);
        if ($param['module']) {
            $regex = new MongoRegex('/' . addcslashes(trim($param['module']), '?') . '/');
            $search['url'] = $regex;
        }
        if ($param['from_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']));
        if ($param['to_time']) $search['time'] = array('$lte'=>strtotime($param['to_time']));
        if ($param['from_time'] && $param['to_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']), '$lte'=>strtotime($param['to_time']));
        if(!$search['time']) $search['time'] = array('$gte'=>strtotime('-2 days'));

        $perpage = $this->perpage;
        $start = $perpage * ($page ? $page - 1 : 0);
        $logs = Common::getMongo()->find($this->table, $search, array('start' => $start, 'limit' => $perpage, 'sort' => array('time' => -1)));
        $total = Common::getMongo()->count($this->table, $search);

        list(, $groups) = Admin_Service_Group::getAllGroup();
        $groups = Common::resetKey($groups, 'groupid');

        $this->assign('uid', $uid);
        $this->assign('logs', $logs);
        $this->assign('groups', $groups);
        $this->assign('param', $param);

        $url = $this->actions['userUrl'] .'/?'. http_build_query($param) . '&uid=' . $uid . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('total', $total);
    }

    /**
     * 详情
     */
    public function detailAction(){
        $id = $this->getInput('id');
        if(!$id) $this->output(-1, '操作失败');

        $log = Common::getMongo()->findOne($this->table, array('_id' => new MongoId("$id")));
        if($log) $log['data'] = json_decode($log['data'], true);

        list(, $groups) = Admin_Service_Group::getAllGroup();
        $groups = Common::resetKey($groups, 'groupid');

        $this->assign('groups', $groups);
        $this->assign('log', $log);
    }

    /**
     *
     * Enter description here ...
     */
    public function patchDeleteAction() {
        $param = $this->getInput(array('module', 'from_time', 'to_time', 'username', 'groupid'));

        $search = array();
        if ($param['module']) {
            $regex = new MongoRegex('/'.trim($param['module']).'/');
            $search['url'] = $regex;
        }
        if ($param['username']) $search['username'] = trim($param['username']);
        if ($param['groupid']) $search['groupid'] = $param['groupid'];
        if ($param['from_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']));
        if ($param['to_time']) $search['time'] = array('$lte'=>strtotime($param['to_time']));
        if ($param['from_time'] && $param['to_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']), '$lte'=>strtotime($param['to_time']));

        $result = Common::getMongo()->delete($this->table, $search, array('justOne'=>false));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

}