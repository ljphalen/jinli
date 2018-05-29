<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 金立产品
 * @author rainkid
 */
class ProductController extends Front_BaseController {

	public $actions = array(
		'indexUrl'  => '/product/index',
		'detailUrl' => '/product/detail'
	);

	public function indexAction() {
		//统计首页点击量
		$t_bi = $this->getSource();
		Gionee_Service_Log::pvLog('3g_index');
		Gionee_Service_Log::uvLog('3g_index', $t_bi);

		//轮播广告
		$ads = Gionee_Service_Ad::getCanUseAds(1, 4, array('ad_type' => 1));
		$this->assign('ads', $ads);

		//系列
		list(, $series) = Gionee_Service_Series::getAllSeries();
		$this->assign('series', $series);
		$this->assign('pageTitle', '产品服务页');
		$this->assign('nav', 'index');
	}

	public function listAction() {
		$series_id = $this->getInput('sid');
		$series    = Gionee_Service_Series::getSeries($series_id);
		$webroot   = Common::getCurHost();
		if (!$series_id || !$series || $series['id'] != $series_id) Common::redirect($webroot);

		//产品
		$product = Gionee_Service_Product::getListBySeries($series_id);

		$this->assign('series', $series);
		$this->assign('product', $product);
		$this->assign('pageTitle', '产品世界');
	}

	public function detailAction() {
		$series_id = $this->getInput('sid');
		$id        = $this->getInput('id');
		list(, $series) = Gionee_Service_Series::getAllSeries();
		$series = Common::resetKey($series, 'id');
		unset($series[$series_id]);
		//产品
		$product = Gionee_Service_Product::getProduct($id);

		$webroot = Common::getCurHost();
		if (!$id || $product['id'] != $id) Common::redirect($webroot);


		//查产品属性
		$attribute_ids = explode(',', $product['attribute_id']);
		$attribute     = Gionee_Service_ProductAttribute::getAttributeByIds($attribute_ids);

		//取图片
		list(, $images) = Gionee_Service_ProductImg::getList(0, 10, array('pid' => intval($product['id'])), array('id' => 'ASC'));
		$this->assign('series', $series);
		$this->assign('series_id', $series_id);
		$this->assign('product', $product);
		$this->assign('attribute', $attribute);
		$this->assign('images', $images);
		$this->assign('pageTitle', '产品世界');
		$this->assign('nav', '1-1');
	}

	public function imgAction() {
		$series_id = $this->getInput('sid');
		$id        = $this->getInput('id');
		$img       = Gionee_Service_ProductImg::getImagesByPid($id);
		$this->assign('img', $img);
		$this->assign('series_id', $series_id);
		$this->assign('id', $id);
		$this->assign('pageTitle', '产品世界');
	}

	public function guigeAction() {
		$series_id = $this->getInput('sid');
		$id        = $this->getInput('id');

		$product = Gionee_Service_Product::getProduct($id);

		$this->assign('product', $product);
		$this->assign('id', $id);
		$this->assign('series_id', $series_id);
	}
}
