<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class EvaluationController extends Game_BaseController{
	
	public $actions = array(
		'listUrl' => '/Evaluation/index',
		'detailUrl' => '/Evaluation/detail/',
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
		list($total, $news) = Client_Service_News::getUseNews($page, $this->perpage, array('ntype' => 2, 'status'=>1));
		$this->assign('news', $news);
		
		//客户端
		$configs = Game_Service_Config::getAllConfig();
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('news', $news);
		$this->assign('configs', $configs); 
		$this->assign('source', $this->getSource());
		
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction() {
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		list($total, $news) = Client_Service_News::getUseNews($page, $this->perpage, array('ntype' => 2, 'status'=>1));
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($news as $key=>$value) {
			$t_id = 'PC'.$value['id'];
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$t_id);
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], $t_id, $href);
			$temp[$key]['img'] = ($value['ctype'] == 2 ? urldecode(Common::getAttachPath().$value['thumb_img']) : $value['thumb_img']);
			$temp[$key]['resume'] = $value['resume'];
			$temp[$key]['profile'] = $value['title'].','.urldecode($webroot.$this->actions['listUrl']. '?id=' . $value['id'].'&intersrc='.$t_id.'&t_bi='.$this->getSource());
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
		if(!$intersrc) {
			$intersrc = 'PC'.$info['out_id'];
		}
		//通过游戏ID 获取游戏信息
		$game_info = Resource_Service_Games::getResourceGames($info['game_id']);
		//通过游戏ID 获取游戏版本
		$game_version = Resource_Service_Games::getGameVersionInfo($info['game_id']);

		$this->assign('configs', $configs);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
		$this->assign('info', $info);
		$this->assign('game_info', $game_info);
		$this->assign('game_version', $game_version);
		$this->assign('out_link', $this->out_link);
	}
}
