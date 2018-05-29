<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class LogController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Log/index',
        'patchDeleteUrl' => '/Admin/Log/patchDelete',
    );

    public $error_level = array(
        1       => 'E_ERROR',
        2       => 'E_WARNING',
        4       => 'E_PARSE',
        8       => 'E_NOTICE',
        2048    => 'E_STRICT',
        8191    => 'E_ALL',
        512     => 'YAF_ERR_STARTUP_FAILED',
        513     => 'YAF_ERR_ROUTE_FAILED',
        514     => 'YAF_ERR_DISPATCH_FAILED',
        515     => 'YAF_ERR_NOTFOUND_MODULE',
        516     => 'YAF_ERR_NOTFOUND_CONTROLLER',
        517     => 'YAF_ERR_NOTFOUND_ACTION',
        518     => 'YAF_ERR_NOTFOUND_VIEW',
        519     => 'YAF_ERR_CALL_FAILED',
        520     => 'YAF_ERR_AUTOLOAD_FAILED',
        521     => 'YAF_ERR_TYPE_ERROR',
    );

    public $perpage = 20;
    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        $param = $this->getInput(array('type', 'from_time', 'to_time'));

        $search = array();
        if ($param['type']) $search['type'] = intval($param['type']);
        if ($param['from_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']));
        if ($param['to_time']) $search['time'] = array('$lte'=>strtotime($param['to_time']));
        if ($param['from_time'] && $param['to_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']), '$lte'=>strtotime($param['to_time']));

        $perpage = $this->perpage;
        $start = $perpage*($page?$page-1:0);
        $logs = Common::getMongo()->find('log', $search, array('start'=>$start, 'limit'=>$perpage, 'sort'=>array('time'=>-1)));
        $total = Common::getMongo()->count('log', $search);

        $this->assign('logs', $logs);
        $this->assign('param', $param);
        $this->assign('error_level', $this->error_level);

        $url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->assign('total', $total);
    }

    /**
     *
     * Enter description here ...
     */
    public function patchDeleteAction() {
        $param = $this->getInput(array('type', 'from_time', 'to_time'));

        $search = array();
        if ($param['type']) $search['type'] = intval($param['type']);
        if ($param['from_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']));
        if ($param['to_time']) $search['time'] = array('$lte'=>strtotime($param['to_time']));
        if ($param['from_time'] && $param['to_time']) $search['time'] = array('$gte'=>strtotime($param['from_time']), '$lte'=>strtotime($param['to_time']));

        $result = Common::getMongo()->delete('log', $search, array('justOne'=>false));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

}