<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TmpController extends Front_BaseController {
	/**
	 * 专题界面
	 */
	public function cleanAction() {
		$ids = $_GET['ids'];

		$imei = $_GET['imei'];
		$rcKey = Common::KN_W3_IDS . $ids;
		var_dump($rcKey);


		echo "<hr>";
		echo "<hr>";
		list($urlIds, $topList, $idsArr) = W3_Service_Source::filterIds($ids);
		var_dump($topList);
		echo "<hr>";
		var_dump($idsArr);
		echo "<hr>";
		$adPosArr = W3_Service_Source::getUserAdPos($imei);
		var_dump($adPosArr);
		echo "<hr>";
		$idsVal   = Widget_Service_Source::filterAdIds($topList, $idsArr, $adPosArr);
		var_dump($idsVal);
		echo "<hr>";

		$ret   = Common::getCache()->delete($rcKey);
		var_dump($ret);
		exit;

	}

}
