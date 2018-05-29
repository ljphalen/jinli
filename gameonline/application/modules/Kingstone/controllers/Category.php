<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Kingstone_BaseController{
	
	public $actions = array(
		'listUrl' => '/kingstone/category/index',
		'detailUrl' => '/kingstone/category/detail/',
		'indexlUrl' => '/kingstone/index/detail/',
		'tjUrl' => '/kingstone/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		$this->assign('source', $this->getSource());
		$this->assign('cache', Game_Service_Config::getValue('game_client_cache'));
		
	}
	
	public function detailAction(){
		$id = intval($this->getInput('id'));
		
		$intersrc = $this->getInput('intersrc');
		$page = intval($this->getInput('page'));
		$sp = $this->getInput('sp');
		
		if ($page < 1) $page = 1;
		$params = array('status'=>1);
		$resource_games = array();
		
		//get games list
		if($id == '100'){              //所有分类
			$orderBy = array('id'=>'DESC');
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$this->assign('title', '全部游戏');
		} else if($id == '101'){      //最新游戏
			$orderBy = array('online_time'=>'DESC');
			$limit = Game_Service_Config::getValue('game_rank_newnum');
			$this->perpage = min($limit, $this->perpage);
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$total = $limit;
			$this->assign('title', '最新游戏');
		} else {
			$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
			$params['category_id'] = $id;
			$params['game_status'] = 1;
			list($total, $games) = Resource_Service_Games::getIdxGamesByCategoryId($page, $this->perpage, $params, $orderBy);
		}
		
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion(); 
		
		foreach($games as $key=>$value) {
			
			if ($value['game_id']) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
			} else {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			}

			if ($info) {
				if ($checkVer >= 2) {
					//增加评测信息
					$info['pc_info'] = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
					//增加礼包信息
					$info['gift_info'] = Client_Service_IndexAdI::haveGiftByGame($info['id']);
				}
				$resource_games[] = $info;
			}
		} 
		
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('checkver', $checkVer);
		$this->assign('sp', $sp);
		$this->assign('resource_games', $resource_games);
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
	}
	
	/**
	 * subject json list
	 */
	public function moreCtAction(){
		$id = intval($this->getInput('id'));
		
		$intersrc = $this->getInput('intersrc');
		$page = intval($this->getInput('page'));
	    
		if ($page < 1) $page = 1;
		$params = array('status'=>1);
		$resource_games = array();
		
		//get games list
		if($id == '100'){              //所有分类
			$orderBy = array('id'=>'DESC');
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$this->assign('title', '全部游戏');
		} else if($id == '101'){      //最新游戏
			$orderBy = array('online_time'=>'DESC');
			$limit = Game_Service_Config::getValue('game_num');
			$this->perpage = min($limit, $this->perpage);
			list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $params, $orderBy);
			$total = Game_Service_Config::getValue('game_rank_newnum');
			$this->assign('title', '最新游戏');
		} else {
			$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
			$params['category_id'] = $id;
			$params['game_status'] = 1;
			list($total, $games) = Resource_Service_Games::getIdxGamesByCategoryId($page, $this->perpage, $params, $orderBy);
		}
		
		$temp = array();
		$webroot = Common::getWebRoot();
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		
		$i = 0;
		foreach($games as $key=>$value) {
			
			$num = $i + (($page - 1) * $this->perpage);
			if ($num >= $total && $id == '101') break;
			
			if ($value['game_id']) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
			} else {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			}
			$intersrc = 'CATEGORY'.$id.'_GID'.$info['id'];
			$href = urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $info['id'].'&pc=1&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			
			if ($checkVer >= 2) {
				//加入评测链接
				$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($info['id']);
				$evaluationUrl = '';
				if ($evaluationId) {
					$evaluationUrl = ',评测,' . $webroot . '/kingstone/evaluation/detail/?id=' . $evaluationId . '&pc=3&intersrc=' . $intersrc . '&t_bi=' . $this->getSource();
				}
				//附加属性处理
				$attach = array();
				if ($evaluationId)	array_push($attach, '评');
				if (Client_Service_IndexAdI::haveGiftByGame($info['id'])) array_push($attach, '礼');
			}
			
			
			$data = array(
						'id'=>$info['id'],
						'name'=>$info['name'],
						'resume'=>$info['resume'],
						'size'=>$info['size'].'M',
						'link'=>Common::tjurl($this->actions['tjUrl'], $info['id'], $intersrc, $info['link']),
						'alink'=>urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $info['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
						'img'=>urldecode($info['img']),
						'profile' => $info['name'].','.$href.','.$info['id'].','.$info['link'].','.$info['package'].','.$info['size'].','.'Android'.$info['min_sys_version_title'].','.$info['min_resolution_title'].'-'.$info['max_resolution_title'],
			);

			if ($checkVer >= 2) {
				//js a 标签 data-infpage 参数数据
				$data_info = '游戏详情,'.$href.','.$info['id'];
				$data['profile'] = $evaluationId ? $data_info . $evaluationUrl : $data_info;
				$data['attach'] = ($attach) ? implode(',', $attach) : '';
				$data['device'] = $info['device'];
				$data['data-type'] = 1;
			}
			$temp[] = $data;
			
			$i++;
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('id', $id);
	}
}
