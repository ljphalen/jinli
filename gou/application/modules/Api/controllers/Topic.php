<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 每日上新
 *
 */
class TopicController extends Api_BaseController {
    public function sharetimesAction(){
        $id=intval($this->getInput('id'));
        $ret = Gou_Service_Topic::updateShare($id);
        $this->output(0,'',array('result'=>$ret));
	}
}
