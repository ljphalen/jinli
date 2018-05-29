<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 天天特价接口
 * @author huangsg
 *
 */
class Apk_V015_IndexController extends Api_BaseController {
	
	public $actions =array(
		'tjUrl'=>'/index/tj',
		'goodsTjUrl'=>'/tejia/redirect',
	);
	
	// 获取的默认的渠道数量
	public $perpage = 12;
	
	/**
	 * 首页获取指定数量的渠道
	 */
	public function channelAction(){
		$version = intval($this->getInput('version'));
		$server_version = Gou_Service_Config::getValue('Channel_Version');
		$channel = array();
		if ($version <= $server_version) {
		    $time = Common::getTime();
			list(, $list) = Client_Service_Channel::getList(0, 18, array('status'=>1, 'channel_id'=>1, 'top'=>1, 'start_time'=>array('<', $time), 'end_time'=>array('>', $time)), array('sort'=>'DESC'));
			if($list){
				$webroot = Common::getWebRoot();
				foreach ($list as $key=>$val){
					$channel[$key]['id'] = $val['id'];
					$channel[$key]['short_description'] = html_entity_decode($val['description1']);
					$channel[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_APK, $val);
					$channel[$key]['img'] = Common::getAttachPath() . $val['img'];
					$channel[$key]['color'] = html_entity_decode($val['color']);
					$channel[$key]['is_recommend'] = $val['is_recommend'];
				}
			}
		}
		$this->output(0, '', array('channel'=>$channel, "version"=>$server_version));
	}
	
	
	/*
	 * 
	 * 用户添加平台
	 */
	public function channel_listAction(){
		$version = intval($this->getInput('version'));
		$server_version = Gou_Service_Config::getValue('Channel_Version');
		
		$webroot = Common::getWebRoot();
		$channelData = array();
		
		if ($version <= $server_version){
		    $time = Common::getTime();
			list(, $cate) = Client_Service_Channelcate::getsBy(array('status'=>1), array('sort'=>'DESC', 'id'=>'DESC'));
			$cate = Common::resetKey($cate, 'id');
			
			$cate_ids = array_keys($cate);
			if(Common::getAndroidClientVersion() >= 248) {
    			$cate_p = array(0=>array('id'=>0, 'title'=>'人气关注', 'sort'=>9999999999, 'status'=>1));
    			$cate = array_merge($cate_p, $cate);
			}
			
			$time = Common::getTime();
           list(,$channel) = Client_Service_Channel::getsBy(array('status'=>1, 'top'=>0, 'channel_id'=>1, 'cate_id'=>array('IN', $cate_ids), 'start_time'=>array('<', $time), 'end_time'=>array('>', $time)), array('sort'=>'DESC'));
           
           $data = $this->_cookData($cate, $channel);
           
			foreach ($data as $key=>$val){
			    if($val['channel']) {
    			    $channelData[] = array(
    			        'type'=>html_entity_decode($val['title']),
    			        'channel'=>$val['channel']
    			    );
			    }
			}
		}
		
		$this->output(0, '', array('channel'=>$channelData, "version"=>$server_version));
	}
	
	/**
	 *
	 * @param array $roots
	 * @param array $childs
	 */
	private function _cookData($cate, $channels) {
	    $tmp = Common::resetKey($cate, 'id');
	    $data = array();
	    foreach ($channels as $key => $v) {
	        if($v['is_hot'] == 1 && Common::getAndroidClientVersion() >= 248 ) {
	            $tmp[0]['channel'][] = array(
	            'id'=>$v['id'],
	            'name'=>html_entity_decode($v['name']),
	            'link'=>Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_APK, $v),
	            'img'=>Common::getAttachPath() . $v['img'],
	            'short_description'=>html_entity_decode($v['description1']),
	            'description'=>html_entity_decode($v['description']),
	            );
	        }

	        $tmp[$v['cate_id']]['channel'][] = array(
					'id'=>$v['id'],
					'name'=>html_entity_decode($v['name']),
					'link'=>Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_APK, $v),
					'img'=>Common::getAttachPath() . $v['img'],
					'short_description'=>html_entity_decode($v['description1']),
					'description'=>html_entity_decode($v['description']),
			);
	    }
	    return $tmp;
	}
	
	
	/**
	 * 广告接口
	 * ads 首页轮播
	 * special 特色专区
	 */
	public function adsAction(){
		$version = intval($this->getInput('version'));
		$server_version = Gou_Service_Config::getValue('Ad_Version');
		if ($version <= $server_version){
			list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 5, array('ad_type'=>1, 'channel_id'=>2));
			list(, $special1) = Gou_Service_Ad::getCanUseAds(1, 3, array('ad_type'=>3, 'channel_id'=>2));
			list(, $special2) = Gou_Service_Ad::getCanUseAds(1, 3, array('ad_type'=>4, 'channel_id'=>2));
			
			$dataAds = $dataSpecial1 = $dataSpecial2 = array();
			if($ads) {
			    foreach ($ads as $key=>$value){
			        $dataAds[$key]['id'] = $value['id'];
			        $dataAds[$key]['title'] = Util_String::substr(html_entity_decode($value['title']), 0, 18, '', true);
			        $dataAds[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $value);
			        $dataAds[$key]['action'] = $value['action'];
			        $dataAds[$key]['img'] = Common::getAttachPath() .$value['img'];
			        
			        //砍价详情的广告，需要返回到本地的砍列表页  by 产品经理
			        if(strpos(html_entity_decode($value['link']), 'gionee.com/cut/detail') !== false) {
			            $dataAds[$key]['type'] = 'cut_detail';
			        }
			    }
			}
			
			if($special1) {
			    foreach ($special1 as $key=>$val){
			        $dataSpecial1[] = array(
			                'id'=>$val['id'],
			                'title'=>Util_String::substr(html_entity_decode($val['title']), 0, 18, '', true),
			                'link'=>Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $val),
			                'img'=>Common::getAttachPath() .$val['img'],
			        );
			    }
			}
			
			if($special2) {
    			foreach ($special2 as $key=>$v){
    			    $dataSpecial2[] = array(
    			            'id'=>$v['id'],
    			            'title'=>Util_String::substr(html_entity_decode($v['title']), 0, 18, '', true),
    			            'link'=>Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $v),
    			            'img'=>Common::getAttachPath() .$v['img'],
    			    );
    			}
			}
		}
		
		$this->output(0, '', array('ads'=>$dataAds, 'ad1'=>$dataSpecial1, 'ad2'=>$dataSpecial2, 'version'=>$server_version));
	}
	
	/**
	 * 首页特色专区接口
	 */
	public function specialAction() {
	    $version = intval($this->getInput('version'));
	    $server_version = Gou_Service_Config::getValue('Ad_Version');
	    $dataAd = array();
	    if ($version <= $server_version){
	        $webroot = Common::getWebRoot();
	        $tjUrl = $webroot.$this->actions['tjUrl'];
	        list(, $ad) = Gou_Service_Ad::getCanUseAds(1, 3, array('ad_type'=>8, 'channel_id'=>2));
	        $dataAd = $imgs = array();
	        if($ad) {
	            foreach ($ad as $key=>$value) {
	                $dataAd[$key]['title'] = Util_String::substr($value['title'], 0, 18, '', true);
	                $imgs[] = $dataAd[$key]['img'] = Common::getAttachPath() . $value['img'];
	                $dataAd[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $value);
	            }
	            $this->cache($imgs, 'special');
	        }
	
	    }
	    $this->output(0, '', array('ad'=>$dataAd, 'version'=>$server_version));
	}

	public function special_newAction() {
	    $version = intval($this->getInput('version'));
	    $server_version = Gou_Service_Config::getValue('Ad_Version');
	    $dataAd = array();
	    if ($version <= $server_version){
	        $webroot = Common::getWebRoot();
//	        $tjUrl = $webroot.$this->actions['tjUrl'];

            $dataAd = $imgs = array();
            $client_version = Common::getAndroidClientVersion();
            //针对预装版2.5.1
            if($client_version == 251){
                list(, $ad) = Gou_Service_Ad::getCanUseAds(1, 4, array('ad_type'=>10, 'channel_id'=>2));
                if($ad) {
                    foreach ($ad as $key=>$value) {
                        $dataAd[$key]['title'] = Util_String::substr($value['title'], 0, 18, '', true);
                        $imgs[] = $dataAd[$key]['img'] = Common::getAttachPath() . $value['img'];
                        $dataAd[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $value);
                        //当客户端版本大于等于clientver, 则传递activity字段
                        if($value['activity'] && intval($client_version) >= intval($value['clientver'])) {
                            $dataAd[$key]['link'] = $value['activity'];
                        }
                    }
                    $this->cache($imgs, 'special');
                }
            }else{
                list(, $ad) = Gou_Service_Ad::getCanUseAds(1, 4, array('ad_type'=>9, 'channel_id'=>2));
                if($ad) {
                    foreach ($ad as $key=>$value) {
                        $dataAd[$key]['title'] = Util_String::substr($value['title'], 0, 18, '', true);
                        $imgs[] = $dataAd[$key]['img'] = Common::getAttachPath() . $value['img'];
                        $dataAd[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_APK, $value);
                        //当客户端版本大于等于clientver, 则传递activity字段
                        if($value['activity'] && intval($client_version) >= intval($value['clientver'])) {
                            $dataAd[$key]['link'] = $value['activity'];
                        }
                    }
                    $this->cache($imgs, 'special');
                }
            }
	    }
	    $this->output(0, '', array('ad'=>$dataAd, 'version'=>$server_version));
	}


	
	/**
	 * 首页便捷服务链接接口
	 */
	public function convenientAction(){
		$version = intval($this->getInput('version'));
		$server_version = Gou_Service_Config::getValue('Ad_Version');
		$app_version = $this->getInput('app_version_code');
		$main_version = substr($app_version,0,strlen($app_version)-5);
		$ad_type = $app_version ? 7 : 5;

        $num=($main_version>11202)?5:4;
		$dataAd = array();
		if ($version <= $server_version){
			$webroot = Common::getWebRoot();
			$tjUrl = $webroot.$this->actions['tjUrl'];
			list(, $ad) = Gou_Service_Ad::getCanUseAds(1, $num, array('ad_type'=>$ad_type, 'channel_id'=>2));
			if($ad){
			    foreach ($ad as $key=>$val){
			        $dataAd[] = array(
			                'id'=>$val['id'],
			                'title'=>Util_String::substr(html_entity_decode($val['title']), 0, 18, '', true),
			                'link'=>Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_APK, $val),
			                'img'=>Common::getAttachPath() .$val['img'],
			        );
			    }
			}
		}
		$this->output(0, '', array('ad'=>$dataAd, 'version'=>$server_version));
	}
	
	/**
	 * 天天特价接口
	 */
	public function tejiaAction(){
		$category = intval($this->getInput('category'));
		$version = intval($this->getInput('version'));
		$server_version = Gou_Service_Config::getValue('Channel_Goods_Version');
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;		 
		if ($page < 1) $page = 1;
		
		$data_array = array();
		if ($version != $server_version){
			$search['status'] = 1;
			$search['start_time'] = array('<', time());
			$search['end_time'] = array('>', time());
			if($category) $search['goods_type'] = intval($category);
			
			$configs = Common::getConfig('tejiaConfig');
			
			list($total, $goodsList) = Client_Service_Channelgoods::getList($page, $perpage, $search, array('sort'=>'DESC', 'id'=>'DESC'));
			
			$data = array();
			if($goodsList) {
				foreach ($goodsList as $key=>$val){
					$data[] = array(
					'id'=>$val['id'],
					'title'=>Util_String::substr(html_entity_decode($val['title']), 0, 20, '', true),
					'category'=>$val['goods_type'],
					'from'=>$configs[$val['supplier']]['name'],
					'market_price'=>$val['market_price'],
					'sale_price'=>$val['sale_price'],
					'discount'=>round($val['sale_price']/$val['market_price'],2) * 10,
					'link'=>Client_Service_Channelgoods::getShortUrl(Stat_Service_Log::V_APK, $val),
					'img'=>$val['img']
				);
				}
			}
			
			//category
			$goods_type_array = array();
			if($category == 0 && $page == 1){
			    $goods_type_list = Client_Service_Channelgoodscate::getAllCategory();
			    $goods_type_array[0] = array(
			            'id'=>0,
			            'name'=>'全部'
			    );
			    	
			    if (!empty($goods_type_list)) {
			        foreach ($goods_type_list as $val){
			            $goods_type_array[] = array(
			                    'id'=>$val['id'],
			                    'name'=>html_entity_decode($val['title'])
			            );
			        }
			    }
			}
			
			$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
			$data_array = array('list'=>$data, 'version'=>$server_version,
			'category'=>$goods_type_array,
			'curpage'=>$page,
			'hasnext'=>$hasnext);
		} 		
		
		$this->output(0, '', $data_array);
	}
	
	
}