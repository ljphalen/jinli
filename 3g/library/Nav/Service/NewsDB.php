<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 二级栏目-新闻 数据库
 * @author huwei
 *
 */
class Nav_Service_NewsDB {

	/**
	 *
	 * @return Nav_Dao_NewsColumn
	 */
	public static function getColumnDao() {
		return Common::getDao("Nav_Dao_NewsColumn");
	}

	/**
	 *
	 * @return Nav_Dao_NewsRecord
	 */
	public static function getRecordDao() {
		return Common::getDao("Nav_Dao_NewsRecord");
	}

	/**
	 *
	 * @return Nav_Dao_NewsSource
	 */
	public static function getSourceDao() {
		return Common::getDao("Nav_Dao_NewsSource");
	}

    /**
     *
     * @return Nav_Dao_NewsOp
     */
    public static function getOpDao() {
        return Common::getDao("Nav_Dao_NewsOp");
    }

}