<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Kingstone_SubjectController extends Api_BaseController {
	public $actions =array(
			'index' => '/kingstone/games/index',
			'detailUrl' => '/kingstone/index/detail/',
			'subDetailUrl' => '/kingstone/subject/detail/',
			'tjUrl'=>'/kingstone/index/tj'
	);
	public $perpage = 8;
	public $cacheKey = 'Kingstone_Subject_index';
	
    /**
     * 
     */
    public function indexAction() {
    	$page = intval($this->getInput('page'));
	    $intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
    	//判断游戏大厅版本
    	$checkVer = $this->checkAppVersion();
    	//游戏分类
    	$params = array('status' => 1);
    	$params['start_time'] = array('<=', Common::getTime());
    	$params['end_time'] = array('>=', Common::getTime());
		list($total, $subjects) = Client_Service_Subject::getList($page, $this->perpage, $params);
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	return $this->_jsonData($subjects, $checkVer, $page, $hasnext ,$intersrc);
    }
    
    
    private  function _jsonData($subjects, $checkVer, $page, $hasnext ,$intersrc) {
    	$webroot = Common::getWebRoot();		
		$temp = $imgs = array();
		foreach($subjects as $key=>$value) {
			$intersrc = 'SUBJECT'.$value['id'];
			$href = urldecode($webroot.$this->actions['subDetailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($webroot.$this->actions['tjUrl'], $value['id'], $intersrc, $webroot.'/subject/detail/?id='.$value['id'].'&intersrc='.$intersrc);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['icon']);
			$temp[$key]['data-infpage'] = $value['title'].','.$href;
			$temp[$key]['start_time'] = date("Y-m-d",$value['start_time']);
			$temp[$key]['resume'] = $value['resume'];
			if($checkVer >= 2){
				$temp[$key]['data-type'] = 0;
			}
			$imgs[] = $value['icon'];
		}
    	if($page < 2 && $imgs)  $this->cache($imgs, 'subject');
    	$this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    	
    	 
    }
}