<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class StorageController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Storage/index',
		'postUrl' => '/Admin/Storage/post',
	);

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$info = $this->getInput(array('key'));
		if(!$info['key']) $info['key'] = 'new';
		
		$stroage_value = html_entity_decode(Lock_Service_Storage::getValue($info['key']));
		
		$ids = array();
		if($stroage_value) $ids = explode(',', $stroage_value);
		
		$files = Lock_Service_File::getAllFile($ids);
		$storage = array();
		foreach ($files as $key=>$value) {
			$storage[$key]['id'] = $value['id'];
			$storage[$key]['title'] = $value['id'].'.'.$value['title'];
		}
		
		//已选中的
		$files_value = array();
		if ($ids) $files_value = Lock_Service_File::getByIds($ids);
		
		$files_value = Common::resetKey($files_value, 'id');
		
		$exsit = array();
		foreach ($ids as $key=>$value) {
			$exsit[$key]['id'] = $files_value[$value]['id'];
			$exsit[$key]['title'] = $files_value[$value]['id'].'.'.$files_value[$value]['title'];
		}
		
		$this->assign('files', json_encode($storage));
		$this->assign('value', $stroage_value);
		$this->assign('info', $info);
		$this->assign('exsit', json_encode($exsit));
	}
	
	/**
	 * post
	 */
	public function postAction() {
		$info = $this->getInput(array('key', 'value'));
		Lock_Service_Storage::setValue($info['key'], $info['value']);
		$this->output(0, '操作成功.');
	}
}
