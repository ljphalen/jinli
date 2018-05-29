<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 问答-问贴
 * QaController
 *
 * @author Terry
 *
 */
class Qa_QuestionController extends Admin_BaseController {

    public $perpage = 15;

    public $actions = array(
        'indexUrl' => '/Admin/Qa_Question/index',
        'previewUrl' => '/apk/Qa/detail',
        'addUrl' => '/Admin/Qa_Question/add',
        'addPostUrl' => '/Admin/Qa_Question/add_post',
        'editUrl' => '/Admin/Qa_Question/edit',
        'editPostUrl' => '/Admin/Qa_Question/edit_post',
        'deleteUrl' => '/Admin/Qa_Question/delete',
        'checkUrl' => '/Admin/Qa_Question/check',
        'uploadUrl' => '/Admin/Qa_Question/upload',
        'uploadPostUrl' => '/Admin/Qa_Question/upload_post',
        'deleteImgUrl' => '/Admin/Qa_Question/deleteimg',
        'recommendUrl' => '/Admin/Qa_Question/recommend',
        'hiddenUrl' => '/Admin/Qa_Question/hidden',
        'answerUrl' => '/Admin/Qa_Answer/index',
    );

    /**
     * 问贴列表
     */
    public function indexAction(){
        $page = intval($this->getInput('page'));
        $perpage = $this->perpage;

        $param = $this->getInput(array('status', 'title', 'from_time', 'to_time', 'is_hidden', 'uid', 'is_admin', 'nickname', 'scoreid', 'recommend'));
        if (!empty($param['title'])) $search['title'] = array('LIKE', $param['title']);
        if (!empty($param['status'])) $search['status'] = $param['status']-1;
        if (!empty($param['is_hidden'])) $search['is_hidden'] = $param['is_hidden']-1;
        if (!empty($param['recommend'])) $search['recommend'] = $param['recommend']-1;

        if ($param['from_time']) $search['create_time'] = array('>=', strtotime($param['from_time']));
        if ($param['to_time']) $search['create_time'] = array('<=', strtotime($param['to_time']) + 24*60*60);
        if ($param['from_time'] && $param['to_time']) {
            $search['create_time'] = array(
                array('>=', strtotime($param['from_time'] )),
                array('<=', strtotime($param['to_time']) + 24*60*60)
            );
        }
        if (!empty($param['uid'])) $search['uid'] = $param['uid'];
        if (!empty($param['is_admin'])) $search['is_admin'] = $param['is_admin']-1;

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

        $orderby = array('create_time'=>'DESC', 'id'=>'DESC');
        if($param['recommend']) $orderby['recommend'] = 'DESC';

        list($total, $result) = Gou_Service_QaQus::getList($page, $perpage, $search, $orderby);

        $user_uids = array();
        if($total){
            $uids = array_keys(Common::resetKey($result, 'uid'));
            $user_uids = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
            $user_uids = Common::resetKey($user_uids, 'uid');
        }

        $staticroot = Common::getAttachPath();
        $status = Gou_Service_QaQus::$status;
        $status[0] = '保存';
        foreach ($result as $k=>&$v) {
            $v['author'] = '';
            if(array_key_exists($v['uid'], $user_uids)) {
                $v['author'] = $user_uids[$v['uid']]['nickname'] ? $user_uids[$v['uid']]['nickname'] : '购物大厅网友';
            }

            $v['recommend_label'] = $v['recommend']?'是':'否';
            $v['is_hidden_label'] = $v['is_hidden']?'是':'否';
            $v['status_label'] = $status[$v['status']];
        }

        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('param', $param);
        $this->assign('data', $result);
        $this->assign('status', $status);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
    }

    /**
     *
     *添加问贴
     *
     */
    public function addAction(){
        $authors = User_Service_Uid::getVirtualUser();

        $status = Gou_Service_QaQus::$status;
        $status[0] = '保存';
        array_pop($status);

        $this->assign('authors', $authors);
        $this->assign('status', $status);
    }

    /**
     * 编辑问贴
     *
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Gou_Service_QaQus::get(intval($id));
        if(!$info) $this->output(-1, '无该记录');

        $author = User_Service_Uid::getByUid($info['uid']);

        $images = $info['images']?explode(',', $info['images']):array();

        $status = Gou_Service_QaQus::$status;
        $status[0] = '保存';

        $this->assign('backurl', $this->getInput('backurl'));
        $this->assign('images', $images);
        $this->assign('images_count', count($images));
        $this->assign('author', $author);
        $this->assign('reason', Gou_Service_QaQus::$reason);
        $this->assign('info', $info);
        $this->assign('status', $status);
    }

    /**
     *
     *删除问贴
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        if (!empty($id)){
            if(is_array($id)){
                $list = Gou_Service_QaQus::getsBy(array('id' => array('IN', $id)));
                $result = Gou_Service_QaQus::deletes('id', $id);
                if($result){
                    foreach($list as $item){
                        $images = explode(',', $item['images']);
                        foreach($images as $img){
                            Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $img);
                        }
                    }
                }
            }else{
                $info = Gou_Service_QaQus::get($id);
                if(!$info){
                    $this->output(-1, '无法删除');
                }
                $result = Gou_Service_QaQus::delete($id);
                if($result){
                    $images = explode(',', $info['images']);
                    foreach($images as $img){
                        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $img);
                    }
                }
            }
        }else{
            $this->output(-1, '请选择条目');
        }

        if (!$result) $this->output(-1, '操作失败');

        $this->_updateVersion();
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
            $result = Gou_Service_QaQus::updates('id', $id, $info);
            $list = Gou_Service_QaQus::getsBy(array('id' => array('IN', $id)));
            foreach($list as $item){
                if($result && ($item['status'] == 2 || $item['status'] == 3)){
                    User_Service_Msg::addQaQusVerifyMsg($item['status'], $item['uid'], $item['title'], '', $item['id'], $item['reason']);
                    $push_title = User_Service_Msg::msgFmt($item['status'], $item['reason']);
                    User_Service_Msg::pushMsg($item['uid'], $push_title, $item['title'], 3, 'com.gionee.client.MesaggeList');
                    $this->_updateVersion();
                }
            }
        }else{
            $result = Gou_Service_QaQus::update($info, $id);
            $info = Gou_Service_QaQus::get($id);
            if($result && ($info['status'] == 2 || $info['status'] == 3)){
                User_Service_Msg::addQaQusVerifyMsg($info['status'], $info['uid'], $info['title'], '', $info['id'], $info['reason']);
                $push_title = User_Service_Msg::msgFmt($info['status'], $info['reason']);
                User_Service_Msg::pushMsg($info['uid'], $push_title, $info['title'], 3, 'com.gionee.client.MesaggeList');
                $this->_updateVersion();
            }
        }
        $this->output(0, '操作成功', array('result' => $result));
    }

    /**
     * 添加提交
     */
    public function add_postAction(){
        $info = $this->getPost(array('title', 'uid', 'status', 'content', 'recommend', 'is_hidden'));
        $info = $this->_cookData($info);
        $info['is_admin'] = 1;

        $simg = $this->getPost('simg');
        if($simg) $info['images'] = implode(',', $simg);

        $result = Gou_Service_QaQus::add($info);

        if (!$result) $this->output(-1, '操作失败');

        $this->_updateVersion();
        $this->output(0, '操作成功', $result);
    }

    /**
     * 编辑提交
     */
    public function edit_postAction(){
        $images = $this->getPost('upImg');
        $info = $this->getPost(array('id', 'title', 'uid', 'status', 'reason', 'content', 'recommend', 'is_hidden'));
        $info = $this->_cookData($info);

        $simg = $this->getPost('simg');
        if($simg){
            $images = array_merge($images, $simg);
            $images = array_filter($images);
        }
        $info['images'] = implode(',', $images);
        $result = Gou_Service_QaQus::update($info, intval($info['id']));

        if($result && ($info['status'] == 2 || $info['status'] == 3)){
            User_Service_Msg::addQaQusVerifyMsg($info['status'], $info['uid'], $info['title'], '', $info['id'], $info['reason']);
            $push_title = User_Service_Msg::msgFmt($info['status'], $info['reason']);
            User_Service_Msg::pushMsg($info['uid'], $push_title, $info['title'], 3, 'com.gionee.client.MesaggeList');
            $this->_updateVersion();
        }

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * ajax 删除图片
     */
    public function deleteimgAction() {
        $id = $this->getInput('id');
        $image = $this->getInput('image');
        if (empty($path) && empty($id)) $this->output(-1, '无法删除');

        $info = Gou_Service_QaQus::get(intval($id));
        $images = explode(',', $info['images']);

        foreach($images as $key=>$item){
            if($item == $image){
                Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $item);
                unset($images[$key]);
                break;
            }
        }

        $images = implode(',', $images);
        $result = Gou_Service_QaQus::update(array('images'=>$images), intval($id));

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 推荐
     */
    public function recommendAction(){
        $id = $this->getPost('id');
        $type = $this->getPost('type');
        $info['recommend'] = $type;
        $result = Gou_Service_QaQus::update($info, intval($id));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 隐藏
     */
    public function hiddenAction(){
        $id = $this->getPost('id');
        $type = $this->getPost('type');
        $info['is_hidden'] = $type;
        $result = Gou_Service_QaQus::update($info, intval($id));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    /**
     * 上传页面
     */
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     * 上传动作
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'qa');
        $info = pathinfo($ret['data']);
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
        $path =  sprintf("%s%s", realpath($attachPath), $info['dirname']);
        $img = $path."/".$info['basename'];
        Util_Image::makeThumb($img, $img, 250, 250);
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     * 获取所有问贴
     * @return json
     */
    public function getAllQuestionAction(){
        $q = trim($this->getInput('q'));

        if(empty($q)) $this->output(0, '', array());

        $search = array('status'=>2, 'is_hidden'=>0, 'title'=>array('LIKE', $q));
        $orderby = array('create_time'=>'DESC', 'id'=>'DESC');

        list($total, $result) = Gou_Service_QaQus::getList(1, 50, $search, $orderby);

        $question = array();
        foreach($result as $item){
            $question[] = array(
                'value' => $item['id'],
                'label' => 'ID:' . $item['id'] .' => '.$item['title']
            );
        }
        $this->output(0, '', $question);
    }

    /**
     * 获取是否有新问贴和问贴信息
     */
    public function getNewQuestionAction(){
        $cache = Common::getCache();
        $server_qus_lasttime = intval($cache->get("qa_qus_last_time"));
        $client_qus_lasttime = intval(Util_Cookie::get('qa_qus_last_time'));
        if(!empty($server_qus_lasttime)){
            if(empty($client_qus_lasttime) || $server_qus_lasttime > $client_qus_lasttime){
                Util_Cookie::set('qa_qus_last_time', $server_qus_lasttime, false, Common::getTime()+3600*24);
                echo json_encode(array('time' => date('Y-m-d H:i:s', $server_qus_lasttime)));
                exit;
            }
        }
        echo 0;
    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
        if(!$info['title']) $this->output(-1, '问题不能为空.');

        if(Util_String::strlen(html_entity_decode($info['title'], ENT_QUOTES)) < 8 || Util_String::strlen(html_entity_decode($info['title'], ENT_QUOTES)) > 50) $this->output(-1, '问题限制为8~50个字, 请修改后发布.');
        if($info['content'] && (Util_String::strlen(html_entity_decode($info['content'], ENT_QUOTES)) < 10 || Util_String::strlen(html_entity_decode($info['content'], ENT_QUOTES)) > 500)) $this->output(-1, '问题描述为10~500个字, 请修改后再点击完成.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($info['title']);
        if(!empty($kwd)) $this->output(-1, '问题不能包含 "' . implode(',',$kwd) . '" ，请修改', $kwd);

        list(, $kwd) = Gou_Service_Sensitive::fuck($info['content']);
        if(!empty($kwd)) $this->output(-1, '描述不能包含 "' . implode(',',$kwd) . '" ，请修改', $kwd);

        $info['title'] = htmlspecialchars_decode($info['title']);
        if(isset($info['content'])) $info['content'] = htmlspecialchars_decode($info['content']);

        return $info;
    }

    /**
     * 更新版本号
     */
    private function _updateVersion(){
        //用于刷新问答概况
        Gou_Service_Config::setValue('Story_Version', Common::getTime());
    }
}