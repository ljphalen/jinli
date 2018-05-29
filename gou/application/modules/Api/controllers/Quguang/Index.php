<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 客户端2.0接口
 *
 */
class Quguang_IndexController extends Api_BaseController {
	
	public $perpage = 6;
	public $actions = array(
				'recommendUrl'=>'/api/client_index/recommend',
			);
	
	/**
	 * 推荐 
	 */
	public function recommendAction() {
		$perpage = intval($this->getInput('perpage'));
		$cache_time = intval($this->getInput('cache_time'));
		if(!$cache_time) $cache_time = strtotime(date('Y-m-d', Common::getTime()).'00:00:00');
		
		if(!$perpage) $perpage = $this->perpage;
	
		$webroot = Common::getWebRoot();
	
		list($total, $goods) = Client_Service_Goods::getNomalGoods(1, $this->perpage, array('start_time'=>$cache_time));
	
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['id'] = $value['id'];
			$data[$key]['title'] = $value['title'];
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_460x460.'.end(explode(".",  $value['img']));
			$data[$key]['start_time'] = $value['start_time'];
			$data[$key]['end_time'] = $value['end_time'];
		}
		$this->output(0, '', array('hasdata'=>$data ? true : false,'list'=>$data, 'pre_cache_time'=>$cache_time ? $cache_time - 86400 : strtotime(date('Y-m-d', Common::getTime()).'00:00:00') - 86400, 'cache_time'=>$cache_time));
	}
	
	/**
	 * 手机配件分类 
	 */
	public function fittingtypeAction() {
		list(, $types) = Client_Service_Category::getCanUseCategorys(0, 10, array('area_id'=>1));
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($types as $key=>$value) {
			$data[$key]['id'] = $value['id'];
			$data[$key]['title'] = $value['title'];
		}
		$this->output(0, '', $data);
	}
	
	/**
	 * 手机配件
	 */
	public function fittingAction() {
		$perpage = intval($this->getInput('perpage'));
		$cache_time = intval($this->getInput('cache_time'));
		if(!$cache_time) $cache_time = strtotime(date('Y-m-d', Common::getTime()).'00:00:00');
		if(!$perpage) $perpage = $this->perpage;
	
		$webroot = Common::getWebRoot();
	
		list(,$categorys) = Client_Service_Category::getCanUseCategorys(1, 20, array('area_id'=>1));
		$categorys = Common::resetKey($categorys, 'id');
		$cids = array_keys($categorys);
		list($total, $goods) = Client_Service_Goods::getByCategorys(1, $perpage, $cids, $cache_time);
	
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['id'] = $value['id'];
			$data[$key]['title'] = $value['title'];
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_460x460.'.end(explode(".",  $value['img']));
			$data[$key]['start_time'] = $value['start_time'];
			$data[$key]['end_time'] = $value['end_time'];
		}
		$this->output(0, '', array('hasdata'=>$data ? true : false,'list'=>$data, 'pre_cache_time'=>$cache_time ? $cache_time - 86400 : strtotime(date('Y-m-d', Common::getTime()).'00:00:00') - 86400,  'cache_time'=>$cache_time));
	}
	
	
	/**
	 * 女装
	 */
	public function womenclothingAction() {
		$perpage = intval($this->getInput('perpage'));
		$cache_time = intval($this->getInput('cache_time'));
		if(!$cache_time) $cache_time = strtotime(date('Y-m-d', Common::getTime()).'00:00:00');
		if(!$perpage) $perpage = $this->perpage;
	
		$webroot = Common::getWebRoot();
	
		list(,$categorys) = Client_Service_Category::getCanUseCategorys(1, 20, array('area_id'=>2));
		$categorys = Common::resetKey($categorys, 'id');
		$cids = array_keys($categorys);
		list($total, $goods) = Client_Service_Goods::getByCategorys(1, $perpage, $cids, $cache_time);
	
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['id'] = $value['id'];
			$data[$key]['title'] = $value['title'];
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_460x460.'.end(explode(".",  $value['img']));
			$data[$key]['start_time'] = $value['start_time'];
			$data[$key]['end_time'] = $value['end_time'];
		}
		$this->output(0, '', array('hasdata'=>$data ? true : false, 'list'=>$data, 'pre_cache_time'=>$cache_time ? $cache_time - 86400 : strtotime(date('Y-m-d', Common::getTime()).'00:00:00') - 86400,  'cache_time'=>$cache_time));
	}	
	
	/**
	 * goods detail page
	 */
	public function goodsdetailAction() {
		$id = intval($this->getInput('id'));
		$info = Client_Service_Goods::getGoods($id);
	
		//taoke goods info
		$topApi  = new Api_Top_Service();
		$taoke_info  = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$info['num_iid'], 'is_mobile'=>'true', 'outer_code'=>$this->getOuterCode()));
		if(!$taoke_info) Client_Service_Goods::updateGoods(array('status'=>0), $info['id']);
		
		if(!$taoke_info) Client_Service_Goods::updateGoods(array('status'=>0), $info['id']);
		//taobao goods info
		$taobao_info = $topApi->getItemInfo($info['num_iid']);
		
		$info['item_imgs'] = $taobao_info['item_imgs']['item_img'];
		if($taoke_info && ($taoke_info['promotion_price'] != $info['price'])) {
			Client_Service_Goods::updateGoods(array('price'=>$taoke_info['promotion_price']), $info['id']);
		}
		
		//taobao goods info
		//$taoke_info['click_url'] = $this->getTaobaokeUrl($taoke_info['click_url']);
	
		$info['item_imgs'] = $taobao_info['item_imgs']['item_img'];
		
		$data = array(
				'id'=>$info['id'],
				'title'=>$info['title'],
				'item_imgs'=>$info['item_imgs'],
				'descrip'=>html_entity_decode($info['descrip']),
				'price'=>$taoke_info['promotion_price'],
				'taobao_url'=>$taoke_info['click_url']
		);
	
		$this->output(0, '', $data);
	}
	
	/**
	 * 收藏
	 */
	public function favoriteAction() {
		$id = intval($this->getInput('id'));
		$imei = $this->getInput('imei');
		
		$goods = Client_Service_Goods::getGoods($id);
		
		if(!$goods) $this->output(-1, 'GOODS_NOT_EXIST.');
		
		$info = Mall_Service_Goods::getMallGoods($id);
	
		$this->output(0, 'success');
	}
	
	/**
	 * 关键字
	 */
	public function keywordsAction() {
		list(,$list) =  Client_Service_Keywords::getList(1, 10, array('status'=>1));
		$data = array();
		foreach ($list as $key=>$value) {
			$data[] = $value['keyword'];
		}
		
		$keyword = Gou_Service_Config::getValue('gou_client_keyword');
		
		$this->output(0, '',  array('keywords'=>$data, 'keyword'=>$keyword, 'taobao_search_url'=>'http://r.m.taobao.com/s?p=mm_32564804_6072328_23038504&q='));
	}
	
	/**
	 * 搜索
	 */
	public function searchAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$keyword = trim($this->getPost('keyword'));
		if($keyword) {
			$info = array(
				'keyword'=>$keyword,
				'keyword_md5'=>md5($keyword),
				'create_time'=>Common::getTime(),
				'dateline'=>date('Y-m-d', Common::getTime())
			);
			Client_Service_KeywordsLog::addKeywordsLog($info);
		}
		
		
		//根据关键字搜索商品列表
		$topApi  = new Api_Top_Service();
		$rs = $topApi->taobaoTbkItemsGet(array(
				'page_no'=>$page,
				'page_size'=>$this->perpage,
				'keyword'=>$keyword,
				'is_mobile'=>"true",
				'sort'=>"commissionNum_desc"
		));
		
		$goods = $rs['tbk_items']['tbk_item'];
		$total = $rs['total_results'];
		
		$webroot = Common::getWebRoot();
		foreach ($goods as $key=>$value){
			$infos = $topApi->tbkMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$data[$key]['title'] = Util_String::substr(preg_replace('/<\/?span([^>]+)?>|\s+/',"",$value['title']), 0, 24, '',true);
			$data[$key]['img'] = strpos($value['pic_url'], 'http://') === false ? $webroot. '/attachs' .$value['img'] : $value['img'].'_310x310.'.end(explode(".",  $value['img']));
			$data[$key]['link'] = $infos['click_url'];
			$data[$key]['price'] = $value['price'];
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
