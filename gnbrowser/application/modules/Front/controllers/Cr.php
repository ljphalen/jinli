<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 统计点击量
 * @author tiansh
 *
 */
class CrController extends Front_BaseController{
	
	public $actions = array(
		'listUrl' => 'Cr/index',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$url = trim($this->getInput('url'));
		//统计点击量
 		if ($url) {
 				$category = Gionee_Service_Crcategory::getDataByUrl(md5($url));
	 			if ($category) {
	 				$ret = Common::getCache()->get('3g_cr');
	 				
	 				$key = md5($url);
	 				
	 				if (!is_array($ret)) $ret = array();
	 				if (isset($ret[$key])) {
	 					$ret[$key]['click'] += 1;
	 					$ret[$key]['category_id'] = $category['id'];
	 				} else {
	 					$ret[$key] = array('url'=>$url, 'click'=>1, 'category_id'=> $category['id']);
	 				}
	 				Common::getCache()->set('3g_cr', $ret);
	 			}
 		}
 		$this->redirect($url);
 		exit;
	}	
}
