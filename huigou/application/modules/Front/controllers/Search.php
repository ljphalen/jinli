<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends Front_BaseController {
	
	public $perpage = 15;

	/**
	 * 
	 */
	public function indexAction() {
		$title = "搜索";
		$default_words = Gc_Service_Config::getValue('gc_search_keyword');
		$this->assign('default_words', explode(",", html_entity_decode($default_words)));
		$this->assign('title', $title);
	}
	
	/**
	 * 
	 */
	public function searchAction() {
		$this->_search();
	}
	
	public function csearchAction() {
		$this->_search();
		$this->display('search');
		exit;
	}
	
	private function _search() {
		$page = intval($this->getInput('page'));
		$keyword = html_entity_decode($this->getInput('keyword'));
		$title = "搜索结果";
		if ($page < 1) $page = 1;
		//get goods list
		$topApi  = new Api_Top_Service();
		$ret = $topApi->findTaobaokes(array('page_no'=>$page, 'page_size'=>$this->perpage, 'keyword'=>$keyword, 'is_mobile'=>'true', 'outer_code'=>$this->getOuterCode()));
		
		$goods = $ret['taobaoke_items']['taobaoke_item'];
		$total = $ret['total_results'];
		
		//$tmpgoods = Common::resetKey($goods, 'num_iid');
		
		
		
		foreach($goods as $key=>$value) {
			$goods[$key]['click_url'] = $this->getTaobaokeUrl($value['click_url']);
			$goods[$key]['pic_url'] = $value['pic_url']. '_200x200.' . end(explode(".", $value['pic_url']));
		}
		
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('keyword', $keyword);
		$this->assign('goods', $goods);
		$this->assign('total', $total);
		$this->assign('title', $title);
	}
	
	public function moreAction() {
		$page = intval($this->getInput('page'));
		$keyword = html_entity_decode($this->getInput('keyword'));
		$title = $keyword;
		
		if ($page < 1) $page = 1;
		//get goods list
		$topApi  = new Api_Top_Service();
		$ret = $topApi->findTaobaokes(array('page_no'=>$page, 'page_size'=>$this->perpage, 'keyword'=>$keyword, 'is_mobile'=>'true', 'outer_code'=>$this->getOuterCode()));
		$goods = $ret['taobaoke_items']['taobaoke_item'];
		$total = $ret['total_results'];
		
		$tmp = array();
		foreach($goods as $key=>$value) {
			$tmp[$key]['title'] = strip_tags($value['title']);
			$tmp[$key]['img'] = $value['pic_url'] . '_200x200.' . end(explode(".", $value['pic_url']));
			$tmp[$key]['price'] = $value['price'];
			$tmp[$key]['commission_num'] = $value['commission_num'];
			$tmp[$key]['click_url'] = $this->getTaobaokeUrl($value['click_url']);
		}
	
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		if ($page == 3) $hasnext = false;
		$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
