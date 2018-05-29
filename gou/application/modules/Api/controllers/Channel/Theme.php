<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh  主题店 子分类
 *
 */
class Channel_ThemeController extends Api_BaseController {
	
	public $actions =array(
			'tjUrl'=>'/index/tj'
	);
	
	public function categoryAction() {
		$id = intval($this->getInput('id'));
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$perpage = 12;
		
		$webroot = Common::getWebRoot();
		
		//子分类
    	$plat_params = array(
    			'cate_id'=>$id,
    			'info_type'=>3,
    			'version_type'=>3,
    			'status'=>1,
    			'start_time'=>array('<', Common::getTime()),
    			'end_time'=>array('>', Common::getTime()),
    	);
    	
    	list($total,$plats) = Store_Service_Info::getList($page, $perpage, $plat_params);
    	
    	$data = array();
    	$tjUrl = $webroot.$this->actions['tjUrl'];
    	foreach ($plats as $key=>$value) {
    		$data[$key]['link'] = Store_Service_Info::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
    		$data[$key]['name'] = html_entity_decode($value['title']);
    		$data[$key]['img'] = Common::getAttachPath().$value['img'];
    	}
		
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}	
}
