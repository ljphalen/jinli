<?php
/**
 * 
 * @author monkey
 *
 */
class AnnouncementClient extends AnnouncementModel {
	private static $daoObj;
	private static function dao() {
		if (! isset ( self::$daoObj )) {
			self::$daoObj = D ( "Announcement" );
		}
		return self::$daoObj;
	}
	
	/**
	 * 检测用户信息
	 * @param int $uid
	 */
	public static function getAnnouncement()
	{
		return self::dao()->getAnnouncement1();
	}
}
?>