<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ActivityController extends Api_BaseController {
	
	public $perpage = 10;
    
	/**
	 * 活动列表
	 */
    public function indexAction() {
    	$page = intval($this->getInput('page'));
    	$sp = $this->getInput('sp');
    	if ($page < 1) $page = 1;
    	//取消活动分组排序
    	$params['start_time'] = array('<',Common::getTime());
    	$params['status'] = 1;
    	$orderBy = array('sort'=>'DESC','start_time'=>'DESC','id'=>'DESC');
    	list($total, $data) = Client_Service_Hd::getList($page, $this->perpage, $params, $orderBy);
    	$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
    	$hds = $this->_getJsonData($data, $sp);
    	$this->output(0, '', array('list'=>$hds, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
 
    /**
     * 组装输出的json格式
     * @param unknown_type $data
     * @return array
     * 
     */
    private function _getJsonData($data, $sp='') {
    	
    	$webroot = Common::getWebRoot();
    	$time = Common::getTime();
    	$temp = array();
    	foreach($data as $key=>$value){
    		$href = urldecode($webroot.'/client/activity/addetail?id=' . $value['id'].'&sp='.$sp);
    		//进行中或历史活动状态标识
    		if($value['start_time'] <= $time && $value['end_time'] >= $time) {
    			$flag = 1;
    		} else if($value['end_time'] < $time){
    			$flag = 0;
    		}
    		$temp[]= array(
    				'id'=>$value['id'],
    				'game_id'=>$value['game_id'],
    				'title'=>html_entity_decode($value['title'], ENT_QUOTES),
    				'content'=> strip_tags(html_entity_decode($value['content'])),
    				'status'=> $flag,
    				'viewType'=>'ActivityDetailView',
    				'actImageUrl'=>Common::getAttachPath(). $value['img'],
    				'activityTime'=>  date("Y-m-d",$value['start_time']).'至'.date("Y-m-d",$value['end_time']),
    				'url' => $href    				
    		);
    	}
    	
    	return $temp;
     }
}
