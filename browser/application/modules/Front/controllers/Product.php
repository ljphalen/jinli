<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ProductController extends Front_BaseController{
	
	public $actions = array(
		'listUrl' => '/product/index',
		'detailUrl' => '/product/detail',
		'picsUrl' => '/product/pics',
		'ajaxUrl' => '/product/ajax'
	);
	
	public $ptypes = array(
					'tianjian',
					'elife',
					'yuyingwang',
					'puji',
			);

	public function indexAction() {
		$type = $this->getInput('type');
		if(!$type) {
			//默认取新品			
			$type = 'new';
			$newlist = Browser_Service_Product::getNewProduct();
			$plist = array();
			foreach ($newlist as $k => $v) {
				$plist[$k]['mark'] = $v['mark'];
				$plist[$k]['img'] = Browser_Service_Product::getProductPic($this->ptypes[$v['type']], $v['mark']);
				$plist[$k]['type'] = $this->ptypes[$v['type']];
			}
		} else {
			$plist = Browser_Service_Product::getProductList($type);			
		}		
		$this->assign('type', $type);
		$this->assign('plist', $plist);
		$this->assign('pageTitle','产品大全');
	}
	
	public function ajaxAction() {
		$type = $this->getInput('type');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		if(!$type) {
			//默认取新品
			$type = 'new';
			$newlist = Browser_Service_Product::getNewProduct();
			$plist = array();
			foreach ($newlist as $k => $v) {
				$plist[$k]['mark'] = $v['mark'];
				$plist[$k]['img'] = Browser_Service_Product::getProductPic($this->ptypes[$v['type']], $v['mark']);
				$plist[$k]['type'] = $this->ptypes[$v['type']];
			}
		} else {
			$plist = Browser_Service_Product::getProductList($type);
		}
		
		$total = count($plist);

		$webroot = Yaf_Application::app()->getConfig()->webroot;
		
		$temp = array();
		foreach($plist as $key=>$value) {
		$temp[$key]['mark'] = $value['mark'];
		$temp[$key]['link'] = urldecode($webroot . $this->actions['detailUrl']. '?type=' . $value['type'] . '&mark=' . $value['mark']);
		$temp[$key]['img'] = urldecode($webroot . '/attachs/'.$value['img']);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}


	public function detailAction() {
		$type = $this->getInput('type');
		$pre = $next = $mark = $this->getInput('mark');
		$plist = Browser_Service_Product::getProductList($type);
		foreach ($plist as $key => $value) {
			if(trim($mark) == $value['mark']) {
				if(isset($plist[$key -1])) {
					$pre = $plist[$key - 1]['mark'];	
				}
			        if(isset($plist[$key + 1])) {
					$next = $plist[$key + 1]['mark'];	
				}
				
			} 
		}
		$info = Browser_Service_Product::getProductByMark($type, $mark);
		
		$info['param'] = str_replace(array("\r\n", "\n", "\r"), "<br/>", $info['param']);
		$info['descrip'] = str_replace(array("\r\n", "\n", "\r"), "<br/>", $info['descrip']);
		
		$info['param'] = html_entity_decode($info['param']);
		$info['descrip'] = html_entity_decode($info['descrip']);
		
		$this->assign('type', $type);
		$this->assign('info', $info);
		$this->assign('pre', $pre);
		$this->assign('next', $next);
		$this->assign('mark', $mark);
		$this->assign('pageTitle',$mark.' - 产品大全');
	}

	public function picsAction() {
		$type = $this->getInput('type');
		$mark = $this->getInput('mark');

		$pics = Browser_Service_Product::getProductPics($type, $mark); 
		$this->assign('pics', $pics);
		$this->assign('mark', $mark);
		$this->assign('pageTitle',$mark.' - 产品大全');
	}
}
