<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SearchController extends Api_BaseController {
	
	public $perpage = 16;
    /**
     * 
     */
    public function keywordAction() {
    	list(, $keywords) = Resource_Service_Keyword::getCanUseResourceKeywords(0,1,array('ktype'=>2));
    	$this->output(0,'',trim($keywords[0]['name']));
    }
    
    public function hotsAction() {
    	list(, $keywords) = Resource_Service_Keyword::getCanUseResourceKeywords(0, 10 ,array('ktype'=>1));
    	$tmp = array();
    	foreach($keywords as $key=>$value) {
    		$tmp[] = array('name'=>trim($value['name']));
    	}
    	$this->output(0, '', $tmp);
    }
}
