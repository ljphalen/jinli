<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 每日上新
 *
 */
class Apk_NewController extends Api_BaseController {
	
	public $perpage = 6;
	public $actions = array(
				'goodsUrl'=>'/api/new/index',
			);
	public $cacheKey = 'Front_New_index';
	
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$webroot = Common::getWebRoot();
		
		list(,$categorys) = Mall_Service_Category::getCanUseMallCategorys(1, 10, array('area_id'=>3));
		$categorys = Common::resetKey($categorys, 'id');
		$cids = array_keys($categorys);
		list($total, $goods) = Mall_Service_Goods::getByCategorys($page, $this->perpage, $cids);
		
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['descrip'] = html_entity_decode($value['title']);
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_460x460.'.end(explode(".",  $value['img']));
			$data[$key]['link'] =  $webroot.'/new/detail?id='.$value['id'].'&t_bi='.$this->t_bi;
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
