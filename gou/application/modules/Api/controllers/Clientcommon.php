<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 * 客户端公用接口
 *
 */
class ClientcommonController extends Api_BaseController {
	public $perpage = 10;
	public $actions = array(
				'startUrl'=>'/api/clientcommon/start',
				'redirectUrl'=>'/api/clientcommon/redirect',
				'tjUrl'=>'/index/tj'
	);
	
	/**
	 * 1 => '预装版',2 => '渠道版',3 => '市场版'
	 */
	public $channels = array(
			1=>'apk',
			2=>'channel',
			3=>'market'
	);
	
	/**
	 * 闪屏接口
	 */
	
	public function startAction() {
		$time = Common::getTime();
		$channel_id = $this->getInput('channel_id');
		$type = $this->getInput('width');
		if(!$channel_id) $channel_id = 3;
		if(!$type) $type = 540;
		
		
		if($type >= 1440) {
		    $type_id = 5;
		}elseif($type >= 1080 && $type < 1440) {
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
		if(!empty($start['url']))$data['url'] = sprintf('%s%s?id=%d',$webroot,$this->actions['redirectUrl'],$start['id']);
		$data['end_time'] = $start['end_time'];
		$data['tab'] = Common::getConfig('tabConfig', $this->channels[$channel_id]);
		$this->output(0, '', $data);
	}

    public function redirectAction(){
        $id = $this->getInput('id');
        $row = Client_Service_Start::getBy(array('id'=>$id));
        Client_Service_Start::updateTJ($id);
        $url = '/';
        if($row['url'])$url=html_entity_decode($row['url']);
        header("Location:$url");
    }
    
	/**
	 * 淘热卖跳转地址 老的
	 */
	public function taobaourlAction() {
		$model = $this->getInput('model');
		
		$url = Client_Service_Taobaourl::getBy(array('model'=>$model));
		
		$webroot = Common::getWebRoot();
		$redirect_url = str_replace('gou', 'apk.gou', $webroot).'/tbhot?model='.$model;
		$this->redirect($redirect_url);
		
		/* if($model && $url) {
			Client_Service_Taobaourl::updateTJ($url['id']);
			$this->redirect(html_entity_decode($url['url']));
		} else {
			$this->redirect('http://m.taobao.com');
		} */
		
	}
	
	/**
	 * 淘热卖跳转地址 新的
	 */
	public function tbhoturlAction() {
		$model = $this->getInput('model');
	
		$url = Client_Service_Taobaourl::getBy(array('model'=>$model));
	
		if($model && $url) {
			Client_Service_Taobaourl::updateTJ($url['id']);
			$this->redirect(html_entity_decode($url['url']));
		} else {
			$this->redirect('http://m.taobao.com');
		}
	
	}
	
	
	/**
	 * sina weibo 登录授权
	 */
	public function weiboAuthAction() {
		$this->redirect('https://open.weibo.cn/oauth2/authorize?client_id=3404693499&redirect_uri=https://api.weibo.com/oauth2/default.html&scope=email%2Cdirect_messages_read%2Cdirect_messages_write%2Cfriendships_groups_read%2Cfriendships_groups_write%2Cstatuses_to_me_read%2Cinvitation_write%2Cfollow_app_official_microblog&response_type=code&display=mobile&packagename=com.gionee.client&key_hash=b49792a5687b641492e10a29152f7454');
	}
}
