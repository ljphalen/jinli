<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 想要
 * @author tiansh
 *
 */
class WantController extends User_BaseController{
	
	public $actions = array(
		'listUrl' => '/user/account/index',
		'morelUrl' => '/user/want/more',
		'buylUrl' => '/cart/buy',
		'shopUrl'=>'/shop/detail'
	);

	public $perpage = 6;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$this->assign('user_info',$this->userInfo);
		$page = intval($this->getInput('page'));
		
		$topApi  = new Api_Top_Service();
		if ($page < 1) $page = 1;
		list($total, $wants) = Gou_Service_WantLog::getNomalWants(1, $this->perpage, array('uid'=>$this->userInfo['id']));
		if ($wants) {
			$goodsids = array();
			$mallgoodsids = array();
			foreach ($wants as $key=>$value) {
				if($value['goods_type'] == 1) {
					$mallgoodsids[] = $value['goods_id'];
				} else {
					$goodsids[] = $value['goods_id'];
				}
				
				$covert = $topApi->tbkMobileItemsConvert(array('num_iids'=>$value['num_iid']));
				$wants[$key]['click_url'] = $covert['click_url'];
			}
			$goods = Gou_Service_Goods::getGoodsByIds($goodsids);
			$goods = Common::resetKey($goods, 'id');
			
			$mallgoods = Mall_Service_Goods::getMallGoodsByIds($mallgoodsids);
			$mallgoods = Common::resetKey($mallgoods, 'id');
			
			$goods_merge = array_merge($goods, $mallgoods);			
			$goods = array(0=>$goods, 1=>$mallgoods);
			
			/*$ids = array();
			foreach ($goods_merge as $key=>$value) {
				$ids[] = $value['num_iid'];
			}
			
			//
			$topApi  = new Api_Top_Service();
			$taokes  = $topApi->taobaokeMobileItemsConverts(array('num_iids'=>implode(',', $ids), 'outer_code'=>$this->getOuterCode(), 'is_mobile'=>'true'));
			$taokes = $taokes['taobaoke_items']['taobaoke_item'];
			
			$taokes = Common::resetKey($taokes, 'num_iid');
			foreach($taokes as $key=>$value) {
				$taokes[$key]['click_url'] = $this->getTaobaokeUrl($value['click_url']);
			} */
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('wants', $wants);
		$this->assign('goods', $goods);
		//$this->assign('taokes', $taokes);
		
		$webroot = Common::getWebRoot();
			
		$nav = array(
				'1'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/account/index"'),
				'2'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/account/order_list"'),
				'3'=> array('selected'=>'selected', 'href'=>''),
				'4'=> array('selected'=>'', 'href'=>'href="'.$webroot.'/user/address/index"'),
		);
		$this->assign('nav', $nav);
		$this->assign('title', '心愿清单');
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		list($total, $wants) = Gou_Service_WantLog::getNomalWants($page, $this->perpage, array('uid'=>$this->userInfo['id']));
		
		$goodsids = array();
		$mallgoodsids = array();
		foreach ($wants as $key=>$value) {
			if($value['goods_type'] == 1) {
				$mallgoodsids[] = $value['goods_id'];
			} else {
				$goodsids[] = $value['goods_id'];
			}
		}
		$goods = Gou_Service_Goods::getGoodsByIds($goodsids);
		$goods = Common::resetKey($goods, 'id');
		
		$mallgoods = Mall_Service_Goods::getMallGoodsByIds($mallgoodsids);
		$mallgoods = Common::resetKey($mallgoods, 'id');
		
		$goods_merge = array_merge($goods, $mallgoods);
		
		$goods = array(0=>$goods, 1=>$mallgoods);
		
		/*$ids = array();
		foreach ($goods_merge as $key=>$value) {
			$ids[] = $value['num_iid'];
		}
		//
		$topApi  = new Api_Top_Service();
		$taokes  = $topApi->taobaokeMobileItemsConverts(array('num_iids'=>implode(',', $ids), 'outer_code'=>$this->getOuterCode()));
		$taokes = $taokes['taobaoke_items']['taobaoke_item'];
		$taokes = Common::resetKey($taokes, 'num_iid');
		*/
		$webroot = Common::getWebRoot();
		$attachsPath = Common::getAttachPath();
		$topApi  = new Api_Top_Service();
		
		$temp = array();
		foreach($wants as $key=>$value) {
			$img = $goods[$value['goods_type']][$value['goods_id']]['img'];
			$covert = $topApi->tbkMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $goods[$value['goods_type']][$value['goods_id']]['title'];
			$temp[$key]['price'] = $goods[$value['goods_type']][$value['goods_id']]['price'];
			$temp[$key]['want'] = $goods[$value['goods_type']][$value['goods_id']]['want'] + $goods[$value['goods_id']]['default_want'];
			$temp[$key]['link'] = urldecode($webroot.'/user/want/detail/?id=' . $value['id'].'&t_bi='.$this->t_bi);
			$temp[$key]['buy'] = $covert['click_url'] ? $covert['click_url'] : urldecode($webroot.'/user/want/detail/?id=' . $value['id'].'&t_bi='.$this->t_bi);
			$temp[$key]['img'] = (strpos($img, 'http://') === false) ? $attachsPath. $img : $img . '_200x200.' . end(explode(".", $img));
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	/**
	 * goods detail page
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$want = Gou_Service_WantLog::getWantLog($id);
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		
		if($want['goods_type'] == 1) {
			$info = Mall_Service_Goods::getMallGoods($want['goods_id']);
		} else {
			$info = Gou_Service_Goods::getGoods($want['goods_id']);
		}
	
		//taoke goods info
		$topApi  = new Api_Top_Service();
		//taoke goods info
		$taoke_info = $topApi->getTbkItemInfo(array('num_iids'=>$info['num_iid']));
		
		//convert
		if($taoke_info) {
			$convert = $topApi->tbkMobileItemsConvert(array('num_iids'=>$info['num_iid']));
			$taoke_info['click_url'] = $convert['click_url'];
		}
		
	
		$this->assign('taoke_info', $taoke_info);
		$this->assign('info', $info);
		$this->assign('title', '商品详情');
	}
}
