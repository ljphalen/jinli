<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Api_BaseController {
	public $actions =array(
			'index' => '/client/games/index',
			'detailUrl' => '/client/index/detail/',
			'subDetailUrl' => Client_Service_Subject::CLIENT_URL,
			'tjUrl'=>'/client/index/tj',
			'customDetailUrl' => Client_Service_Subject::CLIENT_URL,
	);
	public $perpage = 8;
	public $cacheKey = 'Client_Subject_index';
	
    /**
     * 
     */
    public function indexAction() {
    	$sp = $this->getInput('sp');
    	$page = intval($this->getInput('page'));
	    $intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
    	//判断游戏大厅版本
    	$checkVer = $this->checkAppVersion();
    	//游戏分类
    	$params = array('status' => Client_Service_Subject::SUBJECT_STATUS_OPEN);
    	$startTime = strtotime(date('Y-m-d H:00:00'));
    	$endTime = strtotime(date('Y-m-d H:00:00', strtotime("+1 hours")));
    	$params['start_time'] = array('<=', $startTime);
    	$params['end_time'] = array('>=', $endTime);
    	$groups = array();
	    do{
	        if(! $sp) {
	            break;
	        }
	        $groups[] = 0;
    	    $spArr = Common::parseSp($sp);
	        if(! ($spArr && $spArr['device'])) {
	            break;
	        }
	        $group = Resource_Service_Pgroup::getGroupByDevice($spArr['device']);
	        $groups[] = $group;
	    }while(false);
	    if($groups) {
	        $params['pgroup'] = array('IN', $groups);
	    }
    	
    	//1.5.8开始才会显示自定义模板专题
//     	$clientVersion = Common::getClientVersion($spArr['game_ver']);
//         if(! Game_Api_Util_SubjectUtil::isSubjectCustomShowToClient($clientVersion)) {
//             $params['sub_type'] = Client_Service_Subject::SUBTYPE_LIST;
//         }
		list($total, $subjects) = Client_Service_Subject::getList($page, $this->perpage, $params);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	return $this->_jsonData($subjects, $checkVer, $page, $hasnext ,$intersrc);
    }
    
    private  function _jsonData($subjects, $checkVer, $page, $hasnext ,$intersrc) {
    	$webroot = Common::getWebRoot();		
		$temp = $imgs = array();
		foreach($subjects as $key=>$value) {
			$intersrc = 'subject'.$value['id'];
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = html_entity_decode($value['title'], ENT_QUOTES);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['icon']);
			$temp[$key]['start_time'] = date("Y-m-d",$value['start_time']);
			$temp[$key]['resume'] = strip_tags(html_entity_decode($value['resume']));
			if($checkVer >= 2) {
				$temp[$key]['data-type'] = 0;
			}
			$href = urldecode($webroot.Client_Service_Subject::CLIENT_URL. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[$key]['data-infpage'] = htmlentities($value['title']).','.$href;
// 		    $temp[$key]['data-infpage'] = ','.$href;
			$temp[$key]['data-source'] = $intersrc;
			$temp[$key]['link'] = Common::tjurl($webroot.$this->actions['tjUrl'], $value['id'], $intersrc, $webroot.'/subject/detail/?id='.$value['id'].'&intersrc='.$intersrc);
		    $params = Game_Api_Util_SubjectUtil::getClientApiSubjectParams($value);
		    $temp[$key]['viewType'] = $params['viewType'];
		    if($params['subViewType']) {
		        $temp[$key]['subViewType'] = $params['subViewType'];
		    }
		    $imgs[] = $value['icon'];
		}
    	if($page < 2 && $imgs)  $this->cache($imgs, 'subject');
    	$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
    
    
}