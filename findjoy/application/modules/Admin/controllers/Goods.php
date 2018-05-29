<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class GoodsController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Goods/index',
		'addUrl' => '/Admin/Goods/add',
		'addPostUrl' => '/Admin/Goods/add_post',
		'editUrl' => '/Admin/Goods/edit',
		'editPostUrl' => '/Admin/Goods/edit_post',
		'deleteUrl' => '/Admin/Goods/delete',
		'uploadUrl' => '/Admin/Goods/upload',
		'uploadPostUrl' => '/Admin/Goods/upload_post',
		'uploadImgUrl' => '/Admin/Goods/uploadImg',
		'deleteImgUrl' => '/Admin/Goods/deleteimg',
	);
	
	public $show_type = 0;
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$param = $this->getInput(array('status', 'title'));

		if ($param['status']) $search['status'] = intval($param['status'])-1;
		if ($param['title']) $search['title'] = array('LIKE', $param['title']);
		
		list($total, $goods) = Fj_Service_Goods::getList($page, $this->perpage, $search, array('sort'=>'DESC','id'=>'DESC'));
		
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		$this->assign('search', $param);
		
		//get pager
		$url = $this->actions['listUrl'].'/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Goods::getGoods(intval($id));
        $this->assign('dir', "goods");
        $this->assign('ueditor', true);
		$this->assign('info', $info);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addAction() {
        $this->assign('dir', "goods");
        $this->assign('ueditor', true);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort','ishot', 'title', 'img', 'price', 'hk_price', 'start_time', 'end_time', 
				'stock_num', 'limit_num', 'sale_num', 'comment_num', 'status','descrip'));
		$info = $this->_cookData($info);
		$result = Fj_Service_Goods::addGoods($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort', 'ishot', 'title', 'img', 'price', 'hk_price', 'start_time', 'end_time', 
				'stock_num', 'limit_num', 'sale_num', 'comment_num', 'status','descrip'));
		$info = $this->_cookData($info);

		$ret = Fj_Service_Goods::updateGoods($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');

		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['stock_num']) $this->output(-1, '商品库存不能为空.');
		if(!$info['limit_num']) $this->output(-1, '商品限购不能为空.');
		if(!$info['descrip']) $this->output(-1, '商品详情不能为空.');
		if(!$info['ishot']) $info['ishot'] = 0;

		if(!$info['title']) $this->output(-1, '商品名称不能为空.');
		if (Util_String::strlen($info['title']) > 60) $this->output('-1', '商品标题不能超过30个字.');
		
		if(!$info['img']) $this->output(-1, '商品图片不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		$info['descrip'] = html_entity_decode(stripslashes($info['descrip']));

		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Fj_Service_Goods::getGoods($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Fj_Service_Goods::deleteGoods($id);
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'Goods');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }

	/**
	 * 获取可用的商品列表
	 */
	public function getAllGoodsAction(){
		$list = Fj_Service_Goods::getAvailableGoods();
		$goods = array();
		foreach($list as $item){
			$goods[] = array(
				'value' => $item['id'],
				'label' => 'ID:' . $item['id'] .' => '.$item['title'],
				'desc'	=> $item['price']
			);
		}
		$this->output(0, '', $goods);
	}
}
