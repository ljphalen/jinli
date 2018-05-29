<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class AmiweatherController extends Front_BaseController {
	
	public function indexAction(){
		$params = $this->getInput('params');
		$data = $this->getInput('data');
		if(!$params) $params ='1_1';
		//http://mo.m.taobao.com/cardpage/70256?refpid=mm_26632362_9250945_31910435#pt=1&q=%E9%98%B2%E6%99%92%E9%9C%9C
		
		$param = explode('_', $params);
		
		$info = Amigo_Service_Weather::getBy(array('root_id'=>$param[1],'parent_id'=>$param[0]));
		
		//data
		if($data) {
		    $data =  html_entity_decode(urldecode($data));
		    $data = json_decode($data, true);
		    $data['temperature'] = explode('~', $data['temp']);
		    $this->assign('data', $data);
		}
		
		$url = 'http://srd.simba.taobao.com/rd?w=lark&p=mm_31749056_5612673_27700230&f=http%3A%2F%2Fre.m.taobao.com%2Ftssearch%3Frefpid%3Dmm_31749056_5612673_27700230&k=21db83549fb6dd44';
		
	    $this->assign('info', $info);
	    $this->assign('url', $url);
		$this->assign('title', '精品推荐');
	}
	
	
	/**
	 * 穿衣指数
	 */
	public function clothesAction() {
	    $params = $this->getInput('params');
	    $param = explode('_', $params);
	    
	    $info = Amigo_Service_Weather::getBy(array('root_id'=>$param[1],'parent_id'=>$param[0]));
	    $id = explode(',', html_entity_decode($info['num_iid']));
	    
	    $this->assign('id', $id);
	    $this->assign('info', $info);
	}
	
	public function goodsAction(){
	    $params = $this->getInput('params');
	
	    $this->assign('params', $params);
	    $this->assign('title', '精品推荐');
	}
	
}