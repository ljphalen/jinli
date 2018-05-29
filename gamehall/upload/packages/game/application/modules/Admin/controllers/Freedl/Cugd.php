<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 广东联通免流量内容库.
 * @author fanch
 *
 */
class Freedl_CugdController extends Admin_BaseController {
	
	public $actions = array(
			'listUrl' => '/Admin/Freedl_Cugd/index',
			'addUrl'=>'/Admin/Freedl_Cugd/add',
			'addPostUrl'=>'/Admin/Freedl_Cugd/add_post',
			'onUrl' => '/Admin/Freedl_Cugd/on',
			'offUrl' => '/Admin/Freedl_Cugd/off',
			'repeatUrl' => '/Admin/Freedl_Cugd/repeat',
	);
	
	public $perpage = 20;
	
	//合作方式1:联运，2:普通
	public $cooperates = array(
			1 => '联运',
			2 => '普通',
	);
	//联通资源状态
	public $custatus = array(
			1 => '审核中',
			2 => '审核通过',
			3 => '审核不通过',
			4 => '上线',
			5 => '下线'
	);
	
	/**
	 * 广东联通免流量内容库列表
	 */
	public function indexAction() {
		$this->assign('gcategory', $this->getGameCategory());
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$search = $this->getInput(array('title', 'gid', 'gcategory', 'cooperate', 'status', 'custatus'));
		$response = $params = array();
		//游戏id搜索
		$gids = $this->_searchGids($search);
		if($gids==0){
			$params['game_id'] = 0;
		}elseif(is_array($gids) && (count($gids) != 0)){
			$params['game_id'] = $gids;
		}
		//上线状态搜索
		if($search['status']){
			$status = $search['status'] - 1;
			if($status == 0){
				$params['query'] = "(`cu_status` not in (2, 4) OR `game_status` != 1)" ;
			}else{
			$params['cu_status'] = array('IN', array(2, 4)) ;
			$params['game_status']= 1 ;
			}
		}
		//联通资源状态
		if($search['custatus'])	$params['cu_status'] = $search['custatus'];
		
		list($total, $cugames) = Freedl_Service_Cugd::getList($page, $this->perpage, $params);
		//输出数据拼接
		if($total) {
			foreach ($cugames as $value) {
				$game = Resource_Service_Games::getBy(array('id'=>$value['game_id']));
				$response[] =array(
						'id'=>$value['id'],
						'gameid'=>$value['game_id'],
						'appid'=>$value['app_id'],
						'contentid'=>$value['content_id'],
						'icon' => $game['big_img'] ? $game['big_img']: ($game['mid_img'] ? $game['mid_img'] : $game['img']),
						'name'=> $game['name'],
						'version'=> $value['version'],
						'custatus'=> $value['cu_status'],
						'gamestatus' => $value['game_status'],
						'cuonlinetime' => $value['cu_online_time'],
						'createtime' => $value['create_time']
				);
			}
		}
		$this->assign('data', $response);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('custatus', $this->custatus);
		$this->assign("cooperates", $this->cooperates);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 添加游戏操作页面
	 */
	public function addAction(){
		
	}
	
	public function add_postAction(){
		$gid = $this->getInput('gid');
		if(!$gid) $this->output('-1', '游戏id不能为空.');
		$game = Resource_Service_Games::getGameAllInfo(array('id'=>$gid), false, false);
		if(!$game) $this->output('-1', '添加的游戏未找到.');
		$request = Freedl_Service_Cugd::fillData($game);
		$result = Api_Freedl_Cu_Gd::addcontent($request);
		if($result['errcode']) $this->output('-1', '联通免流量游戏添加操作失败.');
		//构造联通免流量资源库数据
		$data = array(
					'game_id'=> $gid,
					'app_id' => $game['appid'],
					'version' => $game['version'],
					'version_code' => $game['version_code'],
					'content_id' => $result['resultData']['contentId'],
					'cu_status' => 1,
					'game_status' =>1,
					'create_time' => Common::getTime(),
		);
		//增加记录到免流量资源表
		$ret = Freedl_Service_Cugd::addCugd($data);
		if(!$ret)$this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 内容库是上线状态，联通资源审核通过|联通资源下线才能执行上线操作
	 */
	public function onAction() {
		$id = $this->getInput('id');
		$cuData = Freedl_Service_Cugd::getBy(array('id'=>$id));
		//联通免流量游戏资源下线状态是不能执行下线操作
		if($cuData['cu_status'] != '5') $this->output('-1', '目前免流量游戏不可以执行上线操作.');
		if($cuData['game_status'] != '1') $this->output('-1', '目前免流量游戏不可以执行上线操作.');
		//重新提交数据到联通
		$game = Resource_Service_Games::getGameAllInfo(array('id'=>$cuData['game_id']), false, false);
		$request = Freedl_Service_Cugd::fillData($game);
		$ret = Api_Freedl_Cu_Gd::updatecontent($cuData['game_id'], $request);
		if($ret['errcode']) $this->output('-1', '联通资源上线操作失败.');
		//联通资源状态更新
		$up['cu_status'] = 1;
		$ret = Freedl_Service_Cugd::updateCugd($up, array('id'=>$id));
		if(!$ret)$this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 内容库是上线状态，联通资源上线才能执行上线操作
	 */
	public function offAction() {
		$id = $this->getInput('id');
		$cuData = Freedl_Service_Cugd::getBy(array('id'=>$id));
		//联通免流量游戏资源不是审核中或上线状态不能执行下线操作
		if(!in_array($cuData['cu_status'], array('2','4'))) $this->output('-1', '目前免流量游戏不可以执行下线操作.');
		$ret = Api_Freedl_Cu_Gd::updatestatus($cuData['game_id'], 'invalid');//下线
		if($ret['errcode']) $this->output('-1', '联通资源下线操作失败.');
		//联通资源状态更新
		$up['cu_status'] = 5;
		$ret = Freedl_Service_Cugd::updateCugd($up, array('id'=>$id));
		if(!$ret)$this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 审核中，审核不通过， 上线操作才能重新提交
	 */
	public function repeatAction() {
		$id = $this->getInput('id');
		$cuData = Freedl_Service_Cugd::getBy(array('id'=>$id));
		//联通免流量游戏资源下线或审核不通过才能执行重新提交
		if($cuData['game_status'] != '1') $this->output('-1', '目前免流量游戏不可以执行重新提交操作.');
		$game = Resource_Service_Games::getGameAllInfo(array('id'=>$cuData['game_id']), false, false);
		$request = Freedl_Service_Cugd::fillData($game);
		$ret = Api_Freedl_Cu_Gd::updatecontent($cuData['game_id'], $request);
		if($ret['errcode']) $this->output('-1', '联通资源重新提交操作失败.');
		//联通资源状态更新为审核中
		$upData = array(
				'version' => $game['version'],
				'version_code' => $game['version_code'],
				'cu_status' => 1,
				'game_status' => 1,
				'create_time' => Common::getTime()
		);
		$ret = Freedl_Service_Cugd::updateCugd($upData, array('id'=>$id));
		if(!$ret)$this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	
	/**
	 * 多条件搜索游戏id处理(16种)
	 */
	private function _searchGids($search){
		$gidsByGcategory = $gidsByCooperate = $gidsByGname = $gidsByGid = $gids= array();
		$categoryFlag = $search['gcategory'] ? '1' : '0';
		$cooperateFlag = $search['cooperate'] ? '1' : '0';
		$titleFlag = $search['title'] ? '1' : '0';
		$gidFlag = $search['gid'] ? '1' : '0';
		$flag = strval($categoryFlag. $cooperateFlag. $titleFlag. $gidFlag);
		switch ($flag){
			case '1000':
				//1000-仅选择分类
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory']));
				if(!$gids) return 0 ;
				return array('IN', $gids);
				break;
			case '0100':
				//0100-仅选择合作方式
				$gids = $this->searchGame(array('cooperate'=> $search['cooperate']));
				if(!$gids) return 0 ;
				return array('IN', $gids);
				break;
			case '0010':
				//0010-仅选择名称
				$gids = $this->searchGame(array('name'=>array('LIKE', trim($search['title']))));
				if(!$gids) return 0 ;
				return array('IN', $gids);
				break;
			case '0001':
				//0001-仅选择游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid'])));
				if(!$gids) return 0 ;
				return array('IN', $gids);
				break;
			case '1100':
				//1100-选择分类+合作方式
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory']));
				if(!$gids) return 0;
				$gids = $this->searchGame(array('cooperate'=> $search['cooperate'], 'id'=> array('IN', $gids)));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;
			case '1010':
				//1010-选择分类+名称
				$gids = $this->searchGame(array('name'=>array('LIKE', trim($search['title']))));
				if(!$gids) return 0;
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory'], 'game_id'=> array('IN', $gids)));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;
			case '1001':
				//1001-选择分类+游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid'])));
				if(!$gids) return 0;
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory'], 'game_id'=> array('IN', $gids)));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;
			case '0110':
				//0110-选择合作方式+名称
				$gids = $this->searchGame(array('name'=>array('LIKE', trim($search['title']), 'cooperate'=> $search['cooperate'])));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;
			case '0101':
				//0101-选择合作方式+游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid']), 'cooperate'=> $search['cooperate']));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;
			case '0011':
				//0011-选择名称+游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid']),'name'=>array('LIKE', trim($search['title']))));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;
			case '1110':
				//111-选择分类+合作方式+名称
				$gids = $this->searchGame(array('name'=>array('LIKE', trim($search['title'])), 'cooperate'=> $search['cooperate']));
				if(!$gids) return 0;
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory'], 'game_id'=>array('IN', $gids)));
				return array('IN', $gids);
				break;
			case '1101':
				//1101-选择分类+合作方式+游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid']), 'cooperate'=> $search['cooperate']));
				if(!$gids) return 0;
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory'], 'game_id'=>array('IN', $gids)));
				return array('IN', $gids);
				break;
			case '1011':
				//111-选择分类+名称+游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid']), 'name'=>array('LIKE', trim($search['title']))));
				if(!$gids) return 0;
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory'], 'game_id'=>array('IN', $gids)));
				return array('IN', $gids);
				break;
			case '0111':
				//111-合作方式+名称+游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid']), 'name'=>array('LIKE', trim($search['title'])), 'cooperate'=> $search['cooperate']));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;
			case '1111':
				//1111-选择分类+合作方式+名称+游戏id
				$gids = $this->searchGame(array('id'=>trim($search['gid']), 'name'=>array('LIKE', trim($search['title'])), 'cooperate'=> $search['cooperate']));
				if(!$gids) return 0;
				$gids = $this->searchGcategory(array('parent_id' => $search['gcategory'], 'game_id'=>array('IN', $gids)));
				if(!$gids) return 0;
				return array('IN', $gids);
				break;			
			default:
				//0000-搜索全部的
				return array();
		}
	}
	
	
	/**
	 * 按条件检索游戏资源数据
	 * @param array $parms
	 */
	private function searchGame($params){
		$ret = array();
		$data = Resource_Service_Games::getsBy($params);
		if($data){
			$data =Common::resetKey($data, 'id');
			$ret = array_unique(array_keys($data));
		}
		return $ret;
	}
	
	/**
	 * 检索条件检索游戏分类对应的游戏id
	 * @param array $params
	 */
	private function searchGcategory($params){
		$ret = array();
		$data = Resource_Service_Games::getIdxResourceCategoryGames($params);
		if($data){
			$data =Common::resetKey($data, 'game_id');
			$ret = array_unique(array_keys($data));
		}
		return $ret;
	}
	
	/**
	 * 获取游戏所有分类
	 */
	private function getGameCategory(){
		$tmp = array();
		$categorys = Resource_Service_Attribute::getsBy(array('id'=>array('NOT IN',array(100,101)),'at_type'=>1, 'editable'=>0, 'status'=>1));
		if(empty($categorys)) return $tmp;
		foreach ($categorys as $value){
				$tmp[]= array($value['id'], $value['title']);
		}
		return $tmp;
	}
}
