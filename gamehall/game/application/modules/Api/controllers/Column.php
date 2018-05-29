<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ColumnController extends Api_BaseController {

	/**
	 * 客户端导航接口
	 */
	public function navAction() {
		$columnVersion = Game_Service_Config::getValue('Column_Nav_Version');
		$version = $this->getInput('v');
		
		$this->saveColumnBehaviour();

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
	
	private function saveColumnBehaviour() {
		$imei = trim($this->getInput('imei'));
		if (!$imei) {
			$sp = $this->getInput('sp');
			$imei = Common::parseSp($sp, 'imei');
		}
		$behaviour = new Client_Service_ClientBehaviour($imei, Client_Service_ClientBehaviour::CLIENT_HALL);
        $behaviour->saveBehaviours(Client_Service_ClientBehaviour::ACTION_GET_COLUMN);
	}

	private function _cookColumn(&$output, $navs) {
		foreach ($navs as $key=>$value) {
			$nav = array(
					'name' => html_entity_decode($value['name']),
					'url' => html_entity_decode($value['link'])
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
