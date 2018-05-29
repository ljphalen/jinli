<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ActivityController extends Api_BaseController {
	
	public $perpage = 10;
    
	/**
	 * 活动列表
	 */
    public function indexAction() {
    	$page = intval($this->getInput('page'));
    	if ($page < 1) $page = 1;
    	
    	$totala = $this->_getCountA();
    	$totalb = $this->_getCountB();
    	
    	$pagea = ceil((int) $totala / $this->perpage);
    	
    	$ret = array();
    	$rnum = $totala % $this->perpage;                      //正在进行的活动求余数
    	$crnum = $totala - ($page - 1) * $this->perpage;       //判断是否有余数
    	list(, $nreta) = $this->_getA($page, $this->perpage);  //判断当前正在进行活动是否有值
    	
    	
    	
    	
    	if($totala ==0 && $pagea == 0){ 
    		//没有正在进行的活动
    		list($totalb, $retb) = $this->_getB($page, $this->perpage);
    		$hasnext = (ceil((int) $totalb / $this->perpage) - $page) > 0 ? true : false;
    		$ret = $retb;
    		
    	} else if($totala <= $this->perpage && $page < 2){ 
    		//正在进行的活动有并且小于第一页
    		$len = $this->perpage - $totala;
    		list($totala, $reta) = $this->_getA($page, $this->perpage);
    		list(, $retb) = $this->_getB(1, $len);
    		    		
    		$hasnext = (ceil((int) ($totala + $totalb) / $this->perpage) - $page) > 0 ? true : false;
    		$ret = (array_merge($reta, $retb));
    		
    	}  else if($totala <= $this->perpage && $page > 1){  
    		//正在进行的活动有并且小于第一页,并且当前页大于第一页
    		$offset = $this->perpage - $totala;
    		$curpage = $page - ceil((int) $totala / $this->perpage);
    		list($totalb, $retb) = $this->_getB($curpage, $this->perpage,$offset);
    		$hasnext = (ceil((int) ($totala + $totalb) / $this->perpage) - $page) > 0 ? true : false;
    		$ret = $retb;
    		
    	} else if($totala > $this->perpage && ($totala % $this->perpage != 0) && ($page <= $pagea - 1) ){ 
    		//正在进行的活动有并且大于第一页，并且当前页在活动和已结束活动交接点之前
    		list($totala, $reta) = $this->_getA($page, $this->perpage);
    		
    		$hasnext = (ceil((int) $totala / $this->perpage) - $page) > 0 ? true : false;
    		$ret = $reta;
    		
    	} else if($totala > $this->perpage && ($totala % $this->perpage != 0) && ($rnum == $crnum && $crnum > 0) ){  
    		//正在进行的活动有并且大于第一页，并且当前页刚好在活动和已结束活动交接点
    		list($totala, $reta) = $this->_getA($page, $this->perpage);
    		list($totalb, $retb) = $this->_getB(1, ($this->perpage - $rnum));
    		
    		$hasnext = ($totalb -  ($this->perpage - $rnum)) > 0 ? true : false;
    		$ret = (array_merge($reta, $retb));
    		
    	} else if($totala > $this->perpage && ($totala % $this->perpage != 0) && (!$nreta && ($totalb - ($this->perpage - $rnum) > 0)) ){ 
    		//正在进行的活动有并且大于第一页，并且当前页刚在活动和已结束活动交接点之后
    		$offset = $this->perpage - $rnum;
    		$curpage = $page - (ceil((int) $totala / $this->perpage));
    		list($totalb, $retb) = $this->_getB($curpage, $this->perpage,$offset);
    		$hasnext = (ceil((int) ($totala + $totalb) / $this->perpage) - $page) > 0 ? true : false;
    		$ret = $retb;
    		
    	} else if(($totala % $this->perpage == 0) &&  $totalb == 0){  
    		//正在进行的活动刚好整除，并且没有已结束活动
    		list($totala, $reta) = $this->_getA($page, $this->perpage);
    		$hasnext = (ceil((int) $totala / $this->perpage) - $page) > 0 ? true : false;
    		$ret = $reta;
    		
    	} else if(($totala % $this->perpage == 0) &&  $totalb > 0 &&  $page <= $pagea){ 
    		//正在进行的活动刚好整除，有已结束活动,并且在交接点之前
    		list(, $reta) = $this->_getA($page, $this->perpage);
    		$hasnext = (ceil((int) ($totala + $totalb) / $this->perpage) - $page) > 0 ? true : false;
    		$ret = $reta;
    		
    	} else if(($totala % $this->perpage == 0) &&  $totalb > 0 &&  $page > $pagea){ 
    		//正在进行的活动刚好整除，有已结束活动,并且在交接点之后
    		$curpage = $page - (ceil((int) $totala / $this->perpage));
    		list(, $retb) = $this->_getB($curpage, $this->perpage);
    		$hasnext = (ceil((int) ($totala + $totalb) / $this->perpage) - $page) > 0 ? true : false;
    		$ret = $retb;
    		
    	}
        
    	$hds = $this->_getJsonData($ret);
    	$this->output(0, '', array('list'=>$hds, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
    
    /**
     * 获取当前正在进行的活动
     * @param unknown_type $start
     * @param unknown_type $limit
     * @return multitype:unknown multitype:
     */
    private function _getA($start, $limit) {
    	$params['start_time'] = array('<=',Common::getTime());
    	$params['end_time'] = array('>=',Common::getTime());
    	$params['status'] = 1;
    	$orderBy = array('sort'=>'DESC','start_time'=>'DESC','id'=>'DESC');
    	return Client_Service_Hd::getList($start, $limit, $params, $orderBy);    	
    }
    
    /**
     * 获取历史活动
     * @param unknown_type $start
     * @param unknown_type $limit
     * @return multitype:unknown multitype:
     */
    private function _getB($start, $limit, $offset) {
    	$params['end_time'] = array('<',Common::getTime());
    	$params['status'] = 1;
    	$orderBy = array('sort'=>'DESC','end_time'=>'DESC','id'=>'DESC');
    	return Client_Service_Hd::getList($start, $limit, $params, $orderBy, $offset);
    }
    
    /**
     * 获取当前正在进行的活动的总记录数
     * @return string
     */
    private function _getCountA() {
    	$params['start_time'] = array('<=',Common::getTime());
    	$params['end_time'] = array('>=',Common::getTime());
    	$params['status'] = 1;
    	return Client_Service_Hd::getCount($params);
    }
    
    /**
     * 获取历史的活动的总记录数
     * @return string
     */
    private function _getCountB() {
    	$params['end_time'] = array('<',Common::getTime());
    	$params['status'] = 1;
    	return Client_Service_Hd::getCount($params);
    }
    
    /**
     * 组装输出的json格式
     * @param unknown_type $data
     * @return array
     */
    private function _getJsonData($data) {
    	
    	$webroot = Common::getWebRoot();
    	$time = Common::getTime();
    	$tmp = array();
    	
    	foreach($data as $key=>$value){
    		$href = urldecode($webroot.'/client/activity/detail?id=' . $value['id']);
    		$value['viewType'] = 'ActivityDetailView';
    		$value['actImageUrl'] = Common::getAttachPath(). $value['img'];
    		$value['activityTime'] = date("Y-m-d",$value['start_time']).'至'.date("Y-m-d",$value['end_time']);
    		$value['url'] = $href;
    		unset($value['placard']);
    		$value['content'] = strip_tags(html_entity_decode($value['content']));
    		 
    		if($value['start_time'] <= $time && $value['end_time'] >= $time) {
    			 
    			$temp['hot'][] = $value;
    		} else if($value['end_time'] < $time){
    			 
    			$temp['history'][] = $value;
    		}
    		 
    	}
    	
    	return $temp;
     }
}
