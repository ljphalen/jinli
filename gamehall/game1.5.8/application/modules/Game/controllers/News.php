<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class NewsController extends Game_BaseController{
	
	public $actions = array(
		'listUrl' => '/News/index',
		'detailUrl' => '/News/detail/',
		'indexlUrl' => '/index/detail/',
		'tjUrl' => '/index/tj'
	);

	public $perpage = 10;
	public $out_link = "http://wap.fwan.cn/info/infos/";

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list($total, $news) = Client_Service_News::getUseNews($page, $this->perpage, array('ntype' => array('IN',array(1,3)), 'create_time' => array('<=', Common::getTime()), 'status'=>1));
		$this->assign('news', $news);
		
		//推荐游戏
		list(, $games) = Game_Service_Ad::getCanUseNormalAds(1, 4, array('ad_type'=>2));		
		$this->assign('games', $games);
		
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('news', $news);
		$this->assign('configs', $configs); 
		$this->assign('source', $this->getSource());
		$webroot = Common::getWebRoot();
		$this->assign('tjUrl', $webroot.$this->actions['tjUrl']);
		
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list($total, $news) = Client_Service_News::getUseNews($page, $this->perpage, array('ntype' => array('IN',array(1,3)), 'create_time' => array('<=', Common::getTime()), 'status'=>1));
		$webroot = Common::getWebRoot();
		$attachPath = Common::getAttachPath();
		$temp = array();
		$type = array(1 => '资讯', 3 => '活动');
		foreach($news as $key=>$value) {
			if($value['ntype']==1){
				$t_id = 'ZX'.$value['id'];
			}
			if($value['ntype']==3){
				$t_id = 'HD'.$value['id'];
			}
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$t_id);
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = "【{$type[$value['ntype']]}】". $value['title'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], $t_id, $href);
			if(empty($value['thumb_img'])) {
				$temp[$key]['img'] = '';
			} elseif((strpos($value['thumb_img'],'http://') !== false)){
				$temp[$key]['img'] = $value['thumb_img'];
			} else{
				$temp[$key]['img'] = $attachPath . $value['thumb_img'];
			}
			$temp[$key]['resume'] = $value['resume'];
			$temp[$key]['profile'] = "【{$type[$value['ntype']]}】". $value['title']. ','.urldecode($webroot.$this->actions['listUrl']. '?id=' . $value['id'].'&intersrc='.$t_id.'&t_bi='.$this->getSource());
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$configs = Game_Service_Config::getAllConfig();
		$info = Client_Service_News::getNews($id);
		$type = array(1 => 'ZX', 3 => 'HD');
		if(!$intersrc) {
			$intersrc = $type[$info['ntype']] . $info['out_id'];
		}
		
		//推荐游戏
		list(, $games) = Game_Service_Ad::getCanUseNormalAds(1, 4, array('ad_type'=>2));
		$this->assign('games', $games);
		
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		$game_version = Resource_Service_Games::getGameVersionInfo($info['game_id']);
		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('info', $info);
		$this->assign('ntype', $type);
		$this->assign('game_info', $game_info);
		$this->assign('game_version', $game_version);
		$this->assign('out_link', $this->out_link);
	}
}
