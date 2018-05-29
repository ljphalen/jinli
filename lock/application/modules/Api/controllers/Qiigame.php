<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

define('ERROR_CONTENT', -100);
define('ERROR_OPERATE', -101);
define('ERROR_CLASSNAME', -103);
define('ERROR_SIGN', -102);
define('ERROR_ADD_LABEL_FAIL', -201);
define('ERROR_UPDATE_LABEL_FAIL', -202);
define('ERROR_DELETE_LABEL_FAIL', -203);
define('ERROR_ADD_SENCE_FAIL', -301);
define('ERROR_UPDATE_SENCE_FAIL', -302);
define('ERROR_DELETE_SENCE_FAIL', -303);
define('ERROR_ADD_SENCELABEL_FAIL', -401);
define('ERROR_ADD_SENCEKERNEL_FAIL', -501);
define('ERROR_UPDATE_SENCEKERNEL_FAIL', -502);

class QiigameController extends Api_BaseController {
	
	public $actions = array(
		"indexUrl"=>"/api/qiigame/index"
	);
	
    /**
     * 
     */
    public function indexAction() {
    	
    	header("Content-type: text/html; charset=utf-8");
    	
    	$rsa = new Util_Rsa();
    	$content = html_entity_decode($this->getPost('content'));
    	$content = str_replace('\r\n', '<br/>', $content);
    	//$content = '{"sign":"dtz04HGiC+oXSBpam5OAiu4S8rMkb9ypTM62U2ZNClIOI9n4543vQeLfYaxgUMdiL2gCNMJ2PEAYOuxSGP0TudHQqH9QgL1eTKbNCFrQ949uRDlwrh2bo7Ejf5rAdZgYex3k2wgQg2XssFPjSmi4o/I2/DYHQqz51/hI/UrHUIM=","className":"scene","operate":"add","object":{"zhName":"aaa","enName":"aaa","sceneCode":68888,"icon":"http://127.0.0.1:8080/resources/locker/scene/label/6/cd38c6ddd49e4ba5a50a8fac758c74e0.jpg","iconHd":"http://127.0.0.1:8080/resources/locker/scene/label/6/cd38c6ddd49e4ba5a50a8fac758c74e0.jpg","iconMicro":"http://127.0.0.1:8080/resources/locker/scene/label/6/cd38c6ddd49e4ba5a50a8fac758c74e0.jpg","intro":"aaa","belongToLabels":"1,4,6","createTime":"Mar 27, 2013 11:11:07 AM","updateTime":"Mar 29, 2013 2:16:24 PM"}}';
    	if(!$content) $this->output(ERROR_CONTENT, 'error_content.');
    	//write logs
    	Lock_Service_QiiLogs::addQiiLogs(array('content'=>$content));
    	
    	$content = json_decode($content, true);
    	if(!$content['className'] || !$content['operate'] || !$content['object']) $this->output(ERROR_CONTENT, 'error_content2.');
    	if(!$content['sign']) $this->output(ERROR_SIGN, 'error_sign.');
		
    	//check sign
    	$verify = self::checkSign(array('className'=>$content['className'], 'operate'=>$content['operate']), $content['sign']);
    	if(!$verify) $this->output(ERROR_SIGN, 'error_sign.');
    	
    	if($content['className'] == 'label') {
    		$this->label($content);
    	} elseif($content['className'] == 'scene') {
    		$this->scene($content);
    	} elseif($content['className'] == 'dataSceneKernelBean') {
    		$this->kernel($content);
    	} else {
    		$this->output(ERROR_CLASSNAME, 'error_classname.');
    	}
    }
    
    /**
     * 
     * @param unknown_type $params
     */
    public function label($params) {
    	if($params['operate'] == 'add') {
    		$result = Lock_Service_QiiLabel::add($params['object']);
    		if(!$result) $this->output(ERROR_ADD_LABEL_FAIL, 'error_add_label_fail.');
    		$this->output(0, 'add_success.');
    	} elseif($params['operate'] == 'update') {
    		$result = Lock_Service_QiiLabel::update($params['object']);
    		if(!$result) $this->output(ERROR_UPDATE_LABEL_FAIL, 'error_update_label_fail.');
    		$this->output(0, 'add_success.');
    	} elseif($params['operate'] == 'delete') {
    		$result = Lock_Service_QiiLabel::del($params['object']);
    		if(!$result) $this->output(ERROR_DELETE_LABEL_FAIL, 'error_delete_label_fail.');
    		$this->output(0, 'add_success.');
    	} else {
    		$this->output(ERROR_OPERATE, 'error_operate.');
    	}
    }
    
    
    /**
     * 
     * @param unknown_type $params
     */
    public function scene($params) {
    	if($params['operate'] == 'add') {
    		$result = Lock_Service_QiiFile::add($params['object']);
    		if(!$result) $this->output(ERROR_ADD_SENCE_FAIL, 'error_add_sence_fail.');
    		$this->output(0, 'add_success.');
    	} elseif($params['operate'] == 'update') {
    		$result = Lock_Service_QiiFile::update($params['object']);
    		if(!$result) $this->output(ERROR_UPDATE_SENCE_FAIL, 'error_update_sence_fail.');
    		$this->output(0, 'update_success.');
    	} elseif($params['operate'] == 'delete') {
    		$result = Lock_Service_QiiFile::delete($params['object']);
    		if(!$result) $this->output(ERROR_DELETE_SENCE_FAIL, 'error_delete_sence_fail.');
    		$this->output(0, 'delete_success.');
    	} else {
    		$this->output(ERROR_OPERATE, 'error_operate.');
    	}
    } 

    /**
     *
     * @param unknown_type $params
     */
    public function kernel($params) {
    	if($params['operate'] == 'add') {
    		$result = Lock_Service_QiiFileKernel::add($params['object']);
    		if(!$result) $this->output(ERROR_ADD_SENCEKERNEL_FAIL_FAIL, 'error_add_sencekernel_fail.');
    		$this->output(0, 'add_success.');
    	} elseif($params['operate'] == 'update') {
    		$result = Lock_Service_QiiFileKernel::update($params['object']);
    		if(!$result) $this->output(ERROR_UPDATE_SENCEKERNEL_FAIL_FAIL, 'error_update_sencekernel_fail.');
    		$this->output(0, 'update_success.');
    	} else {
    		$this->output(ERROR_OPERATE, 'error_operate.');
    	}
    }
    
    /**
     * 
     */
    public function labelListAction() {
    	header("Content-type: text/html; charset=utf-8");
    	$url = Common::getConfig('apiConfig', 'label_list_url');
    	$content = file_get_contents($url);
    	$content = json_decode($content, true);
    	if($content['object']) {
    		$result = Lock_Service_QiiLabel::add($content['object']);
    		if(!$result) $this->output(ERROR_ADD_LABEL_FAIL, 'error_add_label_fail.');
    		$this->output(0, 'add_success.');
    	}
    }
    
    /**
     * 
     */
    public function sceneListAction() {
    	header("Content-type: text/html; charset=utf-8");
    	$url = Common::getConfig('apiConfig', 'scene_list_url');
    	$content = file_get_contents($url.'?vendorKey='.Common::getConfig('apiConfig', 'vendor_key'));
    	$content = str_replace('\r\n', '<br/>', $content);
    	$content = json_decode($content, true);
    	if($content['object']) {
    		$result = Lock_Service_QiiFile::banchAdd($content['object']);
    		if(!$result) $this->output(ERROR_ADD_SENCE_FAIL, 'error_add_sence_fail.');
    		$this->output(0, 'add_success.');
    	}
    }
    
    /**
     *
     */
    public function kernelListAction() {
    	header("Content-type: text/html; charset=utf-8");
    	$url = Common::getConfig('apiConfig', 'scene_kernel_list_url');
    	$content = file_get_contents($url.'?vendorKey='.Common::getConfig('apiConfig', 'vendor_key'));
    	$content = json_decode($content, true);
    	if($content['object']) {
    		$result = Lock_Service_QiiFileKernel::addBatch($content['object']);
    		if(!$result) $this->output(ERROR_ADD_SENCEKERNEL_FAIL_FAIL, 'error_add_sencekernel_fail.');
    		$this->output(0, 'add_success.');
    	}
    }
    
    /**
     *
     * @param array $params
     */
    public function checkSign($params, $sign) {
    	$rsa = new Util_Rsa();
    	
    	$pub  = Common::getConfig('siteConfig', 'rsaPubFile');
    	$verify = $rsa->verify(json_encode($params), $sign, $pub);
    	return $verify;
    }
}