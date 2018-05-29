<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Ami天气购物API
 * @author huangsg
 *
 */
class AmiweatherController extends Api_BaseController {
	public $perpage = 12;
	public $actions = array(
	);
	
	/**
	 * index action
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		// weather_sign = 1_1-2_1-3_2...
		$params = $this->getInput('params');
		if(!$params) $params ='1_1';
		
		$id = explode('_', $params);
		
		$info = Amigo_Service_Weather::getBy(array('root_id'=>$id[1],'parent_id'=>$id[0]));
        
		if(!$info) $this->output(0, '', array());
		//根据关键字搜索商品列表
		$topApi  = new Api_Top_Service();
		$rs = $topApi->taobaoTbkItemsGet(array(
				'page_no'=>$page,
				'page_size'=>$this->perpage,
				'keyword'=>$info['keywords'],
				'is_mobile'=>"true",
				'sort'=>"commissionNum_desc"
		));
		$goods = $rs['tbk_items']['tbk_item'];
		$num_iids = array_keys(Common::resetKey($goods, 'num_iid'));
		$num_iids = implode(',', $num_iids);
		$total = $rs['total_results'];
		
		$convers = $topApi->tbkMobileItemsConvert(array('num_iids'=>$num_iids));
		$convers = Common::resetKey($convers, 'num_iid');
		
		$webroot = Common::getWebRoot();

		foreach ($goods as $key=>$value){
			$data[$key]['title'] = Util_String::substr(preg_replace('/<\/?span([^>]+)?>|\s+/',"",$value['title']), 0, 24, '',true);
			$data[$key]['img'] = $value['pic_url'].'_310x310.jpg';
			$data[$key]['link'] = $convers[$value['num_iid']]['click_url'];
			$data[$key]['price'] = $value['price'];
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}