<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class Gionee_Service_H5game
 */
class Gionee_Service_H5game {


	/**
	 *
	 * @return Gionee_Dao_H5game
	 */
	static public function getDao() {
		return Common::getDao("Gionee_Dao_H5game");
	}

    /**
     *
     * @return Gionee_Dao_H5gameType
     */
    static public function getTypeDao() {
        return Common::getDao("Gionee_Dao_H5gameType");
    }

    /**
     *
     * @return Gionee_Dao_H5gameCate
     */
    static public function getCateDao() {
        return Common::getDao("Gionee_Dao_H5gameCate");
    }
}