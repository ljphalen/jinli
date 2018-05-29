<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_StoryController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
				'indexUrl'=>'/api/story/index',
				'detailUrl'=>'/story/detail',
				'itemUrl'=>'/story/item',
				'tjUrl'=>'/index/tj'
	);
	public $cacheKey = 'Apk_Story_index';
	public $versionName = 'Story_Version';

    public function indexAction()
    {
        $version = intval($this->getInput('version'));
        $uid = strval($this->getInput('uid'));
        $server_version = Gou_Service_Config::getValue($this->versionName);

        if ($version >= $server_version) {
            $search['start_time'] = array(array('<=',time()),array('>=',$version));
            $search['status'] = 1;
            $x= Gou_Service_Story::getBy($search);
            if(empty($x))$this->output(1, '', array('version'=>$server_version));
            Gou_Service_Config::setValue($this->versionName,Common::getTime());
            $server_version = Gou_Service_Config::getValue($this->versionName);
        }

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        //shops
        $condition['start_time'] = array(array('<=',time()));
        $condition['status'] = 1;

        if ($version >= $server_version) {
            $this->output(1, '', array('version'=>$server_version));
        }
        $sort = array('recommend'=>'DESC','sort'=>'DESC','start_time'=>'DESC', 'id'=>'DESC');

        list($total, $data) = Gou_Service_Story::getList($page, $this->perpage, $condition, $sort);
        if($total == 0){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => 1,'version'=>$server_version));
        }
        $fid =array_keys(Common::resetKey($data, 'id'));
        $fav = User_Service_Favorite::getStoryListByUid($uid, $fid);

        $author_ids = array_keys(Common::resetKey($data, 'author_id'));
        $authors = Gou_Service_UserAuthor::getsBy(array('id'=>array('IN', $author_ids)));
        $authors = Common::resetKey($authors, 'id');

        $uids = array_keys(Common::resetKey($data, 'uid'));
        $user_uids = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
        $user_uids = Common::resetKey($user_uids, 'uid');

        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();
        $mid = Gou_Service_Story::getMaxId($condition);
        $result = array();
        foreach ($data as $v) {
            $author = array('nickname'=>'购物大厅网友', 'avatar'=>'');
            if($v['author_id'] && $authors[$v['author_id']]){
                $author = array_merge($author, $authors[$v['author_id']]);
            }elseif($v['uid'] && $user_uids[$v['uid']]){
                $author = array_merge($author, $user_uids[$v['uid']]);
                $author['nickname'] = $author['nickname']?$author['nickname']:'购物大厅网友';
            }

            $item = array(
              'id' => intval($v['id']),
              'title' => html_entity_decode($v['title']),
              'summary' => html_entity_decode($v['summary']),
              'praise' => Common::parise($v['praise']),
              'favorite' => Common::parise($v['favorite']),
              'like' => Common::parise(sprintf('%s',$v['favorite']+$v['praise'])),
              'comment' => Common::parise($v['comment']),
              'start_time' => Gou_Service_Story::fmtTime($v['start_time']),
              'recommend'=>false,
              'is_favorite' => false,
              'is_newest' => false,
              'author' => $author['nickname'],
              'avatar' => sprintf('%s%s', $staticroot, $author['avatar']),
              'url' => $webroot . $this->actions['detailUrl'] . '?id=' . $v['id'] . '&uid=' . $uid
            );

            if($v['recommend'])$item['recommend']=true;
            if (!empty($fav[$v['id']])) {
                $item['fav_id'] = $fav[$v['id']]['id'];
                $item['is_favorite'] = true;
            }
            if($v['id']==$mid)$item['is_newest']=true;

            if($v['images']){
                $v['images'] = explode(",", $v['images']);
                foreach ($v['images'] as &$img) {
                    $img = sprintf('%s%s', $staticroot, $img);
                }
                $item['images'] = $v['images'];
            }
            array_push($result, $item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $result, 'hasnext' => $hasnext, 'curpage' => $page,'version'=>$server_version));
    }

    public function listAction()
    {
        $version = intval($this->getInput('version'));
        $uid = strval($this->getInput('uid'));
        $server_version = Gou_Service_Config::getValue($this->versionName);

        if ($version >= $server_version) {
            $search['start_time'] = array(array('<=',time()),array('>=',$version));
            $search['status'] = 1;
            $x= Gou_Service_Story::getBy($search);
            if(empty($x))$this->output(1, '', array('version'=>$server_version));
            Gou_Service_Config::setValue($this->versionName,Common::getTime());
            $server_version = Gou_Service_Config::getValue($this->versionName);
        }

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        //shops
        $condition['start_time'] = array('<=',time());
        $condition['status'] = 1;
        $sort = array('recommend'=>'DESC','sort'=>'DESC','start_time'=>'DESC');

        list($total, $data) = Gou_Service_Story::getList($page, $this->perpage, $condition, $sort);
        if($total == 0){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => 1,'version'=>$server_version));
        }
        $fid =array_keys(Common::resetKey($data,'id'));
        $fav = User_Service_Favorite::getStoryListByUid($uid,$fid);

        $author_ids = array_keys(Common::resetKey($data, 'author_id'));
        $authors = Gou_Service_UserAuthor::getsBy(array('id'=>array('IN', $author_ids)));
        $authors = Common::resetKey($authors, 'id');

        $uids = array_keys(Common::resetKey($data, 'uid'));
        $user_uids = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
        $user_uids = Common::resetKey($user_uids, 'uid');

        $mid = Gou_Service_Story::getMaxId($condition);

        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();

        $result = array();
        foreach ($data as $v) {
            $author = array('nickname'=>'购物大厅网友', 'avatar'=>'');
            if($v['author_id'] && $authors[$v['author_id']]){
                $author = array_merge($author, $authors[$v['author_id']]);
            }elseif($v['uid'] && $user_uids[$v['uid']]){
                $author = array_merge($author, $user_uids[$v['uid']]);
                $author['nickname'] = $author['nickname']?$author['nickname']:'购物大厅网友';
            }

            $item = array(
              'id' => intval($v['id']),
              'title' => html_entity_decode($v['title']),
              'summary' => html_entity_decode($v['summary']),
              'praise' => Common::parise($v['praise']),
              'favorite' => Common::parise($v['favorite']),
              'like' => Common::parise(sprintf('%s',$v['favorite']+$v['praise'])),
              'comment' => Common::parise($v['comment']),
              'recommend'=>false,
              'is_favorite' => false,
              'is_newest' => false,
              'author' => $author['nickname'],
              'avatar' => sprintf('%s%s', $staticroot, $author['avatar']),
              'url' => $webroot . $this->actions['itemUrl'] . '?id=' . $v['id'] . '&uid=' . $uid,
              'start_time' => Gou_Service_Story::fmtTime($v['start_time'])
            );

            if($v['recommend'])$item['recommend']=true;
            if (!empty($fav[$v['id']])) {
                $item['fav_id'] = $fav[$v['id']]['id'];
                $item['is_favorite'] = true;
            }

            if($v['id']==$mid)$item['is_newest']=true;

            if($v['images']){
                $v['images'] = explode(",", $v['images']);
                foreach ($v['images'] as &$img) {
                    $img = sprintf('%s%s', $staticroot, $img);
                }
                $item['images'] = $v['images'];
            }

            array_push($result, $item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $result, 'hasnext' => $hasnext, 'curpage' => $page,'version'=>$server_version));
    }

    /**
     * 知物列表(带问答头部)
     */
    public function listWithHeaderAction(){
        $version = intval($this->getInput('version'));
        $uid = strval($this->getInput('uid'));
        $server_version = Gou_Service_Config::getValue($this->versionName);

        if ($version >= $server_version) {
            $search['start_time'] = array(array('<=', time()), array('>=', $version));
            $search['status'] = 1;
            $x = Gou_Service_Story::getBy($search);
            if(empty($x)) $this->output(1, '', array('version' => $server_version));
            Gou_Service_Config::setValue($this->versionName, Common::getTime());
            $server_version = Gou_Service_Config::getValue($this->versionName);
        }

        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $perpage = intval($this->getInput('perpage'));
        if($perpage) $this->perpage = $perpage;

        //shops
        $condition['start_time'] = array('<=', time());
        $condition['status'] = 1;
        $sort = array('recommend'=>'DESC', 'sort'=>'DESC', 'start_time'=>'DESC');

        list($total, $data) = Gou_Service_Story::getList($page, $this->perpage, $condition, $sort);
        if($total == 0){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => 1, 'version'=>$server_version));
        }

        $fid =array_keys(Common::resetKey($data, 'id'));
        $fav = User_Service_Favorite::getStoryListByUid($uid, $fid);

        $author_ids = array_keys(Common::resetKey($data, 'author_id'));
        $authors = Gou_Service_UserAuthor::getsBy(array('id'=>array('IN', $author_ids)));
        $authors = Common::resetKey($authors, 'id');

        $uids = array_keys(Common::resetKey($data, 'uid'));
        $user_uids = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
        $user_uids = Common::resetKey($user_uids, 'uid');

        $mid = Gou_Service_Story::getMaxId($condition);

        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();

        $result = array();

        //知物概况
        if($page == 1) array_push($result, $this->_qaIntro());

        foreach ($data as $v) {
            $author = array('nickname'=>'购物大厅网友', 'avatar'=>'');
            if($v['author_id'] && $authors[$v['author_id']]){
                $author = array_merge($author, $authors[$v['author_id']]);
            }elseif($v['uid'] && $user_uids[$v['uid']]){
                $author = array_merge($author, $user_uids[$v['uid']]);
                $author['nickname'] = $author['nickname']?$author['nickname']:'购物大厅网友';
            }

            $item = array(
                'id' => intval($v['id']),
                'title' => html_entity_decode($v['title']),
                'summary' => html_entity_decode($v['summary']),
                'praise' => Common::parise($v['praise']),
                'favorite' => Common::parise($v['favorite']),
                'like' => Common::parise(sprintf('%s',$v['favorite']+$v['praise'])),
                'comment' => Common::parise($v['comment']),
                'recommend'=>false,
                'is_favorite' => false,
                'is_newest' => false,
                'author' => $author['nickname'],
                'avatar' => sprintf('%s%s', $staticroot, $author['avatar']),
                'url' => $webroot . $this->actions['itemUrl'] . '?id=' . $v['id'] . '&uid=' . $uid,
                'start_time' => Gou_Service_Story::fmtTime($v['start_time'])
            );

            if($v['recommend'])$item['recommend']=true;
            if (!empty($fav[$v['id']])) {
                $item['fav_id'] = $fav[$v['id']]['id'];
                $item['is_favorite'] = true;
            }

            if($v['id']==$mid)$item['is_newest']=true;

            if($v['images']){
                $v['images'] = explode(",", $v['images']);
                foreach ($v['images'] as &$img) {
                    $img = sprintf('%s%s', $staticroot, $img);
                }
                $item['images'] = $v['images'];
            }

            array_push($result, $item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $result, 'hasnext' => $hasnext, 'curpage' => $page, 'version'=>$server_version));
    }

    /**
     * 问答信息
     * @return mixed
     */
    private function _qaIntro(){
//        $data['title'] = Gou_Service_Config::getValue('qa_title');
        $data['intro'] = Gou_Service_Config::getValue('qa_intro');
        $data['image'] = Gou_Service_Config::getValue('qa_image');
        $data['image'] = $data['image'] ? Common::getAttachPath() . $data['image']:'';

        $data['qus_total'] = Gou_Service_QaQus::getCount(array('status' => 2));
        $data['ans_total'] = Gou_Service_QaAus::getCount(array('status' => array('IN', array(0, 1, 2))));

        if($data['qus_total'] >= 1000000) $data['qus_total'] = sprintf('%s万', number_format($data['qus_total']/1000000, 1));
        if($data['ans_total'] >= 1000000) $data['ans_total'] = sprintf('%s万', number_format($data['ans_total']/1000000, 1));
        return $data;
    }

    /**
     * 点赞
     */
    public function praiseAction(){
        $id=intval($this->getInput('id'));
        $type  =$this->getInput('type');
        $add=-1;
        if($type)$add=1;
        $ret=Gou_Service_Story::praise($id,$add);
        if($ret){
            Gou_Service_Config::setValue($this->versionName, Common::getTime());
            $story = Gou_Service_Story::get($id);
            $this->output(0,'success',array('type'=>$type,'praise'=>$story['praise']));
        }
        $this->output(1,'failure');
    }

    /**
     * 知物添加收藏
     */
    public function addfavAction(){

        $data['item_id'] = intval($this->getInput('id'));
        $data['uid'] = strval($this->getInput('uid'));
        $data['type'] = 1;

        $story = Gou_Service_Story::get($data['item_id']);
        if(!$story)$this->output(1,'记录不已删除或不存在，无法收藏',array('item_id'=>$data['item_id']));

        //add user
//        if($data['uid']) {
//            $user = User_Service_Uid::getBy(array('uid'=>$data['uid']));
//            if(!$user) User_Service_Uid::addUser(array('uid'=>$data['uid']));
//        }
        
        $item = User_Service_Favorite::getOneByParams($data['uid'],$data['item_id'],$data['type']);
        if($item) $this->output(1,'您已收藏，请勿重复收藏',array('id'=>$item['id']));
        
        $ret = User_Service_Favorite::addFavorite($data);
        if($ret){
            Gou_Service_Config::setValue($this->versionName, Common::getTime());
            Gou_Service_Story::updateFavorite($story['id']);
            
            $this->output(0,'收藏成功',array('fav_id'=>$ret));
        }
        $this->output(1,'收藏出错',$data);
    }

    //==================以下为晒单模块(hotOrder)方法=========================

    /**
     * 晒单提交
     * @return json
     */
    public function submitHotOrderAction(){
        list($uid, $id, $oid, $del, $total, $story_old, $nickname) = $this->_signCheck();

        $story['uid'] = $uid;
        $story['title'] = $this->getInput('title');
        $story['content'] = nl2br($this->getInput('content'));
        $story['order_id'] = $oid;
        $story['channel'] = 1; //渠道为晒单
        $story['is_cancel'] = 0;
        $story['status'] = 3;  //晒单的默认状态为待审核
        $story['start_time'] = Common::getTime();
        $story = $this->_hotOrder_cookData($story);

        if($id){
            $story = array_merge($story_old, $story);
            Gou_Service_Story::update($this->_imgHotOrder($del, $total, $story), $id);
        }else{
            $id = Gou_Service_Story::add($this->_imgHotOrder($del, $total, $story));
        }

        if($nickname){
            User_Service_Uid::updateUserBy(array('nickname'=>$nickname), array('uid'=>$uid));
        }

        if($id) $this->output(0, '', array('id'=>$id));

        $this->output(-1, '晒单提交失败.');
    }


    /**
     * 晒单图片提交
     * @param string $del
     * @param int $total
     * @param string $story
     * @return array
     */
    private function _imgHotOrder($del, $total, $story){
        $attachPath = Common::getConfig('siteConfig', 'attachPath');

        //缩略图
        $images_thumb = $story['images_thumb']?explode(',', $story['images_thumb']):array();

        //客户提交的原始图
        $images_client = $story['images_client']?explode(',', $story['images_client']):array();

        //客户端删除的图片
        if($del && $images_client && $images_thumb) list($images_client, $images_thumb) = $this->_delImage($del, $images_client, $images_thumb);

        //判断提交的图片数量和已经在文章中的图片数量是否少于2张或超过9张
        if(($total+count($images_client))<2 || ($total+count($images_client))>9) $this->output(-1, '图片数量限制为2~9张.');

        //客户端提交的图片
        if($total){
            $images_client_temp = array();

            for($i=0; $i<$total; $i++){
                $ret = Common::upload('img_'.$i, 'showorder');
                if($ret['code']===0){
                    $images_client_temp[$i]['image_org'] = $ret['data'];
                    $images_client_temp[$i]['image_thumb'] = $this->_createThumb(array($attachPath.$ret['data']));
                }else{
                    $this->output(-1, '上传失败！');
                }
            }

            if(count(array_filter($images_client_temp)) == $total){
                $staticroot = Common::getAttachPath();
                foreach($images_client_temp as $item){
                    $images_client[] = sprintf('%s/%s', $staticroot, $item['image_org']);
                    if(isset($item['image_thumb'])) $images_thumb[] = $item['image_thumb'];
                }
                unset($images_client_temp);
            }
        }

        foreach($images_client as $item){
            $story['content'] .= '<img src="'.$item.'" />';
        }

        $story['img'] = $this->_IosThumb($story['content'], $attachPath.$story['img']);
        $story['images_thumb'] = implode(',', $images_thumb);
        $story['images'] = implode(',', array_slice($images_thumb, 0, 3));
        $story['images_client'] = implode(',', $images_client);

        return $story;
    }

    /**
     * 晒单显示
     */
    public function showHotOrderAction(){
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');

        $user_uid = User_Service_Uid::getByUid($uid);
        $data['author'] = $user_uid['nickname']?$user_uid['nickname']:'购物大厅网友';

        $oid = intval($this->getInput('oid'));
        if(!$oid) $this->output(-1, '非法请求.');
        $data['oid'] = $oid;

        $id = intval($this->getInput('id'));
        if($id){
            $data['id'] = $id;
            $story = Gou_Service_Story::get($id);
            if(!$story) $this->output(-1, '晒单记录异常.');

            $data['title'] = html_entity_decode($story['title']);
            $data['content'] = html_entity_decode($story['content']);
            $data['content'] = preg_replace('/<br.*>/isU', "\n", $data['content']);
            $data['content'] = preg_replace('/<img\s.*>/isU', '', $data['content']);
            $data['content'] = strip_tags($data['content']);

            $data['create_time'] = date('Y年m月d日 H:i', $story['create_time']);
            $staticroot = Common::getAttachPath();
            $data['images_thumb'] = array();
            $images_thumb = $story['images_thumb']?explode(',', $story['images_thumb']):array();
            foreach($images_thumb as $item){
                $data['images_thumb'][] = array('img'=>sprintf('%s/%s', $staticroot, $item), 'tag'=>md5($item));
            }
            $status = Gou_Service_Story::$ostatus;
            $data['status_label'] = $status[$story['status']];
            $data['is_edit'] = false;
            if($story['status'] == 3 || $story['status'] == 6) $data['is_edit'] = true;
            if($story['status'] == 6) $data['reason'] = html_entity_decode($story['reason']);
        }

        $this->output(0, '', $data);
    }

    /**
     * 签名检查
     * @return array
     */
    private function _signCheck(){
        $uid = Common::getAndroidtUid();
        //$uid = 'e952234363gfdsddse2ytfdf5fd2rfd4';
        if(!$uid) $this->output(-1, '非法请求.');

        $oid = intval($this->getInput('oid'));
        //$oid = 10;
        if(!$oid) $this->output(-1, '非法请求.');

        $total = intval($this->getInput('total'));

        $id = 0;
        //$id = 5;
        $story = Gou_Service_Story::getBy(array('order_id'=>$oid));
        if($story){
            $id = intval($story['id']);
            if($story['status'] != 3 && $story['status'] != 6) {
                $info = '';
                switch ($story['status']){
                    case 4:
                        $info = '您发布的晒单“正在审核”，暂不能提交。';
                        break;
                    default:
                        $info = '您发布的晒单“已审核通过”，不能提交了。';
                }
                $this->output(-1, sprintf('%s', $info));
            }
        }

        $sign = $this->getInput('sign');
        $del = html_entity_decode($this->getInput('del'));

        $nickname = $this->getInput('author');
        if($nickname){
            list(, $kwd) = Gou_Service_Sensitive::fuck($nickname);
            if(!empty($kwd)) $this->output(-1, '昵称不能包含 "'.implode(',', $kwd).'" ，请修改',$kwd);
        }

        $site_config = Common::getConfig('siteConfig');
        $encrypt_str = sprintf($uid.'%s%s%s'.$site_config['secretKey'], $oid, $total, $del);
        if(md5($encrypt_str) !== $sign) $this->output(-1, '非法请求.');

        return array($uid, $id, $oid, $del, $total, $story, $nickname);
    }

    /**
     * 生成缩略图, 返回缩略图url
     * @param array $images
     * @param int $w
     * @param int $h
     * @return bool|string
     */
     private function _createThumb($images, $w = 225, $h = 160){
        $thumbs = Util_Imagick::makeThumb($images, 'showorder', $w, $h);
        return $thumbs[0];
    }

    /**
     * 获取内容中的图片
     * @param $content
     * @return mixed
     */
    private function _getContentImages($content){
        $pattern = '/<img\s.*src=[\"\'](.*)[\"\']/isU';
        preg_match_all($pattern, $content, $match);
        return $match[1];
    }

    /**
     * 生成ios缩略图, 返回缩略图url
     * @param $content
     * @return bool|string
     */
    private function _IosThumb($content, $old_img){
        Util_File::del($old_img);
        $pattern = '/<img\s.*src=[\"\'](.*)[\"\']/isU';
        preg_match($pattern, $content, $match);
        return $this->_createThumb($match[1], 264, 240);
    }

    /**
     * 删除服务器的图片
     * @param string $del 需要被删除的图片tag, json字符串
     * @param array $images_client 原始图片
     * @param array $images_thumb 缩略图
     * @return array
     */
    private function _delImage($del, $images_client, $images_thumb){
        $del = json_decode($del);
        if(is_array($del)){
            list($images, $images_thumb) = array($images_thumb, array());
            $attachPath = Common::getConfig('siteConfig', 'attachPath');
            foreach($images as $val){
                $images_thumb[] = array('img'=>$attachPath.$val, 'uri'=>$val, 'tag'=>md5($val));
            }
            $images_thumb = Common::resetKey($images_thumb, 'tag');
            foreach($del as $val){
                if(array_key_exists($val, $images_thumb)) {
                    $img_path = $images_thumb[$val]['img'];
                    foreach($images_client as $key=>$item){
                        $fileinfo = pathinfo($item);
                        $filename = $fileinfo['filename'];
                        if(stripos($img_path, $filename)>=0){
                            Util_File::del($img_path);
                            Util_File::del($item);
                            unset($images_thumb[$val]);
                            unset($images_client[$key]);
                            break;
                        }
                    }
                }
            }
            list($images_thumb_temp, $images_thumb) = array($images_thumb, array());
            foreach($images_thumb_temp as $item){
                $images_thumb[] = $item['uri'];
            }
        }
        return array($images_client, $images_thumb);
    }

    private function _hotOrder_cookData($story) {
        if(!$story['title']) $this->output(-1, '标题不能为空.');
        $story['title'] = trim($story['title']);

        list(, $kwd) = Gou_Service_Sensitive::fuck($story['title']);
        if(!empty($kwd)) $this->output(-1, '标题不能包含 "'.implode(',',$kwd).'" ，请修改',$kwd);

        if(!$story['content']) $this->output(-1, '内容不能为空.');
        if(Util_String::strlen($story['content']) > 500)  $this->output(-1, '内容字数不超过500.');

        list(, $kwd) = Gou_Service_Sensitive::fuck($story['content']);
        if(!empty($kwd)) $this->output(-1, '内容不能包含 "'.implode(',',$kwd).'" ，请修改',$kwd);

//        $story['content'] = htmlspecialchars($story['content']);

        $story['order_id'] = intval($story['order_id']);

        return $story;
    }
}
