<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CategoryController extends Api_BaseController {
	
	public $actions = array(
		"indexUrl"=>"/api/category/index"
	);
	
    /**
     * 
     */
    public function indexAction() {
    	$channel = intval($this->getInput('channel'));
    	if($channel == 1) {
    		list(, $list) = Lock_Service_QiiLabel::getAllLabel();
    	} else {
    		list(, $list) = Lock_Service_FileType::getAllFileType();
    	}
    	
    	$data = array();
    	foreach ($list as $key=>$value) {
    		$data[$key]['id'] = $value['out_id'];
    		$data[$key]['name'] = $value['name'];
    	}
    	exit(json_encode(array('lockscreenTypes'=>$data)));
    }
}