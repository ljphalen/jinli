<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 * chl_id 用于位置统计 
 *
 */
class IndexController extends Api_BaseController {
	
	public $actions =array(
			'tjUrl'=>'/api/stat/redirect'
	);
	
	public $perpage = 4;
	
	/**
	 * H5轮播广告
	 * 
	 */
	public function adsAction() {
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
		
		//cache version
		$version = Gou_Service_Config::getValue('Ad_Version');
		
		$data = array();
		if($client_data_version < $version) {
			list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 3, array('ad_type'=>1));
			$webroot = Common::getWebRoot();
			$tjUrl = $webroot.$this->actions['tjUrl'];
			$data = array();
			foreach ($ads as $key=>$value) {
				$data[$key]['title'] = $value['title'];
				$data[$key]['img'] = $webroot . '/attachs' . $value['img'];
				$data[$key]['link'] = Common::tjurl($tjUrl, $value['id'], 'AD', $value['link'], 11, $key + 1);
			}
		}		
		$this->clientoutput(0, '', array('data'=>$data, 'version'=>$version));
	}
	
	
	/**
	 * 达人推荐
	 */
	public function recommendAction() {
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
		
		//cache version
		$version = Gou_Service_Config::getValue('Channel_Version');
		$data = array();
		if($client_data_version < $version) {
			list(, $recommend) = Gou_Service_Channel::getCanUseChanels(0, 8, array('is_recommend'=>1));
			$webroot = Common::getWebRoot();
			$tjUrl = $webroot.$this->actions['tjUrl'];
			
			foreach ($recommend as $key=>$value) {
				$data[$key]['id'] = $value['id'];
				$data[$key]['name'] = $value['name'];
				$data[$key]['img'] = $webroot . '/attachs' . $value['img'];
				$data[$key]['link'] = Common::tjurl($tjUrl, $value['id'], 'CHANNEL', $value['link'], 12, $key+1);
			}
		}
		$this->clientoutput(0, '', array('data'=>$data, 'version'=>$version));
	}
	
	/**
	 * 订制 
	 */
	public function customAction() {
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
		$page = intval($this->getInput('page'));
		$perpage = intval($this->getInput('perpage'));
		if ($page < 1) $page = 1;
		if (!$perpage) $perpage = 10;
	
		//cache version
		$version = Gou_Service_Config::getValue('Channel_Version');
		
		list($total, $recommend) = Gou_Service_Channel::getCanUseChanels($page, $perpage, array('is_recommend'=>0));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = array();
		if($client_data_version < $version) {				
			foreach ($recommend as $key=>$value) {
				$data[$key]['id'] = $value['id'];
				$data[$key]['name'] = $value['name'];
				$data[$key]['img'] = $webroot . '/attachs' . $value['img'];
				$data[$key]['description'] = html_entity_decode($value['descript']);
				$data[$key]['link'] = Common::tjurl($tjUrl, $value['id'], 'CHANNEL', $value['link'], 13, $key+1);
			}
		}
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('data'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page, 'version'=>$version));
	}
	
	/**
	 * start
	 */
	public function startAction() {
		$time = Common::getTime();
		$webroot = Common::getWebRoot();
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
		
		//cache version
		$version = Gou_Service_Config::getValue('Start_Version');
		$data = array();
		if($client_data_version < $version) {
			$start = Gou_Service_Start::getBy(array('start_time'=>array('<', $time), 'end_time'=>array('>', $time), 'status'=>'1'), array('sort'=>'DESC', 'id'=>'DESC'));
			
			$data['img'] = $webroot.'/attachs'.$start['img'];
			$data['start_time'] = $start['start_time'];
			$data['end_time'] = $start['end_time'];
		}		
		$this->output(0, '', array('data'=>$data, 'version'=>$version));
	}
	
	/**
	 * 启动统计
	 */
	public function start_statAction(){
		$this->output(0, 'success');
	}
	
	
	/**
	 * start
	 */
	public function configAction() {
		$webroot = Common::getWebRoot();
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
	
		//cache version
		$version = Gou_Service_Config::getValue('Config_Version');
		$data = array();
		if($client_data_version < $version) {
			$data['tel'] = Gou_Service_Config::getValue('gou_service_tel');
			$data['qq'] = Gou_Service_Config::getValue('gou_qq_qun');
		}
		$this->output(0, '', array('data'=>$data, 'version'=>$version));
	}
	
	/**
	 * start
	 */
	public function scoreAction() {
		$webroot = Common::getWebRoot();
		$is_score = intval($this->getInput('is_score'));
	
		if($is_score) {
			$space_time = 365*12*24*3600;
		} else {
			$space_time = 7*24*3600;
		}
		$this->output(0, '', array('space_time'=>$space_time));
	}
	
	/**
	 * push
	 */
	public function pushAction() {
		$time = Common::getTime();
		$webroot = Common::getWebRoot();
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
	
		//cache version
		$version = Gou_Service_Config::getValue('Msg_Version');
		$data = array();
		if($client_data_version < $version) {
			$msg = Gou_Service_Msg::getBy(array(), array('id'=>'DESC'));
			$data['url'] = urldecode($msg['url']);
			$data['title'] = $msg['title'];
			$data['content'] = html_entity_decode($msg['content']);
		}
		$this->output(0, '', array('data'=>$data, 'version'=>$version));
	}
}
