<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class ClientserverController extends Front_BaseController {
	public function getStartIconAction(){
		$imageurl = Yaf_Application::app()->getConfig()->fontcroot . "/attachs/theme";
		$orderBy = array(
			'created_time' => 'desc'
		);
		$icon = Theme_Service_Client::searchBy(0, 1, 1, $orderBy);
		$result = array();
		if(!empty($icon) && $icon[0]['show']){
			$result['show'] = 1;
			$result['url'] = $imageurl . '/startIcon/' . $icon[0]['path'];
			$result['created_time'] = $icon[0]['created_time'];
		} else {
			$result['show'] = 0;
		}
		echo json_encode($result);
        exit;
	}
}