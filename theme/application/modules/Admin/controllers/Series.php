<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class SeriesController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Series/index',
		'addUrl' => '/Admin/Series/add',
		'addPostUrl' => '/Admin/Series/add_post',
		'editUrl' => '/Admin/Series/edit',
		'editPostUrl' => '/Admin/Series/edit_post',
		'deleteUrl' => '/Admin/Series/delete',
		'filesortUrl' => '/Admin/Series/filesort',
		'filesortPostUrl' => '/Admin/Series/filesort_post',
	);
	
	public $perpage = 20;
	public $appCacheName = 'APPC_Front_Index_index';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		
		list($total, $series) = Theme_Service_Series::getList($page, $perpage);
		$this->assign('series', $series);
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'/?'));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Theme_Service_Series::getSeries(intval($id));		
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('name'));
		$info = $this->_cookData($info);
		$series = Theme_Service_Series::getSeriesByName($info['name']);
		if($series) $this->output(-1, $info['name'].'已存在');
		$result = Theme_Service_Series::addSeries($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'name'));
		$info = $this->_cookData($info);
		$series = Theme_Service_Series::getSeriesByName($info['name']);
		if($series && $series['id'] !=  $info['id']) $this->output(-1, $info['name'].'已存在');
		$ret = Theme_Service_Series::updateSeries($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['name']) $this->output(-1, '名称不能为空.');
		return $info;
	}
	
	/**
	 * 
	 * 文件排序
	 */
	public function filesortAction() {
		$sid = $this->getInput('sid');
		$page = intval($this->getInput('page'));
		$perpage = 50;
		$info = Theme_Service_Series::getSeries(intval($sid));
		
		list($total, $idx_series) = Theme_Service_IdxFileSeries::getList($page, $perpage, array('series_id'=>$sid), array('sort'=>'DESC', 'file_id'=>'DESC'));
		
		$idx_series = Common::resetKey($idx_series, 'file_id');
		
		$file_ids = array_keys($idx_series);
		
		$files = Theme_Service_File::getByIds($file_ids);
		$url = $this->actions['filesortUrl'] .'/?'. http_build_query(array('sid'=>$sid)) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('info', $info);
		$this->assign('idx_series', $idx_series);
		$this->assign('files', Common::resetKey($files, 'id'));
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function filesort_postAction() {
		$info = $this->getPost(array('id', 'sort', 'ids'));
		if (!$info['ids']) $this->output(-1, '请选中需要修改的记录');
		
		foreach ($info['ids'] as $key=>$value) {
			$result = Theme_Service_IdxFileSeries::updateBy(array('sort'=>$info['sort'][$key]), array('id'=>$value));
			if (!$result) $this->output(-1, '操作失败');
		} 
		Theme_Service_File::updateVersion();
		$this->output(0, '操作成功.');
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Theme_Service_Series::getSeries($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$models = Theme_Service_Models::getListBySeriesId($id);
		if($models) $this->output(-1, '该系列下还有机型，请先删除机型');
		$result = Theme_Service_Series::deleteSeries($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
