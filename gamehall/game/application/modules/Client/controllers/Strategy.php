<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class StrategyController extends Client_BaseController {
	
	public $actions =array(
				'index' => '/client/strategy/index',
				'detailUrl' => '/client/index/detail/',
			    'styDetailUrl' => '/client/strategy/detail/',
			    'tjUrl'=>'/client/index/tj'
			);
	public $perpage = 10;
	/**
	 * subject list
	 */
	public function indexAction() {
		$sp = $this->getInput('sp');
		$id = $this->getInput('id');
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$search['status'] = 1;
		$search['ntype'] = 4;
		$search['game_id'] = $id;
		$search['create_time']  = array('<=', Common::getTime());
		list($total, $strategys) = Client_Service_News::getList(1, $this->perpage, $search, array('sort'=>'DESC','create_time'=>'DESC','id' =>'DESC'));
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('strategys', $strategys);
		$this->assign('page', $page);
		$this->assign('source', $this->getSource());
		//客户端
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		//游戏数据
		$gameData = Resource_Service_GameData::getBasicInfo($id);
		$title = $gameData ? html_entity_decode($gameData['name'], ENT_QUOTES) . '-攻略' : '';
		
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
		$this->assign('checkver', $checkVer);
		$this->assign('sp', $sp);
		$this->assign('id', $id);
		Common::addSEO($this, $title);
	}
	
	public function moreAction() {
	    $page = intval($this->getInput('page'));
	    $id = $this->getInput('id');
	    $sp = $this->getInput('sp');
	    //判断游戏大厅版本
	    $checkVer = $this->checkAppVersion();
		if ($page < 1) $page = 1;
	    
		$search['status'] = 1;
		$search['ntype'] = 4;
		$search['game_id'] = $id;
		$search['create_time']  = array('<=', Common::getTime());
		list($total, $strategys) = Client_Service_News::getList($page, $this->perpage, $search, array('sort'=>'DESC','create_time'=>'DESC','id' =>'DESC'));
		
		//游戏数据
		$gameData = Resource_Service_GameData::getBasicInfo($id);
		$title = $gameData ? html_entity_decode($gameData['name'], ENT_QUOTES) . '-攻略' : '';
		
		$webroot = Common::getWebRoot();
		$temp = array();
		foreach($strategys as $key=>$value) {
			$intersrc = 'detail'.$id.'_strategy'.$value['id'];
			$href = urldecode($webroot.$this->actions['styDetailUrl']. '?id=' . $value['id'].'&pc=5&intersrc='.$intersrc.'&t_bi='.$this->getSource().'&sp='.$sp);
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['resume'] = $value['resume'];
			$temp[$key]['link'] = Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $webroot.'/strategy/detail/?id='.$value['id'].'&intersrc='.$intersrc.'&sp='.$sp);
			$temp[$key]['img'] = ($value['thumb_img'] ?  urldecode(Common::getAttachPath().$value['thumb_img']) : '');
			$temp[$key]['create_time'] = date('Y-m-d',$value['create_time']);
			$temp[$key]['data-infpage'] = $title . ','.$href;
			if($checkVer >= 2){
				$temp[$key]['data-type'] = 1;
				$temp[$key]['viewType'] = 'WebView';
			}
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		
		$info = Client_Service_News::getNews(intval($id));
		
		//游戏数据
		$gameData = Resource_Service_GameData::getBasicInfo($info['game_id']);
		$title = $gameData ? html_entity_decode($gameData['name'], ENT_QUOTES) . '-攻略' : '';
		
		$this->assign('info', $info);
		Common::addSEO($this, $title);
	}
}
