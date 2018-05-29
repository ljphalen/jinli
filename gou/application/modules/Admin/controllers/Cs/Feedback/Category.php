<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @description
 * cs = customer service
 * 问题反馈分类
 * @author ryan
 *
 */
class Cs_Feedback_CategoryController extends Admin_BaseController
{

    public $actions = array(
        'indexUrl' => '/Admin/Cs_Feedback_Category/index',
        'listUrl' => '/Admin/Cs_Feedback_Category/index',
        'addUrl' => '/Admin/Cs_Feedback_Category/add',
        'addPostUrl' => '/Admin/Cs_Feedback_Category/add_post',
        'editUrl' => '/Admin/Cs_Feedback_Category/edit',
        'editPostUrl' => '/Admin/Cs_Feedback_Category/edit_post',
        'deleteUrl' => '/Admin/Cs_Feedback_Category/delete',

    );

    public $perpage = 20;


    public function indexAction()
    {
        $params = $this->getInput(array('parent_id','name'));
        $search = array();
        if(!empty($params['name']))      $search['name']      = array('LIKE',$params['name']);
        if(!empty($params['parent_id'])) $search['parent_id'] = $params['parent_id'];

        list($total, $list) = Cs_Service_FeedbackCategory::getsBy($search, array('sort' => 'desc'));
        list(, $parent)     = Cs_Service_FeedbackCategory::getsBy(array('level' => 0), array('sort' => 'desc'));
        $parent = Common::resetKey($parent,'id');

        $fb_cat_count = array();
        if($total){
            $fb_cat_ids = array_keys(Common::resetKey($list, 'id'));
            $fb_cat_ids = array_filter(array_merge($fb_cat_ids,array_keys($parent)));
            $fb_cat_count = Cs_Service_Feedback::getFeedbackCount($fb_cat_ids);
            if($fb_cat_count) $fb_cat_count = Common::resetKey($fb_cat_count, 'cat_id');
        }

        foreach ($list as $k=>$v ) {
            if($v['parent_id']==0){
                $rows[$v['id']]['item'] = $v;
                continue;
            }else{
                $rows[$v['parent_id']]['item'] = $parent[$v['parent_id']];
                $rows[$v['parent_id']]['child'][] = $v;
            }
        }

        $this->assign('parent',  $parent);
        $this->assign('data',    $rows);
        $this->assign('search',  $params);
        $this->assign('fb_cat_count',  $fb_cat_count);
        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($params)) . '&';
//        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }

    public function addAction()
    {
        list(, $parent) = Cs_Service_FeedbackCategory::getsBy(array('level' => 0));
        $this->assign('parent', $parent);
    }

    public function add_postAction()
    {
        $info = $this->getPost(array('parent_id', 'name', 'sort'));
        $info = $this->_cookData($info);
        list($total) = Cs_Service_FeedbackCategory::getsBy(array('name' => $info['name']), array());
        if ($total) $this->output(-1, '分类名称不能重复.');
        $ret = Cs_Service_FeedbackCategory::add($info);
        if (!$ret) $this->output(-1, '操作失败.');
        $this->output(0, '操作成功.');
    }

    public function editAction()
    {
        $id             = $this->getInput('id');
        $info           = Cs_Service_FeedbackCategory::get($id);
        list(, $parent) = Cs_Service_FeedbackCategory::getsBy(array('level' => 0));

        $this->assign('parent', $parent);
        $this->assign('info', $info);
    }

    public function edit_postAction()
    {
        $info = $this->getPost(array('id', 'parent_id', 'name', 'sort'));
        $info = $this->_cookData($info);
        list($total,) = Cs_Service_FeedbackCategory::getsBy(array('name' => $info['name'], 'id' => array('<>', $info['id'])), array());
        if ($total) $this->output(-1, '分类名称不能重复.');
        $ret = Cs_Service_FeedbackCategory::update($info, $info['id']);
        if (!$ret) $this->output(-1, '操作失败.');
        $this->output(0, '操作成功.');
    }

    public function deleteAction()
    {
        $id = $this->getInput('id');
        $info = Cs_Service_FeedbackCategory::get($id);
        if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
        $ret = Cs_Service_FeedbackCategory::delete($id);
        if (!$ret) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    private function _cookData($info)
    {
        if (!$info['name']) $this->output(-1, '名称不能为空.');
        $info['level']=0;
        if($info['parent_id'])$info['level']=1;
        return $info;
    }

}