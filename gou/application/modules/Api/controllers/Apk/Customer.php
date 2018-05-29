<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * @author ryan
 *
 */
class Apk_CustomerController extends Api_BaseController
{


    public $perpage = 5;

    public function listAction()
    {
        $perpage = intval($this->getInput('perpage'));
        if (empty($perpage)) $perpage = $this->perpage;
        //type 0 ask 1 answer
        //status 0 未处理，1。已问题问题问题问题问题问题经处理
        $items[] = array('id' => 2, 'msg' => 'Hi there', 'status' => 1, 'type' => 0, 'kf' => array());
        $items[] = array('id' => 15, 'kid' => 2, 'msg' => 'Hi there', 'status' => 0, 'type' => 1, 'kf' => array('uid' => 'e19e4288b4c0717ede2389fb855b8539', 'nickname' => 'alic', 'avatar' => 'xx.jpg'));
        $items[] = array('id' => 24, 'msg' => 'Hi there', 'status' => 1, 'type' => 0, 'kf' => array());
        $items[] = array('id' => 58, 'kid' => 2, 'msg' => 'Hi there', 'status' => 0, 'type' => 1, 'kf' => array('uid' => 'e19e4288b4c0717ede2389fb855b8539', 'nickname' => 'alic', 'avatar' => 'xx.jpg'));
        $items[] = array('id' => 74, 'msg' => 'Hi there', 'status' => 1, 'type' => 0, 'kf' => array());
        $items[] = array('id' => 88, 'kid' => 2, 'msg' => 'Hi there', 'status' => 0, 'type' => 1, 'kf' => array('uid' => 'e19e4288b4c0717ede2389fb855b8539', 'nickname' => 'alic', 'avatar' => 'xx.jpg'));
        $items[] = array('id' => 56, 'msg' => 'Hi there', 'status' => 0, 'type' => 0, 'kf' => array());
        $items[] = array('id' => 95, 'kid' => 2, 'msg' => 'Hi there', 'status' => 0, 'type' => 1, 'kf' => array('uid' => 'e19e4288b4c0717ede2389fb855b8539', 'nickname' => 'alic', 'avatar' => 'xx.jpg'));
        $items[] = array('id' => 56, 'msg' => 'Hi there', 'status' => 0, 'type' => 0, 'kf' => array());
        $data = $items;
        $total = 400;
        $last = end($items);
        $hasnext = (ceil((int)$total / $this->perpage) - 1) > 0 ? true : false;
        $this->output(0, '', array('list' => $data, 'hasnext' => $hasnext, 'last_id' => $last['id']));

    }

    public function addAction()
    {
        $data = $this->getInput(array('content'));
////        $type = $this->getInput('type');
//        list($comment, $user) = $this->_cookData($data);
//
//        $row = Gou_Service_Comment::getBy($comment);
//        if(!empty($row)) $this->output(0, '请勿重复评论！');
//
//        $ip = $this->getInput('ip');
//        $comment['region'] =  Gou_Service_Comment::getRegion($ip);
//
//        $ret = Gou_Service_Comment::add($comment);
//        $res = Gou_Service_Story::updateComment($data['item_id']);
//
//        Gou_Service_Config::setValue("Config_Version", Common::getTime());
//        Gou_Service_Config::setValue("Story_Version", Common::getTime());
//
//        //添加消息
//        $story = Gou_Service_Story::get($data['item_id']);
//        User_Service_Msg::addStoryCommentMsg(4, $story['uid'], $story['title'], $data['uid'], $story['id']);
//
//        if(intval($data['pid']) > 0){
//            $parent_com = Gou_Service_Comment::get($data['pid']);
//            User_Service_Msg::addStoryCommentMsg(4, $parent_com['uid'], $story['title'], $data['uid'], $story['id']);
//
//            //push
//            list($nickname) = User_Service_Uid::getUserFmtByUid($data['uid']);
//            $push_title = User_Service_Msg::msgFmt(4, 0, $nickname);
//            User_Service_Msg::pushMsg($parent_com['uid'], $push_title, $story['title'], 3, 'com.gionee.client.MesaggeList');
//        }
//
//        if(empty($user)){
//            $this->output(0, '评论成功.',array('id'=>$ret));
//        }
//
//        User_Service_Uid::updateUser($user, array('uid' => $user['uid']));

        $this->output(0, '评论成功.', array('id' => 5, 'tip' => true));
    }


    private function _cookData($info)
    {
        //验证签名
        $site_config = Common::getConfig('siteConfig');
//        $encrypt_str = $info['uid'] . $site_config['secretKey'];
        $encrypt_str = $info['uid'] . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';
        if (md5($encrypt_str) !== $info['sign']) $this->output(-1, '非法请求.');
        unset($site_config);

        if (!$info['uid']) $this->output(-1, 'UID不能为空.');
        if (!$info['content']) $this->output(-1, '评论内容不能为空.');
        $comment['old_content'] = htmlspecialchars_decode($info['content']);

        list($comment['content'], $kwd) = Gou_Service_Sensitive::fuck($comment['old_content']);
//        if(!empty($kwd))$this->clientOutput(2, '内容不能包含 "' . implode(',', $kwd) . '" ，请修改', $kwd);
        if (!empty($kwd)) $this->clientOutput(2, '请不要输入敏感内容', $kwd);

        $comment['item_id'] = $info['item_id'];
        $comment['uid'] = $info['uid'];
        $comment['parent_id'] = $info['pid'];
        $comment['os'] = 1;


        //上传头像
        $info['avatar'] = '';
        if (isset($_FILES['avatar'])) {
            $ret = Common::upload('avatar', 'user');
            if ($ret['code'] === 0) {
                $info['avatar'] = $ret['data'];
            } else {
                $this->output(-1, '上传失败！');
            }
        }

        if (empty($info['nickname']) && empty($info['avatar'])) {
            return array($comment, array());
        }

        $user['uid'] = $info['uid'];
        if ($info['nickname']) $user['nickname'] = $info['nickname'];
        if ($info['avatar']) $user['avatar'] = $info['avatar'];

        return array($comment, $user);
    }
}
