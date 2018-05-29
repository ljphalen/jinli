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
class GouController extends Api_BaseController {
	
	public $actions =array(
				'topic' => '/index/topic',
				'tjUrl'=>'/index/tj'
			);
	
	public $perpage = 4;
	public $cacheKey = 'Front_Index_index';
	
	/**
	 * H5轮播广告
	 * 
	 */
	public function adAction() {
		list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 4, array('ad_type'=>1, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$data = $imgs = array();
		foreach ($ads as $key=>$value) {
			$data[$key]['title'] = $value['title'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_H5, $value);
		}
		$this->cache($imgs, 'ad');
		$this->output(0, '', $data);
	}
		
	/**
	 * 客户端下载地址
	 * 
	 */
	public function downloadAction() {
		$webroot = Common::getWebRoot();
		list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 1, array('ad_type'=>2, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = array();
		if($ads) {
			$data = array(
				'title' => $ads[0]['title'],			
				'link' => Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_H5, $ads[0]),
				'img' => Common::getAttachPath() . $ads[0]['img']
			);
			$imgs[0] = Common::getAttachPath() . $ads[0]['img'];
			$this->cache($imgs, 'down');
		}
		$this->output(0, '', $data);
	}
	
	/**
	 * Amigo商城广告
	 *
	 */
	public function amigoAction() {
		$webroot = Common::getWebRoot();
		list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 1, array('ad_type'=>3, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = array();
		if($ads) {
			$data = array(
					'title' => $ads[0]['title'],
					'link' => Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_H5, $ads[0]),
					'img' => Common::getAttachPath() . $ads[0]['img']
			);
			$imgs[0] = Common::getAttachPath() . $ads[0]['img'];
			$this->cache($imgs, 'amigoAd');
		}
		$this->output(0, '', $data);
	}
	
	/**
	 * 推荐
	 */
	public function recommendAction() {
		list(, $recommend) = Gou_Service_Channel::getCanUseChanels(0, 4, array('type_id'=>2, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($recommend as $key=>$value) {
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_H5, $value);
		}
		$this->cache($imgs, 'recommend');
		$this->output(0, '', $data);
	}
	
	/**
	 * 综合购物商城
	 */
	public function mallAction() {
		list(, $mall) = Gou_Service_Channel::getCanUseChanels(0, 8, array('type_id'=>1, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_H5, $value);
		}
		$this->cache($imgs, 'mall');
		$this->output(0, '', $data);
	}
		
	/**
	 * 生活便民助手
	 */
	public function helperAction() {
		list(, $mall) = Gou_Service_Channel::getCanUseChanels(0, 4, array('type_id'=>3, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_H5, $value);
		}
 		$this->cache($imgs, 'helper');
		$this->output(0, '', $data);
	}
	
	/**
	 * 主题店
	 */
	public function themeAction() {
		list($total, $mall) = Gou_Service_Channel::getCanUseChanels(0, 8, array('type_id'=>4, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_H5, $value);
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
		list(, $mall) = Gou_Service_Channel::getCanUseChanels(0, 4,  array('type_id'=>5, 'channel_id'=>1));
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$data = $imgs = array();
		foreach ($mall as $key=>$value) {
			$data[$key]['name'] = $value['name'];
			$imgs[] = $data[$key]['img'] = Common::getAttachPath() . $value['img'];
			$data[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_H5, $value);
		}
		$this->cache($imgs, 'tuan');
		$this->output(0, '', $data);
	}
	
}
