<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Third_ShopController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' 		=> '/admin/Third_Shop/index',
      	'listUrl' 		=> '/Admin/Third_Shop/index',
      	'addUrl' 		=> '/Admin/Third_Shop/add',
      	'addPostUrl' 	=> '/Admin/Third_Shop/add_post',
      	'editUrl' 		=> '/Admin/Third_Shop/edit',
      	'editPostUrl' 	=> '/Admin/Third_Shop/edit_post',
      	'deleteUrl' 	=> '/Admin/Third_Shop/delete',
      	'uploadUrl' 	=> '/Admin/Third_Shop/upload',
      	'uploadPostUrl' => '/Admin/Third_Shop/upload_post',
	);

	public $perpage = 14;

	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {

		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('shop_id', 'item_id', 'id', 'channel_id', 'name', 'system'));
		if ($page < 1) $page = 1;

		$search = array();
        if (!empty($params['item_id'])) $params['shop_id'] = $params['item_id'];
        if ($params['shop_id']) $search['shop_id'] = $params['shop_id'];
        if ($params['channel_id']) $search['channel_id']  = $params['channel_id'] ;
		if ($params['name']) $search['name'] = array("LIKE",$params['name']);
		if ($params['id']) $search['id'] = $params['id'];
		if (is_numeric($params['system'])) $search['system'] = intval($params['system']);
		
		list($total, $list) = Third_Service_Shop::getList($page, $this->perpage, $search, array('id'=>'DESC'));

        $channels = Third_Service_Shop::$channel;
        $status = Third_Service_Shop::$status;
        foreach($list as &$v){
            $v['channel'] = isset($channels[$v['channel_id']])?$channels[$v['channel_id']]:'';
            $v['status'] = isset($status[$v['status']])?$status[$v['status']]:'';
        }

		$this->assign('channel', $channels);
		$this->assign('status',  $status);
		$this->assign('list',    $list);
		$this->assign('total',   $total);
		$this->assign('params',  $params);

		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('total', $total);
		$this->cookieParams();
	}

    public function countAction(){
//         Third_Service_Shop::getCount();
    }

    /**
     * 编辑
     *
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Third_Service_Shop::get(intval($id));
        $this->assign('info', $info);
    }

    /**
     *
     *删除
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Third_Service_Shop::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['image']);
        $result = Third_Service_Shop::deleteShop($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }



    /**
     *
     * 编辑
     */
    public function edit_postAction(){
        $info = $this->getPost(array('id', 'shop_id', 'request_count', 'logo', 'name'));
        $info = $this->_cookData($info);
        $result = Third_Service_Shop::update($info, intval($info['id']));
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
        $ret = Common::upload('img', 'third');
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
        if(!$info['name']) $this->output(-1, '标题不能为空.');

        if(!empty($info['name']))$info['name']=html_entity_decode($info['name']);
        if(!empty($info['request_count']))$info['request_count']=intval($info['request_count']);

        return $info;
    }
}
