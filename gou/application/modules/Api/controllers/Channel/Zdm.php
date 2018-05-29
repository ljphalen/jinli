<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 今天值得买API
 * @author tiansh
 *
 */
class Channel_ZdmController extends Api_BaseController {
	public $perpage = 12;
	public $actions = array(
	);
	
	/**
	 * index action
	 */
	public function indexAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$id = intval($this->getInput('id'));
		
		$topic = Fanfan_Service_Topic::getTopic($id);
		$data = array();
		if($topic && $topic['type_id'] == 2) {
		    list($total, $goods) = Fanfan_Service_Topicgoods::getList($page, $this->perpage, array('topic_id'=>$id, 'status'=>1));
		    $goods_res = Common::resetKey($goods, 'goods_id');
		    $goods_ids = implode(',', array_keys($goods_res));
		    
		    $topApi  = new Api_Top_Service();
		    $taoke_info = $topApi->getTbkItemInfo(array('num_iids'=>$goods_ids));
		    $taoke_info = Common::resetKey($taoke_info, 'num_iid');
		    
		    $converts = $topApi->tbkMobileItemsConvert(array('num_iids'=>$goods_ids));
		    $converts = Common::resetKey($converts, 'num_iid');
		
    		$webroot = Common::getWebRoot();
    
    		foreach ($goods as $key=>$value){
    			$data[$key]['title'] = Util_String::substr(preg_replace('/<\/?span([^>]+)?>|\s+/',"",$taoke_info[$value['goods_id']]['title']), 0, 24, '',true);
    			$data[$key]['img'] = $taoke_info[$value['goods_id']]['pic_url'].'_310x310.jpg';
    			//$data[$key]['link'] = $webroot.'/zdm/goods_redirect?id='.$value['id'];
    			$data[$key]['link'] = $converts[$value['goods_id']]['click_url'];
    			$data[$key]['price'] = $value['price'];
    			$data[$key]['pro_price'] = $value['pro_price'];
    			$data[$key]['discount'] = round($value['pro_price']/$value['price'],2) * 10;
    		}
    		
    		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
		}
	}
}