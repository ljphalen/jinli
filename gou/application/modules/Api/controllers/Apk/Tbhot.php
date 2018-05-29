<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_TbhotController extends Api_BaseController {
	
	public $actions =array(
			'tjUrl'=>'/index/tj'
	);
	
	/**
	 * 淘热卖跳转地址
	 */
	public function taobaourlAction() {
		$model = $this->getInput('model');
		
		//广告
		//$ad_data = array();
		$time = Common::getTime();
		$ad = Gou_Service_Ad::getBy(array('ad_type'=>2, 'channel_id'=>2, 'status'=>1, 'start_time'=>array('<', $time), 'end_time'=>array('>', $time)), array('sort'=>'DESC', 'id'=>'DESC'));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		
		//淘热卖跳转地址
		$url = Client_Service_Taobaourl::getBy(array('model'=>$model));
		if($model && $url) {
			Client_Service_Taobaourl::updateTJ($url['id']);
			$tbhot_url = html_entity_decode($url['url']);
		} else {
			$tbhot_url = 'http://m.taobao.com';
		}
		
		if($ad) {
			$ad_data = array(
				'img'=> Common::getAttachPath() . $ad['img'],
				'link' =>Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $ad),
				'color'=>Gou_Service_Config::getValue('tb_hot_bgcolor')
			);
		}
		
		$this->output(0, '', array('ad'=>$ad_data, 'tbhot_url'=>$tbhot_url));		
	}
}
