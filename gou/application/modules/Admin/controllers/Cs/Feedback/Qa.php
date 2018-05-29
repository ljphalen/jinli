<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description
 * cs = customer service
 * 常见问题及答案
 *
 * @author ryan
 *
 */
class Cs_Feedback_QaController extends Admin_BaseController {

    public $versionName = 'Feedback_Version';

    public $actions = array(
        'indexUrl' => '/Admin/Cs_Feedback_Qa/index',
        'exportUrl' => '/Admin/Cs_Feedback_Qa/export',
        'viewUrl' => '/Admin/Cs_Feedback_Qa/list',
        'catUrl' => '/Admin/Cs_Feedback_Qa/getCate',
        'replyUrl' => '/Admin/Cs_Feedback_Qa/reply',
        'replyPostUrl' => '/Admin/Cs_Feedback_Qa/reply_post',
        'appendUrl' => '/Admin/Cs_Feedback_Qa/append',
        'appendPostUrl' => '/Admin/Cs_Feedback_Qa/append_post',
        'catePostUrl' => '/Admin/Cs_Feedback_Qa/cate_post',
        'addUrl' => '/Admin/Cs_Feedback_Qa/add',
        'addPostUrl' => '/Admin/Cs_Feedback_Qa/add_post',
        'editUrl' => '/Admin/Cs_Feedback_Qa/edit',
        'editPostUrl' => '/Admin/Cs_Feedback_Qa/edit_post',
        'uploadImgUrl' => '/Admin/Cs_Feedback_Qa/uploadImg',
        'deleteUrl' => '/Admin/Cs_Feedback_Qa/delete',
        'uploadUrl' => '/Admin/Cs_Feedback_Qa/upload',
        'uploadPostUrl' => '/Admin/Cs_Feedback_Qa/upload_post',
    );

    public $perpage = 20;

    public function indexAction(){

        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;

//        $search['status'] = 1;
        $params = $this->getInput(array('uid', 'content', 'model', 'parent_id', 'cat_id', 'version', 'has_new', 'time_type', 'from_time', 'to_time'));
        $cat_id = (!empty($params['cat_id'])) ? $params['cat_id'] : $params['parent_id'];

        $search['type'] = 0;
        $params['has_new'] = $params['has_new'] == '' ? -1 : $params['has_new'];
        if (!empty($params['uid']))        $search['uid']        = $params['uid'];
        if (!empty($cat_id))               $search['cat_id']     = $cat_id;
        if (!empty($params['model']))      $search['model']      = array('LIKE', $params['model']);
        if (!empty($params['version']))    $search['version']    = array('LIKE', $params['version']);
        if (!empty($params['content']))    $search['content']    = array('LIKE', $params['content']);

        if (!empty($params['time_type'])){
            $tCol = $params['time_type'];
            if ($params['from_time']) $search[$tCol] = array('>=', strtotime($params['from_time']));
            if ($params['to_time'])   $search[$tCol] = array('<=', strtotime($params['to_time']));
            if ($params['from_time'] && $params['to_time']) {
                $search[$tCol] = array(
                    array('>=', strtotime($params['from_time'] )),
                    array('<=', strtotime($params['to_time']))
                );
            }
        }
        $orderby = array();
        $groupby = 'uid';

        list($user_count,) = Cs_Service_FeedbackUser::getList(1,1,array(),array());
        list($count, )     = Cs_Service_Feedback::getListByGroup(1,1,array(),array(),'type');
        $count = Common::resetKey($count,'type');
        $count[0]['name'] = '反馈数量';
        $count[1]['name'] = '回复数量';
        $count[3]['name'] = '追加回复';
        $total_count =  array_sum(array_keys(Common::resetKey($count,'num')));
        array_push($count,array('name'=>'用户总数','num'=>$user_count));


        //获取uid
        list(, $rows) = Cs_Service_Feedback::getsByGroup($search, $orderby, $groupby);
        $uids = array_keys(Common::resetKey($rows, 'uid'));

        array_walk($uids, function (&$v) {
            $v = trim($v);
        });

        $condition = array('uid' => array('IN', array_filter($uids)));
        if ($params['has_new']!=-1)    $condition['has_new'] = $params['has_new'];
        $orderby = array('last_time' => 'desc', 'has_new' => 'desc');
        list($total, $data) = Cs_Service_FeedbackUser::getList($page, $perpage, $condition, $orderby);

        list(, $parent) = Cs_Service_FeedbackCategory::getsBy(array('level'=>0));
        $child = array();
        if($params['parent_id']){
            list(, $child) = Cs_Service_FeedbackCategory::getsBy(array('level'=>1,'parent_id'=>$params['parent_id']));
        }

        //自动回复
        $is_auto   = Gou_Service_Config::getValue('feedback_auto_reply');
        $this->assign('data',          $data);
        $this->assign('is_auto',     $is_auto);
        $this->assign('count',         $count);
        $this->assign('total_count',   $total_count);
        $this->assign('child',         $child);
        $this->assign('search',        $params);
        $this->assign('parent',        $parent);
        $url = $this->actions['indexUrl'] . '/?' . http_build_query(array_filter($params)) . '&';
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }


    public function getTipAction() {
        $cache = Common::getCache();
        $new_num =$cache->get('feedback_tip');
        $num = Util_Cookie::get('feedback_tip');
        if($new_num>$num){
            Util_Cookie::set('feedback_tip', $new_num, false, Common::getTime()+3600*24);
            $url = "/Admin/Cs_Feedback_Qa/index";
            echo json_encode(array('num'=>$new_num,'url'=>$url));
            exit;
        }
        Util_Cookie::set('feedback_tip', $new_num, false, Common::getTime()+3600*24);
        echo json_encode(array('num'=>0,'url'=>''));
        exit;
    }

    public function exportAction() {
        header('Content-Encoding: none');
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export-feedback-data-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);

        $perpage = 1000;
        $page = 1;
        $params = $this->getInput(array('uid', 'content', 'model', 'parent_id', 'cat_id', 'version', 'time_type', 'create_time', 'reply_time'));
        $cat_id = (!empty($params['cat_id'])) ? $params['cat_id'] : $params['parent_id'];

        $search['type'] = 0;
        if (!empty($params['uid']))        $search['uid']        = $params['uid'];
        if (!empty($cat_id))               $search['cat_id']     = $cat_id;
        if (!empty($params['model']))      $search['model']      = $params['model'];
        if (!empty($params['version']))    $search['version']    = $params['version'];
        if (!empty($params['content']))    $search['content']    = array('LIKE', $params['content']);
        if (!empty($param['time_type'])){
            $tCol = $param['time_type'];
            if ($param['from_time']) $search[$tCol] = array('>=', strtotime($param['start_time']));
            if ($param['to_time'])   $search[$tCol] = array('<=', strtotime($param['end_time']));
            if ($param['from_time'] && $param['to_time']) {
                $search[$tCol] = array(
                    array('>=', strtotime($param['from_time'] )),
                    array('<=', strtotime($param['to_time']))
                );
            }
        }

        //文件头处理
        $file_header = array("用户标识", "反馈归类", "反馈内容", "设备型号", "软件版本", "反馈时间");
        array_walk($file_header, function (&$v) {
            return $v = mb_convert_encoding($v, 'gb2312', 'UTF-8');
        });
        fputcsv($fp, $file_header);
        list(,$cates) = Cs_Service_FeedbackCategory::getAll();
        $cates = Common::resetKey($cates, 'id');
        list($total, ) = Cs_Service_Feedback::getList($page, $perpage, $search);
        while($total>$perpage*($page-1)){
            $row = "";

            $sort = array('create_time' => 'desc');

            $orderby = array('last_time' => 'desc', 'has_new' => 'desc');
            list($total, $question_list) = Cs_Service_Feedback::getList($page, $perpage, $search, $sort);
            //获取用户列表
            $uids = array_keys(Common::resetKey($question_list,'uid'));
            $user = array();
            $params['has_new'] = $params['has_new'] == '' ? -1 : $params['has_new'];
            if ($params['has_new']!=-1){
                $condition = array('uid'=>array('IN',array_filter($uids)));
                $condition['has_new'] = $params['has_new'];
                $user = Cs_Service_FeedbackUser::getsBy($condition);
                $user = array_keys(Common::resetKey($user,'uid'));
            }
            //获取答案 id 数组

            foreach ($question_list as $q) {
                if($params['has_new']!=-1&&!in_array($q['uid'], array_filter($user))){
                    continue ;
                }
                $cate     = mb_convert_encoding($cates[$q['cat_id']]['name'], 'gb2312', 'UTF-8');
                $cate     = mb_convert_encoding($cates[$q['cat_id']]['name'], 'gb2312', 'UTF-8');
                $content  = mb_convert_encoding(html_entity_decode($q['content']), 'gb2312', 'UTF-8') ;
                $item = array();
                $item['uid']         = $q['uid'];
                $item['category']    = $cate;
                $item['content']     = $content;
                $item['model']       = $q['model'];
                $item['version']     = $q['version'];
                $item['create_time'] = date('Y-m-d H:i:s',$q['create_time']);
                fputcsv($fp, $item);
            }
            $page ++;
            ob_flush();
            flush();
        }
        exit;
    }

    private function _getList($page = 1, $perpage = 100, $condition = array())
    {

        //获取最新一百条
        list($total, $questions) = Cs_Service_Feedback::getList($page, $perpage, $condition, array('id' => 'desc'));
        $questions = array_reverse($questions);

        //获取链接列表
        $link_ids = array_keys(Common::resetKey($questions, 'link_id'));
        list(, $links) = Cs_Service_FeedbackLink::getsBy(array('id' => array('IN', $link_ids)));
        $links = Common::resetKey($links, 'id');

        //获取客服列表
        $kf_ids = array_keys(Common::resetKey($questions, 'kf_id'));
        list(, $kfs) = Cs_Service_FeedbackKefu::getsBy(array('id' => array('IN', $kf_ids)));
        $kfs = Common::resetKey($kfs, 'id');
        array_walk($kfs, function (&$kf) {
            $kf['avatar'] = Common::getAttachPath() . $kf['avatar'];
        });

        $kf = Cs_Service_FeedbackKefu::getBy(array(), array('sort' => 'desc'));
        $default_kf['id'] = $kf['id'];
        $default_kf['avatar'] = Common::getAttachPath() . $kf['avatar'];
        $default_kf['nickname'] = $kf['nickname'];

        $links = Common::resetKey($links, 'id');
        foreach ($questions as $k => &$v) {
            $content = html_entity_decode($v['content']);
            $v['content'] = $content;
            $v['kf'] = empty($kfs[$v['kf_id']]) ? $default_kf : $kfs[$v['kf_id']];
        }
        return $questions;
    }

    public function listAction()
    {
        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;
        $uid = $this->getInput('uid');
        if (!$uid) $this->output(-1, '非法请求.');

        $condition = array('uid' => $uid);
        $questions = $this->_getList($page, $perpage, $condition);

        list(,$cates) = Cs_Service_FeedbackCategory::getAll();
        $cates = Common::resetKey($cates, 'id');

        $this->assign('uid',   $uid);
        $this->assign('data',  $questions);
        $this->assign('cates', $cates);
//        $url = $this->actions['indexUrl'] . '/?';
//        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }


    public function replyAction() {
        $uid = $this->getInput('uid');
        list(, $rows)     = Cs_Service_Feedback::getsBy(array('status' => 0, 'type' => 0, 'uid' => $uid));
        list(, $user)     = Cs_Service_FeedbackKefu::getAll(array('sort' => 'desc'));
        list(, $link)     = Cs_Service_FeedbackLink::getsBy(array('status'=>1));
        list(, $quick)    = Cs_Service_FeedbackQuick::getsBy(array('status'=>1));
        list(, $parent)   = Cs_Service_FeedbackCategory::getsBy(array('level' => 0), array('sort'=>'desc'));
        list(, $child)    = Cs_Service_FeedbackCategory::getsBy(array('parent_id'=>$parent[0]['id']), array('sort'=>'desc'));

        $condition = array('uid' => $uid, 'status' => 1, 'type' => array('<>', 3));
        $questions = $this->_getList(1, 5, $condition);

        list(,$cates) = Cs_Service_FeedbackCategory::getAll();
        $cates = Common::resetKey($cates, 'id');

        $this->assign('cates', $cates);
        $this->assign('data',  $questions);
        $this->assign('uid',   $uid);
        $this->assign('user',  $user);
        $this->assign('link',  $link);
        $this->assign('rows',  $rows);
        $this->assign('quick', $quick);
        $this->assign('child', $child);
        $this->assign('parent',$parent);

        $this->assign('ueditor', true);
        $this->assign('dir', 'question');
    }

    public function appendAction() {
        $uid = $this->getInput('uid');
        list(, $link)     = Cs_Service_FeedbackLink::getAll();
        list(, $quick)    = Cs_Service_FeedbackQuick::getAll();
        list(, $user)     = Cs_Service_FeedbackKefu::getAll(array('sort' => 'desc'));

        $condition = array('uid' => $uid,'status' => 1, 'type' => array('<>', 3));
        $questions = $this->_getList(1, 5, $condition);

        list(,$cates) = Cs_Service_FeedbackCategory::getAll();
        $cates = Common::resetKey($cates, 'id');

        $this->assign('cates', $cates);
        $this->assign('data',  $questions);
        $this->assign('uid',   $uid);
        $this->assign('user',  $user);
        $this->assign('link',  $link);
        $this->assign('quick', $quick);
        $this->assign('ueditor', true);
        $this->assign('dir', 'question');
    }

    public function getCateAction()
    {
        $cat_id = $this->getInput('cat_id');
        list(,$cats) = Cs_Service_FeedbackCategory::getsBy(array('parent_id'=>$cat_id), array('sort'=>'desc'));
        $this->output(0,'',$cats);
    }

    public function deleteAction() {
        $id = $this->getInput('id');
        $result = Cs_Service_Feedback::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    public function append_postAction(){

        Gou_Service_Config::setValue($this->versionName, Common::getTime());
        $info = $this->getPost(array('kf_id', 'link_id', 'content', 'uid'));
        $item = Cs_Service_Feedback::getBy(array('uid' => $info['uid'], 'answer_id' => array('>', 0)), array('create_time' => 'desc'));

        $info['answer_id'] = $item['answer_id'];

        $info = $this->_cookData($info);
        $rid = Cs_Service_Feedback::add($info);

        $user = Cs_Service_FeedbackUser::getBy(array('uid' => $info['uid']));

        $row = array('has_reply' => 1, 'is_auto' => 0, 'reply_time' => time());
        Cs_Service_FeedbackUser::update($row, $user['id']);

        if (!$rid) $this->output(-1, '操作失败');

        //发送push
        $kefu    = Cs_Service_FeedbackKefu::get($info['kf_id']);
        $title   = '客服'.$kefu['nickname'].'回复了您的反馈！';
        $content = '客服'.$kefu['nickname'].'回复了您的反馈,请点击查看>>';
        User_Service_Msg::pushMsg($info['uid'], $title, $content, 3, 'com.gionee.client.Conversation');

        $this->output(0, '操作成功',$rid);
    }

    public function reply_postAction(){

        Gou_Service_Config::setValue($this->versionName, Common::getTime());
        $info = $this->getPost(array('kf_id', 'link_id', 'content', 'uid', 'items'));
        list($reply, $questions) = $this->_cookReplyData($info);

        //更新被回复的问题
        $rid = Cs_Service_Feedback::add($reply);
        foreach ($questions as $k => $v) {
            $v['answer_id'] = $rid;
            $v['reply_time'] = Common::getTime();
            Cs_Service_Feedback::update($v, $k);
        }

        //设置后台提醒
        $cache = Common::getCache();
        $num = $cache->get('feedback_tip');
        $num = $num - count($info['items']);
        $num = $num < 0 ? 0 : $num;
        $cache->set("feedback_tip", $num, Common::getTime() + 3600 * 24);
        Util_Cookie::set('feedback_tip', $num, false, Common::getTime()+3600*24);

        $new_row = Cs_Service_Feedback::getBy(array('uid'=>$info['uid'],'status'=>0, 'type'=>0));
        //更新用户状态
        $user = Cs_Service_FeedbackUser::getBy(array('uid' => $info['uid']));
        $row = array('has_new' => !empty($new_row), 'has_reply' => 1, 'is_auto' => 0, 'reply_time' => time());
        Cs_Service_FeedbackUser::update($row, $user['id']);

        if (!$rid) $this->output(-1, '操作失败');
        //发送push
        $kefu    = Cs_Service_FeedbackKefu::get($info['kf_id']);
        $title   = '客服'.$kefu['nickname'].'回复了您的反馈！';
        $content = '客服'.$kefu['nickname'].'回复了您的反馈,请点击查看>>';
        User_Service_Msg::pushMsg($info['uid'], $title, $content, 3, 'com.gionee.client.Conversation');

        $this->output(0, '操作成功', $rid);
    }


    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }


    public function upload_postAction() {
        $ret = Common::upload('img', 'news');
        $imgId = $this->getPost('imgId');
        $this->assign('code' , $ret['data']);
        $this->assign('msg' , $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'question');
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
    }


    private function _cookReplyData($info) {
        if(empty($info['kf_id'])) $this->output(-1,'请选择客服');
        if(empty($info['items'])) $this->output(-1,'请选择分类');
        $info['content'] =trim($info['content']);
        if(empty($info['content'])) $this->output(-1,'内容不能为空');
        foreach ($info['items'] as $k=>$v) {
            if(empty($v['parent_id']))$this->output(-1,'请补全所有分类');
            $questions[$k]['cat_id'] = $v['cat_id']?$v['cat_id']:$v['parent_id'];
            $questions[$k]['status'] = 1;
        }
        $reply['type']    = 1;
        $reply['status']  = 1;
        $reply['uid']     = $info['uid'];
        $reply['kf_id']   = $info['kf_id'];
        $reply['link_id'] = $info['link_id'];
//        $reply['link_id']  = empty($info['link_id'])?'':implode(',',$info['link_id']);
        $reply['content'] = html_entity_decode($info['content']);


        return array($reply, $questions);
    }
    private function _cookData($info) {
        if(empty($info['kf_id']))     $this->output(-1, '请选择客服');
        $info['content'] =trim($info['content']);
        if(empty($info['content'])) $this->output(-1, '内容不能为空');
        $info['type']     = 1;
        $info['status']   = 1;
//        $info['link_id'] = empty($info['link_id'])?'':implode(',',$info['link_id']);
        $info['content']  = html_entity_decode($info['content']);
        return $info;
    }
}