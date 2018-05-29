<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Favorite_StoryController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' 		=> '/admin/Favorite_Story/index',
      	'listUrl' 		=> '/Admin/Favorite_Story/index',
      	'addUrl' 		=> '/Admin/Favorite_Story/add',
      	'addPostUrl' 	=> '/Admin/Favorite_Story/add_post',
      	'editUrl' 		=> '/Admin/Favorite_Story/edit',
      	'editPostUrl' 	=> '/Admin/Favorite_Story/edit_post',
      	'deleteUrl' 	=> '/Admin/Favorite_Story/delete',
      	'uploadUrl' 	=> '/Admin/Favorite_Story/upload',
      	'uploadPostUrl' => '/Admin/Favorite_Story/upload_post',
	);

	public $perpage = 14;
	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {

		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('item_id', 'uid','id', 'channel_id', 'title'));
		if ($page < 1) $page = 1;

		$search = array();
		if ($params['item_id']) $search['item_id'] = $params['item_id'];
        if (is_numeric($params['channel_id'])) $search['channel_id'] = intval($params['channel_id']);
		if ($params['uid']) $search['uid'] = $params['uid'];
		if ($params['title']) $search['title'] = array('LIKE',$params['title']);
		if ($params['id']) $search['id'] = $params['id'];
		$search['type'] = 1;	//知物类型
		
		list($total, $list) = User_Service_Favorite::getList($page, $this->perpage, $search);
		$favorite_list = Common::resetKey($list, 'item_id');
		$item_ids = array_keys($favorite_list);
		
		$storys = array();
		if($item_ids) {
		    list(,$storys) = Gou_Service_Story::getsBy(array('id'=>array('IN', $item_ids)), array('id'=>'DESC'));
		    $storys = Common::resetKey($storys, 'id');
		}
		
        foreach ($list as &$v) {
            if($v['data']){
                $tmp=json_decode($v['data'],true);
                if(!empty($tmp['image'])&&empty($v['image']))$v['image']=$tmp['image'];
                if(!empty($tmp['title'])&&empty($v['title']))$v['title']=$tmp['title'];
                if(!empty($tmp['src'])&&empty($v['src']))$v['src']=$tmp['src'];
                if(!empty($tmp['price'])&&empty($v['price']))$v['price']=$tmp['price'];
            }
            $story = isset($storys[$v['item_id']])?$storys[$v['item_id']]:array();
            $v['title'] = !empty($story)?$story['title']:$v['title'];
            $v['hits'] = !empty($story)?$story['hits']:0;
        }

		$this->assign('list', $list);
		$this->assign('total', $total);
		$this->assign('params', $params);

		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('total', $total);
		$this->cookieParams();
	}

    public function countAction(){
        User_Service_Favorite::getCount();
    }

    /**
     * 编辑收藏
     *
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = User_Service_Favorite::get(intval($id));
        $tmp=json_decode($info['data'],true);
        if(!empty($tmp['image'])&&empty($info['image']))$info['image']=$tmp['image'];
        if(!empty($tmp['title'])&&empty($info['title']))$info['title']=$tmp['title'];
        if(!empty($tmp['src'])&&empty($info['src']))$info['src']=$tmp['src'];
        if(!empty($tmp['price'])&&empty($info['price']))$info['price']=$tmp['price'];

        $this->assign('info', $info);
    }

    /**
     *
     *删除收藏
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = User_Service_Favorite::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['image']);
        $result = User_Service_Favorite::deleteFavorite($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }



    /**
     *
     * 编辑提交
     */
    public function edit_postAction(){
        $info = $this->getPost(array('uid', 'id', 'item_id', 'src', 'image', 'title', 'url', 'price', ));
        $info = $this->_cookData($info);
        $result = User_Service_Favorite::update($info, intval($info['id']));
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');

    }

    /**
     *
     * 上传页面
     */
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     *
     * 上传动作
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'favorite');
        $imgId = $this->getPost('imgId');
        $this->assign('code' , $ret['data']);
        $this->assign('msg' , $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($info) {
        if(!$info['title']) $this->output(-1, '收藏标题不能为空.');
        if(!$info['url']) $this->output(-1, '收藏链接不能为空.');

        if(!empty($info['title']))$info['title']=html_entity_decode($info['title']);
        if(!empty($info['title']))$data['title']=html_entity_decode($info['title']);
        if(!empty($info['src']))$data['src']=$info['src'];
        if(!empty($info['image']))$data['image']=$info['image'];
        if(!empty($info['price']))$data['price']=$info['price'];
        if(!empty($data))$info['data']=json_encode($data);
        return $info;
    }
}
