<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Channel_SubjectController extends Api_BaseController {
	
public $actions =array(
			'index' => '/channel/games/index',
			'detailUrl' => '/channel/index/detail/',
			'subDetailUrl' => '/channel/subject/detail/',
			'tjUrl'=>'/channel/index/tj'
	);
	public $perpage = 8;
	public $cacheKey = 'Channel_Subject_index';
	
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
    	
        //小辣椒版本每页sp参数
		list($total, $subjects) = Client_Service_Subject::getList($page, $this->perpage, $params);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	return $this->_jsonData($subjects, $checkVer, $page, $hasnext ,$intersrc);
    }
    
    
    private  function _jsonData($subjects, $checkVer, $page, $hasnext ,$intersrc) {
    	$webroot = Common::getWebRoot();
	    //判断游戏大厅版本
		$temp = $imgs = array();
		foreach($subjects as $key=>$value) {
			$intersrc = 'SUBJECT'.$value['id'];
			$href = urldecode($webroot.$this->actions['subDetailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = html_entity_decode($value['title'], ENT_QUOTES);
			$temp[$key]['link'] = Common::tjurl($webroot.$this->actions['tjUrl'], $value['id'], $intersrc, $webroot.'/subject/detail/?id='.$value['id'].'&intersrc='.$intersrc);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['icon']);
			$temp[$key]['data-infpage'] = htmlentities($value['title']).','.$href;
			$temp[$key]['start_time'] = date("Y-m-d",$value['start_time']);
			$temp[$key]['resume'] = strip_tags(html_entity_decode($value['resume']));
			if($checkVer >= 2){
				$temp[$key]['data-type'] = 0;
			}
			$imgs[] = $value['icon'];
		}
    	if($imgs)  $this->cache($imgs, 'subject');
    	$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    	
    	 
    }
}