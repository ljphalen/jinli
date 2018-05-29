<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Service_IccidConvert
 * @author lichanghua
 *
 */
class Freedl_Service_IccidConvert{
	/** ICCID中国移动代码 */
	const CMCC_CODE_1 = '898600';
	/** ICCID中国移动代码 */
	const CMCC_CODE_2 = '898602';
	/** ICCID中国联通代码 */
	const CU_CODE = '898601';
	/** ICCID中国电信代码 */
	const CTC_CODE = '898603';
	
	/** 中国移动标识符 */
	const CMCC = 'cmcc';
	/** 中国联通标识符 */
	const CU = 'cu';
	/** 中国电信标识符 */
	const CTC = 'ctc';
	
	/** 中国移动地区标识符起始位置 */
	const CMCC_PROV_START = 8;
	const CMCC_PROV_LENGTH = 2;
	/** 中国联通地区标识符起始位置 */
	const CU_PROV_START = 9;
	const CU_PROV_LENGTH = 2;
	/** 中国电信地区标识符起始位置 */
	const CTC_PROV_START = 10;
	const CTC_PROV_LENGTH = 3;
	
	/**
	 * 根据不同移动运营商返回对应的内部编码
	 * @param $iccid
	 * @return string
	 */
	public static function convert($iccid) {
		if ((substr($iccid, 0, 6) == self::CMCC_CODE_1) || (substr($iccid, 0, 6) == self::CMCC_CODE_2)) {
			return self::_convertCMCCIccid($iccid);
		} else if (substr($iccid, 0, 6) == self::CU_CODE) {
			return self::_convertCUIccid($iccid);
		} else if (substr($iccid, 0, 6) == self::CTC_CODE) {
			return self::_convertCTCIccid($iccid);
		}
		return $iccid;
	}
	
	/**
	 * 移动iccid地区编码转换
	 * @param unknown $iccid
	 */
	private static function _convertCMCCIccid($iccid){
			$provinceCode = array(self::CMCC, substr($iccid, self::CMCC_PROV_START, self::CMCC_PROV_LENGTH));
			return $provinceCode;
	}
	
	/**
	 * 联通iccid地区编码转换
	 * @param unknown $iccid
	 */
	private static function _convertCUIccid($iccid){
		$regcode =  Common::getConfig("apiConfig", 'cuprov'); //联通地区编码
		$provinceCode = array(self::CU, $regcode[substr($iccid, self::CU_PROV_START, self::CU_PROV_LENGTH)]);
		return $provinceCode;
	}
	
	/**
	 * 电信iccid地区编码转换
	 * @param unknown $iccid
	 */
	private static function _convertCTCIccid($iccid){
		$regcode =  Common::getConfig("apiConfig", 'ctcprov'); //电信地区编码
		$provinceCode = substr($iccid, self::CTC_PROV_START, self::CTC_PROV_LENGTH);
		if((substr($provinceCode, 0, 2) != '01') && (substr($provinceCode, 0, 2) != '02') && (substr($provinceCode, 0, 2) != '89')){
			$provinceCode = substr($provinceCode, 0, 2);
		}
		$provinceCode = array(self::CTC, $regcode[$provinceCode]);
		return $provinceCode;
	}
}