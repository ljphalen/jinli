<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class Gou_Service_Stat{
	
	/**
	 * 
	 * @param int $pv
	 * @return bool
	 */
	public static function incrementPv($pv) {
		return self::_increment(array('pv'=>$pv));
	}

    /**
     * 增加pv
     * @param string $field 字段名称
     * @param int    $pv    pv数量
     * @return bool|int
     */
    public static function increment($field, $pv) {
		return self::_increment(array($field=>$pv));
	}
	
	/**
	 * increment same show
	 * @param int $same_show
	 * @return bool
	 */
	public static function incrementSSPv($same_show) {
		return self::_increment(array('same_show'=>$same_show));
	}	
	
	/**
	 * increment same show
	 * @param int $same_hits
	 * @return bool
	 */
	public static function incrementSHPv($same_hits) {
		return self::_increment(array('same_hits'=>$same_hits));
	}

	/**
	 * 收藏商品降价提示pv增加
	 * @param int $reduce
	 * @return bool
	 */
	public static function incrementRGPv($reduce) {
		return self::_increment(array('reduce_goods'=>$reduce));
	}

	/**
	 *
	 * @param int $pv
	 * @return bool
	 */
	public static function incrementUv($uv) {
		$ret = self::_getUvDao()->getBy(array('dateline' => date('Y-m-d')));
		return self::_getUvDao()->replace(array('uv' => $ret['uv'] + $uv, 'dateline' => date('Y-m-d')));
	}

	public static function _increment($data) {
		$ret = self::_getPvDao()->getBy(array('dateline'=>date('Y-m-d')));
		if (!$ret) $ret = array(
			'apk_same_hits'		=>0,
			'ios_same_hits'		=>0,
			'apk_same_show'		=>0,
			'ios_same_show'		=>0,
			'reduce_goods'	=>0,
			'pv'=>0,
		);
		$data = self::cookData($data);
        $replacement = array(
            'pv'=>$ret['pv']+$data['pv'],
            'dateline'=>date('Y-m-d'),
            'apk_same_hits'=>$ret['apk_same_hits']+$data['apk_same_hits'],
            'ios_same_hits'=>$ret['ios_same_hits']+$data['ios_same_hits'],
            'apk_same_show'=>$ret['apk_same_show']+$data['apk_same_show'],
            'ios_same_show'=>$ret['ios_same_show']+$data['ios_same_show'],
            'reduce_goods' =>$ret['reduce_goods']+$data['reduce_goods'],
        );
        return self::_getPvDao()->replace($replacement);
	}

	public static function cookData($data) {
		$tmp = array(
			'apk_same_hits'		=>0,
			'ios_same_hits'		=>0,
			'apk_same_show'		=>0,
			'ios_same_show'		=>0,
			'reduce_goods'   	=>0,
			'pv'			    =>0
		);
		if (isset($data['apk_same_hits'])) $tmp['apk_same_hits'] = $data['apk_same_hits'];
		if (isset($data['ios_same_hits'])) $tmp['ios_same_hits'] = $data['ios_same_hits'];
		if (isset($data['apk_same_show'])) $tmp['apk_same_show'] = $data['apk_same_show'];
		if (isset($data['ios_same_show'])) $tmp['ios_same_show'] = $data['ios_same_show'];
		if (isset($data['reduce_goods'])) $tmp['reduce_goods'] = $data['reduce_goods'];
		if (isset($data['pv'])) $tmp['pv'] = $data['pv'];
		return $tmp;
	}
		
	/**
	 * 
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getPvLineData($sDate, $eDate) {
		if (!$data = self::getPvList($sDate, $eDate)) return array();
		$lineData = array();
		$field = array(
			array('pv', '访问量'),
			array('apk_same_show', 'apk同款显示'),
			array('ios_same_show', 'ios同款显示'),
			array('apk_same_hits', 'apk同款点击'),
			array('ios_same_hits', 'ios同款点击'),
			array('reduce_goods', '商品收藏降价'),
		);
		foreach($data as $k => $v) {
			foreach($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$lineData[$key]['name'] = $value[1];
					$lineData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $lineData);
	}
	
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:|Ambigous <multitype:, multitype:number >
	 */
	public static function getUvLineData($sDate, $eDate) {
		if (!$data = self::getUvList($sDate, $eDate)) return array();
		$lineData = array();
		$field = array(
				array('uv', '访问量'),
		);
		foreach($data as $k => $v) {
			foreach($field as $key => $value) {
				if (isset($v[$value[0]])) {
					$lineData[$key]['name'] = $value[1];
					$lineData[$key]['data'][] = array((intval(strtotime($v['dateline'])) * 1000), intval($v[$value[0]]));
				}
			}
		}
		return array($data, $lineData);
	}
	
	/**
	 * 
	 */
	public static function _resetArray($list) {
		$temp = array();
		foreach ($list as $key=>$value) {
			if ($temp[$value['dateline']]) {
				$temp[$value['dateline']]['total'] + 1;
			} else {
				$temp[$value['dateline']]['dateline'] = $value['dateline'];
				$temp[$value['dateline']]['total'] = 1;
			}
		}
		return $temp;
	}
	
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:
	 */
	public static function getPvList($sDate, $eDate) {
		return self::_getPvDao()->getListByTime($sDate, $eDate);
	}
	
	/**
	 *
	 * @param unknown_type $sDate
	 * @param unknown_type $eDate
	 * @return multitype:
	 */
	public static function getUvList($sDate, $eDate) {
		return self::_getUvDao()->getListByTime($sDate, $eDate);
	}
	
	/**
	 *
	 * @return Gou_Dao_Pv
	 */
	private static function _getPvDao() {
		return Common::getDao("Gou_Dao_Pv");
	}
	
	/**
	 *
	 * @return Gou_Dao_Uv
	 */
	private static function _getUvDao() {
		return Common::getDao("Gou_Dao_Uv");
	}
}
