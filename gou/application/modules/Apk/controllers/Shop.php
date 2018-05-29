<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ShopController extends Apk_BaseController {
	
	public $actions =array(
				'index' => '/shop/index',
			);
	
	public $perpage = 10;

	public function indexAction() {
		$this->forward("apk", "tejia","index");
		return false;
		/*$code = Gou_Service_Config::getValue('taobao_shop_url_code');
		if(!$code) $code = 'utf-8';
		$webroot = Common::getWebRoot();
		//header("Content-type: text/html;  charset=$code");
		//$url = html_entity_decode(Gou_Service_Config::getValue('taobao_shop_url'));
		//if(!$url) $url = 'http://m.taobao.com/channel/chn/wap/testmunion201301.html?ttid=momo_mZPluSxPzew4uyoFK-UGdg&refpid=mm_31749056_3065731_10271361';
		echo file_get_contents($webroot.'/tejia');
		exit;*/
	}
	
	/**
	 * shop list
	 */
	public function listAction() {
        $uid  = "";
        $ua = Util_Http::getServer('HTTP_USER_AGENT');
        preg_match('/uid\/([a-z0-9]*)/',$ua,$match);
        if(!empty($match[1]))$uid= $match[1];
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
		$shop_info = $topApi->getShopInfo($nick);
		
		//cats
		$shop_cats = $topApi->getShopCatsList();
		$shop_cats = Common::resetKey($shop_cats, 'cid');
		$shop_info['cat_name'] = $shop_cats[$shop_info['cid']]['name'];

		//shop converts		
		$shop_convert = $topApi->TaobaokeShopsConvert(array('seller_nicks'=>$nick, 'is_mobile'=>"true"));
		//items
		//$items = $topApi->taobaokeItemsRelate(array('relate_type'=>4, 'seller_id'=>$shop_convert['user_id'], 'sort'=>'commissionNum_desc', 'is_mobile'=>"true"));
		
		$this->assign('shop', $shop);
		$this->assign('shop_info', $shop_info);
		$this->assign('shop_convert', $shop_convert);
		//$this->assign('items', $items);
		$this->assign('title', '店铺详情');
	}
}
