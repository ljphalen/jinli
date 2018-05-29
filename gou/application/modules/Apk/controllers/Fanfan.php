<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class FanfanController extends Apk_BaseController {
	
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
		if($topic) Fanfan_Service_Topic::updateTopic(array('hits'=>$topic['hits'] + 1), $topic['id']);
		
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
			$url = html_entity_decode($goods['link']);
			
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
				
		$url = sprintf('%s%s',Common::getConfig ('apiConfig', 'taobao_search_pid'), $keyword);
		$this->redirect($url);
	
	}
}
