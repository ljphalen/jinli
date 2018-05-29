<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class ElifeController extends Api_BaseController {
	
	public $perpage = 10;
	
	public function goodsAction() {
		$cid = intval($this->getInput('cid'));
		
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;		 
		if ($page < 1) $page = 1;
		
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		
		$search = array();
		if($cid) {
			$search['category_id'] = $cid;
			list($total, $goods) = Mall_Service_Goods::getNormalMallGoods($page, $this->perpage, $search);
		} else {
			list(,$categorys) = Mall_Service_Category::getCanUseMallCategorys(1, 40, array('area_id'=>2));
			$categorys = Common::resetKey($categorys, 'id');
			$cids = array_keys($categorys);
			list($total, $goods) = Mall_Service_Goods::getByCategorys($page, $perpage, $cids);
		}
		
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$data[$key]['price'] = $value['price'];
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? Common::getAttachPath()  .$value['img'] : $value['img'].'_160x160.'.end(explode(".",  $value['img']));
			$data[$key]['link'] =  $webroot.'/elife/detail?from=elife&id='.$value['id'].'&cid='.$cid;
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
