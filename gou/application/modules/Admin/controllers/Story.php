<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @description 知物后台
 * StoryController
 *
 * @author ryan
 *
 */
class StoryController extends Admin_BaseController {

	public $actions = array(
		'indexUrl' => '/Admin/Story/index',
		'listUrl' => '/Admin/Story/list',
		'previewUrl' => '/Story/detail',
		'addUrl' => '/Admin/Story/add',
		'topUrl' => '/Admin/Story/top',
		'activeUrl' => '/Admin/Story/active',
		'addPostUrl' => '/Admin/Story/add_post',
		'editUrl' => '/Admin/Story/edit',
		'editPostUrl' => '/Admin/Story/edit_post',
        'uploadImgUrl' => '/Admin/Story/uploadImg',
		'deleteUrl' => '/Admin/Story/delete',
		'uploadUrl' => '/Admin/Story/upload',
		'uploadPostUrl' => '/Admin/Story/upload_post',
	);

	public $perpage = 15;

    public $versionName = 'Story_Version';
	/**
	 *
	 * 知物发布列表
	 *
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;

        $search['status'] = 1;
        $param = $this->getInput(array('category_id', 'author_id', 'status', 'time_type', 'from_time', 'to_time', 'title', 'channel'));
        if (!empty($param['category_id'])) $search['category_id'] = $param['category_id'];
        if (!empty($param['author_id']))   $search['author_id']   = $param['author_id'];
        if (!empty($param['title']))       $search['title']       = array('LIKE', $param['title']);
        if (!empty($param['status'])) {
                $search['start_time'] = array('>=', time());
            if ($param['status'] == 1)
                $search['start_time'] = array('<=', time());
        }

        if (!empty($param['time_type'])){
            $timeColumn = $param['time_type'] == 1 ? 'create_time' :'start_time' ;
            if ($param['from_time']) $search[$timeColumn] = array('>=', strtotime($param['start_time']));
            if ($param['to_time']) $search[$timeColumn] = array('<=', strtotime($param['end_time']));
            if ($param['from_time'] && $param['to_time']) {
                $search[$timeColumn] = array(
                    array('>=', strtotime($param['from_time'] )),
                    array('<=', strtotime($param['to_time']))
                );
            }
        }

        if (intval($param['channel'])>=0) $search['channel'] = $param['channel'];

        $orderby = array('recommend'=>'DESC','sort'=>'DESC','create_time'=>'DESC',  'id'=>'DESC');

        $cats = Gou_Service_StoryCategory::getCats();
		list($total, $result) = Gou_Service_Story::getList($page, $perpage, $search, $orderby);

        $authors = $user_uids = array();
        if($total){
            $author_ids = array_keys(Common::resetKey($result, 'author_id'));
            $authors = Gou_Service_UserAuthor::getsBy(array('id'=>array('IN', $author_ids)));
            $authors = Common::resetKey($authors, 'id');

            $uids = array_keys(Common::resetKey($result, 'uid'));
            $user_uids = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
            $user_uids = Common::resetKey($user_uids, 'uid');
        }

        $staticroot = Common::getAttachPath();
        foreach ($result as $k=>&$v) {
            $author = array('nickname'=>'购物大厅网友', 'avatar'=>'');
            if($v['author_id'] && $authors[$v['author_id']]){
                $author = array_merge($author, $authors[$v['author_id']]);
            }elseif($v['uid'] && $user_uids[$v['uid']]){
                $user_uids[$v['uid']]['nickname'] = $user_uids[$v['uid']]['nickname']?$user_uids[$v['uid']]['nickname']:'购物大厅网友';
                $author = array_merge($author, $user_uids[$v['uid']]);
            }

            $v['category'] = isset($cats[$v['category_id']])?$cats[$v['category_id']]['title']:'';
            $v['author'] = $author['nickname'];
            $v['pub_status']='待发布';
            if($v['start_time']<=time())$v['pub_status']='已发布';
        }

        $authors = Gou_Service_UserAuthor::getAuthors();

        $url = $this->actions['indexUrl'].'/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('param', $param);
        $this->assign('authors', $authors);
        $this->assign('channel', Gou_Service_Story::$channel);
        $this->assign('cats', $cats);
		$this->assign('data', $result);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->cookieParams();
	}

    /**
     * 知物草稿列表
     */
    public function listAction(){
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		//搜索参数
        $search['status'] = array('<>',1);
        $param = $this->getInput(array('category_id', 'author_id', 'status','time_type', 'from_time','to_time', 'title', 'channel'));
        if (!empty($param['category_id'])) $search['category_id'] = $param['category_id'];
        if (!empty($param['author_id'])) $search['author_id'] = $param['author_id'];
        if (!empty($param['title'])) $search['title'] = array('LIKE',$param['title']);
        if (!empty($param['status'])){
            $search['status'] = $param['status'];
            if($param['status']==1){
                $search['status'] = 0;
                $search['is_cancel'] = 0;
            }

            if($param['status']==2) {
                unset($search['status']);
                $search['is_cancel']=1;
            }
        }

        if (!empty($param['time_type'])){
            $timeColumn = $param['time_type'] == 1 ? 'create_time' :'start_time' ;
            if ($param['from_time']) $search[$timeColumn] = array('>=', strtotime($param['start_time']));
            if ($param['to_time']) $search[$timeColumn] = array('<=', strtotime($param['end_time']));
            if ($param['from_time'] && $param['to_time']) {
                $search[$timeColumn] = array(
                    array('>=', strtotime($param['from_time'] )),
                    array('<=', strtotime($param['to_time']))
                );
            }
        }

        if (intval($param['channel'])>=0) $search['channel'] = $param['channel'];

        $orderby = array('create_time'=>'DESC', 'id'=>'DESC');

        //获取知物分类列表
        $cats = Gou_Service_StoryCategory::getCats();
        list($total, $result) = Gou_Service_Story::getList($page, $perpage, $search, $orderby);

        $authors = $user_uids = array();
        if($total){
            $author_ids = array_keys(Common::resetKey($result, 'author_id'));
            $authors = Gou_Service_UserAuthor::getsBy(array('id'=>array('IN', $author_ids)));
            $authors = Common::resetKey($authors, 'id');

            $uids = array_keys(Common::resetKey($result, 'uid'));
            $user_uids = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
            $user_uids = Common::resetKey($user_uids, 'uid');
        }

        $status = Gou_Service_Story::$status;

        foreach ($result as &$v) {
            $author = array('nickname'=>'购物大厅网友', 'avatar'=>'');
            if($v['author_id'] && $authors[$v['author_id']]){
                $author = array_merge($author, $authors[$v['author_id']]);
            }elseif($v['uid'] && $user_uids[$v['uid']]){
                $user_uids[$v['uid']]['nickname'] = $user_uids[$v['uid']]['nickname']?$user_uids[$v['uid']]['nickname']:'购物大厅网友';
                $author = array_merge($author, $user_uids[$v['uid']]);
            }

            $v['category'] = isset($cats[$v['category_id']])?$cats[$v['category_id']]['title']:'-';
            $v['author'] = $author['nickname'];
            $v['pub_status'] = $status[$v['status']];
            if($v['is_cancel']) $v['pub_status'] = '已撤稿';
        }

        $authors = Gou_Service_UserAuthor::getAuthors();

        $url = $this->actions['listUrl'] . '/?' . http_build_query(array_filter($param)) . '&';
        $this->assign('param', $param);
        $this->assign('authors', $authors);
        $this->assign('channel', Gou_Service_Story::$channel);
        $this->assign('cats', $cats);
        $this->assign('data', $result);
        $this->assign('pager', Common::getPages($total, $page, $perpage, $url));
        $this->cookieParams();
	}

	/**
	 *
	 *添加知物
	 *
	 */
	public function addAction(){
        $authors = Gou_Service_UserAuthor::getAuthors();
        $cats= Gou_Service_StoryCategory::getCats();
        $this->assign('authors', $authors);
        $this->assign('cats', $cats);
        $this->assign('status', Gou_Service_Story::$status);
        $this->assign('ueditor', true);
        $this->assign('dir', 'story');
	}

	/**
	 * 编辑知物
	 *
	 */
	public function editAction() {
        $id = $this->getInput('id');
        $authors = Gou_Service_UserAuthor::getAuthors();
        $cats = Gou_Service_StoryCategory::getCats();
        $info = Gou_Service_Story::get(intval($id));

        $author = $author_temp = array('nickname'=>'购物大厅网友', 'avatar'=>'');
        if($info['author_id']){
            $author_temp = Gou_Service_UserAuthor::get($info['author_id']);
        }elseif($info['uid']){
            $author_temp = User_Service_Uid::getByUid($info['uid']);
            $author_temp['nickname'] = $author_temp['nickname']?$author_temp['nickname']:'购物大厅网友';
        }
        $author = array_merge($author, $author_temp);
        $this->assign('author',$author);

        $this->assign('backurl', $this->getInput('backurl'));
        $this->assign('authors',$authors);
        $this->assign('cats',$cats);
		$this->assign('info', $info);
        $this->assign('status', Gou_Service_Story::$status);
        $this->assign('ueditor', true);
        $this->assign('dir', 'story');
	}

	/**
	 *
	 *删除知物
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
        $type = $this->getInput('type');

		$info = Gou_Service_Story::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        $this->dropThumb(intval($info['id']));
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['images']);
        $result = Gou_Service_Story::delete($id);

		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

    /**
     * 删除缩略图
     * @param $id
     */
    private function dropThumb($id){
        $info = Gou_Service_Story::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');

        $images = explode(',', $info['images']);
        $images_thumb = explode(',', $info['images_thumb']);
        $images = array_diff($images, $images_thumb);

        foreach ($images as $img) {
            Util_File::del(Common::getConfig('siteConfig', 'attachPath') .$img);
        }
    }
	/**
	 * 添加提交
	 */
	public function add_postAction(){
        $pre_id = $this->getPost('pre_id');
        if(!empty($pre_id)){
            foreach ($pre_id as $id) {
                $result= Gou_Service_Story::delete($id);
            }
        }
		$info = $this->getPost(array('title', 'category_id', 'author_id', 'sort', 'status', 'start_time', 'summary', 'content', 'channel', 'reason'));
        $info = $this->_cookData($info);
        $result = Gou_Service_Story::add($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功',$result);
	}

	/**
	 * 编辑提交
	 */
	public function edit_postAction(){
        $pre_id = $this->getPost('pre_id');
        $info = $this->getPost(array('id', 'title', 'sort', 'category_id', 'author_id', 'status', 'start_time', 'summary', 'channel', 'content', 'reason'));
        $result= Gou_Service_Story::deletes('id', $pre_id);
        $this->dropThumb(intval($info['id']));
        $info = $this->_cookData($info);
		$result = Gou_Service_Story::update($info, intval($info['id']));
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');

	}

    /**
     * 置顶/取消置顶
     */
    public function topAction(){
        $id = $this->getPost('id');
        $type = $this->getPost('type');
        $info['recommend'] = $type;
		$result = Gou_Service_Story::update($info, intval($id));
		if (!$result) $this->output(-1, '操作失败');
        Gou_Service_Config::setValue($this->versionName, Common::getTime());
		$this->output(0, '操作成功');

	}

    /**
     * 激活/撤稿
     */
    public function activeAction(){
        $info = $this->getPost(array('id', 'status'));
        $info['is_cancel'] = intval(!$info['status']);
		$result = Gou_Service_Story::update($info, intval($info['id']));
		if (!$result) $this->output(-1, '操作失败');
        Gou_Service_Config::setValue($this->versionName, Common::getTime());
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
        $ret = Common::upload('imgFile', 'story');
        $attachPath = Common::getConfig('siteConfig', 'attachPath');
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
    }


    /**
     * 从文章提取图片地址生成缩略图
     * @param string $content 文章内容
     * @return bool|string
     */
    public function createThumb($content){
        $str = htmlspecialchars_decode($content);
        $pattern = '/<img\s.*src=[\"\'](.*)[\"\']/isU';
        preg_match_all($pattern,$str,$match);
        if(empty($match[1])){
            return false;
        }

        //android版有三张缩略图
        $images = array_slice($match[1], 0, 3);
        $thumbs = Util_Imagick::makeThumb($images, 'story', 225, 160);
        
        //ios版只有一张缩略图
        $image = array_slice($match[1], 0, 1);
        $thumb = Util_Imagick::makeThumb($image, 'story', 264, 240);

        return array(implode(',', $thumbs), $thumb[0]);
    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
    	if(!$info['title']) $this->output(-1, '标题不能为空.');
    	if(!$info['content']) $this->output(-1, '内容不能为空.');
    	if(!$info['summary']) $this->output(-1, '摘要不能为空.');
        if(!$info['category_id']) $this->output(-1, '栏目不能为空.');
    	
    	$info['content'] = $this->updateImgUrl($info['content']);

        list($thumbs, $info['img']) = $this->createThumb($info['content']);

        $info['images'] = $thumbs ? $thumbs : '';

        if(isset($info['summary'])) $info['summary'] = htmlspecialchars_decode($info['summary']);
        if(isset($info['start_time'])) $info['start_time'] = strtotime($info['start_time']);
        if(isset($info['end_time'])) $info['end_time'] = strtotime($info['end_time']);

        if($info['status']==1&&$info['start_time']>time()+180)$this->output(-1, '已发布时间现在起保持在3分钟以内.');
        if($info['status']==2&&$info['start_time']<time())$this->output(-1, '预发布时间不能小于当前时间.');

        //当是发布和待发布状态, 那文章肯定是没有撤销的
        if($info['status']==1||$info['status']==2) $info['is_cancel'] = 0;
        //当设置为待发布状态, 保存是为发布状态, 待发布是通过start_time来判断的
        if($info['status']==2) $info['status']=1;

    	return $info;
    }
}