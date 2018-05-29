<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 搜索
 *
 */
class StatController extends Api_BaseController {
	
	/**
	 * 关键字
	 */
	public function redirectAction() {
		$id = intval($this->getInput('id'));
		$type = $this->getInput('type');
		$url = html_entity_decode(urldecode($this->getInput('_url')));
		if (!id || !$type) return false;
		switch ($type)
		{
			case AD:
				Gou_Service_Ad::updateTj($id);
				break;
			case CHANNEL:
				Gou_Service_Channel::updateTj($id);
				break;
			case COD_GUIDE:
				Cod_Service_Guide::updateTj($id);
				break;
			case COD_TYPE:
				Cod_Service_Type::updateTj($id);
				break;
			default:
		}		
		$this->redirect($url);
	}
}
