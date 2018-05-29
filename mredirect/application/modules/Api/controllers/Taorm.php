<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 * 淘热卖接口
 *
 */
class TaormController extends Api_BaseController {
	/**
	 * 淘热卖跳转地址
	 */
	public function taormurlAction() {
		$model = $this->getInput('model');
	
		$url = Gou_Service_Taobaourl::getBy(array('model'=>$model));
	
		if($model && $url) {
			Gou_Service_Taobaourl::updateTJ($url['id']);
			$this->redirect(html_entity_decode($url['url']));
		} else {
			$this->redirect('http://m.taobao.com');
		}
	
	}

}
