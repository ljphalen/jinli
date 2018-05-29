<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Web_ConfigController extends Admin_BaseController {
	public $actions = array(
		'addUrl' => '/Admin/Web_Config/add',
		'addPostUrl' => '/Admin/Web_Config/add_post',
		'andoridImgUrl' => '/Admin/Web_Config/img',
		'addImgPostUrl' => '/Admin/Web_Config/img_post',
		'deleteUrl' => '/Admin/Web_Config/delete',
		'uploadUrl' => '/Admin/Web_Config/upload',
		'uploadPostUrl' => '/Admin/Web_Config/upload_post',
	);

	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$ami_web_url = Game_Service_Config::getValue('ami_web_url');
		$this->assign('ami_web_url', $ami_web_url );
		
		$ami_android_url = Game_Service_Config::getValue('ami_android_url');
		$this->assign('ami_android_url', $ami_android_url );
		
		$ami_web_bbs = Game_Service_Config::getValue('ami_web_bbs');
		$this->assign('ami_web_bbs', $ami_web_bbs );
		
		$ami_sina_weibo = Game_Service_Config::getValue('ami_sina_weibo');
		$this->assign('ami_sina_weibo', $ami_sina_weibo );
	
		$ami_web_weixin = Game_Service_Config::getValue('ami_web_weixin');
		$this->assign('ami_web_weixin', $ami_web_weixin );
		
		$ami_web_game_share_text = Game_Service_Config::getValue('ami_web_game_share_text');
		$this->assign('ami_web_game_share_text', $ami_web_game_share_text );
		
		$ami_web_index_share_text = Game_Service_Config::getValue('ami_web_index_share_text');
		$this->assign('ami_web_index_share_text', $ami_web_index_share_text );
		
		$ami_web_new_share_text = Game_Service_Config::getValue('ami_web_new_share_text');
		$this->assign('ami_web_new_share_text', $ami_web_new_share_text );
		
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$config  = $this->getInput(array('ami_web_url','ami_android_url','ami_web_bbs','ami_web_weixin','ami_web_game_share_text','ami_web_index_share_text','ami_web_new_share_text','ami_sina_weibo'));
	    $info    = $this->_cookData($config);
		foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0,'操作成功');
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		//if(!$info['ami_web_gid']) $this->output(-1, 'id不能为空');
		if (strpos($info['ami_web_bbs'], 'http://') === false || !strpos($info['ami_web_bbs'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
		}  
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function imgAction() {
		$ami_imgs = Game_Service_Config::getValue('ami_web_imgs');
		$ami_imgs = $ami_imgs?json_decode($ami_imgs,true):'';
		$this->assign('ami_web_imgs',$ami_imgs );
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function img_postAction() {
		$config  = $this->getInput(array('ami_web_imgs'));
		foreach($config as $key=>$value) {
			 $value = json_encode($value) ;
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0,'操作成功');
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$path= $this->getInput('path');
		Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $path);
		$this->output(0, '操作成功');
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

	/**
	 * 
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'webimg');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
    }
}