<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Channel_ColumnController extends Api_BaseController {

	/**
	 * 客户端导航接口
	 */
	public function navAction() {
		$columnVersion = Game_Service_Config::getValue('Column_Nav_Version');
		$version = $this->getInput('v');
		
		if ($version >= $columnVersion) exit();		
		
		$data = array();
		$data['version'] = $columnVersion;
		$data['sign'] = 'GioneeGameHall';
		
		$columns = Client_Service_Column::getAllColumn();
		$navs = $this->_buildCanUseColumn($columns);
		
		if (empty($navs)) exit();
		
		$tmp = array();
		self::_cookColumn($tmp, $navs);
		$data['data'] = $tmp;
		
		echo json_encode($data);
	}
	
	private function _cookColumn(&$output, $navs) {
		$webroot = Common::getWebRoot();
		foreach ($navs as $key=>$value) {
			//处理外发包url
			$tmp = parse_url(html_entity_decode($value['link']));
			if($tmp['query']){
				$link = $webroot . ereg_replace("/client/", "/channel/", $tmp['path']) . '?' . $tmp['query'];
			} else {
				$link = $webroot . ereg_replace("/client/", "/channel/", $tmp['path']);
			}
			$nav = array(
					'name' => html_entity_decode($value['name']),
					'url' => html_entity_decode($link)
			);
			if ($value['items']) {
				unset($nav['url']);
				self::_cookColumn($nav['items'], $value['items']);
			}
			$output[] = $nav;
		}
	}
	
	
	/**
	 * 处理获取所需的导航数据
	 * @param array $items
	 * @param number $pid
	 * @return array
	 */
	private function _buildCanUseColumn($items, $pid = 0) {
		$items = Common::resetKey($items, 'id');
		$tmp = array();
		foreach ($items as $value){
			if(($value['pid'] == $pid) && ($value['status'] == 1)){
				$tmp[$value['id']] = $value;
				$childs = self::_buildCanUseColumn($items, $value['id']);
				if (!empty($childs)){
					$tmp[$value['id']]['items'] = $childs;
				}
			}
		}
		return $tmp;
	}
}
