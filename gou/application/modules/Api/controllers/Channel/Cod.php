<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Channel_CodController extends Api_BaseController {
	
	public $perpage = 4;
	public $actions = array(
				'guideUrl'=>'/api/cod/guide',
				'tjUrl'=>'/cod/tj'
			);
	public $cacheKey = 'Channel_Cod_index';
	
	
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		
		list($total, $types) = Cod_Service_Type::getList($page, $this->perpage, array('status'=>1));
		$data = array();
		$i = ($page - 1) * $this->perpage + 1;
		foreach ($types as $key=>$value) {
			$data[$key]['type_name'] = Util_String::substr($value['title'], 0, 10, true);
			$data[$key]['type_dir'] = $i % 2 == 0 ? 1 : 0;
			$data[$key]['color'] = html_entity_decode($value['color']);
			$data[$key]['link'] = Cod_Service_Type::getShortUrl(Stat_Service_Log::V_CHANNEL, $value);
			
			//pic
			$img_guides[$key] = Cod_Service_Guide::getCanUseImgGuides(0, 1, array('ptype'=>$value['id'], 'channel_id'=>3));
			$data[$key]['img_data']['img'] = Common::getAttachPath() . $img_guides[$key][0]['img'];
			$data[$key]['img_data']['title'] = Util_String::substr($img_guides[$key][0]['title'], 0, 10, true);
			$data[$key]['img_data']['link'] = Cod_Service_Guide::getShortUrl(Stat_Service_Log::V_CHANNEL, $img_guides[$key][0]);
			
			//text
			$text_guides[$key] = Cod_Service_Guide::getCanUseTextGuides(0, 10, array('ptype'=>$value['id'], 'channel_id'=>3));
			$text_data = array();
			$n = 1;
			foreach ($text_guides[$key] as $k=>$val) {
				$text_data[$k]['title'] = Util_String::substr($val['title'], 0, 10, true);
				$text_data[$k]['link'] =  Cod_Service_Guide::getShortUrl(Stat_Service_Log::V_CHANNEL, $val);
				$text_data[$k]['color'] = $val['color'];
				$n++;
			}
			$data[$key]['text_data'] = $text_data;
			$i++;
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	public function searchAction() {
		$webroot = Common::getWebRoot();
		//$action = $webroot.'/cod/search';
		$keyword = Gou_Service_Config::getValue('gou_cod_keyword');
		//hash
		$channel_cod_search =  json_decode(Gou_Service_Config::getValue('channel_cod_search'), true);
		$action = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_CHANNEL, $channel_cod_search['module_id'], $channel_cod_search['channel_id'], 0, $channel_cod_search['url'], 'channel货到付款搜索', $channel_cod_search['channel_code']);
		
		$this->output(0, '', array('action'=>$action, 'keyword'=>$keyword, 'name'=>'keyword'));
	}
}
