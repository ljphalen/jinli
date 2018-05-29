<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ShopController extends Channel_BaseController {
	
	public $actions =array(
				'index' => '/shop/index',
			);
	
	public $perpage = 10;

	
	/**
	 * list
	 */
	public function indexAction() {
		$url = html_entity_decode(Gou_Service_Config::getValue('taobao_shop_url'));
		if(!$url) $url = 'http://m.taobao.com/channel/chn/wap/testmunion201301.html?ttid=momo_mZPluSxPzew4uyoFK-UGdg&refpid=mm_31749056_3065731_10271361';
		$this->redirect($url);
	}
	
	/**
	 * shop list
	 */
	public function listAction() {
	    $uid = $this->getInput('uid');
	    $this->assign('uid',$uid);
	    $this->assign('title', '淘宝好店');
	}
	
	/**
	 * goods detail page
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		
		if($id) {
			$shop = Client_Service_Shops::getBy(array('id'=>$id));
			$nick  = $shop['nick'];
		} else {
			$nick = urldecode($this->getInput('nick'));
		}
		
		
		$topApi  = new Api_Top_Service();
		//shop_info
		$shop_info = $topApi->getTbkShopInfo(array('seller_nicks'=>$nick, 'is_mobile'=>"true"));
		
		//cats
		$shop_cats = $topApi->getShopCatsList();
		$shop_cats = Common::resetKey($shop_cats, 'cid');
		$shop_info['cat_name'] = $shop_cats[$shop_info['cid']]['name'];

		//shop converts		
		$shop_convert = $topApi->TaobaokeMobileShopsConvert(array('seller_nicks'=>$nick, 'is_mobile'=>"true"));
		//items
		//$items = $topApi->taobaokeItemsRelate(array('relate_type'=>4, 'seller_id'=>$shop_convert['user_id'], 'sort'=>'commissionNum_desc', 'is_mobile'=>"true"));
		
		$this->assign('shop', $shop);
		$this->assign('shop_info', $shop_info);
		$this->assign('shop_convert', $shop_convert);
		//$this->assign('items', $items);
		$this->assign('title', '店铺详情');
	}
}
