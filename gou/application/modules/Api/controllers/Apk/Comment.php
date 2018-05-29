<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_CommentController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
            'listUrl'=>'/api/comment/list',
	);

    /**
     * 知物评论列表
     */
    public function listAction(){
        $type = $this->getInput('type');
        $item_id = intval($this->getInput('item_id'));
        if(empty($item_id)||!$type){
            $this->output(1, '参数有误');
        }

        $perpage = intval($this->getInput('perpage'));
        if (empty($perpage)) $perpage = $this->perpage;

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $version = Common::getAndroidClientVersion();
        $orderby =  array('create_time' => 'DESC', 'id' => 'DESC');
        if($version>=248){
            $orderby =  array('praise'=>'DESC', 'create_time' => 'DESC', 'id' => 'DESC');
        }
        $condition['item_id'] = $item_id;

        list($total, $data) = Gou_Service_Comment::getList($page, $perpage, $condition, $orderby);
        if($total==0){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => 1));
        }

        //获取评论作者列表
        $uids = array_keys(Common::resetKey($data, 'uid'));
        $author_list = User_Service_Uid::getUsersFmtByUid($uids);

        //获取回评论作者列表
        $parent_ids = array_keys(Common::resetKey($data, 'parent_id'));
        $parent_ids = array_filter($parent_ids);
        $parent_ids = array_unique($parent_ids);
        $parent_cm_list = $parent_uids = array();
        if($parent_ids){
            list(, $parent_cm_list) = Gou_Service_Comment::getsBy(array('id'=>array('IN', $parent_ids)));
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

        foreach ($data as &$v) {
            unset($v['old_content']);
            $v['nickname'] = html_entity_decode($author_list[$v['uid']]['nickname']);
            $v['avatar'] = $author_list[$v['uid']]['avatar'];
            $v['content'] = html_entity_decode($v['content']);
            $v['create_time'] = Common::fmtTime($v['create_time']);

            $v['from'] = '';
            if($v['parent_id']){
                if(isset($parent_cm_list[$v['parent_id']])){
                    $v['from'] = sprintf('%s%s：', '回复', $parent_cm_list[$v['parent_id']]['nickname']);
                }else{
                    $v['from'] = sprintf('%s%s：', '回复', '匿名');
                }
            }
        }
        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $data, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    /**
     * 知物评论点赞
     */
    public function likeAction(){
        $item_id = $this->getInput('item_id');
        $type = $this->getInput('type');
        $step = intval($type) ? 1 : -1;
        $ret = Gou_Service_Comment::like($item_id, $step);
        if($ret)$this->output(0, '点赞成功.',array('id'=>$item_id));
        $this->output(-1, 'Sorry.',array('id'=>$item_id));
    }

    /**
     * 发表知物评论
     */
    public function addAction(){
        $data = $this->getInput(array('uid', 'sign', 'item_id', 'content', 'nickname', 'pid'));
//        $type = $this->getInput('type');
        list($comment, $user) = $this->_cookData($data);

        $row = Gou_Service_Comment::getBy($comment);
        if(!empty($row)) $this->output(0, '请勿重复评论！');

        $ip = $this->getInput('ip');
        $comment['region'] =  Gou_Service_Comment::getRegion($ip);

        $ret = Gou_Service_Comment::add($comment);
        $res = Gou_Service_Story::updateComment($data['item_id']);

        Gou_Service_Config::setValue("Config_Version", Common::getTime());
        Gou_Service_Config::setValue("Story_Version", Common::getTime());

        //添加消息
        $story = Gou_Service_Story::get($data['item_id']);
        User_Service_Msg::addStoryCommentMsg(4, $story['uid'], $story['title'], $data['uid'], $story['id']);

        if(intval($data['pid']) > 0){
            $parent_com = Gou_Service_Comment::get($data['pid']);
            User_Service_Msg::addStoryCommentMsg(4, $parent_com['uid'], $story['title'], $data['uid'], $story['id']);

            //push
            list($nickname) = User_Service_Uid::getUserFmtByUid($data['uid']);
            $push_title = User_Service_Msg::msgFmt(4, 0, $nickname);
            User_Service_Msg::pushMsg($parent_com['uid'], $push_title, $story['title'], 3, 'com.gionee.client.MesaggeList');
        }

        if(empty($user)){
            $this->output(0, '添加评论成功.',array('id'=>$ret));
        }

        User_Service_Uid::updateUser($user, array('uid' => $user['uid']));

        $this->output(0, '添加评论成功.', array('id' => $ret));
    }

    private function _cookData($info) {
        //验证签名
        $site_config = Common::getConfig('siteConfig');
//        $encrypt_str = $info['uid'] . $site_config['secretKey'];
        $encrypt_str = $info['uid'] . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';
//        if(md5($encrypt_str) !== $info['sign']) $this->output(-1, '非法请求.');
        unset($site_config);

//        if(!$info['uid']) $this->output(-1, 'UID不能为空.');
        if(!$info['content']) $this->output(-1, '评论内容不能为空.');
        $comment['old_content'] = htmlspecialchars_decode($info['content']);

        list($comment['content'], $kwd) = Gou_Service_Sensitive::fuck($comment['old_content']);
        $version = Common::getAndroidClientVersion();
        if (!empty($kwd)) {
            if ($version > 258)$this->clientOutput(1, implode(',', $kwd));
            $this->clientOutput(2, '内容包含敏感内容', $kwd);
        }

        $comment['item_id'] = $info['item_id'];
        $comment['uid'] = $info['uid'];
        $comment['parent_id'] = $info['pid'];
        $comment['os'] = 1;

        if($info['nickname']){
            if(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $info['nickname'])) $this->clientOutput(2, '昵称限制1~12个字符.');
            list(, $kwd) = Gou_Service_Sensitive::fuck($info['nickname']);
            if (!empty($kwd)) {
                if ($version >258)$this->clientOutput(1, implode(',', $kwd));
                $this->clientOutput(2, '昵称包含敏感内容', $kwd);

            }
        }

        //上传头像
        $info['avatar'] = '';
        if(isset($_FILES['avatar'])){
            $ret = Common::upload('avatar', 'user');
            if($ret['code']===0){
                $info['avatar'] = $ret['data'];
            }else{
                $this->output(-1, '上传失败！');
            }
        }

        if(empty($info['nickname']) && empty($info['avatar'])){
            return array($comment, array());
        }

        $user['uid'] = $info['uid'];
        if($info['nickname']) $user['nickname'] = $info['nickname'];
        if($info['avatar']) $user['avatar'] = $info['avatar'];

        return array($comment, $user);
    }
}
