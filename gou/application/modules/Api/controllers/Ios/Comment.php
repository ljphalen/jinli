<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Ios_CommentController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
				'listUrl'=>'/api/comment/list',
	);

    public function listAction()
    {

        $type = $this->getInput('type');
        $item_id = intval($this->getInput('item_id'));
        if(empty($item_id)||!$type){
            $this->output(1, '参数有误');
        }

        $perpage = intval($this->getInput('perpage'));
        $page = intval($this->getInput('page'));

        if ($page < 1) $page = 1;

        $orderby =  array('create_time' => 'ASC', 'id' => 'ASC');
        //shops
        $condition['item_id'] = $item_id;
        if (empty($perpage)) $perpage = $this->perpage;

        list($total, $data) = Gou_Service_Comment::getList($page,$perpage , $condition, $orderby);
        if($total==0){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => 1));
        }

        $uids = array_keys(Common::resetKey($data,'uid'));
        $user = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
        $user = Common::resetKey($user,'uid');

        foreach ($data as &$v) {
            unset($v['old_content']);
            $v['nickname']=html_entity_decode($user[$v['uid']]['nickname']);
            $v['content']=html_entity_decode($v['content']);
            $v['create_time'] = Common::fmtTime($v['create_time']);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $data, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    public function likeAction(){
        $item_id = $this->getInput('item_id');
        $type = $this->getInput('type');
        $step = $type ? 1 : -1;
        $ret = Gou_Service_Comment::like($item_id, $step);
        if($ret)$this->output(0, '点赞成功.',array('id'=>$item_id));
        $this->output(-1, 'Sorry.',array('id'=>$item_id));
    }

    public function countAction()
    {
        $type = $this->getInput('type');
        $item_id = intval($this->getInput('item_id'));
        if(empty($item_id)||!$type)$this->output(1, '参数有误');

        $condition['item_id'] = $item_id;
        $count = Gou_Service_Comment::getCount($condition);
        $this->output(0, '', array('count' => $count));
    }

    public function addAction(){
        $data = $this->getInput(array('sign', 'item_id', 'content', 'nickname'));
        $type = $this->getInput('type');
        list($comment,$user) = $this->_cookData($data);
        $row = Gou_Service_Comment::getBy($comment);
        if(!empty($row))$this->output(1, '请勿重复评论！');
        $ip = $this->getInput('ip');
        $comment['region'] =  Gou_Service_Comment::getRegion($ip);

        $ret = Gou_Service_Comment::add($comment);
        $res = Gou_Service_Story::updateComment($data['item_id']);
        $count = Gou_Service_Comment::getCount(array('item_id'=>$data['item_id']));

        if(empty($user)){
           $this->output(0, '添加评论成功.',array('id'=>$ret,'count'=>$count));
        }

        Gou_Service_Config::setValue("Config_Version", Common::getTime());
        Gou_Service_Config::setValue("Story_Version", Common::getTime());
//        $u=User_Service_Uid::getBy(array('uid'=>$user['uid']));
//        if (empty($u)) {
//            User_Service_Uid::addUser($user);
//        } else {
//            User_Service_Uid::updateUser($user, $u['id']);
//        }
        User_Service_Uid::updateUser($user, array(array('uid' => $user['uid'])));
        $this->output(0, '添加评论成功.',array('id'=>$ret,'count'=>$count));
    }

    private function _cookData($info) {
        $info['uid'] = Common::getIosUid();
        if(!$info['uid']) $this->output(-1, 'UID不能为空.');
        if(!$info['content']) $this->output(-1, '评论内容不能为空.');
        if(!$info['sign']||md5($info['uid'].'NTQzY2JmMzJhYTg2N2RvY3Mva2V5')!=$info['sign']) $this->output(-1,'非法请求.');
        $comment['old_content'] = htmlspecialchars_decode($info['content']);

        list($comment['content'],$kwd) = Gou_Service_Sensitive::fuck($comment['old_content']);
        if(!empty($kwd))$this->clientOutput(2, '内容不能包含 "'.implode(',',$kwd).'" ，请修改',$kwd);

        $comment['item_id'] = $info['item_id'];
        $comment['uid'] = $info['uid'];
        $comment['os'] = 2;

        if(empty($info['nickname'])){
            return array($comment,array());
        }else{
            if(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $info['nickname'])) $this->clientOutput(2,'昵称限制1~12个字符.');
            list(, $kwd) = Gou_Service_Sensitive::fuck($info['nickname']);
            if(!empty($kwd)) $this->clientOutput(2, '昵称不能包含 "'.implode(',',$kwd).'" ，请修改',$kwd);
        }

        $user['uid'] = $info['uid'];
        $user['nickname'] = $info['nickname'];

        return array($comment,$user);
    }
}
