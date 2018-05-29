
<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {
	
	
	public function indexAction() {
		//baner ads
		$title = "金立购";
		list(,$ads) = Gc_Service_Ad::getCanUseAds(0, 5, array('ad_type'=>1));
		$this->assign('ads', $ads);
		//subjects list 
		list(, $labels) = Gc_Service_GoodsLabel::getCanUseLabels(0, 4, array('parent_id'=>0));
		$this->assign('labels', $labels);
		//panic buying products
		list(, $buygoods) = Gc_Service_LocalGoods::getCanUseLocalGoods(0, 1,array());
		$this->assign('buygoods', $buygoods[0]);
		$this->assign('title', $title);
	}
}

