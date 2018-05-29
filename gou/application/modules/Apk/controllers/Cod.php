<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class CodController extends Apk_BaseController {
	
	public $perpage = 4;
	public $actions = array(
				'tjUrl'=>'/cod/tj'
			);

	public function indexAction() {
		list(, $ads) = Cod_Service_Ad::getCanUseAds(0, 5);
		$this->assign('ads', $ads);
		
		list(, $types) = Cod_Service_Type::getList(0, 100, array('status'=>1));
		$this->assign('types', $types);
		
		$type_ids = array();
		foreach($types as $key=>$value) {
			$type_ids[] = $value['id'];
		}
		
		$guides = Cod_Service_Guide::getByGuideTypes($type_ids);
		$tmp = array();
		foreach($guides as $key=>$value) {
			if (!is_array($tmp[$value['ptype']])) $tmp[$value['ptype']] = array();
			array_push($tmp[$value['ptype']], $value);
		}
		
		$this->assign('cache', Gou_Service_Config::getValue('gou_index_cache'));
		$this->assign('guides', $tmp);
		$this->assign('hasfooter', false);
		$this->assign('title', '货到付款');
	}
	

	/**
	 * 货到付款搜索
	 */
	public function searchAction() {
		$keyword = trim($this->getInput('keyword'));
		$this->redirect('http://mmb.cn/wap/touch/s.jsp?fr=54762&keyword='.urlencode($keyword));
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
			default:
		}
		$this->redirect($url);
	}
}
