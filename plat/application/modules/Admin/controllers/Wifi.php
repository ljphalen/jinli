<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class WifiController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/admin/wifi/index',
		'logUrl'=>'/admin/wifi/log',
		'editUrl'=>'/admin/wifi/edit',
		'editPostUrl'=>'/admin/wifi/edit_post',
		'detailUrl'=>'/admin/wifi/detail',
		'adeditUrl'=>'/admin/wifi/adedit',
		'adeditPostUrl'=>'/admin/wifi/adedit_post',
		'uploadUrl' => '/admin/wifi/upload',
		'uploadPostUrl' => '/admin/wifi/upload_post',
		'configUrl'=>'/admin/wifi/config'
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $devices) = Wifi_Service_Device::getList($page, $perpage);
		
		$this->assign('devices', $devices);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	public function logAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $logs) = Wifi_Service_Log::getList($page, $perpage);
		
		$this->assign('logs', $logs);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['logUrl'].'/?'));
	}
	
	public function adeditAction() {
		$ht = $this->getInput('ht');
		$ads = Wifi_Service_Ad::getsBy(array('ht'=>$ht));
		$ads = Common::resetKey($ads, 'position');
		$this->assign('ht', $ht);
		$this->assign('ads', $ads);
	}
	
	public function adedit_postAction() {
		$infos = $this->getInput(array('ht', 'imgs', 'links'));
		if (!$infos['ht']) $this->output(-1, '操作失败mac', array());
		if (count($infos['links']) != count($infos['imgs'])) $this->output(-1, '广告参数不完整.');
		foreach($infos['links'] as $key=>$value) {
			if (($link = $infos['links'][$key]) && $img = $infos['imgs'][$key]) {
				$d = array('position'=>$key, 'img'=>$img, 'link'=>$link, 'ht'=>$infos['ht']);
				$ad = Wifi_Service_Ad::getBy(array('position'=>$key, 'ht'=>$infos['ht']));
				if ($ad) {
					$ret = Wifi_Service_Ad::update($d, $ad['id']);
				} else {
					$ret = Wifi_Service_Ad::add($d);
				}
				if (!$ret) $this->output(-1, '操作失败.', array()); 
			}
		}
		$this->output(0, '操作成功.', array());
	}
	
	public function editAction() {
		$ht = $this->getInput('ht');
		$device = Wifi_Service_Device::getBy(array('ht'=>$ht));
		$this->assign('device', $device);
	}

	public function edit_postAction() {
		$info = $this->getInput(array('name', 'hb_interval'));
		$id = $this->getInput('id');
		
		$info['hb_interval'] = intval($info['hb_interval']);
		if ($info['hb_interval'] < 10) $this->output(-1, '心跳频率必须大于10');
		
		$ret = Wifi_Service_Device::update($info, $id);
		if (!$ret) $this->output(-1,'操作失败', array());
		$this->output(0, '操作成功', array());
	}
	
	public function detailAction() {
		$ht = $this->getInput('ht');
		$info = Wifi_Service_Device::getBy(array('ht'=>$ht));
		$this->assign('info', $info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	public function configAction() {
		$ht = $this->getInput("ht");
		$action = $this->getInput("action");
		
		$cache = Common::getCache();
		$key = sprintf("%s_%s", $ht, $action);
		
		if (in_array($action, array("reboot", "reconnect", "upgrade"))) {
			$ret = $cache->set($key, 1, 60);
			if (!$ret) $this->output(-1, '操作失败.', array());
			$this->output(0,'操作成功.', array());	
		}
		$this->output(-1, '操作失败.',array());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'ads');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
}
