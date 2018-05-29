<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class Third_GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' 		=> '/admin/Third_Goods/index',
      	'listUrl' 		=> '/Admin/Third_Goods/index',
      	'addUrl' 		=> '/Admin/Third_Goods/add',
      	'addPostUrl' 	=> '/Admin/Third_Goods/add_post',
      	'editUrl' 		=> '/Admin/Third_Goods/edit',
      	'editPostUrl' 	=> '/Admin/Third_Goods/edit_post',
      	'deleteUrl' 	=> '/Admin/Third_Goods/delete',
      	'uploadUrl' 	=> '/Admin/Third_Goods/upload',
      	'uploadPostUrl' => '/Admin/Third_Goods/upload_post',
	);

	public $perpage = 14;


	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {

		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('goods_id', 'item_id', 'unique_pid'));
		if ($page < 1) $page = 1;

		$search = array();
        if (!empty($params['item_id'])) $params['goods_id'] = $params['item_id'];
        if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
        /*if ($params['channel_id']) $search['channel_id']  = $params['channel_id'] ;
		if ($params['title']) $search['title'] = $params['title'];
		if ($params['id']) $search['id'] = $params['id'];*/
		if ($params['unique_pid']) $search['unique_pid'] = $params['unique_pid'];
		/*if (is_numeric($params['system'])) $search['system'] = intval($params['system']);*/

        $list = array();
		if ($params['goods_id']) {
            if ($info = Third_Service_Goods::get($params['goods_id'])) {
                array_push($list, $info);
            }
         } else if ($params['unique_pid']) {
                list(, $goods_ids) = Third_Service_GoodsUnipid::getsBy(array("unique_pid"=>$params['unique_pid']));
                foreach ($goods_ids as $val) {
                     if ($info = Third_Service_Goods::get($val["goods_id"])) {
                        array_push($list, $info);
                    }
                }
         }
		// list($total, $list) = Third_Service_Goods::getList($page, $this->perpage, $search, array('id'=>'DESC'));

        $channels = Third_Service_Goods::$channel;
        $status = Third_Service_Goods::$status;
        foreach($list as &$v){
            $v['channel'] = isset($channels[$v['channel_id']])?$channels[$v['channel_id']]:'';
            $v['status'] = isset($status[$v['status']])?$status[$v['status']]:'';
        }

		$this->assign('channel', Third_Service_Goods::$channel);
		$this->assign('status', Third_Service_Goods::$status);
		$this->assign('list', $list);
		$this->assign('total', count($list));
		$this->assign('params', $params);

		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('total', $total);
		$this->cookieParams();
	}

    public function countAction(){
        //Third_Service_Goods::getCount();
    }
    
    /**
     * 编辑收藏
     *
     */
    public function editAction() {
        $goods_id = $this->getInput('goods_id');
        $info = Third_Service_Goods::get(intval($goods_id));
        $this->assign('info', $info);
    }

    /**
     *
     *删除收藏
     */
    public function deleteAction() {
        $goods_id = $this->getInput('goods_id');
        $info = Third_Service_Goods::get($goods_id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
        $result = Third_Service_Goods::delete($goods_id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }



    /**
     *
     * 编辑提交
     */
    public function edit_postAction(){
        $info = $this->getPost(array('id', 'goods_id', 'request_count', 'img', 'title', 'price', ));
        $info = $this->_cookData($info);
        $result = Third_Service_Goods::update($info, intval($info['goods_id']));
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
        if(!$info['title']) $this->output(-1, '标题不能为空.');

        if(!empty($info['title']))$info['title']=html_entity_decode($info['title']);
        if(!empty($info['request_count']))$info['request_count']=intval($info['request_count']);
        if(!empty($info['price']))$info['price']=Common::money($info['price'], 2);
        
        return $info;
    }
}
