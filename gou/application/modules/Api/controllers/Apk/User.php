<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Ryan
 * Class Apk_UserController
 */
class Apk_UserController extends Api_BaseController {

    private $perpage=10;
    public $actions = array(
        'story_detailUrl'=>'/story/detail',
    );

    /**
     * 我的收藏列表
     */
    public function favoriteAction() {

        $page = intval($this->getInput('page')); 
        $uid = $this->getInput("uid");

        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'
        $type = intval($this->getInput("type"));
       
        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        if (!$type) $type = 1;

        /**
         * 收藏列表
         */
        list($total,$favorite) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));
        $favorite = Common::resetKey($favorite, 'item_id');

        //知物列表
        $sid = array_keys($favorite);
        $authors= Gou_Service_UserAuthor::getAuthors();
        list(, $data) = Gou_Service_Story::getsBy(array('id'=>array("IN", $sid)));

        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();
        $result=array();
        foreach ($data as $v) {
            $item = array(
              'id' => $v['id'],
              'title' => html_entity_decode($v['title']),
              'summary' => html_entity_decode($v['summary']),
              'praise' => Common::parise($v['praise']),
              'uid' => $uid,
              'favorite' => Common::parise($v['favorite']),
              'fav_id' => $favorite[$v['id']]['id'],
              'is_favorite' => true,
              'author' => $authors[$v['author_id']]['nickname'],
              'avatar' => sprintf('%s%s', $staticroot, $authors[$v['author_id']]['avatar']),
              'url' => $webroot . $this->actions['story_detailUrl'] . '?id=' . $v['id'] . '&uid=' . $uid,
              'start_time' => $v['start_time'],
              'create_time' => $favorite[$v['id']]['create_time'],
            );

            if (!empty($v['images'])) {
                $v['images'] = explode(",", $v['images']);
                foreach ($v['images'] as &$img) {
                    $img = sprintf('%s%s', $staticroot, $img);
                }
                $item['images']=$v['images'];
            }

            array_push($result,$item);
        }
        $result = Common::resetKey($result,'create_time');
        krsort($result);
        $result=array_values($result);
        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $result, 'hasnext' => $hasnext, 'curpage' => $page));
	}


    /**
     * 取消收藏
     */
    public function cancelfavAction(){
        $id=intval($this->getInput('id'));
        $type=intval($this->getInput('type'));
        $item_id=intval($this->getInput('item_id'));
        $ret = User_Service_Favorite::deleteFavorite($id);
        if(!$ret){
            $this->output(1,'取消收藏失败',array('id'=>$id));
        }
        if($type==1){
            Gou_Service_Story::updateFavorite($item_id,-1);
            Gou_Service_Config::setValue('Story_Version', Common::getTime());
            $this->output(0,'取消收藏成功',array('id'=>$id));
        }
        $this->output(0,'取消收藏成功',array('id'=>$id));
    }

    /**
     * 保存用户信息编辑(积分)
     * @return json
     */
    public function saveUidAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1,'无法获取UID.');

        $nickname = trim($this->getInput('nickname'));
        if(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $nickname)) $this->output(-1,'昵称限制1~12个字符.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($nickname);
//        if(!empty($kwd)) $this->output(-1, '不能包含 "'.implode(',',$kwd).'" ，请修改', $kwd);
        if(!empty($kwd)) $this->output(-1, '请不要输入敏感内容', $kwd);

        $mobile = trim($this->getInput('mobile'));
        if(!Common::checkMobile($mobile)) $this->output(-1,'请输入11位手机号码.');

        if(User_Service_Uid::updateUserBy(array('nickname'=>$nickname, 'mobile'=>$mobile), array('uid'=>$uid))){
            $this->standOutput(0, '提交成功.');
        }
        $this->output(-1, '提交失败.');
    }

    /**
     * 用户信息
     */
    public function userAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        list($nickname, $avatar, $scoreid) = User_Service_Uid::getUserFmtByUid($uid);
        $data = array(
            'nickname' => $nickname,
            'avatar' => $avatar,
            'scoreid' => $scoreid
        );
        $this->output(0, '', $user);
    }

    /**
     * 用户信息编辑
     */
    public function modifyAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $input = $this->getInput(array('nickname', 'mobile'));
        $input['uid'] = $uid;
        $input = $this->_cookData($input);

        $avatar = '';

        //上传头像
        if(isset($_FILES['img_avatar'])){
            $ret = Common::upload('img_avatar', 'user');
            if($ret['code']===0){
                $avatar = $ret['data'];
            }else{
                $this->output(-1, '上传失败！');
            }
        }

        $author['nickname'] = $input['nickname'];
        if($avatar) $author['avatar'] = $avatar;
        if($input['mobile']) $author['mobile'] = $input['mobile'];

        $result = User_Service_Uid::updateUserBy($author, array('uid'=>$uid));

        if($result){
            if($avatar){
                $attach = Common::getAttachPath();
                $this->output(0, '', array('avatar'=>$attach . $avatar));
            }
            $this->standOutput(0, '');
        }
        $this->output(-1, '提交失败.');
    }

    /**
     * 我的消息列表
     */
    public function msgAction(){
        $uid = Common::getAndroidtUid();
//        测试
//        $uid = '222ec205fffb58c0fd1c9267c8e016ed';
        if(!$uid) $this->output(-1, '非法请求.');

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        //获取消息列表
        list($total, $msg) = User_Service_Msg::getList(
            $page,
            $this->perpage,
            array('uid' => $uid, 'cate' => array('!=', 2)), //去掉问答类型
            array('create_time' => 'DESC')
        );

        $data = array();
        if($msg){
            $cate = User_Service_Msg::$msg_cat;

            $by_uids = array_keys(Common::resetKey($msg, 'by_uid'));
            $by_uids = array_unique($by_uids);
            $by_uids = array_filter($by_uids);
            $authors = User_Service_Uid::getUsersFmtByUid($by_uids);
            $ids_no_read = array();

            $story_ids = array();
            foreach($msg as $item){
                if($item['cate'] == 1) $story_ids[] = $item['true_id'];
            }
            $story_favs = User_Service_Favorite::getStoryListByUid($uid, $story_ids);
            $story_favs = Common::resetKey($story_favs, 'item_id');
            list(, $stories) = Gou_Service_Story::getsBy(array('id'=>array('IN', $story_ids)));
            $stories = Common::resetKey($stories, 'id');




            foreach($msg as $item){
                $msg_item = array(
                    'id' => $item['id'],
                    'label' => isset($cate[$item['cate']]) ? $cate[$item['cate']] : "",
                    'msg' => '',
                    'cate' => $item['cate'],
                    'true_id' => $item['true_id'],
                    'desc' => html_entity_decode($item['desc']),
                    'url' => '',
                    'is_sys' => (bool)$item['is_sys'],
                    'time' => Common::fmtTime($item['create_time']),
                );

                $nickname = '';
                if(isset($authors[$item['by_uid']])) $nickname = $authors[$item['by_uid']]['nickname'];
                $msg_con = User_Service_Msg::msgFmt($item['msg_type'], '', $nickname);
                $msg_item['msg'] = $msg_con;

                $webroot = Common::getWebRoot();
                switch($item['cate']){
                    case 1:
                        $msg_item['url'] = sprintf('%s/apk/%s', $webroot, $item['url']);
                        $msg_item['is_favorite'] = isset($story_favs[$item['true_id']]) ? true : false;
                        $msg_item['fav_id'] = isset($story_favs[$item['true_id']]) ? $story_favs[$item['true_id']]['id'] : 0;
                        $msg_item['comment'] = isset($stories[$item['true_id']]) ? Common::parise($stories[$item['true_id']]['comment']) : '';
                        $msg_item['like'] = isset($stories[$item['true_id']]) ? Common::parise($stories[$item['true_id']]['comment'] + $stories[$item['true_id']]['praise']) : '';
                        break;
                }
                if(!$item['is_read']) $ids_no_read[] = $item['id'];
                array_push($data, $msg_item);
            }

            //设置消息已读
            if(!empty($ids_no_read)) User_Service_Msg::updatesReadMsg($uid);
        }
//        Common::log($data, 'msg.log');
        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    /**
     * 验证
     * @param $data
     * @return mixed
     */
    private function _cookData($data){
        $data['nickname'] = trim($data['nickname']);
        if(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $data['nickname'])) $this->output(-1, '昵称限制1~12个字符.');

        $data['mobile'] = trim($data['mobile']);
        if($data['mobile'] && !Common::checkMobile($data['mobile'])) $this->output(-1, '请输入11位手机号码.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($data['nickname']);
        if(!empty($kwd)) $this->output(-1, '请不要输入敏感内容', $kwd);

        return $data;
    }
}
