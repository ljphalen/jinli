<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_SubjectController extends Api_BaseController {
	public $perpage = 10;
		
    /**
     * 
     */
    public function subjectInfoAction() {
    	$id = intval($this->getInput('id'));		
		$info = Client_Service_Subject::getSubject($id);
		$webroot = Common::getWebRoot();
		$href = urldecode($webroot.'/Api/Local_Subject/subjectList?id=' . $id);
		$hdUrl = ($info['hdinfo']) ? urldecode($webroot.'/client/subject/hdinfo/?id=' . $id) : '';
		$tmp = array(
				'viewType'=>'TopicDetailView',
				'topicImageUrl'=>Common::getAttachPath(). $info['img'],
				'topicDescript'=>html_entity_decode($info['resume']),
				'topicTime'=>date("Y-m-d",$info['start_time']),
				'topicHdUrl'=> $hdUrl,
				'listGameUrl'=> $href,
				);
		
		$data = $this->_subjectList($id,1);
		header("Content-type:text/json");
		$subjectlist = json_encode(array(
				'success' => $data  ? true : false ,
				'msg' => '',
				'sign' => 'GioneeGameHall',
				'data' => $data,
		));
		
		$tmp['listData'] = $subjectlist;
		$tmp['totalCount'] = $data['totalCount'];
		unset($data['totalCount']);
		
		exit(json_encode(array(
				'success' => $tmp  ? true : false ,
				'msg' => '',
				'sign' => 'GioneeGameHall',
				'title'=>$info['title'],
				'data' => $tmp,
		)));
    }
    
    public function subjectListAction() {
    	$id = intval($this->getInput('id'));
    	$page = intval($this->getInput('page'));
    	$data = $this->_subjectList($id, $page);
    	$this->localOutput('','',$data);
    }
    
    private  function _subjectList($id, $page) {
		$intersrc = 'SUBJECT'.$id;
		$page = intval($this->getInput('page'));
		$webroot = Common::getWebRoot();
		
		if ($page < 1) $page = 1;
		//get game_ids 
	    list($total,$subject_game_ids) = Client_Service_Game::getSubjectGames($page, $this->perpage, array('subject_id'=>$id,'status'=>1,'game_status'=>1));
		$subject_game_ids = Common::resetKey($subject_game_ids, 'resource_game_id');
		$resource_ids = array_unique(array_keys($subject_game_ids));

		
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		$tmp = Resource_Service_Games::getClientGameData($resource_ids, $intersrc, $checkVer, 0);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		return $data;
    }
}