<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Resource_ScoreController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Resource_Score/index',
		'exportUrl' => '/Admin/Resource_Score/export',
		'logsUrl' => '/Admin/Resource_Score/logs',
		'exscoreUrl' => '/Admin/Resource_Score/export',
		'exlogsUrl' => '/Admin/Resource_Score/exportlogs'
	);
	
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		//游戏分类
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1, 'editable'=>0));
		$this->assign('categorys', $categorys);
		$page = intval($this->getInput('page'));
		$info  = $this->getInput(array('gcategory', 'gcoop', 'gstatus', 'gname', 'lscore', 'rscore'));
		//分数范围有效性判断
		if($info['rscore'] && $info['lscore']) {
			if($info['rscore'] < $info['lscore']) $this->output('-1', '分数范围填写错误.');
		}
		$search = $info;
		//处理查询条件
		$pScore = $this->_searchHandle($info);
		list($total, $data) = Resource_Service_Score::getScoreList($page,$this->perpage,$pScore);
		$data = $this->_resultHandle($data);
		$this->assign('data', $data);
		$this->assign("total", $total);
		$this->assign('search', $search);
		//页面链接处理
		$url = $this->actions['listUrl'] .'/?'. http_build_query($info) . '&';
		$exportUrl = $this->actions['exscoreUrl'] .'/?'. http_build_query($info);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('exscore', $exportUrl);
	}
	
	/**
	 * 导出游戏总表评分数据
	 */
	public function exportAction(){
		$info  = $this->getInput(array('gcategory', 'gcoop', 'gstatus', 'gname', 'lscore', 'rscore'));
		$pScore = $this->_searchHandle($info);
		$page = 1;
		//excel-head
		$filename = '游戏评分总表_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('游戏ID', '游戏名称','游戏平均评分','游戏总评分','评分总人数','最后评分时间'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list(, $data) = Resource_Service_Score::getScoreList($page, 10, $pScore);
			if(!$data) break;
			$tmp = array();
			foreach ($data as $key => $value){
				$game = Resource_Service_Games::getBy(array('id'=>$value['game_id']));
				$tmp[] = array($value['game_id'], $game['name'], $value['score'], $value['total'], $value['number'], date('Y-m-d H:i:s', $value['update_time']));
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	public function logsAction(){
		$gid = $this->getInput('id');
		$gscore = $this->_getGameScore($gid);
		$this->assign('gscore', $gscore);
		$page = intval($this->getInput('page'));
		$info  = $this->getInput(array('user', 'nickname', 'lscore', 'rscore','start_time', 'end_time'));
		//分数范围有效性判断
		if($info['rscore'] && $info['lscore']) {
			if($info['rscore'] < $info['lscore']) $this->output('-1', '分数范围填写错误.');
		}
		if($info['start_time'] && $info['end_time']) {
			if($info['end_time'] < $info['start_time']) $this->output(-1, '开始时间不能大于结束时间.');
		}
		$search = $info;
		//查询条件处理
		$params = $this->_searchLogsHandle($info);
		$params['game_id'] = $gid;
		list($total, $data) = Resource_Service_Score::getLogsList($page,$this->perpage, $params);
		$this->assign('data', $data);
		$this->assign("total", $total);
		$this->assign('search', $search);
		//页面链接处理
		$url = $this->actions['logsUrl'] .'/?'. http_build_query($info) . '&';
		$exportUrl = $this->actions['exlogsUrl'] .'/?id='. $gid .'&'. http_build_query($info);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('exlogs', $exportUrl);
	}

	/**
	 *
	 * 导出单游戏用户评分记录
	 *
	 */
	public function exportlogsAction(){
		$gid = $this->getInput('id');
		$info  = $this->getInput(array('user', 'nickname', 'lscore', 'rscore','start_time', 'end_time'));
		$params = $this->_searchLogsHandle($info);
		$params['game_id'] = $gid;
		$page = 1;
		$game = Resource_Service_Games::getBy(array('id'=>$gid));
		//excel-head
		$filename = $game['name'].'_用户评分_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('游戏ID', '游戏名称','用户','IMEI','评分', '机型', '评分来源', '客户端版本', 'Android版本', '评分时间'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list(, $data) = Resource_Service_Score::getLogsList($page,10, $params);
			if(!$data) break;
			$tmp = array();
			foreach ($data as $key => $value){
				$stype = ($value['stype'] == 1) ? '艾米游戏' : (($value['stype'] == 2) ? '游戏大厅' : '其他');
				$tmp[] = array($value['game_id'], $game['name'], $value['user'], $value['imei'], $value['score'], $value['model'], $stype, $value['version'], $value['android'], date('Y-m-d H:i:s', $value['create_time']));
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	private function _getGameScore($gid){
		$tmp = array();
		$game  = Resource_Service_Games::getBy(array('id'=>$gid));
		$score = Resource_Service_Score::getByScore(array('game_id'=>$gid));
		$tmp = array(
				'id'=>$gid,
				'icon' => $game['big_img'] ? $game['big_img'] : $game['mid_img'] ? $game['mid_img'] : $game['img'],
				'name'=>$game['name'],
				'score'=>$score['score'],
				'number'=>$score['number'],
				'status'=>$game['status'],
		);
		return $tmp;
	}
	
	private function _searchHandle($info){
		$params = $pScore = array();
		//检索指定分类的游戏id
		if($info['gcategory']){
			$cgames = Resource_Service_GameCategory::getsBy(array('parent_id'=> $info['gcategory'], 'status' => 1, 'game_status' => 1));
			if ($cgames) {
				$cgames = Common::resetKey($cgames, 'game_id');
				$cgameIds = array_keys($cgames);
				$params['id'] = array('IN', $cgameIds);
			}else {
				$params['id'] = 0;//分类中没有对应的游戏id
			}
		}
		//合作方式检索
		if($info['gcoop'])	$params['cooperate'] = $info['gcoop'];
		//状态检索
		if($info['gstatus']) $params['status'] = $info['gstatus'] - 1;
		//名称检索
		if($info['gname'])	$params['name'] = array('LIKE', trim($info['gname']));
		if($params){
			//根据条件检索游戏id
			$games = Resource_Service_Games::getsBy($params);
			if ($games) {
				$games = Common::resetKey($games, 'id');
				$gameIds = array_keys($games);
				$pScore['game_id'] = array('IN', $gameIds);
			} else {
				$pScore['game_id'] = 0 ; //没有对应的游戏id
			}
		}
		if($info['lscore']!= '') $pScore['score']= array('>=', $info['lscore']);
		if($info['rscore']!= '') $pScore['score']= array('<=', $info['rscore']);
		if($info['rscore']!= '' && $info['lscore']!= '') $pScore['score'] = array(array('>=', $info['lscore']), array('<=', $info['rscore']));
		return $pScore;
	}
	
	private function _searchLogsHandle($info){
		$params =  array();
		//账号检索
		if($info['user']) $params['user'] = trim($info['user']);
		//昵称检索
		if($info['nickname'])	$params['nickname'] = array('LIKE', trim($info['nickname']));
		//评分条件检索
		if($info['lscore'] != '') $params['score']= array('>=', $info['lscore']);
		if($info['rscore'] != '') $params['score']= array('<=', $info['rscore']);
		if($info['rscore'] != '' && $info['lscore']!= '' ) $params['score'] = array(array('>=', $info['lscore']), array('<=', $info['rscore']));
		//时间范围检索
		if($info['start_time']) $params['create_time']= array('>=', strtotime($info['start_time']));
		if($info['end_time']) $params['create_time']= array('>=', strtotime($info['end_time']));
		if($info['start_time'] && $info['end_time']) $params['create_time'] = array(array('>=', strtotime($info['start_time'])), array('<=', strtotime($info['end_time'])));
		return $params;
	}
	
	private function _resultHandle($data){
		$tmp = array();
		foreach ($data as $key => $value){
			$game = Resource_Service_Games::getBy(array('id'=>$value['game_id']));
			$tmp[$key]= array(
					'gid' => $value['game_id'],
					'icon' => $game['big_img'] ? $game['big_img'] : $game['mid_img'] ? $game['mid_img'] : $game['img'],
					'name'=> $game['name'],
					'number' => $value['number'],
					'score' => $value['score'],
					'uptime' => date('Y-m-d H:i:s', $value['update_time']),
					'status' => $game['status']
			);
		}
		return $tmp;
	}
	
}
