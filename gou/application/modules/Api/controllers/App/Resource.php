<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class App_ResourceController extends Api_BaseController {
	
	public $perpage = 16;
    /**
     * 
     */
    public function listAction() {
    	$page = intval($this->getInput('page'));
    	$perpage = intval($this->getInput('perpage'));
    	if ($perpage) $this->perpage = $perpage;
    	
    	if ($page < 1) $page = 1;
    	list($total, $result) = Gou_Service_Resource::getList($page, $this->perpage, array('status'=>1)); 
    	$tmp = array();
    	$webroot = Common::getWebRoot();
    	foreach($result as $key=>$value) {
    		$tmp[] = array(
    				'id'=>$value['id'],
    				'name'=>$value['name'],
    				'resume'=>html_entity_decode($value['resume']),
    				'size'=>$value['size'] * 1024,
    				'company'=>$value['company'],
    				'package'=>$value['package'],
    				'ptype'=>$value['ptype'],
    				'link'=>$value['link'],
    				'icon'=>Common::getAttachPath() .$value['icon'],	
    			);
    	}
    	
    	$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
    	$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
    
    public function dtjAction() {
    	$id = intval($this->getInput('id'));
    	$type = $this->getInput('type');
    	$url = html_entity_decode(urldecode($this->getInput('_url')));
    	if (!id || !$type) return false;
    	switch ($type)
    	{
    		case RESOURCE:
    			Gou_Service_Resource::updateTJ($id);
    			break;
    		default:
    	}
    	
    	$this->redirect($url);
    }
    
    public function tjAction() {
    	$this->output(0, '');
    }
    
}
