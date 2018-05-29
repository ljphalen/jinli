<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * url转换 仅测试用
 * @author tiansh
 *
 */
class UrlController extends Front_BaseController{
	
	public $actions = array(
		'indexUrl' => '/Url/index',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$info = $this->getPost(array('type', 'url', 'token'));
		
		if($info['token']){
			if($info['url']) {
				$url = 'http://3g.gionee.com/redirect?type='.$info['type'].'&url='.urlencode(trim(html_entity_decode($info['url'])));
			}
		}
		
 		$this->assign('url', $url);
	}
}
