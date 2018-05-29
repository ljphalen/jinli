<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 注意：
 * 因需求多次变更，在添加或者修改接口信息需要做以下几个步骤：
 * 1.Admin下 ChannelGoods 中的 $goodsAPI 数组 添加接口地址信息
 * 2.Library下 Client_Service_Channelgoods 中添加对应接口的适配函数
 * 3.front下 TejiaController redirectAction 方法添加对应的渠道编码
 * 4.apk下 TejiaController redirectAction 方法添加对应的渠道编码
 * 5.api下 Apk_V015_IndexController tejiaAction 设置对应的信息
 * 
 * 天天特价功能
 * @author huangsg
 *
 */
class ChannelgoodsController extends Admin_BaseController{
	
	public $actions = array(
			'indexUrl' => '/Admin/Channelgoods/index',
			'addUrl' => '/Admin/Channelgoods/add',
			'editUrl' => '/Admin/Channelgoods/edit',
			'searchUrl'=>'/Admin/Channelgoods/search',
			'yhdUrl'=>'/Admin/Channelgoods/yhd',
			'addPostUrl' => '/Admin/Channelgoods/add_post',
			'addYhdUrl' => '/Admin/Channelgoods/addyhd',
			'editPostUrl' => '/Admin/Channelgoods/edit_post',
			'deleteUrl' => '/Admin/Channelgoods/delete',
        	'meilishuoStep1Url' => '/Admin/Channelgoods/meilishuo_step1',
        	'meilishuoStep2Url' => '/Admin/Channelgoods/meilishuo_step2',
	        'batchUpdateUrl'=>'/Admin/Channelgoods/batchUpdate',
	);
	
	public $versionName = 'Channel_Goods_Version';
	public $perpage = 20;
	public $configs = array();

    public function init() {
        parent::init();
        $this->configs = Common::getConfig('tejiaConfig');
        $this->assign('configs', $this->configs);
    }
	
	/**
	 * 商品列表页
	 */
	public function indexAction(){
	    
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = $this->perpage;
		
		$cate_id = intval($this->getInput('cate_id'));
		if(!empty($cate_id)){
			$search['goods_type'] = $cate_id;
		}
		
		$param = $this->getInput(array('title', 'supplier', 'status', 'start_time', 'end_time', 'goods_type'));
		if ($param['title'] != '') $search['title'] = array("LIKE", $param['title']);
		if (!empty($param['supplier'])) $search['supplier'] = $param['supplier'];
		if (!empty($param['goods_type'])) $search['goods_type'] = $param['goods_type'];
		if (!empty($param['status'])) $search['status'] = $param['status'];
		if ($param['start_time']) $search['start_time'] = array('>=', strtotime($param['start_time'] . ' 00:00:00'));
		if ($param['end_time']) $search['end_time'] = array('<=', strtotime($param['end_time'] . ' 23:59:59'));
		
		list($total, $list) = Client_Service_Channelgoods::getList($page, $perpage, $search);
		$this->assign('list', $list);
		$url = $this->actions['indexUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		
		$this->assign('param', $param);
		
		$goods_type_list = Client_Service_Channelgoodscate::getAllCategory();
		$goods_type = array();
		if (!empty($goods_type_list)) {
			foreach ($goods_type_list as $val){
				$goods_type[$val['id']] = $val;
			}
		}
		
		$this->assign('goods_type', $goods_type);
		$this->cookieParams();
	}

	public function listAction(){
			$page = intval($this->getInput('page'));
			$page < 1 && $page = 1;
			$perpage = 100;
			$supplier = 10;
			$param = $this->getInput(array('title', 'category_name','supplier'));
			if (!empty($param['title'])) $search['title'] = array('LIKE',$param['title']);
			if (!empty($param['category_name'])) $search['category_name'] = array('LIKE',$param['category_name']);
			if (!empty($param['supplier'])) $search['supplier'] = array('LIKE',$param['supplier']);
			list($total,$data) = Api_Channel_Service::getList($page,$perpage,$search);
			$url = $this->actions['yhdUrl'].'/?' . http_build_query($param) . '&';
			$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
			$this->assign('data',$data);
			$this->assign('param',$param);
	}

	public function goodsAction()
	{
		if (!empty($supplier)){
			if (!array_key_exists($supplier, $this->configs)){
				return false;
			}

			$result = Client_Service_Channelgoods::search($supplier);
			$title = $this->getInput('title');
			$min = $this->getInput('min');
			$max = $this->getInput('max');

			$title = !empty($title) ? $title : '^';
			$min = !empty($min) ? $min : 0;
			$max = !empty($max) ? $max : 10;

			$tmp = array();
			if (!empty($result)){
				foreach ($result as $key=>$val){
					$discount = round($val['sale_price']/$val['market_price'], 2) * 10;
					if ($discount < $max && $discount > $min && stristr($val['title']."^", $title)){
						$tmp[$key] = $val;
					}
				}
			}

			$param = array(
			  'title'=>str_replace("^", "", $title),
			  'min'=>$min,
			  'max'=>$max
			);

			$this->assign('param', $param);
			$this->assign('supplier', $supplier);
			$this->assign('list', $tmp);
			$this->cookieParams();
		}

		$this->assign('supplier', $supplier);
	}

	public function searchAction(){
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = 50;

		$param = $this->getInput(array('title', 'category_name','supplier'));

		$param['supplier']=$param['supplier']?$param['supplier']:1;
		if (!empty($param['title']))          $search['title'] = array('LIKE',$param['title']);
		if (!empty($param['category_name']))  $search['category_name'] = array('LIKE',$param['category_name']);
		if (!empty($param['supplier']))       $search['supplier'] = $param['supplier'];

		list($total,$list) = Client_Service_Source::getList($page,$perpage,$search);
		$goods_type_list   = Client_Service_Channelgoodscate::getAllCategory();
		$goods_type = array();
		if (!empty($goods_type_list)) {
			foreach ($goods_type_list as $val){
				$goods_type[$val['id']] = $val;
			}
		}
		//从Cookie中获取上一次添加商品使用的分类
		$goodsType = Common::encrypt(Util_Cookie::get('tiantiantejia_add_goods_category_id'), 'DECODE');
		$this->assign('goodsType', $goodsType);

		$this->assign('goods_type', $goods_type);
		$this->assign('data', $list);
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
		$this->assign('search', $param);

		$url = $this->actions['searchUrl'].'/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		//module channel

	}
	
	public function addAction(){
		$id = $this->getInput('id');
		$supplier = $this->getInput('supplier');
		$result = Client_Service_Source::get($id);
		$goods_type_list = Client_Service_Channelgoodscate::getAllCategory();
		$goods_type = array();
		if (!empty($goods_type_list)) {
			foreach ($goods_type_list as $val){
				$goods_type[$val['id']] = $val;
			}
		}
		//从Cookie中获取上一次添加商品使用的分类
		$goodsType = Common::encrypt(Util_Cookie::get('tiantiantejia_add_goods_category_id'), 'DECODE');
		$this->assign('goodsType', $goodsType);
		
		$goodsInfo = Client_Service_Channelgoods::getGoodsBySign($supplier . '_' . $result['goods_id']);
		$this->assign('goods_info', $goodsInfo);

		$this->assign('goods_type', $goods_type);
		$this->assign('supplier', $supplier);
		$this->assign('info', $result);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}

	public function add_postAction(){
		$info = $this->getPost(array('title', 'market_price', 'market_price_min', 'sale_price', 'sale_price_min',
				'sort', 'link', 'status', 'img', 'goods_type', 'category_name',
				'start_time', 'end_time', 'supplier', 'goods_id','module_id', 'cid', 'channel_code'));
		
		if(empty($info['end_time'])){
			$this->output(-1, '结束时间不能为空');
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if ($info['start_time'] >= $info['end_time']){
			$this->output(-1, '开始时间不能超过结束时间');
		}
		
		$ret = Client_Service_Channelgoods::addGoods($info);
		Util_Cookie::set('tiantiantejia_add_goods_category_id', Common::encrypt($info['goods_type']), 
			false, Common::getTime() + (5 * 3600));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function editAction(){
		$id = $this->getInput('id');
		$info = Client_Service_Channelgoods::getGoods($id);
		$goods_type_list = Client_Service_Channelgoodscate::getAllCategory();
		$goods_type = array();
		if (!empty($goods_type_list)) {
			foreach ($goods_type_list as $val){
				$goods_type[$val['id']] = $val;
			}
		}
		$this->assign('goods_type', $goods_type);
		list($info['module_id'], $info['cid']) = explode('_', $info['module_channel']);
		$this->assign('info', $info);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	public function edit_postAction(){
		$info = $this->getPost(array('id', 'sort', 'status', 'goods_type',
				'start_time', 'end_time', 'module_id', 'cid', 'channel_code'));
		
		if(empty($info['end_time'])){
			$this->output(-1, '结束时间不能为空');
		}
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if ($info['start_time'] >= $info['end_time']){
			$this->output(-1, '开始时间不能超过结束时间');
		}
		
		$ret = Client_Service_Channelgoods::updateGoods($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	public function deleteAction(){
		$id = $this->getInput('id');
		$info = Client_Service_Channelgoods::getGoods($id);
		if (empty($info) && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Client_Service_Channelgoods::deleteGoods($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	/**
	 *
	 * taobaoke Mallgoods list
	 */
	public function meilishuo_step1Action() {
	    $page = intval($this->getInput('page'));
	    $cid = intval($this->getInput('cid'));
	    $keyword = $this->getInput('keyword');
	    if(!$keyword) $keyword = '';
	
	    if ($page < 1) $page = 1;
	    $perpage = 40;
	    $total = 4000;
	
	    $goods = Api_Meilishuo_Goods::getGoods(array('keyword'=>$keyword, 'page_no'=>$page, 'sort'=>'commissionNum_desc'));

	    $this->assign('goods', $goods['data']);
	
	    $this->assign('cid', $cid);
	    $this->assign('keyword', $keyword);
	
		$url = $this->actions['meilishuoStep1Url'] .'/?'. http_build_query(array('cid'=>$cid, 'keyword'=>$keyword)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}

	
	/**
	* add Mallgoods page show
	*/
	public function meilishuo_step2Action() {
	    $item_id = $this->getInput('item_id');
	    $info = Api_Meilishuo_Goods::getGoods(array('keyword'=>'', 'item_id'=>$item_id));
		$this->assign('info', $info['data'][0]);
		$supplier = 9;
		$goods_type_list = Client_Service_Channelgoodscate::getAllCategory();
		$goods_type = array();
		if (!empty($goods_type_list)) {
		    foreach ($goods_type_list as $val){
		        $goods_type[$val['id']] = $val;
		    }
		}
		//从Cookie中获取上一次添加商品使用的分类
		$goodsType = Common::encrypt(Util_Cookie::get('tiantiantejia_add_goods_category_id'), 'DECODE');
		$this->assign('goodsType', $goodsType);
		
		$this->assign('goods_type', $goods_type);
		$this->assign('supplier', $supplier);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}
	
	/*
	 * 批量操作
	*
	*/
	function batchUpdateAction() {
	    $info = $this->getPost(array('action', 'ids', 'sort'));
	    if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
	     
	    //排序
	    if($info['action'] =='sort'){
	        $data = array();
	        foreach ($info['ids'] as $value) {
	            $data[$value] =  $info['sort'][$value];
	        }
	        $ret = Client_Service_Channelgoods::sort($data);
	    }
	    //开启
	    if ($info['action'] == 'open') {
	        $ret = Client_Service_Channelgoods::updates($info['ids'], array('status'=>1));
	    }
	    //关闭
	    if ($info['action'] == 'close') {
	        $ret = Client_Service_Channelgoods::updates($info['ids'], array('status'=>2));
	    }
	    if (!$ret) $this->output('-1', '操作失败.');
	    $this->output('0', '操作成功.');
	}
	
}