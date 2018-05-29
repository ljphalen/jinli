<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ZdmController extends App_BaseController {
	
	public $perpage = 10;

	public function indexAction() {
		$id = intval($this->getInput('id'));
		$topic  = Fanfan_Service_Topic::getTopic($id);
		
		//goods
		list($total,$goods) = Fanfan_Service_Topicgoods::getsBy(array('topic_id'=>$topic['id'], 'status'=>1), array('sort'=>'DESC', 'id'=>'DESC'));
		
		//cate
		$topic_cate = Fanfan_Service_Topiccate::getCategory($topic['cate_id']);
		
		$keywords = explode(',', html_entity_decode($topic['keywords']));
		
		//update hits
		if($topic) Fanfan_Service_Topic::updateTJ($topic['id']);
		
		$this->assign('topic', $topic);
		$this->assign('goods', $goods);	
		$this->assign('topic_cate', $topic_cate);
		$this->assign('total', $total);
		$this->assign('keywords', $keywords);
		$this->assign('title', html_entity_decode($topic['title'])); 
	}
	
	/**
	 * goods_redirect
	 */
	public function goods_redirectAction() {
		$id = intval($this->getInput('id'));
		$goods = Fanfan_Service_Topicgoods::getTopicgoods($id);
		if($goods) {
		    $topApi  = new Api_Top_Service();
		    $convert = $topApi->tbkMobileItemsConvert(array('num_iids'=>$goods['goods_id']));
			if($convert) {
			    $url = $convert['click_url'];
			} else {
			    $url = Common::getWebRoot();
			}
			
			//update hits
			Fanfan_Service_Topicgoods::updateTopicgoods(array('hits'=>$goods['hits'] + 1), $id);
		} else {
			$url = Common::getWebRoot();
		}
		$this->redirect($url);
	}
	
	/**
	 * keywords_redirect
	 */
	public function keyword_redirectAction() {
		$keyword = trim($this->getInput('keyword'));
		
		$info = array(
    		'keyword'=>$keyword,
    		'keyword_md5'=>md5($keyword),
    		'create_time'=>Common::getTime(),
    		'dateline'=>date('Y-m-d', Common::getTime())
		);
		Client_Service_KeywordsLog::addKeywordsLog($info);
		
		$url = sprintf('%s%s',Common::getConfig ('apiConfig', 'taobao_search_pid'), $keyword);
		$this->redirect($url);
	
	}
}
