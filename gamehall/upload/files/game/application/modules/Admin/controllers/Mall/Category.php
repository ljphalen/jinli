<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Mall_CategoryController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Mall_Category/index',
	    'indexUrl' => '/Admin/Mall_Goods/index',
		'addUrl' => '/Admin/Mall_Category/add',
		'addPostUrl' => '/Admin/Mall_Category/add_post',
		'editUrl' => '/Admin/Mall_Category/edit',
		'editPostUrl' => '/Admin/Mall_Category/edit_post',
		'batchUpdateUrl'=>'/Admin/Mall_Category/batchUpdate',
	    'pointShopIndexUrl' => '/Admin/Mall_Goods/index',
		'pointPrizeIndexUrl' => '/Admin/Point_Prize/index',
	    'presendIndexUrl' => '/Admin/Mall_Goods/presendIndex',
	    'pointDescIndexUrl' => '/Admin/Mall_Goods/pointIndex',
	    'addGoodUrl' => '/Admin/Mall_Goods/addGood',
	);
	
	public $perpage = 20;
	public $type = array(
			Mall_Service_Goods::ACOUPON => 'A券,游戏券',
			Mall_Service_Goods::ENTITY => '实物奖品',
	        Mall_Service_Goods::GIFT => '游戏礼包',
	        Mall_Service_Goods::DISCOUNT_COUPON => '优惠券',
	        Mall_Service_Goods::PHONE_RECHARGE_CARD => '话费,充值卡',
	);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $params = array();
		$params['id'] = array('!=',Mall_Service_Goods::SECKILL_CATEGORY);
		$orderBy = array('sort'=>'DESC','create_time'=>'DESC');
		list($total, $categorys) = Mall_Service_Category::getList($page, $this->perpage,$params ,$orderBy);
		$goodTotal = $this->getGoodTotal($categorys);
		$this->assign('categorys', $categorys);
		$this->assign('total', $total);
		$this->assign('goodTotal', $goodTotal);
		$this->assign('type', $this->type);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	
	private  function getGoodTotal($categorys) {
	    $goodTotal = array();
	    if(!$categorys) return $goodTotal;
	    foreach($categorys as $key=>$value){
	        $params['status'] = Mall_Service_Goods::STATUS_OPEN;
	        $params['start_time'] = array('<=',Common::getTime());
	        $params['end_time'] = array('>=',Common::getTime());
	        $params['category_id'] = $value['id'];
	        $onlineTotal = Mall_Service_Goods::count($params);
	        $params = $this->setGoodParmes($params);
	        $goodsTotal = Mall_Service_Goods::count($params);
	        $goodTotal[$value['id']]['onlineTotal'] = $onlineTotal;
	        $goodTotal[$value['id']]['goodsTotal'] = $goodsTotal;
	    }
	    return $goodTotal;
	}
	
	private  function setGoodParmes($params) {
	    if(!$params){
	        return array();
	    }
        unset($params['status']);
        unset($params['start_time']);
        unset($params['end_time']);
        
        return $params;
	}
	
	/**
	 * 
	 * edit an subject
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Mall_Service_Category::get(intval($id));
		$this->assign('info', $info);
		$this->assign('type', $this->type);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	    $this->assign('type', $this->type);
	}

	//批量上线，下线，排序
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='sort'){
			$ret = Mall_Service_Category::updateSort($info['sort']);
		}
	
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort','goods_type','title','status', 'create_time'));
		$info = $this->_cookData($info);
		$info['create_time'] = Common::getTime();
		$ret = Mall_Service_Category::add($info);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'sort','goods_type','title','status'));
		$info = $this->_cookData($info);
		$categorys = $this->checkIsNotCategory($info);
		if(!$categorys && !$info['status']) $this->output(-1, '商品分类至少必须存在一个.'); 
		$ret = Mall_Service_Category::update($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}
	
	private function checkIsNotCategory($info) {
	    $params['status'] = Mall_Service_Category::STATUS_OPEN;
	    $params['id'][] = array('!=',Mall_Service_Goods::SECKILL_CATEGORY);
	    $params['id'][] = array('!=',$info['id']);
	    return Mall_Service_Category::getsBy($params);
	}

	/**
	 * 
	 * Enter description here ...
	 * preg_match("/<[^>]*>/", $info['title'])
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '商品分类名称不能为空.'); 
		$title = html_entity_decode($info['title']);
		if(preg_match("/<[^>]*>/", $title)) $this->output(-1, '活动名称不能包含特殊标记(< >).');
		return $info;
	}

}
