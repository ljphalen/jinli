<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 开服列表
 * Game_GameOpenController
 * @author wupeng
 */
class Game_GameOpenController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Game_GameOpen/index',
	);

	public $perpage = 20;

	public function indexAction() {
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$requestData = $this->getInput(array('game_name', 'start_time', 'end_time', 'group'));
		if (is_null($requestData['game_name']) && is_null($requestData['group']) && is_null($requestData['start_time']) && is_null($requestData['end_time'])) {
            $requestData['group'] = '0~1';
        }
		$searchParams = $this->getSearchParams($requestData);
		if($searchParams !== false) {
		    $sortParams = array('open_time'=>'desc', 'id'=>'asc');
		    list($total, $list) = Game_Service_GameOpen::getPageList($page, $this->perpage, $searchParams, $sortParams);
		}else{
		    $total = 0;
		    $list = array();
		}
		$list = $this->initGameNames($list);
		$this->assign('search', $requestData);
		$this->assign('list', $list);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($requestData) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('open_type', Game_Service_GameOpen::$open_type);
		$this->assign('status', Game_Service_GameOpen::$status);
	}
	
	private function initGameNames($list) {
	    $gameList = Common::resetKey($list, 'game_id');
	    $idList = array_keys($gameList);
	    if(! $idList) {
	        return $list;
	    }
	    $gameNameList = Resource_Service_Games::getGameNameListBy($idList);
	    foreach ($list as $key => $info) {
	        $info['game_name'] = $gameNameList[$info['game_id']];
	        $list[$key] = $info;
	    }
	    return $list;
	}

	private function getSearchParams($search) {
	    $searchParams = array();
		if ($search['game_name']) {
		    $params = array();
		    $params['status'] = 1;
		    $params['name'] = array('LIKE', $search['game_name']);
		    $gameIdList = Resource_Service_Games::getGameIdListBy($params);
		    $gameIdList = Common::resetKey($gameIdList, 'id');
		    if(! $gameIdList) {
                return false;
		    }
		    $searchParams['game_id'] = array('IN', array_keys($gameIdList));
		}
		if ($search['group']) {
		    $dayArr = split('~', $search['group']);
		    $curDate = Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_DAY);
		    if(strlen($dayArr[0]) > 0) {
		        $startTime = Util_TimeConvert::addDays($dayArr[0], $curDate);
		        $search['start_time'] = date('Y-m-d', $startTime);
		    }
		    if(strlen($dayArr[1]) > 0) {
		        $endTime = Util_TimeConvert::addDays($dayArr[1], $curDate);
		        $search['end_time'] = date('Y-m-d', $endTime);
		    }
		}
		if($search['start_time']) {
		    $searchParams['open_time'][] = array('>=', strtotime($search['start_time']));
		}
		if($search['end_time']) {
		    $searchParams['open_time'][] = array('<', strtotime($search['end_time']));
		}
	    return $searchParams;
	}
	
	public function updateClientCacheAction() {
	    Async_Task::execute('Async_Task_Adapter_UpdateOpenListCache', 'update');
	    $this->output(0, '操作成功');
	}

}
