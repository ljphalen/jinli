<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 浏览器购物搜索接口
 *
 */
class Browser_SearchController extends Api_BaseController {
	
	/**
	 * 关键字
	 */
	public function keywordsAction() {
		$webroot = Common::getWebRoot();
		list(,$list) =  Client_Service_Keywords::getList(1, 8, array('status'=>1));
		$data = array();
		foreach ($list as $key=>$value) {
			$data[$key]['keyword'] = $value['keyword'];
			$data[$key]['link'] = $webroot.'/redirect/browserSearch?keyword='.$value['keyword'];
		}		
		$this->output(0, '',  array('keywords'=>$data, 'form_action'=>$webroot.'/redirect/browserSearch'));
	}
}
