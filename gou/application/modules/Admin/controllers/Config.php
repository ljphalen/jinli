<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ConfigController extends Admin_BaseController {
	
	public $actions = array(
		'editUrl'=>'/admin/config/index',
		'editPostUrl'=>'/admin/config/edit_post',
		'urlUrl'=>'/admin/config/url',
		'urlPostUrl'=>'/admin/config/url_post',
		'txtUrl'=>'/admin/config/txt',
		'txtPostUrl'=>'/admin/config/txt_post',
        'uploadUrl' => '/admin/config/upload',
        'uploadPostUrl' => '/admin/config/upload_post',
	);
	public $appCacheName = 'APPC_Front_Index_index';
	public $versionName = 'Config_Version';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Gou_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$config = $this->getInput(array(
				'History_Config_Version',
				'gou_silver_coin_rate', 
				'gou_index_cache', 
				'gou_client_keyword', 
				'gou_cod_keyword',
                'fanli_commission_rate',
                'gou_search_default_keyword',
                'gou_search_keyword',
                'cmp_goods_url_regex',
                'feedback_auto_reply_text',
                'feedback_auto_reply',
                'taobao_shop_url',
                'taobao_shop_url_code',
                'taobao_shop_color',
                'tb_hot_bgcolor',
				'gou_score_sdate',
				'gou_score_edate',
				'gou_score',
                'amigo_order_desc',
                'amigo_order_return_protocol',
                'amigo_weather_keyword',
                'has_stock_desc',
                'null_stock_desc',
                'recharge_notice',
                'gou_recharge_sms',
                'gou_baidu_stat',
                'gou_weibo_notice',
                'gou_plugin',
                'qa_title',
                'qa_intro',
                'qa_image',
                'qa_switch',
                'cut_game_probability',
                'cut_game_times',
                'cut_game_tlimit',
		        'cut_close_time',
			));

		foreach($config as $key=>$value) {
			Gou_Service_Config::setValue($key, html_entity_decode($value));
		}
        Gou_Service_Config::setValue('Config_Version', Common::getTime());
        //用于刷新问答概况
        Gou_Service_Config::setValue('Story_Version', Common::getTime());
		$this->output(0, '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function urlAction() {
		$key_array = array(
				'h5_cod_search',
				'apk_cod_search',
				'channel_cod_search',
				'market_cod_search',
				'app_cod_search',
		        'ios_cod_search',
				'h5_taobao_search',
				'ami_taobao_search',
				'apk_taobao_search',
				'channel_taobao_search',
				'market_taobao_search',
				'app_taobao_search',
		        'ios_taobao_search',
		        'type_taobao_search',
		        'ios_type_taobao_search'
				);
		$configs = array();
		foreach($key_array as $key) {
			$configs[$key] = json_decode(Gou_Service_Config::getValue($key), true);
		}
		$this->assign('configs', $configs);
		
		//module channel
		list($modules, $channel_names) = Gou_Service_ChannelModule::getsModuleChannel();
		$this->assign('modules', $modules);
		$this->assign('channel_names', $channel_names);
	}

	/**
	 *
	 */
	public function url_postAction() {
		$config = $this->getInput(array(
				'h5_cod_search',
				'apk_cod_search',
				'channel_cod_search',
				'market_cod_search',
				'app_cod_search',
		        'ios_cod_search',
				'h5_taobao_search',
                'ami_taobao_search',
				'apk_taobao_search',
				'channel_taobao_search',
				'market_taobao_search',
				'app_taobao_search',
		        'ios_taobao_search',
		       'ios_type_taobao_search'
		));
		foreach($config as $key=>$value) {
			Gou_Service_Config::setValue($key, json_encode(array('url'=>$value['url'], 
			'module_id'=>$value['module_id'], 'channel_id'=>$value['cid'], 'channel_code'=>$value['channel_code'])));
		}
		$this->output(0, '操作成功.');
	}

	/**
	 *
	 */
	public function txtAction () {
		$configs=Gou_Service_Config::getAllTxtConfig();
		$this->assign('configs', $configs);
	}

	/**
	 *
	 */
	public function txt_postAction() {
		$config = $this->getInput(array(
			'gou_cut_regular_txt',
			'amigo_order_return_protocol_txt',
            'gou_cut_blacklist_txt',
            'gou_stat_click_version_txt'
		));

		foreach($config as $key=>$value) {
			Gou_Service_Config::setTxtValue($key, html_entity_decode($value));
		}

		$this->output(0, '操作成功.');
	}

    /**
     * 上传页面
     */
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }
    /**
     *
     * 上传
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'config');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

}
