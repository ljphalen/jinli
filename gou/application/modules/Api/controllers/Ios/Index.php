<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh ios接口
 *
 */
class Ios_IndexController extends Api_BaseController {
	
	public $perpage = 6;
	public $actions = array(
				'recommendUrl'=>'/api/client_index/recommend',
			);
	
	/**
	 * 头部搜索默认关键字
	 */
	public function keywordAction(){
	    $keyword = Gou_Service_Config::getValue('gou_search_default_keyword');
	    $this->output(0,'',array('keyword'=>$keyword));
	}
	
	
	/**
	 * 广告接口
	 * ads 首页轮播
	 */
	public function indexAction(){
	    //客户端版本号
	    $client_version = Common::getIosClientVersion();
        $version = $this->getInput('data_version');
        $ad_server_version = Gou_Service_Config::getValue('Ad_Version');
        $channel_server_version = Gou_Service_Config::getValue('Channel_Version');
        $server_version = $ad_server_version + $channel_server_version;        
        if ($version >= $server_version) $this->emptyOutput(0, '');
        
        list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 5, array('ad_type'=>1, 'channel_id'=>6));
        	
        $dataAds = array();
        if($ads) {
            foreach ($ads as $key=>$value){
                $dataAds[] = array(
                'id'=>$value['id'],
                'title'=>Util_String::substr(html_entity_decode($value['title']), 0, 18, '', true),
                'link'=>Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_IOS, $value),
                'img'=>Common::getAttachPath() .$value['img'],
                );
            }
	     }
	     
	     //v1.3版本区分
	     $webroot = Common::getWebRoot();
	     if($client_version >= 130) {
	         $s_ad_type = 5;
	         $s_num = 4;
	     }else{
	         $s_ad_type = 2;
	         $s_num = 3;
	     }
	     
	     list(, $special) = Gou_Service_Ad::getCanUseAds(1, $s_num, array('ad_type'=>$s_ad_type, 'channel_id'=>6));
	     $dataSpecial = $imgs = array();
	     if($special) {
	         foreach ($special as $key=>$value) {
	             if(strpos($value['link'], 'http') === false) {
	                 $link = $value['link'];
	             } else {
	                 $link = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_IOS, $value);
	             }
	             $dataSpecial[$key]['title'] = Util_String::substr($value['title'], 0, 18, '', true);
	             $dataSpecial[$key]['img'] = Common::getAttachPath() . $value['img'];
	             $dataSpecial[$key]['link'] = $link;
	         }
	     }
	     
	     //v1.3版本区分, 并且链接可以打开本地的activity
	     $dataServer = $imgs = array();
	     if($client_version >= 130) {
	         $server_ad_type = 6;
	         $server_num = 5;
	     }else{
	         $server_ad_type = 3;
	         $server_num = 4;
	     }
	     list(, $server) = Gou_Service_Ad::getCanUseAds(1, $server_num, array('ad_type'=>$server_ad_type, 'channel_id'=>6));
	     if($server){
	         foreach ($server as $key=>$val){
	             $dataServer[] = array(
	             'id'=>$val['id'],
	             'title'=>Util_String::substr(html_entity_decode($val['title']), 0, 18, '', true),
	             'link'=>Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_IOS, $val),
	             'img'=>Common::getAttachPath() .$val['img'],
	             );
	         }
	     }
	     
	     $channel = array();
	     list(, $list) = Client_Service_Channel::getList(0, 18, array('status'=>1, 'top'=>1, 'channel_id'=>2), array('sort'=>'DESC'));
	     if($list){
	         $webroot = Common::getWebRoot();
	         foreach ($list as $key=>$val){
	             $channel[$key]['id'] = $val['id'];
	             $channel[$key]['short_description'] = html_entity_decode($val['description1']);
	             $channel[$key]['name'] = html_entity_decode($val['name']);
	             $channel[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_IOS, $val);
	             $channel[$key]['img'] = Common::getAttachPath() . $val['img'];
	             $channel[$key]['color'] = html_entity_decode($val['color']);
	             $channel[$key]['is_recommend'] = $val['is_recommend'];
	         }
	     }
	
	    $this->output(0, '', array('ads'=>$dataAds, 'special'=>$dataSpecial, 'service'=>$dataServer, 'channel'=>$channel, 'version'=>$server_version));
	}
	
	/**
	 * 广告接口
	 * ads 首页轮播
	 */
	public function adsAction(){
	    $version = $this->getInput('data_version');
	    $server_version = Gou_Service_Config::getValue('Ad_Version');
	    if ($version >= $server_version) $this->emptyOutput(0, '');
	
	    list(, $ads) = Gou_Service_Ad::getCanUseAds(1, 4, array('ad_type'=>1, 'channel_id'=>6));
	     
	    $dataAds = array();
	    if($ads) {
	        foreach ($ads as $key=>$value){
	            $dataAds[] = array(
	            'id'=>$value['id'],
	            'title'=>Util_String::substr(html_entity_decode($value['title']), 0, 18, '', true),
	            'link'=>Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_IOS, $value),
	            'img'=>Common::getAttachPath() .$value['img'],
	            );
	        }
	    }	
	
	    $this->output(0, '', array('ads'=>$dataAds, 'version'=>$server_version));
	}
	
	/**
	 * 首页特色专区 便捷服务接口
	 */
	public function specialAction() {
	    $version = $this->getInput('data_version');
	    $server_version = Gou_Service_Config::getValue('Ad_Version');
	    if ($version >= $server_version) $this->emptyOutput(0, '');
	    
	    $dataAd = array();
        $webroot = Common::getWebRoot();
        list(, $special) = Gou_Service_Ad::getCanUseAds(1, 3, array('ad_type'=>2, 'channel_id'=>6));
        $dataSpecial = $imgs = array();
        if($special) {
            foreach ($special as $key=>$value) {
                $dataSpecial[$key]['title'] = Util_String::substr($value['title'], 0, 18, '', true);
                $dataSpecial[$key]['img'] = Common::getAttachPath() . $value['img'];
                $dataSpecial[$key]['link'] = Gou_Service_Ad::getShortUrl(Stat_Service_Log::V_IOS, $value);
            }
            $this->cache($imgs, 'special');
        }
        
        $dataServer = $imgs = array();
        list(, $server) = Gou_Service_Ad::getCanUseAds(1, 4, array('ad_type'=>3, 'channel_id'=>6));
        if($server){
            foreach ($server as $key=>$val){
                $dataServer[] = array(
                'id'=>$val['id'],
                'title'=>Util_String::substr(html_entity_decode($val['title']), 0, 18, '', true),
                'link'=>Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_IOS, $val),
                'img'=>Common::getAttachPath() .$val['img'],
                );
            }
        }
	
	    $this->output(0, '', array('special'=>$dataSpecial, 'service'=>$dataServer, 'version'=>$server_version));
	}
	
	
	
	/**
	 * 推荐 
	 */
	public function recommendAction() {
        $version = $this->getInput('data_version');
        $server_version = Gou_Service_Config::getValue('Channel_Version');
        if ($version >= $server_version) $this->emptyOutput(0, '');
        $channel = array();
        list(, $list) = Client_Service_Channel::getList(0, 8, array('status'=>1, 'top'=>1, 'channel_id'=>2), array('sort'=>'DESC'));
        if($list){
        $webroot = Common::getWebRoot();
        foreach ($list as $key=>$val){
              $channel[$key]['id'] = $val['id'];
              $channel[$key]['name'] = html_entity_decode($val['name']);
				$channel[$key]['short_description'] = html_entity_decode($val['description1']);
				$channel[$key]['link'] = Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_IOS, $val);
				$channel[$key]['img'] = Common::getAttachPath() . $val['img'];
				$channel[$key]['color'] = html_entity_decode($val['color']);
				$channel[$key]['is_recommend'] = $val['is_recommend'];
			}
		}
	
		$this->output(0, '', array('data'=>$channel, "version"=>$server_version));
	}
	
	/*
	 *
	* 用户添加频道
	*/
	public function channel_listAction(){
	    $version = $this->getInput('data_version');
	    $server_version = Gou_Service_Config::getValue('Channel_Version');
	
	    if ($version >= $server_version) $this->emptyOutput(0, '');
	    $webroot = Common::getWebRoot();
	    $channelData = array();
	
        list(, $cate) = Client_Service_Channelcate::getsBy(array('status'=>1), array('sort'=>'DESC', 'id'=>'DESC'));
        $time = Common::getTime();
        list(, $channel) = Client_Service_Channel::getsBy(array('status'=>1, 'top'=>0, 'channel_id'=>2,  'start_time'=>array('<', $time), 'end_time'=>array('>', $time)), array('sort'=>'DESC'));
        	$data = $this->_cookData($cate, $channel);
    		foreach ($data as $key=>$val){
    		    if($val['channel']) {
        		    $channelData[] = array(
        		        'type'=>html_entity_decode($val['title']),
        		        'channel'=>$val['channel']
        		    );
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
	        $tmp[$v['cate_id']]['channel'][] = array(
	        'id'=>$v['id'],
	        'name'=>html_entity_decode($v['name']),
	        'link'=>Gou_Service_Channel::getShortUrl(Stat_Service_Log::V_IOS, $v),
	        'img'=>Common::getAttachPath() . $v['img'],
	        'short_description'=>html_entity_decode($v['description1']),
	        'description'=>html_entity_decode($v['description']),
	        );
	    }
	    return $tmp;
	}
	
	/**
	 * 闪屏接口
	 */
	
	public function startAction() {
	    $time = Common::getTime();
	    $channel_id = $this->getInput('channel_id');
	    $type = $this->getInput('width');
	    if(!$channel_id) $channel_id = 3;
	    if(!$type) $type = 540;
	
	    if($type >= 1080) {
	        $type_id = 4;
	    }elseif($type >= 720 && $type < 1080) {
	        $type_id = 3;
	    }elseif($type >= 480 && $type < 720) {
	        $type_id = 2;
	    } else {
	        $type_id = 1;
	    }
	
	    $webroot = Common::getWebRoot();
	    $start = Client_Service_Start::getBy(array('start_time'=>array('<', $time), 'end_time'=>array('>', $time), 'status'=>'1', 'channel_id'=>$channel_id, 'type_id'=>$type_id), array('sort'=>'DESC', 'id'=>'DESC'));
	    $data = array();
	    $data['img'] = Common::getAttachPath() .$start['img'];
	    $data['start_time'] = $start['start_time'];
	    $data['url'] = $start['url'];
	    $data['end_time'] = $start['end_time'];
	    $this->output(0, '', $data);
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
	    $data_version = $this->getInput('data_version');
	
	    //cache version
	    $version = Gou_Service_Config::getValue('Config_Version');
	    
	    if($version <= $data_version) {
	        $this->emptyOutput(0, '');
	    }
	    $data = array();
	    
        //hash
        $apk_taobao_search =  json_decode(Gou_Service_Config::getValue('ios_taobao_search'), true);
        $action = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_IOS, $apk_taobao_search['module_id'],
        $apk_taobao_search['channel_id'], 0, $apk_taobao_search['url'], 'IOS版淘宝搜索', $apk_taobao_search['channel_code']);
        
        
        $data['tel'] = '0755-82583525';
        $data['weixin'] = 'shopping8019';
        $data['qq'] = '298964534';
        $data['about_url'] = Common::getWebRoot().'/about';
        $data['wuliu_url'] = 'http://m.kuaidi100.com/';
        
        $data['keyword'] = Gou_Service_Config::getValue('gou_client_keyword');
        $data['search_url'] = $action.'&keyword=';
        
        $data['gou_url'] = array(
            'http://s.gou.gionee.com',
            'http://s.gou.3gtest.gionee.com',
            'http://ios.gou.3gtest.gionee.com',
            'http://ios.gou.gionee.com',
            'http://gou.gionee.com/amigo',
            'http://gou.3gtest.gionee.com/amigo',
        );
        
	    $this->output(0, '', array('data'=>$data, 'version'=>$version));
	}
	
	/**
	 * get token
	 *
	 */
	public function tokenAction() {
	    $token = $this->getPost('token');
	    $sign = $this->getPost('sign');
	    
	    $uid = Common::getIosUid();
	    
	    if(!$uid || !$token || !$token) $this->output(-1, 'upload fail1');
	
	    if($sign != md5($token.$uid))  $this->output(-1, 'upload fail2');
	    
	    $info = Ios_Service_Token::getByToken($token);
	    if(!$info) {
	        $ret = Ios_Service_Token::addToken(array('token'=>$token, 'uid'=>Common::getIosUid()));
	        if(!$ret) $this->output(-1, 'upload fail3');
	    } 
	    $this->output(0, 'upload success');	    
	}
	
}
