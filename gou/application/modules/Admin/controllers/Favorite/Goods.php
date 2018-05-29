<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Favorite_GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' 		=> '/admin/Favorite_Goods/index',
      	'listUrl' 		=> '/Admin/Favorite_Goods/index',
      	'addUrl' 		=> '/Admin/Favorite_Goods/add',
      	'itemUrl' 		=> '/Admin/Third_Goods/index',
      	'addPostUrl' 	=> '/Admin/Favorite_Goods/add_post',
      	'editUrl' 		=> '/Admin/Favorite_Goods/edit',
      	'editPostUrl' 	=> '/Admin/Favorite_Goods/edit_post',
      	'deleteUrl' 	=> '/Admin/Favorite_Goods/delete',
      	'uploadUrl' 	=> '/Admin/Favorite_Goods/upload',
      	'uploadPostUrl' => '/Admin/Favorite_Goods/upload_post',
	);

	public $perpage = 14;


	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {

		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('item_id', 'uid', 'id', 'channel_id', 'title', 'src'));
		if ($page < 1) $page = 1;

		$search = array();
		if ($params['item_id']) $search['item_id'] = $params['item_id'];
        if (is_numeric($params['channel_id'])) $search['channel_id'] = intval($params['channel_id']);
		if ($params['uid'])   $search['uid']   = $params['uid'];
		if ($params['title']) $search['title'] = array('LIKE', $params['title']);
		if ($params['src'])   $search['src']   = $params['src'];
		if ($params['id'])    $search['id']    = $params['id'];
		$search['type'] = 2;	//商品类型
		
		list($total, $list) = User_Service_Favorite::getList($page, $this->perpage, $search);
		$favorite_list = Common::resetKey($list, 'item_id');
		$item_ids = array_keys($favorite_list);
		
		$goods = array();
		if($item_ids) {
              foreach ($item_ids as $id) {
                  $goods[] = Third_Service_Goods::get($id);
              }
              $goods = Common::resetKey($goods, 'goods_id');
          }

        $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
        foreach($list as &$v){
            $good = isset($goods[$v['item_id']])?$goods[$v['item_id']]:array();
            $v['title'] = !empty($good)?$good['title']:'';
            $v['src']   = !empty($good)?$channels[$good['channel_id']]['name']:'';
            $v['image'] = !empty($good)?$good['img']:'';
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
