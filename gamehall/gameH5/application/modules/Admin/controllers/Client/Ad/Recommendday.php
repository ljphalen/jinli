<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author wupeng
 *
 */
class Client_Ad_RecommenddayController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Recommendday/index',
		'addUrl' => '/Admin/Client_Ad_Recommendday/add',
		'editUrl' => '/Admin/Client_Ad_Recommendday/edit',
		'addPostUrl' => '/Admin/Client_Ad_Recommendday/addPost',
		'editPostUrl' => '/Admin/Client_Ad_Recommendday/editPost',
		'deleteUrl' => '/Admin/Client_Ad_Recommendday/delete',
		'batchUpdateUrl'=>'/Admin/Client_Ad_Recommendday/batchUpdate',
		'gameNameUrl' => '/Admin/Client_Ad_Recommendday/gameName',
		'recommendUrl' => '/Admin/Client_Ad_Recommendnew/index',
		'closeUrl' => '/Admin/Client_Ad_Recommendnew/close',
	);
	
	public $perpage = 20;
	
	/**
	 * 列表
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$search = $this->getInput(array('title', 'start_time', 'end_time', 'status'));
		$params = $this->getSearchParams($search);
		list($total, $list) = Game_Service_RecommendDay::getList($page, $this->perpage, $params, array('sort'=>'desc', 'id'=>'desc'));
		$this->setGameIcon($list);
		$this->assign('search', $search);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**查询参数*/
	private function getSearchParams($search) {
	    $params = array();
	    if ($search['title']) $params['title'] = array('like', $search['title']);
	    if ($search['start_time']) $params['start_time'] = array('>=', strtotime($search['start_time']));
	    if ($search['end_time'] && $search['start_time']) $params['end_time'] = array('<=', strtotime($search['end_time']));
	    if ($search['status']) $params['status'] =  intval($search['status']) -1;
	    return $params;
	}
	
	/**设置游戏图标*/
	private function setGameIcon(&$list) {
	    foreach ($list as $key => $value) {
	        $game = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
	        $value['gameIcon'] = $game['img'];
	        $list[$key] = $value;
	    }
	}
	
	/**
	 * 添加
	 */
	public function addAction() {
	        
	}
	
	/**
	 * 添加内容提交
	 */
	public function addPostAction() {
		$data = $this->getInput(array('title', 'game_id', 'content', 'start_time', 'end_time', 'status', 'sort'));
        $info = $this->cookData($data);
		$info['game_status'] = Resource_Service_Games::STATE_ONLINE;
		$info['create_time'] = Common::getTime();
        $result = Game_Service_RecommendDay::addRecommendDay($info);
        if (!$result){
        	$this->output(- 1, '操作失败');
        }
        Game_Service_RecommendDay::updateIndexRecommendCacheData();
        $this->output(0, '操作成功');
	}
	
	/**
	 * 编辑
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Game_Service_RecommendDay::getRecommendDay(intval($id));
		$game = Resource_Service_Games::getGameAllInfo(array('id'=>$info['game_id']));
		$info['game_name'] = $game['name'];
		$this->assign('info', $info);
	}
	
	/**
	 * 编辑内容提交
	 */
	public function editPostAction() {
		$id = intval($this->getInput('id'));
		$info = $this->getInput(array('id', 'title', 'game_id', 'content', 'start_time', 'end_time', 'status', 'sort'));
		$info = $this->cookData($info);
		
		$editInfo = Game_Service_RecommendDay::getRecommendDay($id);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');

		$updateParams = $this->getUpdateParams($info, $editInfo);
		if (count($updateParams) >0) {
		    $ret = Game_Service_RecommendDay::updateRecommendDay($updateParams, $id);
		    if (!$ret) $this->output(-1, '操作失败');
		}
		Game_Service_RecommendDay::updateIndexRecommendCacheData();
		$this->output(0, '操作成功.');
	}
	
	/**检查数据是否需要更新*/
	private function getUpdateParams($data, $dbInfo) {
	    $updateParams = array();
	    foreach ($data as $key => $value) {
	        if ($value == $dbInfo[$key])
	            continue;
	        $updateParams[$key] = $value;
	    }
	    return $updateParams;
	}
	
	/**
	 * 删除
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Game_Service_RecommendDay::getRecommendDay($id);
		if (!$info) $this->output(-1, '无法删除');
		$result = Game_Service_RecommendDay::deleteRecommendDay($id);
		if (!$result) $this->output(-1, '操作失败');
		Game_Service_RecommendDay::updateIndexRecommendCacheData();
		$this->output(0, '操作成功');
	}
	
	/**
	 * 批量操作
	 */
	public function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		sort($info['ids']);
		if($info['action'] =='delete') {
			     $ret = Game_Service_RecommendDay::deleteRecommendDayList($info['ids']);
		}elseif ($info['action'] =='sort') {
			     $ret = Game_Service_RecommendDay::updateListSort($info['ids'], $info['sort']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		Game_Service_RecommendDay::updateIndexRecommendCacheData();
		$this->output('0', '操作成功.');
	}
	
	/**查询游戏名称*/
	public function gameNameAction() {
		 $id = $this->getInput('id');
        $game = Resource_Service_Games::getGameAllInfo(array('id'=>$id));
        if(!$game) {
              $this->output(-1, '游戏不存在.');
        }
		$this->output('0', $game['name']);
	}
	
	/**
	 * 检查数据有效性
	 */
	private function cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
		if(!$info['game_id']) {
		        $this->output(-1, '游戏ID不能为空.');
		}
        $game = Resource_Service_Games::getGameAllInfo(array('id'=>$info['game_id']));
        if(!$game) {
              $this->output(-1, '游戏不存在.');
        }
		if(!isset($info['status'])) $this->output(-1, '状态不能为空.');
		if(!isset($info['sort'])) $this->output(-1, '排序为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime(date('Y-m-d H:00:00', strtotime($info['start_time'])));
		$info['end_time'] = strtotime(date('Y-m-d H:00:00', strtotime($info['end_time'])));
		if($info['start_time'] >= $info['end_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		$counts = Game_Service_RecommendDay::getShowGameCountsByTime($info['start_time'], $info['end_time'], $info['id']);
		if($info['status'] == 1 && $counts > 0) {
		    $this->output(-1, '同一个时间段不能出现两个及以上游戏');
		}
		return $info;
	}

}
