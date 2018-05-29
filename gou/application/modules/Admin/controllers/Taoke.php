<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class TaokeController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Taoke/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$cid = intval($this->getInput('cid'));
		$keyword = $this->getInput('keyword');
		
		if ($page < 1) $page = 1;
		//get goods list
		$topApi  = new Api_Top_Service();
		$ret = $topApi->findTaobaokes(array('page_no'=>$page, 'page_size'=>$this->perpage, 'cid'=>$cid, 'keyword'=>$keyword, 'is_mobile'=>'true'));
		
		$goods = $ret['taobaoke_items']['taobaoke_item'];
		$total = $ret['total_results'];
		
		//get taobao num_iids
		$num_iids = array();
		foreach($goods as $key=>$value) {
			$num_iids[] = $value['num_iid'];
			$goods[$key]['pic_url'] .= '_120x120.' . end(explode(".", $goods[$key]['pic_url']));
		}
		
		//get info infos;
		if (count($num_iids)) {
			$infos = $topApi->getItemInfos($num_iids);
			$infos = Common::resetKey($infos, 'num_iid');
		}
		
		$this->assign('goods', $goods);
		$this->assign('infos', $infos);		

		//get taobao item cates
		$item_cats = $topApi->getTaoBaoItemCats();
		$this->assign('item_cats', $item_cats['item_cats']['item_cat']);
		
		$this->assign('cid', $cid);
		$this->assign('keyword', $keyword);
		
		$url = $this->actions['listUrl'] .'/?'. http_build_query(array('cid'=>$cid, 'keyword'=>$keyword)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
}
