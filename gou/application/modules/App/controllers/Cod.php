<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class CodController extends App_BaseController {
	
	public $perpage = 4;
	public $actions = array(
				'tjUrl'=>'/cod/tj'
			);

	public function indexAction() {
		$this->assign('hasfooter', false);
		$this->assign('title', '货到付款');
	}
	
	
	/**
	 * 货到付款搜索
	 */
	public function searchAction() {
		$keyword = $this->getInput('keyword');
		$this->redirect('http://mmb.cn/wap/touch/s.jsp?fr=54763&keyword='.urlencode($keyword));
	}
	
	public function tjAction() {
		$id = intval($this->getInput('id'));
		$type = $this->getInput('type');
		$url = html_entity_decode(urldecode($this->getInput('_url')));
		if (!id || !$type) return false;
		switch ($type)
		{
			case COD_GUIDE:
				Cod_Service_Guide::updateTJ($id);
				break;
			case COD_AD:
				Cod_Service_Ad::updateAdTJ($id);
				break;
				case COD_TYPE:
					Cod_Service_Type::updateTJ($id);
					break;
			default:
		}
		$this->redirect($url);
	}
}
