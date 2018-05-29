<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 想要
 * @author lichanghua
 *
 */
class WantController extends Front_BaseController{
	
	public $actions = array(
		'listUrl' => '/user/want/index',
		'morelUrl' => '/user/want/more',
		'buylUrl' => '/cart/buy',
	);

	public $perpage = 6;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$title = "我的心愿清单";
		if ($this->userInfo) {
			$page = intval($this->getInput('page'));
			if ($page < 1) $page = 1;
			list($total, $wants) = Gc_Service_WantLog::getList($page, $this->perpage, array('uid'=>$this->userInfo['id']));
			if ($total) {
				$goodsids = array();
				foreach ($wants as $key=>$value) {
					$goodsids[] = $value['goods_id'];
				}
				$goods = Gc_Service_TaokeGoods::getGoodsByIds($goodsids);
				$goods = Common::resetKey($goods, 'id');
				
				$ids = array();
				foreach ($goods as $key=>$value) {
					$ids[] = $value['num_iid'];
				}
				//
				$topApi  = new Api_Top_Service();
				$taokes  = $topApi->getTaobaokes(array('num_iids'=>implode(',', $ids), 'outer_code'=>$this->getOuterCode(), 'is_mobile'=>'true'));
				$taokes = $taokes['taobaoke_items']['taobaoke_item'];
				$taokes = Common::resetKey($taokes, 'num_iid');
				foreach($taokes as $key=>$value) {
					$taokes[$key]['click_url'] = $this->getTaobaokeUrl($value['click_url']);
				}
			}
		}
		
		$this->assign('wants', $wants);
		$this->assign('goods', $goods);
		$this->assign('taokes', $taokes);
		$this->assign('total', $total);
		$this->assign('title', $title);
//print_r($this->userInfo);
		$this->assign('user', $this->userInfo);
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		list($total, $wants) = Gc_Service_WantLog::getList($page, $this->perpage, array('uid'=>$this->userInfo['id']));
		foreach ($wants as $key=>$value) {
			$goodsids[] = $value['goods_id'];
		}
		$goods = Gc_Service_TaokeGoods::getGoodsByIds(array_unique($goodsids));
		$goods = Common::resetKey($goods, 'id');
		$ids = array();
		foreach ($goods as $key=>$value) {
			$ids[] = $value['num_iid'];
		}
		//
		$topApi  = new Api_Top_Service();
		$taokes  = $topApi->getTaobaokes(array('num_iids'=>implode(',', $ids), 'outer_code'=>$this->getOuterCode()));

		$taokes = $taokes['taobaoke_items']['taobaoke_item'];
		$taokes = Common::resetKey($taokes, 'num_iid');
		
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		
		$temp = array();
		foreach($wants as $key=>$value) {
			$img = $goods[$value['goods_id']]['img'];
			$click_url = $taokes[$goods[$value['goods_id']]['num_iid']]['click_url'];
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $goods[$value['goods_id']]['title'];
			$temp[$key]['price'] = $goods[$value['goods_id']]['price'];
			$temp[$key]['want'] = $goods[$value['goods_id']]['want'] + $goods[$value['goods_id']]['default_want'];
			$temp[$key]['link'] = urldecode($webroot.'/subject/detail/?id=' . $value['goods_id']);
			$temp[$key]['href'] = $this->getTaobaokeUrl($click_url);
			$temp[$key]['img'] = (strpos($img, 'http://') === false) ? $webroot.'/attachs/'. $img : $img . '_200x200.' . end(explode(".", $img));
		}
		$totalpage = ceil((int) $total / $this->perpage);
		$hasnext = ($totalpage - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'total'=>$totalpage));
	}
}
