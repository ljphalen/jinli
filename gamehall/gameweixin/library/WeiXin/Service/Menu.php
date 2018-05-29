<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class WeiXin_Service_Menu
 */
class WeiXin_Service_Menu extends WeiXin_Service_Base{
	const WEIXIN_MAIN_TYPE_STRING = '';
	const WEIXIN_URL_TYPE_STRING = 'view';
	const WEIXIN_KEYWORD_TYPE_STRING = 'click';
	
	public static function createMenu($List) {
		return json_decode(self::requestByJsonParams('/menu/create', self::makeMenuJson($List)), true);
	}
	public static function delMenu() {
		return json_decode(self::requestByJsonParams('/menu/delete'), true);
	}
	
	public static function getAllMenu(){
	    return json_decode(self::requestByJsonParams('/menu/get'), true);
	}
	
	private static function makeMenuJson($List) {
		foreach($List as $key => $value) {
					if($value['opt_type'] == self::WEIXIN_MAIN_TYPE_STRING) {
						$newList['button'][$value['id']] = array('name' => $value['name']);
					} else {
						$newList['button'][$value['id']] = self::handleMainMenu($value);
					}
					foreach($value['smallList'] as $k => $v) {
					    $newList['button'][$value['id']]['sub_button'][] = self::handleSubMenu($v);
					}
		}
		ksort($newList['button']);
		$newList['button'] = array_values($newList['button']);
		return str_replace('\\', '', json_encode($newList, JSON_UNESCAPED_UNICODE));
	}
	
	private static function handleMainMenu($value) {
		$newMenuList = array(
				'type' => $value['opt_type'], 'name' => $value['name']
		);
		if($value['opt_type'] == self::WEIXIN_URL_TYPE_STRING) {
			$newMenuList['url'] = $value['menuset'];
		} else {
			$newMenuList['key'] = $value['menuset'];
		}
		return $newMenuList;
	}
	
	private static function handleSubMenu($value) {
		$newMenuList = array(
				'type' => $value['opt_type'], 'name' => $value['name']
		);
		if($value['opt_type'] == self::WEIXIN_URL_TYPE_STRING) {
			$newMenuList['url'] = $value['menuset'];
		} else {
			$newMenuList['key'] = $value['menuset'];
		}
		return $newMenuList;
	}
	
}