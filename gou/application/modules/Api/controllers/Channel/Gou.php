<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 * chl_id 用于位置统计 
 * 11：轮播广告；
 * 12：综合购物商城； 
 * 13：主题店； 
 * 14：团购&折扣
 * 15：生活便民助手
 *
 */
class Channel_GouController extends Api_BaseController {
	
	public $actions =array(
				'topic' => '/index/topic',
				'tjUrl'=>'/index/tj'
			);
	
	public $perpage = 4;
	public $cacheKey = 'Channel_Index_index';
	
	/**
	 * H5轮播广告
	 * 
	 */
	public function adAction() {
		list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 3, array('ad_type'=>1, 'channel_id'=>3));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($ads as $key=>$value) {
			$data[$key]['title'] = $value['title'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
		$this->cache($imgs, 'ad');
		$this->output(0, '', $data);
	}	
	
	/**
	 * 首页两个静态链接 
	 */
	public function linkAction() {
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		list(, $ad1) = Gou_Service_Ad::getCanUseAds(1, 10, array('ad_type'=>2, 'channel_id'=>3));
		list(, $ad2) = Gou_Service_Ad::getCanUseAds(1, 10, array('ad_type'=>3, 'channel_id'=>3));
		$dataAd1 = $imgs = array();
		foreach ($ad1 as $key=>$value) {
		    $dataAd1[$key]['title'] = Util_String::substr($value['title'], 0, 18, '', true);
		    $imgs[] = $dataAd1[$key]['img'] = Common::getAttachPath() . $value['img'];
		    $dataAd1[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
		$this->cache($imgs, 'link1');
		
		$dataAd2 = $imgs = array();
		foreach ($ad2 as $key=>$value) {
		    $dataAd2[$key]['title'] = Util_String::substr($value['title'], 0, 18, '', true);
		    $imgs[] = $dataAd2[$key]['img'] = Common::getAttachPath() . $value['img'];
		    $dataAd2[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
		$this->cache($imgs, 'link2');
		$this->output(0, '', array('ad1'=>$dataAd1, 'ad2'=>$dataAd2));
	}
	
	/**
	 * 客户端下载地址
	 * 
	 */
	public function downloadAction() {
		$webroot = Common::getWebRoot();
		//$url = '';
		//if(strpos(Util_Http::getServer('HTTP_USER_AGENT'), 'Android') === true) {
			$apk_download_url = html_entity_decode(Gou_Service_Config::getValue('apk_download_url'));
			if($apk_download_url) $url = $apk_download_url ? $webroot.'/redirect?type=APKDOWNLOAD&url='.urlencode($apk_download_url) : '';
		//}
		$this->output(0, '', $url);
	}
	
	/**
	 * 推荐
	 */
	public function recommendAction() {
		list(, $recommend) = Gou_Service_Channel::getCanUseChanels(0, 4, array('type_id'=>2, 'channel_id'=>3));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($recommend as $key=>$value) {
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
		$this->cache($imgs, 'recommend');
		$this->output(0, '', $data);
	}
	
	/**
	 * 综合购物商城
	 */
	public function mallAction() {
		list(, $mall) = Gou_Service_Channel::getCanUseChanels(0, 8, array('type_id'=>1, 'channel_id'=>3));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
		$this->cache($imgs, 'mall');
		$this->output(0, '', $data);
	}
		
	/**
	 * 生活便民助手
	 */
	public function helperAction() {
		list(, $mall) = Gou_Service_Channel::getCanUseChanels(0, 4, array('type_id'=>3, 'channel_id'=>3));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
 		$this->cache($imgs, 'helper');
		$this->output(0, '', $data);
	}
	
	/**
	 * 主题店
	 */
	public function themeAction() {
		list($total, $mall) = Gou_Service_Channel::getCanUseChanels(0, 8, array('type_id'=>4, 'channel_id'=>3));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
		
		/* //E6最后一个显示显示‘限时抢购’
		$webroot = Common::getWebRoot();
		$ua = Util_Http::getServer('HTTP_USER_AGENT');
		$count = count($data);
		if(strpos($ua, 'E6 ') || strpos($ua, 'GiONEE-E6')) {
			$data[$count - 1] = array('name'=>'限时抢','link'=>Common::tjurl($tjUrl, 82, 'CHANNEL', 'http://12650.mmb.cn/wap/touch/groupRate.do', 13, $count));
			$imgs[$count - 1] = $data[$count - 1]['img'] = 'http://gou.gionee.com/attachs/channel/201305/220558.png';
		} */
		
		$this->cache($imgs, 'theme');
		$this->output(0, '', $data);
	}
	
	/**
	 * 团购和折扣
	 */
	public function tuanAction() {
		$domain = Common::getWebRoot();
		list(, $mall) = Gou_Service_Channel::getCanUseChanels(0, 4, array('type_id'=>5, 'channel_id'=>3));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
		}
		$this->cache($imgs, 'tuan');
		$this->output(0, '', $data);
	}
	
}
