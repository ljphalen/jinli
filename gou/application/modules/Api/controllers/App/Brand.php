<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 品牌聚合
 * @author huangsg
 *
 */
class App_BrandController extends Api_BaseController {
	public $cacheKey = 'Api_Brand';
	public $perpage = 18;
	
	/**
	 * 品牌列表API
	 */
	public function indexAction(){
		$cate_id = $this->getInput('cate_id');
		$search = array();
		if (!empty($cate_id)){
			$brandList = Gou_Service_Brand::getBrandByCateId($cate_id);
			$brandList = Common::resetKey($brandList, 'brand_id');
			$brand_ids = array_keys($brandList);
			if (!empty($brand_ids)){
				$search['id'] = array('IN', $brand_ids);
			}else{
				$search['id'] = 0;
			}
		}
		
		$search['status'] = 1;
		$search['is_top'] = 2;
		$search['start_time'] = array('<=', time());
		$search['end_time'] = array('>=', time());
		
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = intval($this->getInput('perpage'));
		$perpage = !empty($perpage) ? $perpage : $this->perpage;
		
		list($count, $brand_list) = Gou_Service_Brand::getList($page, $perpage, $search);
		$list = array();
		$imgs = array();
		if(!empty($brand_list)){
			foreach ($brand_list as $key=>$val){
				$list[$key] = array(
					'id'=>$val['id'],
					'banner_img'=>Common::getAttachPath(). $val['banner_img'],
					'brand_img'=>Common::getAttachPath() . $val['brand_img'],
					'logo_img'=>Common::getAttachPath() . $val['logo_img'],
				);
		
				$imgs[] = Common::getAttachPath(). $val['banner_img'];
				$imgs[] = Common::getAttachPath(). $val['brand_img'];
				$imgs[] = Common::getAttachPath(). $val['logo_img'];
			}
			$this->cache($imgs, 'brand');
		}
		
		$hasNext = false;
		if($count > $perpage && ceil($count/$perpage) > $page){
			$hasNext = true;
		}
		
		$search['is_top'] = 1;
		list(, $top_info) = Gou_Service_Brand::getList(1, 1, $search);
		$top = !empty($top_info) ? array(
			'id'=>$top_info[0]['id'],
			'banner_img'=>Common::getAttachPath(). $top_info[0]['banner_img'],
			'brand_img'=>Common::getAttachPath(). $top_info[0]['brand_img'],
			'logo_img'=>Common::getAttachPath(). $top_info[0]['logo_img']
		) : null;
		
		$this->output(0, '', array('top'=>$top, 'list'=>$list, 
				'curpage'=>$page,
				'hasnext'=>$hasNext));
	}
	
	/**
	 * 品牌聚合分类API
	 */
	public function categoryAction(){
		$categoryList = Gou_Service_BrandCate::getAllCategory();
		$category = array();
		if (!empty($categoryList)){
			foreach ($categoryList as $val){
				$category[] = array(
					'id'=>$val['id'],
					'title'=>$val['title'],
				);
			}
		}
		
		$this->output(0, '', array('list'=>$category));
	}
	
	/**
	 * 品牌详细页面API
	 */
	public function detailAction(){
		$id = $this->getInput('id');
		if(empty($id)) $this->output(0, '', array());
		$info = Gou_Service_Brand::getBrand($id);
		$data = array(
			'id'=>$info['id'],
			'title'=>$info['title'],
			'banner_img'=>Common::getAttachPath(). $info['banner_img'],
			'brand_img'=>Common::getAttachPath(). $info['brand_img'],
			'logo_img'=>Common::getAttachPath(). $info['logo_img'],
			'brand_desc'=>$info['brand_desc'],
		);
		Gou_Service_Brand::updateHits($id);
		$this->output(0, '', $data);
	}
	
	/**
	 * 品牌商品列表
	 */
	public function goodsAction(){
		$brand_id = $this->getInput('brand_id');
		if(empty($brand_id)) $this->output(0, '', array());
		
		$search = array();
		$search['brand_id'] = $brand_id;
		$search['status'] = 1;
		$search['start_time'] = array('<=', time());
		$search['end_time'] = array('>=', time());
		
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = intval($this->getInput('perpage'));
		$perpage = !empty($perpage) ? $perpage : 16;
		
		list($total, $goods_list) = Gou_Service_BrandGoods::getList($page, $perpage, $search);
		$goods = common::resetKey($goods_list, 'num_iid');
		$num_iids = array_keys($goods);
		$list = array();		
		
		if(!empty($goods_list)){
			$webroot = Common::getWebRoot();
			$topApi  = new Api_Top_Service();
			
			$converts = $topApi->tbkMobileItemsConvert(array('num_iids'=>implode(',', $num_iids)));
			if($converts['click_url']) $converts = array($converts);
			$converts = Common::resetKey($converts, 'num_iid');
			
			foreach ($goods_list as $key=>$val){
				$info= $topApi->getTbkItemInfo(array('num_iids'=>$val['num_iid']));
				if($info) {
					$list[$key]['id'] = $val['id'];
					$list[$key]['num_iid'] = $val['num_iid'];
					$list[$key]['title'] = Util_String::substr(html_entity_decode($val['title']), 0, 22, '', true);
					$list[$key]['pic_url'] = html_entity_decode($info['pic_url'].'_220x220');
					$list[$key]['price'] = $info['price'];
					$list[$key]['url'] = $webroot.'/brand/redirect?id='.$val['id'].'&_url='.$converts[$val['num_iid']]['click_url'];
				} else {
					Gou_Service_BrandGoods::updateBrandgoods(array('status'=>2), $val['id']);
				}
			}
		}

		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$list, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}