<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Widget_Service_Stat {
	/**
	 *
	 * @param int $pv
	 * @return bool
	 */
	public static function incrementPv($pv, $type) {
		$ret = self::_getPvDao()->getBy(array('dateline' => strtotime(date('Y-m-d')), 'tj_type' => intval($type)));
		if ($ret) return self::_getPvDao()->update(array('pv' => $pv + $ret['pv']), $ret['id']);
		return self::_getPvDao()->insert(array('pv' => $pv, 'tj_type' => intval($type), 'dateline' => strtotime(date('Y-m-d'))));
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getPvLineData($sDate, $eDate) {
		$data   = self::_getPvDao()->getsBy(array('dateline' => array(array('>=', $sDate), array('<=', $eDate)), 'tj_type' => 0));
		$pvData = array();
		$field  = array(
			array('pv', 'PV'),
		);
		foreach ($data as $k => $v) {
			foreach ($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$pvData[$key]['name']   = $value[1];
					$pvData[$key]['data'][] = array(($v['dateline'] * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $pvData);
	}

	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return array()
	 */
	public static function getVisitData($sDate, $eDate) {
		$data   = Common::getDao("Widget_Dao_Visit")->getsBy(array('date' => array(array('>=', $sDate), array('<=', date))));
		$pvData = array();

		$filed = array(
			'WidgetController::indexAction'    => 'index',
			'WidgetController::versionAction'  => 'version',
			'WidgetController::sortAction'     => 'sort',
			'WidgetController::newsAction'     => 'news',
			'WidgetController::columnAction'   => 'column',
			'WidgetController::version2Action' => 'version2',
		);

		foreach ($data as $k => $v) {
			$d   = substr($v['date'], 0, 8);
			$val = json_decode($v['val'], true);

			foreach ($filed as $keyName => $valName) {
				$pvData[$valName][$d] += isset($val[$keyName]) ? $val[$keyName] : 0;
			}

		}
		return $pvData;
	}

	/**
	 *
	 * @return Widget_Dao_Pv
	 */
	private static function _getPvDao() {
		return Common::getDao("Widget_Dao_Pv");
	}
}
