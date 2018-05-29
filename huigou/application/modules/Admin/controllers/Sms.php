<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class SmsController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Sms/index',
		'postUrl' => '/Admin/Sms/sms_post',
		'logUrl' => '/Admin/Sms/log'
	);
	public $perpage = 20;
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function sms_postAction() {
		$info = $this->getInput(array('mobile', 'content'));
		if (!$info['mobile']) $this->output(-1, '手机号码不能为空.');
		if (!$info['content']) $this->output(-1, '短信内容不能为空.');
		$tels = explode(',',html_entity_decode($info['mobile']));
		$data = array();
		foreach($tels as $key=>$val){
			$ret = Common::sms($val, $info['content']);
			$arr = array('tel'=>$val, 'content'=>$info['content'], 'status'=>0);

            if(strpos($ret->headers["Warning"], "200") !== false){
				$arr['status'] = 1;	
			}
			array_push($data, $arr);
		}
		$ret = Gc_Service_Sms::addSms($data);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function logAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		list($total, $sms) = Gc_Service_Sms::getList($page, $perpage);
		$this->assign('sms', $sms);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['logUrl'].'?'));
	}
}
