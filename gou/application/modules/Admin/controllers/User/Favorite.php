<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class User_FavoriteController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' =>'/admin/User_Favorite/index',
	);
	
	public $perpage = 14;



    public $types = array(
	    1=>'知物',
	    2=>'商品',
	    3=>'店铺',
	    4=>'网页',
	);
	
	/**
	 * 
	 * Get order list
	 */
	public function indexAction() {

		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('item_id', 'uid'));
		if ($page < 1) $page = 1;
		
		$search = array();
		if ($params['item_id']) $search['item_id'] = $params['item_id'];
		if ($params['uid']) $search['uid'] = $params['uid'];

		
		list($total, $list) = User_Service_Favorite::getList($page, $this->perpage, $search);
		
		$favorite_list = Common::resetKey($list, 'item_id');
		$item_ids = array_keys($favorite_list);
		
		$storys = array();
		if($item_ids) {
		    list(,$storys) = Gou_Service_Story::getsBy(array('id'=>array('IN', $item_ids)), array('id'=>'DESC'));
		    $storys = Common::resetKey($storys, 'id');
		}
		
		$this->assign('list', $list);
		$this->assign('storys', $storys);
		$this->assign('total', $total);
		$this->assign('params', $params);

		//get pager
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('types', $this->types);
		$this->assign('total', $total);
		$this->cookieParams();
	}
}
