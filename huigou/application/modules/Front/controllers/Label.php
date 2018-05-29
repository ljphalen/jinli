<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LabelController extends Front_BaseController {
	public $perpage = 15;
	
	public function indexAction() {
		$id = intval($this->getInput('id'));
		$pid = intval($this->getInput('pid'));
		$labels = Gc_Service_GoodsLabel::getListByParentId($pid);
		$labelinfo = Gc_Service_GoodsLabel::getGoodsLabel($pid);
		if (!$id) $id = $labels[0][id];
		
		$this->assign('labels', $labels);
		$this->assign('id', $id);
		$this->assign('pid', $pid);
		$this->assign('title', $labelinfo['name']);
		
		$page = intval($this->getInput('page'));
		list($total, $goods) = Gc_Service_TaokeGoods::getNomalGoods($page, $this->perpage, array('label_id'=>$id));
		
		$this->assign('goods', $goods);
		$this->assign('total', $total);
	}
	
	/**
	 * get more goods list
	 */
	public function moreAction() {
		$id = intval($this->getInput('id'));
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$tmp = array();
		
		$pid = intval($this->getInput('pid'));
		$labels = Gc_Service_GoodsLabel::getListByParentId($pid);
		$labelinfo = Gc_Service_GoodsLabel::getGoodsLabel($pid);
		if (!$id) $id = $labels[0][id];
		
		$this->assign('labels', $labels);
		$this->assign('id', $id);
		$this->assign('pid', $pid);
		$this->assign('title', $labelinfo['name']);
		
		list($total, $goods) = Gc_Service_TaokeGoods::getNomalGoods($page, $this->perpage, array('label_id'=>$id));
		
		foreach($goods as $key=>$value) {
			$tmp[$key]['title'] = strip_tags($value['title']);
			if(strpos($value['img'], 'http://') === false) {
				$img = $attachPath.$good['img'];
			}else{
				$img = $value['img'].'_200x200.'.end(explode(".",  $value['img']));
			};
			$tmp[$key]['img'] = $img;
			$tmp[$key]['price'] = $value['price'];
			$tmp[$key]['want'] = $value['want'] + $value['default_want'];
			$tmp[$key]['link'] = $webroot.'/goods/detail/?id='.$value['id'];
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}