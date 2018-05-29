<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author terry
 *
 */
class Apk_QaController extends Api_BaseController {

    public $perpage = 10;
    public $actions = array(
        'indexUrl'=>'/api/apk_qa/list',
    );

    /**
     * 问贴列表
     */
    public function listAction(){
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $perpage = intval($this->getInput('perpage'));
        if($perpage) $this->perpage = $perpage;

        $data = array();

        //获取没有隐藏的问贴列表
        list($total, $qus) = Gou_Service_QaQus::getList(
            $page,
            $this->perpage,
            array('status'=>2, 'is_hidden'=>0),
            array('recommend'=>'DESC', 'create_time'=>'DESC')
        );

        if($qus){
            $ids = array_keys(Common::resetKey($qus, 'id'));

            //获取问贴列表的有效回答数
            $qus_aus_counts = Gou_Service_QaAus::getAusCount($ids);
            $qus_aus_counts = Common::resetKey($qus_aus_counts, 'item_id');

            //获取问贴列表对应的最新回帖列表
            $aus_list = Gou_Service_QaAus::getLastOneAus($ids);
            $aus_list = Common::resetKey($aus_list, 'item_id');

            //获取问贴列表和最新回帖列表的作者信息
            $uids = array_merge(array_keys(Common::resetKey($aus_list, 'uid')), array_keys(Common::resetKey($qus, 'uid')));
            $uids = array_unique($uids);
            $author_list = User_Service_Uid::getUsersFmtByUid($uids);

            foreach($qus as $item){
                $aus_total = isset($qus_aus_counts[$item['id']]) ? $qus_aus_counts[$item['id']]['total'] : 0;
                $qus_item = array(
                    'id' => $item['id'],
                    'title' => html_entity_decode($item['title']),
                    'q_author_avatar' => '',
                    'q_author_nickname' => '',
                    'id' => $item['id'],
                    'ans_total' => intval($aus_total) > 999 ? '999+' : $aus_total,
                    'ans_list' => array(),
                );

                if(isset($author_list[$item['uid']])){
                    $qus_item['q_author_avatar'] = $author_list[$item['uid']]['avatar'];
                    $qus_item['q_author_nickname'] = $author_list[$item['uid']]['nickname'];
                }

                if(isset($aus_list[$item['id']])){
                    $aus_item = array(
                        'ans_content' => html_entity_decode($aus_list[$item['id']]['content']),
                        'a_anthor_nickname' => '',
                    );
                    $a_uid = $aus_list[$item['id']]['uid'];
                    if(isset($author_list[$a_uid])){
                        $aus_item['a_anthor_nickname'] = $author_list[$a_uid]['nickname'];
                    }
                    array_push($qus_item['ans_list'], $aus_item);
                }
                array_push($data, $qus_item);
            }
        }
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    /**
     * 问题详情
     */
    public function detailAction(){
        $id = intval($this->getInput('id'));
        if(!$id) $this->output(-1, '非法请求.');

        //回帖信息列表
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $perpage = intval($this->getInput('perpage'));
        if($perpage) $this->perpage = $perpage;

        $qus = Gou_Service_QaQus::get($id);
        $data = array();
        if($qus){
            //问贴信息和作者信息
            if($page == 1){
                list($nickname, $avatar) = User_Service_Uid::getUserFmtByUid($qus['uid']);
                $images = $qus['images'] ? explode(',', $qus['images']):'';
                $attach = Common::getAttachPath();
                array_walk($images, function(&$v, $k, $attach){ $v = $attach . $v; }, $attach);

                $webroot = Common::getWebRoot();

                //获取问贴列表的有效回答数
                $qus_aus_counts = Gou_Service_QaAus::getAusCount(array($id));
                $qus_aus_counts = Common::resetKey($qus_aus_counts, 'item_id');
                $aus_total = isset($qus_aus_counts[$id]) ? $qus_aus_counts[$id]['total'] : 0;
                unset($qus_aus_counts);

                $qus_detail = array(
                    'id' => $qus['id'] . '_qus',
                    'url' => sprintf('%s/apk/qa/detail?id=%d', $webroot, $qus['id']),
                    'q_author_avatar' => $avatar,
                    'q_author_nickname' => $nickname,
                    'ans_total' => intval($aus_total) > 999 ? '999+' : $aus_total,
                    'title' => html_entity_decode($qus['title']),
                    'content' => html_entity_decode($qus['content']),
                    'images' => $images
                );
                array_push($data, $qus_detail);
            }

            $condition = array('item_id'=>$qus['id'], 'status'=>array('IN', array(0, 1, 2)));
            $orderBy = array('praise'=>'DESC', 'create_time'=>'DESC');
            list($total, $aus) = Gou_Service_QaAus::getList($page, $this->perpage, $condition, $orderBy);
            if($aus){
                //获取回帖作者的UID
                $uids = array_keys(Common::resetKey($aus, 'uid'));
                $author_list = User_Service_Uid::getUsersFmtByUid($uids);

                //获取回帖的父贴作者的UID
                $parent_ids = array_keys(Common::resetKey($aus, 'parent_id'));
                $parent_ids = array_filter($parent_ids);
                $parent_ids = array_unique($parent_ids);
                $parent_cm_list = $parent_uids = array();
                if($parent_ids){
                    $parent_cm_list = Gou_Service_QaAus::getsBy(array('id'=>array('IN', $parent_ids)));
                    $parent_uids = array_keys(Common::resetKey($parent_cm_list, 'uid'));
                    $parent_cm_list = Common::resetKey($parent_cm_list, 'id');
                }
                $parent_author_list = User_Service_Uid::getUsersFmtByUid($parent_uids);
                foreach($parent_cm_list as $key => $item){
                    if(isset($parent_author_list[$item['uid']]))
                        $parent_cm_list[$key]['nickname'] = $parent_author_list[$item['uid']]['nickname'];
                }
                unset($uids);
                unset($parent_ids);
                unset($parent_uids);
                unset($parent_author_list);

                foreach($aus as $item){
                    //设置回帖信息
                    $aus_item = array(
                        'id' => $item['id'],
                        'ans_content' => html_entity_decode($item['content']),
                        'ans_praise' => intval($item['praise']) > 999 ? '999+' : $item['praise'],
                        'ans_time' => date('Y-m-d', $item['create_time']),
                        'jid' => $item['relate_item_id'],
                        'j_title' => ''
                    );

                    //获取跳转问贴
                    if($item['relate_item_id']){
                        $relate_qus = Gou_Service_QaQus::get($item['relate_item_id']);
                        if($relate_qus) $aus_item['j_title'] = html_entity_decode($relate_qus['title']);
                    }

                    //获取回帖作者信息
                    $uid = $item['uid'];
                    if(isset($author_list[$uid])){
                        $aus_item['a_author_nickname'] = $author_list[$uid]['nickname'];
                        $aus_item['a_author_avatar'] = $author_list[$uid]['avatar'];
                    }

                    //获取回帖的回帖作者信息
                    $aus_item['from'] = '';
                    if($item['parent_id']){
                        if(isset($parent_cm_list[$item['parent_id']])){
                            $aus_item['from'] = sprintf('%s%s：', '回复', $parent_cm_list[$item['parent_id']]['nickname']);
                        }else{
                            $aus_item['from'] = sprintf('%s%s：', '回复', '匿名');
                        }
                    }

                    array_push($data, $aus_item);
                }
            }
        }
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    /**
     * 提出问题
     */
//    public function qusAction(){
//        $uid = Common::getAndroidtUid();
//        if(!$uid) $this->output(-1, '非法请求.');
//
//        list($nickname, $avatar, , $is_edit) = User_Service_Uid::getUserFmtByUid($uid);
//        $data = array(
//            'author_avatar'     => $avatar,
//            'author_nickname'   => $nickname,
//            'is_edit'           => $is_edit
//        );
//        $this->output(0, '', $data);
//    }

    /**
     * 提交问题
     */
    public function qusSubmitAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $input = $this->getPost(array('sign', 'title', 'content', 'nickname'));
        $input['uid'] = $uid;
        $input = $this->_qus_cookData($input);

        //上传头像
        $avatar = '';
        if(isset($_FILES['img_avatar'])){
            $ret = Common::upload('img_avatar', 'user');
            if($ret['code']===0){
                $avatar = $ret['data'];
            }else{
                $this->output(-1, '上传失败！');
            }
        }

        $author = array();
        if($avatar) $author['avatar'] = $avatar;
        if($input['nickname']) $author['nickname'] = $input['nickname'];
        if($author) User_Service_Uid::updateUserBy($author, array('uid'=>$uid));

        //上传问贴图片, 仅可上传3张图片
        $images = '';
        for($i=0; $i<3; $i++){
            if(isset($_FILES['img_'.$i])){
                $ret = Common::upload('img_'.$i, 'qa');
                if($ret['code']===0){
                    $images[] = $ret['data'];
                }else{
                    $this->output(-1, '上传失败！');
                }
            }
        }

        if($images) $images = implode(',', $images);
        $qus = array(
            'title'     => $input['title'],
            'content'   => $input['content'],
            'images'    => $images,
            'uid'       => $uid,
        );
        $result = Gou_Service_QaQus::add($qus);

        if($result){
            $this->_updateVersion();
            $cache = Common::getCache();
            $cache->set("qa_qus_last_time", Common::getTime(), Common::getTime()+3600*24);
            $this->standOutput(0, '');
        }
        $this->output(-1, '提交失败.');
    }

    /**
     * 验证问贴提交
     * @param $data
     * @return mixed
     */
    private function _qus_cookData($data){
        //验证签名
        $encrypt_str = $data['uid'] . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';
        if(md5($encrypt_str) !== $data['sign']) $this->output(-1, '非法请求.');

        $data['nickname'] = trim($data['nickname']);
        if($data['nickname'] && !preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $data['nickname'])) $this->output(-1, '昵称限制1~12个字符.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($data['nickname']);
        if(!empty($kwd)) $this->output(-1, '请不要输入敏感内容', $kwd);

        if(Util_String::strlen(html_entity_decode($data['title'], ENT_QUOTES)) < 8 || Util_String::strlen(html_entity_decode($data['title'], ENT_QUOTES)) > 50) $this->output(-1, '问题限制为8~50个字符.');
        if($data['content'] && (Util_String::strlen(html_entity_decode($data['content'], ENT_QUOTES)) < 10 || Util_String::strlen(html_entity_decode($data['content'], ENT_QUOTES)) > 500)) $this->output(-1, '问题描述为10~500个字符.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($data['title']);
        if(!empty($kwd)) $this->output(-1, '请不要输入敏感内容', $kwd);

        list(, $kwd) = Gou_Service_Sensitive::fuck($data['content']);
        if(!empty($kwd)) $this->output(-1, '请不要输入敏感内容', $kwd);

        return $data;
    }

    /**
     * 问答搜索
     */
    public function searchAction(){
        $search = trim($this->getInput('s'));
        if(empty($search)) $this->standOutput(0, '');

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $perpage = intval($this->getInput('perpage'));
        if($perpage) $this->perpage = $perpage;

        list($total, $qus) = Gou_Service_QaQus::searchQus($page, $this->perpage, $search);

        //获取问贴列表的有效回答数
        $qus_ids = array_keys(Common::resetKey($qus, 'id'));
        $qus_aus_counts = Gou_Service_QaAus::getAusCount($qus_ids);
        $qus_aus_counts = Common::resetKey($qus_aus_counts, 'item_id');

        $data = array();
        foreach($qus as $key => $item){
            $aus_total = isset($qus_aus_counts[$item['id']]) ? $qus_aus_counts[$item['id']]['total'] : 0;
            $data[$key]['id'] = $item['id'];
            $data[$key]['title'] = html_entity_decode($item['title']);
            $data[$key]['ans_total'] = intval($aus_total) > 999 ? '999+' : $aus_total;
        }

        //添加关键词统计
        if($page == 1) Gou_Service_QaSkey::updataSkey($search, $total);

        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    /**
     * 我的问题列表
     */
    public function myQusListAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $perpage = intval($this->getInput('perpage'));
        if($perpage) $this->perpage = $perpage;

        $data = array();

        //获取没有隐藏的问贴列表
        list($total, $qus) = Gou_Service_QaQus::getList($page, $this->perpage, array('uid'=>$uid), array('create_time'=>'DESC'));

        if($qus){
            //获取问贴列表对应的最新回帖列表
            $ids = array_keys(Common::resetKey($qus, 'id'));
            $aus_list = Gou_Service_QaAus::getLastOneAus($ids);
            $aus_list = Common::resetKey($aus_list, 'item_id');

            //获取问贴列表的有效回答数
            $qus_aus_counts = Gou_Service_QaAus::getAusCount($ids);
            $qus_aus_counts = Common::resetKey($qus_aus_counts, 'item_id');

            //获取问贴列表和最新回帖列表的作者信息
            $uids = array_keys(Common::resetKey($aus_list, 'uid'));
            $author_list = User_Service_Uid::getUsersFmtByUid($uids);

            $status = Gou_Service_QaQus::$status;
            $status[0] = '审核中';
            $status[1] = '审核中';
            $reason = Gou_Service_QaQus::$reason;
            foreach($qus as $item){
                $aus_total = isset($qus_aus_counts[$item['id']]) ? $qus_aus_counts[$item['id']]['total'] : 0;
                $qus_item = array(
                    'id' => $item['id'],
                    'qus_id' => $item['id'],
                    'title' => html_entity_decode($item['title']),
                    'ans_total' => intval($aus_total) > 999 ? '999+' : $aus_total,
                    'status_label' => $status[$item['status']],
                    'status' => $item['status'],
                    'ans_list' => array(),
                );
                if($item['status'] == 3 && $item['reason']) $qus_item['status_label'] = sprintf('%s:%s', $status[$item['status']], $reason[$item['reason']]);

                if(isset($aus_list[$item['id']])){
                    $aus_item = array(
                        'ans_content' => html_entity_decode($aus_list[$item['id']]['content']),
                        'author_avatar' => '',
                        'author_nickname' => '',
                    );
                    $uid = $aus_list[$item['id']]['uid'];
                    if(isset($author_list[$uid])){
                        $aus_item['author_avatar'] = $author_list[$uid]['avatar'];
                        $aus_item['author_nickname'] = sprintf('%s：', $author_list[$uid]['nickname']);
                    }
                    array_push($qus_item['ans_list'], $aus_item);
                }
                array_push($data, $qus_item);
            }
        }
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    /**
     * 我的回答列表
     */
    public function myAnsListAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $perpage = intval($this->getInput('perpage'));
        if($perpage) $this->perpage = $perpage;

        $data = array();

        //获取我的回帖列表
        list($total, $aus) = Gou_Service_QaAus::getList($page, $this->perpage, array('uid'=>$uid), array('create_time'=>'DESC'));

        if($aus){
            $reason = Gou_Service_QaQus::$reason;

            //获取问贴列表
            $qus_ids = array_keys(Common::resetKey($aus, 'item_id'));
            $qus_list = array();
            $qus_list = Gou_Service_QaQus::getsBy(array('id'=>array('IN', $qus_ids)));

            //获取我回复的问贴的作者列表
            $uids = array_keys(Common::resetKey($qus_list, 'uid'));
            $author_list = User_Service_Uid::getUsersFmtByUid($uids);
            foreach($qus_list as &$item){
                if(isset($author_list[$item['uid']])) $item['author'] = $author_list[$item['uid']];
            }
            $qus_list = Common::resetKey($qus_list, 'id');
            unset($item);
            unset($qus_ids);
            unset($uids);
            unset($author_list);

            //获取我回复的回帖的作者列表
            $parent_ids = array_keys(Common::resetKey($aus, 'parent_id'));
            $parent_ids = array_filter($parent_ids);
            $parent_ids = array_unique($parent_ids);
            $parent_aus_list = $parent_uids = $parent_author_list = array();
            if($parent_ids){
                $parent_aus_list = Gou_Service_QaAus::getsBy(array('id'=>array('IN', $parent_ids)));
                $parent_aus_list = Common::resetKey($parent_aus_list, 'id');
                $parent_uids = array_keys(Common::resetKey($parent_aus_list, 'uid'));
            }
            $parent_author_list = User_Service_Uid::getUsersFmtByUid($parent_uids);
            foreach($parent_aus_list as &$item){
                if(isset($parent_author_list[$item['uid']]))
                    $item['nickname'] = $parent_author_list[$item['uid']]['nickname'];
            }

            unset($item);
            unset($parent_ids);
            unset($parent_uids);
            unset($parent_author_list);

            foreach($aus as $item){
                $aus_item = array(
                    'id' => $item['id'],
                    'qus_id' => 0,
                    'title' => '',
                    'author_avatar' => '',
                    'author_nickname' => '',
                    'ans_content' => html_entity_decode($item['content']),
                    'from' => '',
                    'reason' => '',
                    'status' => $item['status'],
                );
                if(isset($qus_list[$item['item_id']])){
                    $aus_item['qus_id'] = $qus_list[$item['item_id']]['id'];
                    $aus_item['title'] = html_entity_decode($qus_list[$item['item_id']]['title']);
                    $aus_item['author_avatar'] = $qus_list[$item['item_id']]['author']['avatar'];
                    $aus_item['author_nickname'] = sprintf('%s：', $qus_list[$item['item_id']]['author']['nickname']);
                }

                if($item['parent_id']){
                    if(isset($parent_aus_list[$item['parent_id']])){
                        $aus_item['from'] = sprintf('%s%s：', '回复', $parent_aus_list[$item['parent_id']]['nickname']);
                    }else{
                        $aus_item['from'] = sprintf('%s%s：', '回复', '匿名');
                    }
                }else{
                    $aus_item['from'] = sprintf('%s：', '回答');
                }

                if($item['status'] == 3 && isset($reason[$item['reason']])){
                    $aus_item['reason'] = sprintf('%s：%s', '已被删除', $reason[$item['reason']]);
                    $aus_con_n = Util_String::strlen($aus_item['ans_content']);
                    $aus_item['ans_content'] = sprintf('内容已删除 %s', str_repeat('*', $aus_con_n));
                }
                array_push($data, $aus_item);
            }

        }
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    /**
     * 回答问题
     */
    public function ansAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $input = $this->getPost(array('sign', 'content', 'qus_id', 'pid', 'nickname'));
        $input['uid'] = $uid;
        $input = $this->_aus_cookData($input);

        if($input['nickname']) User_Service_Uid::updateUserBy(array('nickname' => $input['nickname']), array('uid'=>$uid));

        $aus = array(
            'content'   => $input['content'],
            'uid'       => $uid,
            'item_id'   => $input['qus_id'],
            'parent_id' => $input['pid'],
        );

        $result = Gou_Service_QaAus::add($aus);

        if($result){
            Gou_Service_QaQus::updateTotal($aus['item_id']);
            $qus = Gou_Service_QaQus::get($aus['item_id']);

            //给问贴人发消息
            User_Service_Msg::addQaAusMsg(4, $qus['uid'], $qus['title'], $uid, $qus['id']);
            //push
            list($nickname) = User_Service_Uid::getUserFmtByUid($uid);
            $push_title = User_Service_Msg::msgFmt(4, 0, $nickname);
            //关闭Push
            //User_Service_Msg::pushMsg($qus['uid'], $push_title, $qus['title'], 3, 'com.gionee.client.MesaggeList');

            if($aus['parent_id']){
                //给回帖人发消息
                $aus_p = Gou_Service_QaAus::get($aus['parent_id']);
                User_Service_Msg::addQaAusMsg(4, $aus_p['uid'], $qus['title'], $uid, $qus['id']);
                //push
                //关闭Push
                //if($qus['uid'] != $aus_p['uid']) User_Service_Msg::pushMsg($aus_p['uid'], $push_title, $qus['title'], 3, 'com.gionee.client.MesaggeList');
            }
            $this->_updateVersion();
            $this->standOutput(0, '');
        }

        $this->output(-1, '提交失败.');
    }

    private function _aus_cookData($data){
        if(intval($data['qus_id']) == 0) $this->output(-1, '非法请求.');
        //验证签名
        if(empty($data['pid'])) $data['pid'] = 0;
        $encrypt_str = $data['uid'] . $data['qus_id'] . $data['pid'] . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';

        if(md5($encrypt_str) !== $data['sign']) $this->output(-1, '非法请求.');

        $data['nickname'] = trim($data['nickname']);
        if($data['nickname'] && !preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $data['nickname'])) $this->output(-1, '昵称限制1~12个字符.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($data['nickname']);
        if(!empty($kwd)) $this->output(-1, '请不要输入敏感内容', $kwd);
        if(Util_String::strlen(html_entity_decode($data['content'], ENT_QUOTES)) < 2 || Util_String::strlen(html_entity_decode($data['content'], ENT_QUOTES)) > 500) $this->output(-1, '回答限制2~500个字符.');
        list(, $kwd) = Gou_Service_Sensitive::fuck($data['content']);
        if(!empty($kwd)) $this->output(-1, '请不要输入敏感内容', $kwd);

        return $data;
    }

    /**
     * 回帖点赞
     */
    public function praiseAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $id = intval($this->getInput('id'));
        if(!$id) $this->output(-1, '非法请求.');

        $result = Gou_Service_QaAus::praise($id, 1);
        if(!$result) $this->output(-1, '');

        $this->output(0, '', array('id' => $id));
    }

    /**
     * 更新版本号
     */
    private function _updateVersion(){
        //用于刷新问答概况
        Gou_Service_Config::setValue('Story_Version', Common::getTime());
    }
}
