<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 手机配件接口
 *
 */
class App_FittingController extends Api_BaseController {
	
	public $perpage = 6;
	public $actions = array(
				'goodsUrl'=>'/api/fitting/goods',
			);
	public $cacheKey = 'Front_Fitting_index';
	
	
	public function typeAction() {
		list($count, $types) = Mall_Service_Category::getCanUseMallCategorys(0, 3, array('area_id'=>1));
		$webroot = Common::getWebRoot();
		$cid = Util_Cookie::get('FCID', true);
		
		$types_data = array();
		for($i = 0; $i < count($types); $i++) {
			$types_data[$i+1]['id'] = $types[$i]['id'];
			$types_data[$i+1]['title'] = $types[$i]['title'];
		}
		$types_data[0] = array('id'=>0, 'title'=>'最新');
		ksort($types_data);
		$data = array();
		foreach ($types_data as $key=>$value) {
			$data[$key]['id'] = $value['id'];
			$data[$key]['title'] = $value['title'];
			$data[$key]['ajax_url'] = $webroot.$this->actions['goodsUrl'].'?cid='.$value['id'].'&chl_id=3'. ($key + 1) .'&idx_id='.($key+1);
			if($cid == $value['id']) $data[$key]['selected'] = true;
		}
		$this->output(0, '', $data);
	
		
	}
	
	public function goodsAction() {
		$cid = intval($this->getInput('cid'));
		
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$webroot = Common::getWebRoot();
		
		$search = array();
		if($cid) {
			$search['category_id'] = $cid;
			list($total, $goods) = Mall_Service_Goods::getNormalMallGoods($page, $this->perpage, $search);
		} else {
			list(,$categorys) = Mall_Service_Category::getCanUseMallCategorys(1, 3, array('area_id'=>1));
			$categorys = Common::resetKey($categorys, 'id');
			$cids = array_keys($categorys);
			list($total, $goods) = Mall_Service_Goods::getByCategorys($page, $this->perpage, $cids);
		}
		
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['price'] = $value['price'];
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_460x460.'.end(explode(".",  $value['img']));
			$data[$key]['link'] =  $webroot.'/mall/detail?id='.$value['id'].'&cid='.$cid.'&t_bi='.$this->t_bi;
			$data[$key]['descrip'] = html_entity_decode($value['title']);
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
