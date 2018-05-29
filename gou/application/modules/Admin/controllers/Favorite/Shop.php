<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Favorite_ShopController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' 		=> '/admin/Favorite_Shop/index',
      	'listUrl' 		=> '/Admin/Favorite_Shop/index',
      	'addUrl' 		=> '/Admin/Favorite_Shop/add',
        'itemUrl' 		=> '/Admin/Third_Shop/index',
      	'addPostUrl' 	=> '/Admin/Favorite_Shop/add_post',
      	'editUrl' 		=> '/Admin/Favorite_Shop/edit',
      	'exportUrl' 	=> '/Admin/Favorite_Shop/export',
      	'editPostUrl' 	=> '/Admin/Favorite_Shop/edit_post',
      	'deleteUrl' 	=> '/Admin/Favorite_Shop/delete',
      	'uploadUrl' 	=> '/Admin/Favorite_Shop/upload',
      	'uploadPostUrl' => '/Admin/Favorite_Shop/upload_post',
	);

	public $perpage = 20;

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
		if ($params['title']) $search['title'] = array('LIKE', $params['title']);
		if ($params['src']) $search['src'] = $params['src'];
		if ($params['id']) $search['id'] = $params['id'];
		$search['type'] = 3;	//店铺类型
		
		list($total, $list) = User_Service_Favorite::getList($page, $this->perpage, $search);
		$favorite_list = Common::resetKey($list, 'item_id');
		$item_ids = array_keys($favorite_list);
		
		$shops = array();
		if($item_ids) {
			list(,$shops) = Third_Service_Shop::getsBy(array('shop_id'=>array('IN', $item_ids)), array('id'=>'DESC'));
			$shops = Common::resetKey($shops, 'shop_id');
		}

        $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
        foreach($list as &$v){
            $shop = isset($shops[$v['item_id']])?$shops[$v['item_id']]:array();
            $v['title'] = !empty($shop)?$shop['name']:'';
            $v['src'] = !empty($shop)?$channels[$shop['channel_id']]['name']:'';
            $v['image'] = !empty($shop)?$shop['logo']:'';
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
     *
     * Get order list
     */
    public function exportAction() {
        header('Content-Encoding: none');
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export-shop-fav-'.date('Y-m-d').'.csv"');
        header('Cache-Control: no-cache');

        $fp = fopen('php://output', 'a');

        set_time_limit(0);


        $perpage = 1000;
        $page = 1;
        $params  = $this->getInput(array('item_id', 'uid','id', 'channel_id', 'title', 'src'));


        $search = array();
        if ($params['item_id']) $search['item_id'] = $params['item_id'];
        if (is_numeric($params['channel_id'])) $search['channel_id'] = intval($params['channel_id']);
        if ($params['uid']) $search['uid'] = $params['uid'];
        if ($params['title']) $search['title'] = array('LIKE', $params['title']);
        if ($params['src']) $search['src'] = $params['src'];
        if ($params['id']) $search['id'] = $params['id'];
        $search['type'] = 3;	//店铺类型
        list($total, ) = User_Service_Favorite::getList($page, $perpage, $search);

        $file_header = array("编号", "产品编号", "标题", "平台", "设备", "用户标识", "连接地址");
        array_walk($file_header,function(&$v){ return $v =  mb_convert_encoding($v, 'gb2312', 'UTF-8');});
        fputcsv($fp,$file_header);
        while($total>$perpage*($page-1)){
            $row = "";
            list($total, $list) = User_Service_Favorite::getList($page, $perpage, $search);
            $favorite_list = Common::resetKey($list, 'item_id');
            $item_ids = array_keys($favorite_list);

            $shops = array();
            if($item_ids) {
                list(,$shops) = Third_Service_Shop::getsBy(array('shop_id'=>array('IN', $item_ids)), array('id'=>'DESC'));
                $shops = Common::resetKey($shops, 'shop_id');
            }

            $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');

            foreach($list as $v){
                $shop = isset($shops[$v['item_id']])?$shops[$v['item_id']]:array();
                $v['title'] = !empty($shop)?$shop['name']:'';
                $v['src'] = !empty($shop)?$channels[$shop['channel_id']]['name']:'';
                //$v['image'] = !empty($shop)?$shop['logo']:'';
                $p = $v['channel_id']?'iOS':'Android';
                //$img =strpos($v['image'], 'http://') === false ? Common::getAttachPath().$v['image']:$v['image'];
                $row['id']      = $v['id'];
                $row['item_id'] = $v['item_id'];
                $row['title']   = $this->en($v['title']);
                $row['src']     = $this->en($v['src']);
                $row['device']  = $p;
                //$row['image']   = $img;
                $row['uid']     = $v['uid'];
                $row['url']     = $v['url'];
                fputcsv($fp,$row);
            }
            $page ++;
            ob_flush();
            flush();
        }
        exit;

    }


    public function en($str){
        return mb_convert_encoding($str, 'gb2312', 'UTF-8');
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
        
        $shop = array();
        $shop = Third_Service_Shop::getBy(array('shop_id'=>$info['item_id']));
        
        $tmp=json_decode($shop['data'],true);
        if(!empty($tmp['image'])&&empty($shop['logo']))$shop['logo']=$tmp['image'];
        if(!empty($tmp['title'])&&empty($shop['name']))$shop['name']=$tmp['title'];
        if(!empty($tmp['src'])&&empty($info['src']))$info['src']=$tmp['src'];
        if(!empty($tmp['price'])&&empty($info['price']))$info['price']=$tmp['price'];

        $this->assign('info', $info);
        $this->assign('shop', $shop);
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
