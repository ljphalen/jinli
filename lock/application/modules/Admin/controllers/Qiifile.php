<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class QiifileController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Qiifile/index',
		'detailUrl' => '/Admin/Qiifile/detail',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $files) = Lock_Service_QiiFile::getList($page, $perpage, array(), 'out_id', 'DESC');
		$this->assign('files', $files);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'] .'/?'));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function detailAction() {
		$id = $this->getInput('id');
		$info = Lock_Service_QiiFile::getFile(intval($id));
		if(!$info || !$id) $this->redirect($this->actions['listUrl']);		
		
		//分类
		$idx_file_label = Lock_Service_IdxFileLabel::getByFileId($info['out_id']);
		
		$ids = array();
		foreach ($idx_file_label as $key=>$value) {
			$ids[$key]  = $value['label_id'];
		}	
		$labels = Lock_Service_QiiLabel::getByIds($ids);
		$str_type = '';
		foreach ($labels as $key=>$value) {
			$str_type .= strlen($str_type) ? ','.$value['name'] :  $value['name'] ;
		}
		$info['labels'] = $str_type;
		
		
		$this->assign('info', $info);
	}
}
