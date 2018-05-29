<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Channel_ResourceController extends Api_BaseController {
	
	public $perpage = 10;
	
    /**
     * 
     */
    public function listAction() {
    	$page = intval($this->getInput('page'));
    	
    	if ($page < 1) $page = 1;
    	list($total, $result) = Resource_Service_Games::getList($page, $this->perpage); 
    	$tmp = array();
    	
    	$webroot = Common::getWebRoot();
    	foreach($result as $key=>$value) {
    		$tmp[] = array(
    				'id'=>$value['id'],
    				'sort'=>$value['sort'],
    				'name'=>$value['name'],
    				'resume'=>$value['resume'],
    				'language'=>$value['language'],
    				'price'=>$value['price'],
    				'version'=>$value['version'],
    				'recommend'=>$value['recommend'],
    				'link'=>$value['link'],
    				'img'=>$value['img'],
    				'company'=>$value['company'],
    				'size'=>$value['size'],
    				'status'=>$value['status'],
    				'hits'=>$value['hits'],
    				'descrip'=>$value['descrip'],
    			);
    	}
    	
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
    
    public function infoAction(){
    	$id = intval($this->getInput('id'));
    	if (!$id) $this->output(-1, '');
    	$info['info'] = Resource_Service_Games::getResourceGames(intval($id));
    	list(, $info['imgs']) = Resource_Service_Img::getList(0, 20, array('game_id'=>intval($id)));
    	$this->output(0, '', $info);
    }
    
}