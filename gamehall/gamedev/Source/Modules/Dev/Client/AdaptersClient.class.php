<?php
/**
 * SDK版本处理的逻辑类
 *
 * @name AdaptersClient.class.php
 * @author yang.xia c61811@163.com
 * @datetime 2013-12-15 08:27:30
 */
class AdaptersClient {
	/**
	 * 获取产品修改sdk适用版本记录
	 *
	 * @param int $hid
	 *        	修改记录ID
	 * @param int $appid        	
	 * @param int $apkid        	
	 */
	public static function getHistorySdk($hid, $appid, $apkid) {
		$oModel = D ( 'Orther' );
		$oModel->setProperty ( 'trueTableName', '`app_adapters_history`' );
		
		$sdkinfo = $oModel->where ( array (
				'history_id' => $hid 
		) )->select ();
		
		if (! $sdkinfo) {
			
			$d = D ( 'AppAdapters' );
			$sdkinfo = $d->appAdaptersInfo ( $appid, $apkid );
			if ($sdkinfo)
				$sdkinfo = $sdkinfo [0];
			
			if (! $sdkinfo) {
				$apkinfo = D ( 'ApkFiles' )->where ( array (
						'id' => $apkid 
				) )->find ();
				if ($apkinfo) {
					$minsdk = $apkinfo ['min_sdk_version'];
					$tarsdk = $apkinfo ['target_sdk_version'];
					$screens = $apkinfo ['supports_screens'];
				} else {
					$minsdk = 3;
					$tarsdk = 0;
					$screens = '';
				}
				$sdkinfo = array_merge ( self::sdk ( $minsdk, $tarsdk ), self::screens ( $screens ) );
			}
			
			$sdkinfo ['history_id'] = $hid;
			$sdkinfo ['app_id'] = $appid;
			$sdkinfo ['apk_id'] = $apkid;
			$cttime = date ( 'Y-m-d H:i:s' );
			$sdkinfo ['created_at'] = $cttime;
			$sdkinfo ['updated_at'] = $cttime;
			$oModel->data ( $sdkinfo )->add ();
		} else {
			$sdkinfo = $sdkinfo [0];
		}
		
		return $sdkinfo;
	}
	/**
	 * 保存信息
	 *
	 * @param unknown_type $sdkinfo        	
	 * @return number
	 */
	public static function saveHistorySdk($sdkinfo) {
		$oModel = D ( 'Orther' );
		$oModel->setProperty ( 'trueTableName', '`app_adapters_history`' );
		
		$hid = $sdkinfo ['history_id'];
		$appid = $sdkinfo ['app_id'];
		$apkid = $sdkinfo ['apk_id'];
		$where = array (
				'app_id' => $appid,
				'apk_id' => $apkid,
				'history_id' => $hid 
		);
		$adinfo = $oModel->where ( $where )->select ();
		if ($adinfo) {
			unset ( $sdkinfo ['history_id'] );
			unset ( $sdkinfo ['app_id'] );
			unset ( $sdkinfo ['apk_id'] );
			
			$r = $oModel->where ( $where )->save ( $sdkinfo );
		} else
			$r = $oModel->data ( $sdkinfo )->add ();
			
			// echo $oModel->getLastSql();
			// exit;
		if ($r === false)
			return 0;
		return 1;
	}
	/**
	 * 产品适用版本
	 *
	 * @param unknown_type $minsdk        	
	 * @return array $sdkarr
	 */
	public static function sdk($minsdk, $targetSdkVersion = 0) {
		$sdkconfig = C ( 'APK' );
		$sdkarr = array ();
		foreach ( $sdkconfig ['SDK_VER'] as $k => $v ) {
			if ($k >= $minsdk && ($k <= $targetSdkVersion || ! $targetSdkVersion)) {
				$sdkarr [$v] = 1;
			} else {
				$sdkarr [$v] = 0;
			}
		}
		return $sdkarr;
	}
	/**
	 * 适应屏幕分辨率
	 *
	 * @param unknown_type $screens        	
	 * @return multitype:number |number
	 */
	public static function screens($screens) {
		if (! $screens) {
			return array (
					'res_240_320' => 1,
					'res_320_480' => 1,
					'res_480_800' => 1,
					'res_480_854' => 1 
			);
		}
		$screens = explode ( ' ', $screens );
		
		foreach ( $screens as $v ) {
			if (mb_strtolower ( $v ) == "'small'")
				$screensarr ['res_240_320'] = 1;
			elseif (mb_strtolower ( $v ) == "'normal'")
				$screensarr ['res_320_480'] = 1;
			elseif (mb_strtolower ( $v ) == "'large'")
				$screensarr ['res_480_800'] = 1;
			elseif (mb_strtolower ( $v ) == "'xlarge'")
				$screensarr ['res_480_854'] = 1;
		}
		return $screensarr;
	}
}

?>