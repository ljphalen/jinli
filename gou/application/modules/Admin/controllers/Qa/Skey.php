<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 问答-搜索关键词
 * QaController
 *
 * @author Terry
 *
 */
class Qa_SkeyController extends Admin_BaseController {

    public $perpage = 15;

    public $actions = array(
        'indexUrl' => '/Admin/Qa_Skey/index',
    );

    /**
     *
     * 问题搜索关键词列表
     *
     */
    public function indexAction(){
        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;

        $param = $this->getInput(array('skey', 'from_time', 'to_time'));
        if (!empty($param['skey'])) $search['skey'] = array('LIKE', $param['skey']);

        if ($param['from_time']) $search['dateline'] = array('>=', $param['from_time']);
        if ($param['to_time']) $search['dateline'] = array('<=', $param['to_time']);
        if ($param['from_time'] && $param['to_time']) {
            $search['dateline'] = array(
                array('>=', $param['from_time']),
                array('<=', $param['to_time'])
            );
        }
        $orderby = array('dateline' => 'DESC', 'count' => 'DESC');

        list($total, $result) = Gou_Service_QaSkey::getList($page, $perpage, $search, $orderby);

        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('param', $param);
        $this->assign('data', $result);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }
}