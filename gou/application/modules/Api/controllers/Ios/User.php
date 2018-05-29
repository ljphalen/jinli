<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Ryan
 * Class Ios_UserController
 */
class Ios_UserController extends Api_BaseController {

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
     * 保存用户信息编辑
     * @return json
     */
    public function saveUidAction(){
        $uid = Common::getIosUid();
        if(!$uid) $this->output(-1,'无法获取UID.');

        $nickname = trim($this->getInput('nickname'));
        if(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $nickname)) $this->output(-1,'昵称限制1~12个字符.');

//        $nickname_len = Util_String::strlen($nickname);
//        if($nickname_len<2 || $nickname_len>14) $this->output(-1,'请输入2-14位字符.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($nickname);
        if(!empty($kwd)) $this->output(-1, '不能包含 "'.implode(',',$kwd).'" ，请修改',$kwd);

        $mobile = trim($this->getInput('mobile'));
        if(!Common::checkMobile($mobile)) $this->output(-1,'请输入11位手机号码.');

        if(User_Service_Uid::updateUserBy(array('nickname'=>$nickname, 'mobile'=>$mobile), array('uid'=>$uid))){
            $this->output(0,'提交成功.');
        }
        $this->output(-1,'提交失败.');
    }

    /**
     * 用户信息
     */
    public function userAction(){
        $uid = Common::getIosUid();
        if(!$uid) $this->output(-1, '无法获取UID.');

        $user = User_Service_Uid::getByUid($uid);
        $attach = Common::getAttachPath();

        if($user){
            $data['uid'] = $uid;
            $data['nickname'] = $user['nickname'] ? $user['nickname'] : $user['scoreid'];
            $data['avatar'] = $user['avatar'] ? $attach . $user['avatar'] : '';
        }

        $this->output(0, '', $data);
    }

    /**
     * 用户信息编辑
     */
    public function modifyAction(){
        $uid = Common::getIosUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $input = $this->getPost(array('sign', 'nickname'));
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

        $author = array(
            'nickname'  => $input['nickname'],
            'avatar'    => $avatar
        );

        $result = User_Service_Uid::updateUserBy($author, array('uid'=>$uid));

        if($result) $this->output(0, '');

        $this->output(-1, '提交失败.');
    }

    /**
     * 验证
     * @param $data
     * @return mixed
     */
    private function _cookData($data){
        //验证签名
        $sign = $this->getPost('sign');
        $site_config = Common::getConfig('siteConfig');
        $encrypt_str = $data['uid'] . $site_config['secretKey'];
        if(md5($encrypt_str) !== $data['sign']) $this->output(-1, '非法请求.');
        unset($site_config);

        $data['nickname'] = trim($data['nickname']);
        if(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]{1,12}$/u", $data['nickname'])) $this->output(-1,'昵称限制1~12个字符.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($data['nickname']);
        if(!empty($kwd)) $this->output(-1, '不能包含 "'.implode(',',$kwd).'" ，请修改',$kwd);

        return $data;
    }

}
