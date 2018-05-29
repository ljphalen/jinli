<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class ConfigController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl'=>'/Admin/config/index',
		'editPostUrl'=>'/Admin/config/edit_post',
		'uploadUrl' => '/Admin/config/upload',
		'uploadPostUrl' => '/Admin/config/upload_post',
		'uploadImgUrl' => '/Admin/config/uploadImg',
		'moduleIndexUrl' => '/Admin/config/moduleIndex',
		'modulePostUrl' => '/Admin/config/modulePost',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$postParams = array('game_react',
							'game_title',
							'game_link',
							'game_num',
							'game_search_filter',
							'game_h5_cache',
							'game_account_agreement_url',
							array('game_account_agreement_data', '#s_z'),
							'game_account_forgetpwd',
							'game_account_tips',
							'game_aimi_url',
							'game_aimi_size',
							'game_amgo_url',
							'game_amgo_size',
							'game_time',
							'game_start_per',
							'game_end_per',
							'game_filter_default',
							'game_filter_gamehall',
							'game_filter_amigame',
							'game_gift_eamil',
							'game_gift_num',
							'bbs_share_url',
							'bbs_share_simg',
							'bbs_share_bimg',
							'log_status'
				            );

		$config = $this->getPost($postParams);

		$config['game_account_agreement_data'] = str_replace("<br />","",html_entity_decode($config['game_account_agreement_data']));

		if($config['game_start_per'] < 0 || $config['game_start_per'] <0) {
			$this->output(-1, '游戏升级百分比不能小于0,必须大于0.');
		}

		foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function moduleIndexAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 *
	 */
	public function modulePostAction() {
		$config = $this->getPost(array('module_freedl_status', 'module_freedl_allow_imsi'));
		foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
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
		$ret = Common::upload('img', 'news');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 编辑器中上传图片
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'news');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}
}
