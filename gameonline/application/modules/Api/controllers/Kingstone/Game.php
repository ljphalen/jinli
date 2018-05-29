<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Kingstone_GameController extends Api_BaseController {
	
	
    /**
     * 
     */
    public function attributeAction() {
    	$sid = $this->getInput('sid');
    	if (!$sid) $this->output(-1, 'invalid sid.', array());
    	
    	list(, $result) = Resource_Service_Attribute::getList(0,100, array('at_type'=>$sid));
    	
    	$tmp = array();
    	foreach($result as $key=>$value) {
    		$tmp[] = array('id'=>$value['id'], 'title'=>$value['title'], 'status'=>$value['status']);
    	}
    	$this->output(0, '', $tmp);
    }
    
    public function labelAction() {
    	$sid = $this->getInput('lid');
    	if (!$sid) $this->output(-1, 'invalid lid.', array());
    	 
    	list(, $result) = Resource_Service_Label::getList(0,100, array('btype'=>$sid));
    	 
    	$tmp = array();
    	foreach($result as $key=>$value) {
    		$tmp[] = array('id'=>$value['id'], 'title'=>$value['title'], 'status'=>$value['status']);
    	}
    	$this->output(0, '', $tmp);
    }
}