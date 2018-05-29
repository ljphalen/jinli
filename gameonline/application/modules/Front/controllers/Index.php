<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class IndexController extends Front_BaseController{

	/**
	 * index page view
	 */
	public function indexAction() {
		//$this->redirect(Common::getWebRoot().'/Front/index/close');
		//exit;
		//seo
		Common::addSEO($this,'首页');
		//首页轮转广告
		list($ad_total, $ads) = Web_Service_Ad::getCanUseNormalAds(1, 5, array('ad_type'=>1));
		$this->assign('ads', $ads);
		
        //大家都在玩的游戏
		list($playgames_total, $playgames) = Web_Service_Ad::getCanUseNormalAds(1, 10, array('ad_type'=>2,'status'=>1));
		$playgames_list = array();
		foreach ($playgames as $key=>$val){
			$info= Resource_Service_Games::getGameAllInfo(array("id"=>$val['link']));
			if($info) {
				$playgames_list[$key] =  $info;
			}
		}
		$this->assign('playgames', $playgames_list);
		
		//火爆网游 ctype=1 单击 ctype=2 网游
		list($total, $web_games) = Client_Service_Channel::getList(1, 10, array('ctype'=>2, 'game_status'=>1), array('sort'=>'DESC','game_id'=>'DESC'));
		$web_games_list = array();
		if ($web_games) {
			foreach($web_games as $key=>$value) {
				$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
				if ($info) $web_games_list[] = $info;
			}
		}
		$this->assign('webgames', $web_games_list);
		
		//新闻公告 ntype 新闻类型 1 资讯 3 活动
		list(, $news) = Client_Service_News::getList(1, 8, array('ntype'=>array('IN',array(1,3)), 'create_time' => array('<=', Common::getTime()), 'status' => 1));
		$this->assign('news', $news);
		$news_img = array();
		$new_img_flag = false;
		foreach ($news as $val){
			 if($val['thumb_img']){
			 	$news_img =array('id'=>$val['id'],
			 			         'thumb_img'=>$val['thumb_img']
			 			);
			 $new_img_flag = true;	
			 break;
			 }
			 
		}
		$this->assign('news_img', $news_img);
		$this->assign('new_img_flag', $new_img_flag);
	
		//下载排行

		$this->assign('downgames', $this->getDowloadRank());
		
	    //最新游戏 
	    $params = array('status'=> 1,'game_status'=>1,'start_time'=>array('<=',Common::getTime()));	 
	    list(, $newgames) = Client_Service_Taste::getList(1, 10, $params, array('start_time' => 'DESC','game_id' => 'DESC'));
	    $new_game_list = array();
	    foreach($newgames as $key=>$value) {
	    	$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
	    	if ($info) $new_game_list[] = $info;
	    }
	    $this->assign('newgames', $new_game_list);	
	}
	
	/**
	 * get hits
	 * @return boolean
	 */
	public function tjAction(){
		$type = $this->getInput('type');
		if($type == 2){
			$webroot = Common::getWebRoot().'/Front/Index/index/?action=dbclickurl';
			$Shortcut = "[InternetShortcut]
						 URL=$webroot
						 IDList=
						 [{000214A0-0000-0000-C000-000000000046}]
						 Prop3=19,2
						 ";
			Header("Content-type: application/octet-stream");
			$ua = $_SERVER["HTTP_USER_AGENT"];
			$filename = "金立游戏.url";
			$encoded_filename = urlencode($filename);
			$encoded_filename = str_replace("+", "%20", $encoded_filename);
			//兼容IE11
			if(preg_match("/MSIE/", $ua) || preg_match("/Trident\/7.0/", $ua)){
				header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
			} else if (preg_match("/Firefox/", $ua)) {
				header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $filename . '"');
			}
			echo $Shortcut;
			exit;
		}else{
			$url  = $this->getInput('_url');
			$this->redirect($url);
			exit;
		}

	}

	public function qrtjAction(){
		$from = $this->getInput('from');
		$id = $this->getInput('id');
		//获取H5的webroot
		$h5_webroot = Yaf_Application::app()->getConfig()->webroot;
		$url = $h5_webroot.'/game/index/detail?id='.$id;
		if($from == 'baidu'){
			$url = $h5_webroot.'/search/detail/?stype=1&from='.$from.'&id='.$id;
		}			
		$this->redirect($url);
		exit;
	
	}
	
	public function closeAction(){
		//站点关闭维护控制器
	}
	
}