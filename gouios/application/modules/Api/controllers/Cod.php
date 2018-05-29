<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class CodController extends Api_BaseController {
	
	public $actions =array(
			'tjUrl'=>'/api/stat/redirect'
	);
	
	/**
	 * cod
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$perpage = 4;
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
		
		//cache version
		$version = Gou_Service_Config::getValue('Cod_Version');
		
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		
		list($total, $types) = Cod_Service_Type::getList($page, $perpage, array('status'=>1));
		$data = array();
		$i = ($page - 1) * $this->perpage + 1;
		foreach ($types as $key=>$value) {
			$data[$key]['type_name'] = Util_String::substr($value['title'], 0, 10, true);
			$data[$key]['type_dir'] = $i % 2 == 0 ? 1 : 0;
			$data[$key]['color'] = trim(html_entity_decode($value['color']));
			$data[$key]['link'] = Common::tjurl($tjUrl, $value['id'], 'COD_TYPE', $value['url'], '2'.$i, 1);
			
			//pic
			$img_guides[$key] = Cod_Service_Guide::getCanUseImgGuides(0, 1, array('ptype'=>$value['id']));
			$data[$key]['img_data'] = array();
			if($img_guides[$key]) {
				$data[$key]['img_data']['img'] = $webroot . '/attachs' . $img_guides[$key][0]['img'];
				$data[$key]['img_data']['title'] = Util_String::substr($img_guides[$key][0]['title'], 0, 10, true);
				$data[$key]['img_data']['link'] =  Common::tjurl($tjUrl, $img_guides[$key][0]['id'], 'COD_GUIDE', $img_guides[$key][0]['link'], '2'.$i, 2);
			}
			
			//text
			$text_guides[$key] = Cod_Service_Guide::getCanUseTextGuides(0, 10, array('ptype'=>$value['id']));
			$text_data = array();
			$n = 1;
			foreach ($text_guides[$key] as $k=>$val) {
				$text_data[$k]['title'] = Util_String::substr($val['title'], 0, 10, true);
				$text_data[$k]['link'] =  Common::tjurl($tjUrl, $val['id'], 'COD_GUIDE', $val['link'], '2'.$i, '3'.$n);
				$text_data[$k]['color'] = trim(html_entity_decode($val['color']));
				$n++;
			}
			$data[$key]['text_data'] = $text_data;
			$i++;
		}
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page, 'version'=>$version));
	}
	
	
	/**
	 * search_form
	 */
	public function search_formAction() {
		$client_version = intval($this->getInput('client_version'));
		$client_data_version = intval($this->getInput('client_data_version'));
		
		$webroot = Common::getWebRoot();
		$action = $webroot.'/cod/search';
		$this->output(0, '', array('action'=>$action, 'version'=>Common::getConfig('siteConfig', 'version')));
	}
	
	/**
	 * search
	 */
	public function searchAction() {
		$keyword = trim($this->getInput('keyword'));
	
		if($keyword) {
			$info = array(
					'keyword'=>$keyword,
					'keyword_md5'=>md5($keyword),
					'create_time'=>Common::getTime(),
					'dateline'=>date('Y-m-d', Common::getTime())
			);
			Gou_Service_KeywordsLog::addKeywordsLog($info);
		}
	
		$this->redirect('http://mmb.cn/wap/touch/s.jsp?fr=54763&keyword='.$keyword);
	}
}
