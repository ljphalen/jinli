<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_SubjectController extends Api_BaseController {
	public $perpage = 10;
		
    /**
     * 客户端列表专题接口
     */
    public function subjectInfoAction() {
    	$id = intval($this->getInput('id'));		
		$info = Client_Service_Subject::getSubject($id);
		$webroot = Common::getWebRoot();
		$href = urldecode($webroot.'/Api/Local_Subject/subjectList?id=' . $id);
		$hdUrl = ($info['hdinfo']) ? urldecode($webroot.'/client/subject/hdinfo/?id=' . $id) : '';
		$tmp = array(
			'viewType'=>Game_Api_Util_RecommendListUtil::getViewType(Game_Service_Util_Link::LINK_SUBJECT),
			'topicImageUrl'=>Common::getAttachPath(). $info['img'],
			'topicDescript'=>strip_tags(html_entity_decode($info['resume'], ENT_QUOTES)),
// 			'topicDescript'=>html_entity_decode($info['resume'], ENT_QUOTES),
			'topicTime'=>date("Y-m-d",$info['start_time']),
			'topicHdUrl'=> $hdUrl,
			'listGameUrl'=> $href,
		);
		$data = $this->_subjectList($info, 1);
		header("Content-type:text/json");
		$subjectlist = array(
			'success' => $data  ? true : false ,
			'msg' => '',
			'sign' => 'GioneeGameHall',
			'data' => $data,
		);
		
		$tmp['listData'] = $subjectlist;
		$tmp['totalCount'] = $data['totalCount'];
		$tmp['type'] = $info['view_tpl'] + 1;
		unset($data['totalCount']);
		
		exit(json_encode(array(
			'success' => $tmp  ? true : false ,
			'msg' => '',
			'sign' => 'GioneeGameHall',
			'title'=> html_entity_decode($info['title'], ENT_QUOTES),
			'data' => $tmp,
		)));
    }
    
    public function subjectListAction() {
    	$id = intval($this->getInput('id'));
    	$page = intval($this->getInput('page'));
		$info = Client_Service_Subject::getSubject($id);
    	$data = $this->_subjectList($info, $page);
    	$this->localOutput('','',$data);
    }
    
    private  function _subjectList($info, $page) {
        $id = $info['id'];
		$intersrc = 'SUBJECT'.$id;
		$page = intval($this->getInput('page'));
		$webroot = Common::getWebRoot();
		
		if ($page < 1) $page = 1;
		$params = array('subject_id'=>$id, 'game_status'=>1);
		if($info['status'] == Client_Service_Subject::SUBJECT_STATUS_OPEN) {
            list($total, $subject_games) = Client_Service_SubjectGames::getPageDistinctGameList($page, $this->perpage, $params);
		}
		$subject_game_ids = Common::resetKey($subject_games, 'game_id');
		$resource_ids = array_keys($subject_game_ids);

		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		$tmp = Resource_Service_Games::getClientGameData($resource_ids, $intersrc, $checkVer, 0);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		return $data;
    }
    
    /**
     * 分类首页顶部两个热门专题
     */
    public function hotAction() {
    	$sp = $this->getInput('sp');
    	$spArr = Common::parseSp($sp);
    	$device  = $spArr['device'];
    	$subjectList = $this->getHotList($device);
        $webroot = Common::getWebRoot();
        $subData = array();
        foreach ($subjectList as $subject) {
            $tmp = array();
            $tmp["title"] = html_entity_decode($subject['title'], ENT_QUOTES);
            $tmp["imageUrl"] = Common::getAttachPath(). $subject['icon'];
            $params = Game_Api_Util_SubjectUtil::getClientApiSubjectParams($subject);
            $tmp["viewType"] = $params['viewType'];
            $tmp["param"] = array(
                "contentId" => $params['contentId'],
                'url' => '',
            );
            if($params['subViewType']) {
                $tmp["param"]['subViewType'] = $params['subViewType'];
                $tmp["param"]['url'] = $params['url'];
                $tmp["param"]['source'] = $params['source'];
            }
            $subData[] = $tmp;
        }
        $data['items'] = $subData;
        exit(json_encode(array(
            'success' => $subData  ? true : false,
            'msg' => '', 'sign' => 'GioneeGameHall',
            'data' => $data,
        )));
    }
    
    private function getHotList($device) {
        $groupList = array(0);
        $groupList[] = Resource_Service_Pgroup::getGroupByDevice($device);
        $params['pgroup'] = array('in', $groupList);
        $params['status'] = Client_Service_Subject::SUBJECT_STATUS_OPEN;
        $startTime = strtotime(date('Y-m-d H:00:00'));
        $endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
        $params['start_time'] = array('<=', $startTime);
        $params['end_time'] = array('>=', $endTime);
        $subjectList = Client_Service_Subject::getTopList($params, 2);
        return $subjectList;
    }
    
    public function topicListAction() {
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;
        $params = $this->getListParams();
        list($total, $subjects) = Client_Service_Subject::getList($page, $this->perpage, $params);
        foreach($subjects as $key=>$value) {
            $params = array();
            $params = Game_Api_Util_SubjectUtil::getClientApiSubjectParams($value);
            $list[$key]['id'] = $value['id'];
			$list[$key]['iconUrl'] = urldecode(Common::getAttachPath().$value['icon']);
			$list[$key]['title'] = html_entity_decode($value['title'], ENT_QUOTES);
			$list[$key]['topicDescript'] = strip_tags(html_entity_decode($value['resume']));
			$list[$key]['timeStamp'] = $value['start_time'];
			$list[$key]['viewType'] =  $params['viewType'];
			unset($params['viewType']);
			$list[$key]['param'] = $params;
        }
        $hasnext = $this->perpage * $page <  $total ? true : false;
        $data = array('list'=>$list, 'hasnext'=>$hasnext, 'curpage'=>$page);
        $this->localOutput('','',$data);
    }
    
    private function getListParams() {
        $sp = $this->getInput('sp');
        $params = array('status' => Client_Service_Subject::SUBJECT_STATUS_OPEN);
        $startTime = strtotime(date('Y-m-d H:00:00'));
        $params['start_time'] = array('<=', $startTime);
        $params['end_time'] = array('>=', $startTime);
        
        $groups = array();
        if(!$sp) {
               return $params;
        }
        
        $groups[] = 0;
        $spArr = Common::parseSp($sp);
        if(!($spArr && $spArr['device'])) {
             return $params;
        }
        
        $group = Resource_Service_Pgroup::getGroupByDevice($spArr['device']);
        $groups[] = $group;
        
        if($groups) {
            $params['pgroup'] = array('IN', $groups);
        }
        
        return $params;
    }
}