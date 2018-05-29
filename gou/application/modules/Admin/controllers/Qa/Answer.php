<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 问答-回帖
 * QaController
 *
 * @author Terry
 *
 */
class Qa_AnswerController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Qa_Answer/index',
        'addUrl' => '/Admin/Qa_Answer/add',
        'replyUrl' => '/Admin/Qa_Answer/reply',
        'addPostUrl' => '/Admin/Qa_Answer/add_post',
        'replyPostUrl' => '/Admin/Qa_Answer/reply_post',
        'editUrl' => '/Admin/Qa_Answer/edit',
        'editPostUrl' => '/Admin/Qa_Answer/edit_post',
        'deleteUrl' => '/Admin/Qa_Answer/delete',
        'checkUrl' => '/Admin/Qa_Answer/check',
    );

    public $perpage = 15;

    /**
     *
     * 回帖列表
     *
     */
    public function indexAction(){
        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;

        $param = $this->getInput(array('uid', 'item_id', 'status','from_time','to_time', 'content', 'item_id', 'nickname', 'scoreid'));
        if (!empty($param['uid'])) $search['uid'] = $param['uid'];
        if (!empty($param['item_id'])) $search['item_id'] = $param['item_id'];
        if (!empty($param['content'])) $search['content'] = array('LIKE', $param['content']);
        if (!empty($param['status'])) $search['status'] = $param['status']-1;
        if (!empty($param['item_id'])) $search['item_id'] = intval($param['item_id']);
        if ($param['from_time']) $search['create_time'] = array('>=', strtotime($param['start_time']));
        if ($param['to_time']) $search['create_time'] = array('<=', strtotime($param['end_time']) + 24*60*60);
        if ($param['from_time'] && $param['to_time']) {
            $search['create_time'] = array(
                array('>=', strtotime($param['from_time'])),
                array('<=', strtotime($param['to_time']) + 24*60*60)
            );
        }

        $users = $uids = array();
        $s_n_seach = false;
        if(!empty($param['scoreid']) && !empty($param['nickname'])){
            $s_n_seach = true;
            $users = User_Service_Uid::getsBy(array('scoreid'=>$param['scoreid'], 'nickname'=>$param['nickname']));
        }elseif(!empty($param['scoreid']) && empty($param['nickname'])){
            $s_n_seach = true;
            $users = User_Service_Uid::getsBy(array('scoreid'=>$param['scoreid']));
        }elseif(empty($param['scoreid']) && !empty($param['nickname'])){
            $s_n_seach = true;
            $users = User_Service_Uid::getsBy(array('nickname'=>$param['nickname']));
        }

        if($users){
            $uids = array_keys(Common::resetKey($users, 'uid'));
            if(isset($search['uid'])) $uids[] = $search['uid'];
            $uids = array_unique($uids);
            if($uids) $search['uid'] = array('IN', $uids);
        }else{
            if($s_n_seach && !isset($search['uid'])) $search['uid'] = array('IN', '');
        }

        $orderby = array('create_time'=>'DESC','id'=>'DESC');
        list($total, $result) = Gou_Service_QaAus::getList($page, $perpage, $search, $orderby);

        $uid = Common::resetKey($result, 'uid');
        if(!empty($uid)){
            $uid = User_Service_Uid::getsBy(array('uid'=>array('IN', array_keys($uid))));
            $uid = Common::resetKey($uid, 'uid');
        }

        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('url', $url);
        $this->assign('uid', $uid);
        $this->assign('param', $param);
        if($param['item_id']) $this->assign('item_id', $param['item_id']);
        $this->assign('data', $result);
        $status = Gou_Service_QaAus::$status;
        $status[0] = '保存';
        $this->assign('status', $status);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }

    /**
     *
     *添加回帖
     *
     */
    public function addAction(){
        $item_id = $this->getInput('item_id');
        if(empty($item_id)) $this->output(-1, '参数有误.');

        $authors = User_Service_Uid::getVirtualUser();

        $status = Gou_Service_QaAus::$status;
        $status[0] = '保存';
        array_pop($status);

        $callback = $this->getInput('callback');

        $this->assign('item_id', $item_id);
        $this->assign('callback', $callback);
        $this->assign('authors', $authors);
        $this->assign('status', $status);
    }

    /**
     * 回复回贴
     */
    public function replyAction(){
        $item_id = $this->getInput('item_id');
        $id = $this->getInput('id');
        if(empty($item_id) || empty($id)) $this->output(-1, '参数有误.');

        $authors = User_Service_Uid::getVirtualUser();

        $status = Gou_Service_QaAus::$status;
        $status[0] = '保存';

        $callback = $this->getInput('callback');

        $this->assign('item_id', $item_id);
        $this->assign('id', $id);
        $this->assign('authors', $authors);
        $this->assign('status', $status);
        $this->assign('callback', $callback);
    }

    /**
     * 编辑回贴
     */
    public function editAction() {
        $id = $this->getInput('id');

        $info = Gou_Service_QaAus::get(intval($id));
        if(!$info) $this->output(-1, '无该记录');

        $status = Gou_Service_QaAus::$status;
        $status[0] = '保存';

        $author = User_Service_Uid::getByUid($info['uid']);

        $relate_item_title = '';
        if($info['relate_item_id']){
            $relate_item = Gou_Service_QaQus::get($info['relate_item_id']);
            $relate_item_title = $relate_item ? $relate_item['title']:'';
        }

        $callback = $this->getInput('callback');

        $this->assign('status', $status);
        $this->assign('info', $info);
        $this->assign('author', $author);
        $this->assign('relate_item_title', $relate_item_title);
        $this->assign('reason', Gou_Service_QaAus::$reason);
        $this->assign('callback', $callback);
    }

    /**
     * 回帖提交
     */
    public function add_postAction(){
        $info = $this->getPost(array('uid', 'status', 'content', 'praise', 'item_id', 'relate_item_id'));
        $info = $this->_cookData($info);
        $info['is_admin'] = 1;

        $result = Gou_Service_QaAus::add($info);
        if($result){
            Gou_Service_QaQus::updateTotal($info['item_id']);
            $qus = Gou_Service_QaQus::get($info['item_id']);
            User_Service_Msg::addQaAusMsg(4, $qus['uid'], $qus['title'], $info['uid'], $qus['id']);

            //push
            list($nickname) = User_Service_Uid::getUserFmtByUid($info['uid']);
            $push_title = User_Service_Msg::msgFmt(4, 0, $nickname);
            User_Service_Msg::pushMsg($qus['uid'], $push_title, $qus['title'], 3, 'com.gionee.client.MesaggeList');
        }

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功',$result);
    }

    /**
     * 追贴提交
     */
    public function reply_postAction(){
        $info = $this->getPost(array('parent_id', 'uid', 'status', 'content', 'praise', 'item_id', 'relate_item_id'));
        $info = $this->_cookData($info);
        $info['is_admin'] = 1;

        $result = Gou_Service_QaAus::add($info);
        if($result){
            Gou_Service_QaQus::updateTotal($info['item_id']);
            $qus = Gou_Service_QaQus::get($info['item_id']);
            //给问贴人发消息
            User_Service_Msg::addQaAusMsg(4, $qus['uid'], $qus['title'], $info['uid'], $qus['id']);

            //给回帖人发消息
            $aus = Gou_Service_QaAus::get($info['parent_id']);
            User_Service_Msg::addQaAusMsg(4, $aus['uid'], $qus['title'], $info['uid'], $qus['id']);

            //push
            list($nickname) = User_Service_Uid::getUserFmtByUid($info['uid']);
            $push_title = User_Service_Msg::msgFmt(4, 0, $nickname);
            User_Service_Msg::pushMsg($qus['uid'], $push_title, $qus['title'], 3, 'com.gionee.client.MesaggeList');
            if($qus['uid'] != $aus['uid']) User_Service_Msg::pushMsg($aus['uid'], $push_title, $qus['title'], 3, 'com.gionee.client.MesaggeList');
        }

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功',$result);
    }

    /**
     * 编辑提交
     */
    public function edit_postAction(){
        $info = $this->getPost(array('id', 'uid', 'content', 'status', 'reason', 'praise', 'relate_item_id'));
        $info = $this->_cookData($info);

        $result = Gou_Service_QaAus::update($info, intval($info['id']));

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }

    /**
     * 单个/批量审核
     */
    public function checkAction(){
        $id = $this->getInput('id');
        $status = $this->getInput('status');

        if(empty($id))  $this->output(1, '请选择条目');
        if(is_null($status) )  $this->output(1, '参数有误');

        $info['status'] = intval($status);

        if(is_array($id) && !empty($id)){
            $result = Gou_Service_QaAus::updates('id', $id, $info);
        }else{
            $result = Gou_Service_QaAus::update($info, $id);
        }
        $this->output(0, '操作成功', array('result' => $result));
    }

    /**
     *
     *删除问贴
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        if (!empty($id)){
            if(is_array($id)){
                $list = Gou_Service_QaAus::getsBy(array('id' => array('IN', $id)));
                array_walk($list, function(&$v){$v = $v['item_id'];});
                $count = array_count_values($list);
                $result = Gou_Service_QaAus::deletes('id', $id);
                foreach($count as $k=>$v){
                    Gou_Service_QaQus::updateTotal($k, -$v);
                }
            }else{
                $info = Gou_Service_QaAus::get($id);
                if(!$info){
                    $this->output(-1, '无法删除');
                }
                $result = Gou_Service_QaAus::delete($id);
                $res = Gou_Service_QaQus::updateTotal($info['item_id'], -1);
            }
        }else{
            $this->output(-1, '请选择条目');
        }

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
        if(!$info['uid']) $this->output(-1, '作者不能为空.');
        if(!$info['content']) $this->output(-1, '内容不能为空.');

        if(Util_String::strlen(html_entity_decode($info['content'], ENT_QUOTES)) < 10 || Util_String::strlen(html_entity_decode($info['content'], ENT_QUOTES)) > 500) $this->output(-1, '回答限制为10~500个字, 请修改后发布.');
        list(, $kwd) = Gou_Service_Sensitive::fuck($info['content']);
        if(!empty($kwd)) $this->output(-1, '内容不能包含 "' . implode(',',$kwd) . '" ，请修改', $kwd);

        $info['content'] = htmlspecialchars_decode($info['content']);

        return $info;
    }
}