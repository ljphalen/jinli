<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Favorite_WebController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' 		=> '/admin/Favorite_Web/index',
      	'listUrl' 		=> '/Admin/Favorite_Web/index',
      	'addUrl' 		=> '/Admin/Favorite_Web/add',
        'itemUrl' 		=> '/Admin/Third_Web/index',
      	'addPostUrl' 	=> '/Admin/Favorite_Web/add_post',
      	'editUrl' 		=> '/Admin/Favorite_Web/edit',
      	'editPostUrl' 	=> '/Admin/Favorite_Web/edit_post',
      	'deleteUrl' 	=> '/Admin/Favorite_Web/delete',
      	'uploadUrl' 	=> '/Admin/Favorite_Web/upload',
      	'uploadPostUrl' => '/Admin/Favorite_Web/upload_post',
	);

	public $perpage = 14;

	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {

		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('item_id', 'uid','id', 'channel_id', 'title', 'src'));
		if ($page < 1) $page = 1;

		$search = array();
		if ($params['item_id']) $search['item_id'] = $params['item_id'];
        if (is_numeric($params['channel_id'])) $search['channel_id'] = intval($params['channel_id']);
		if ($params['uid']) $search['uid'] = $params['uid'];
		if ($params['title']) $search['title'] = array('LIKE',$params['title']);
		if ($params['src']) $search['src'] = $params['src'];
		if ($params['id']) $search['id'] = $params['id'];
		$search['type'] = 4;	//网页类型
		
		list($total, $list) = User_Service_Favorite::getList($page, $this->perpage, $search);
		$favorite_list = Common::resetKey($list, 'item_id');
		$item_ids = array_keys($favorite_list);
		
		$webs = array();
		if($item_ids) {
			list(,$webs) = Third_Service_Web::getsBy(array('url_id'=>array('IN', $item_ids)), array('id'=>'DESC'));
			$webs = Common::resetKey($webs, 'url_id');
		}		
		
        $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
        foreach($list as &$v){
            $web = isset($webs[$v['item_id']])?$webs[$v['item_id']]:array();
            $v['title'] = !empty($web)?$web['title']:'';
            $v['src'] = !empty($web)?$channels[$web['channel_id']]['name']:'';
            if(empty($v['src'])){
                $v['src'] = Client_Service_Spider::getChannelName(html_entity_decode($v['url']));
            }
            $v['url'] = !empty($web)?$web['url']:'';
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
