<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {
	
	public $actions =array(
				'apiUrl' => '/api/H5/index',
			);

	public function indexAction() {
		/*$webroot = Common::getWebRoot();
		$kid = $this->getInput("kid");
		$type = Common::getConfig('typeConfig', 'goods_type');
		
		$params = array();
		$time = Common::getTime();
		if(in_array($kid, array_keys($type))) {
			if($kid == 1) {
				$params = array('status'=>1, 'start_time'=>array('<', $time), 'end_time'=>array('>', $time));
			} else {
				$params = array('status'=>1, 'start_time'=>array('<', $time), 'end_time'=>array('>', $time), 'category_id'=>$kid);
			}
			
		} else {
			$kid = 1;
			$params = array('status'=>1, 'start_time'=>array('<=', $time), 'end_time'=>array('>=', $time));
		}
		
		$webroot = Common::getWebRoot();
		
		list(, $goods) = Gou_Service_Goods::getList(1, 8, $params);
		
		$num_iids = '';
		foreach ($goods as $key=>$value) {
			$num_iids .= strlen($num_iids) ? ','.$value['num_iid'] : $value['num_iid'];
		}
		
		$topApi  = new Api_Top_Service();
		$taobaoke_items = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$num_iids));
		
		$items = Common::resetKey($taobaoke_items, 'num_iid');
		
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['title'] = $value['title'];
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? $webroot. '/attachs' .$value['img'] : $value['img'].'_180x180.'.end(explode(".",  $value['img']));
			$data[$key]['link'] = $items[$value['num_iid']]['click_url'];
			$data[$key]['price'] = $items[$value['num_iid']]['promotion_price'];
		}
		$this->assign('kid', $kid);
		$this->assign('data', $data);*/
		
		$webroot = Common::getWebRoot();
		//$keyword = $this->getInput("keyword");
		
		$kid = $this->getInput("kid");
		$type = Common::getConfig('typeConfig','goods_type');
		$keyword = $type[$kid];
		if(!$keyword) $keyword = '情趣用品';
		
		$topApi  = new Api_Top_Service();
		$ret = $topApi->taobaoTbkItemsGet(array('page_no'=>1, 'page_size'=>8, 'keyword'=>$keyword, 'is_mobile'=>"true", 'sort'=>"commissionNum_desc"));
		$goods = $ret['tbk_items']['tbk_item'];
		$total = $ret['total_results'];
		
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$infos = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$data[$key]['title'] = preg_replace('/<\/?span([^>]+)?>|\s+/',"",$value['title']);
			$data[$key]['img'] = $value['pic_url'].'_180x180.jpg';
			$data[$key]['link'] = $infos['click_url'];
			$data[$key]['price'] = $value['price'];
		}
		$this->assign('kid', $kid);
		$this->assign('data', $data);
	}
}
