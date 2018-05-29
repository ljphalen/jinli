<?php

if (!defined('BASE_PATH'))
    exit('Access Denied!');

class Game_Service_H5HomeAutoHandle {

    private static $mTable = array();
    private static $mOpenTmp = true;

    const CACHE_SET_IN_MAIN = 1;
    const CACHE_SET_IN_TMP = 0;

    private static function getTmpTableConfig($getTableName = 'tmp') {
        $_tmpTableName = array(
            'game_h5_recommend_banner_tmp',
            'game_h5_recommend_games_tmp',
            'game_h5_recommend_hdnew_tmp',
            'game_h5_recommend_imgs_tmp',
            'game_h5_recommend_rank_tmp',
            'game_h5_recommend_tmp',
        );
        $_tableMainName = array(
            'game_h5_recommend' => array(
                'list' => array(
                    'game_h5_recommend_games',
                    'game_h5_recommend_hdnew',
                    'game_h5_recommend_imgs',
                    'game_h5_recommend_rank',
                ),
                'fetchKey' => 'recommend_id'
            ),
            'game_h5_recommend_banner' => array()
        );
        $tableList = $getTableName == 'tmp' ? $_tmpTableName : $_tableMainName;
        return $tableList;
    }

    public static function initID($dayid) {
        return Util_Cookie::set('changeDay', $dayid, true, Common::getTime() + (2 * 3600));
    }

    public static function getID() {
        return Util_Cookie::get('changeDay', true);
    }

    public static function handleTmpDataToSave() {
        Common_Service_Base::beginTransaction();
        $tmpTableName = self::getTmpTableConfig();
        try {
            foreach ($tmpTableName as $key => $table) {
                $listDataTmp = self::getTableName($table)->getList('0', '99', array('tmp_id' => self::getID()));
                $showField = self::getTableField($table);
                self::SaveToMainTable($table, $listDataTmp, $showField);
            }
            self::deleteTmpRowHandle();
            Common_Service_Base::commit();
            return true;
        } catch (Exception $e) {
            Common_Service_Base::rollBack();
            return false;
        }
    }

    /**
     * 
     * @param int $fromDay
     * @param int $toDay
     * @return boolean
     */
    public static function copyDayToDay($fromDay, $toDay, $noTmpInit = false) {
        Common_Service_Base::beginTransaction();
        self::$mOpenTmp = $noTmpInit == true ? false : true;
        $tableNameList = self::getTmpTableConfig('main');
        try {
            foreach ($tableNameList as $key => $table) {
                $tableName = $key;
                $tmpIdList = self::saveToFirstStepData($tableName, array('day_id' => $fromDay), $toDay);
                if ($table['fetchKey'] && $tmpIdList) {
                    self::saveToNextStepData($table['list'], $tmpIdList);
                }
            }
            if ($noTmpInit) {
                $editInfo = Game_Service_H5RecommendList::getRecommendList($toDay);
                if (!$editInfo) {
                    $requestData = array();
                    $requestData['day_id'] = $toDay;
                    $requestData['create_time'] = Common::getTime();
                    Game_Service_H5RecommendList::addRecommendList($requestData);
                }
            }
            Common_Service_Base::commit();
            return true;
        } catch (Exception $e) {
            Common_Service_Base::rollBack();
            return false;
        }
    }

    public static function cleanAllOldTmpData($dayId) {
        Common_Service_Base::beginTransaction();
        $tmpTableName = self::getTmpTableConfig();
        $tmpTableName[] = 'game_h5_recommend_delete_tmp';
        try {
            foreach ($tmpTableName as $key => $table) {
                self::CleanOldTmpData($table);
            }
            $tableNameList = self::getTmpTableConfig('main');
            foreach ($tableNameList as $key => $table) {
                $tableName = $key;
                $tmpIdList = self::saveToFirstStepData($tableName, array('day_id' => $dayId), '');
                if ($table['fetchKey'] && ($tmpIdList || $dayId)) {
                    self::saveToNextStepData($table['list'], $tmpIdList, $dayId);
                }
            }
            Common_Service_Base::commit();
            return true;
        } catch (Exception $e) {
            Common_Service_Base::rollBack();
            return false;
        }
    }

    private static function deleteTmpRowHandle() {
        $params = array('tmp_id' => Game_Service_H5HomeAutoHandle::getID(), 'maininit' => 0);
        $deleteList = Game_Service_H5RecommendDelete::getsBy($params);
        foreach ($deleteList as $key => $value) {
            if (is_numeric($value['delete_id'])) {
                self::deleteTmpRowById($value['table_name'], $value['delete_id'], $value['table_main_name']);
            } else {
                self::deleteTmpRowByAnyThing($value['table_name'], $value['delete_id'], $value['table_main_name']);
            }
        }
    }

    private static function deleteTmpRowById($table, $id, $field) {
        $table = str_replace('_tmp', '', $table);
        self::getTableName($table)->deleteBy(array($field => $id));
    }

    private static function deleteTmpRowByAnyThing($table, $params, $field = '') {
        $params = unserialize($params);
        if (!$params)
            return;
        if (!isset($params['where'])) {
            $params = self::makeParams($params, $field);
        } else {
            $params = array($field => $params);
        }
        $table = str_replace('_tmp', '', $table);
        self::getTableName($table)->deleteBy($params);
    }

    private static function makeParams($params, $field) {
        return array($field => array('IN', $params));
    }

    private static function saveToFirstStepData($tableName, $param, $toDay = '') {
        $listDataTmp = self::getTableName($tableName)->getList('0', '99', $param);
        $tmpIdList = self::handleListToTmp($tableName, $listDataTmp, $toDay);
        return $tmpIdList;
    }

    private static function saveToNextStepData($tableList, $idList, $isCopy = '') {
        $oldIdList = !$isCopy ? array_keys($idList) : $idList;
        foreach ($tableList as $key => $table) {
            $listDataTmp = self::getTableName($table)->getList('0', '99', array('recommend_id' => array('IN', $oldIdList)));
            foreach ($listDataTmp as $key => $listInfo) {
                if (!$isCopy) {
                    unset($listInfo['id']);
                    $listInfo['recommend_id'] = $idList[$listInfo['recommend_id']];
                } elseif (self::$mOpenTmp == true) {
                    $listInfo['maininit'] = self::CACHE_SET_IN_MAIN;
                }
                if (self::$mOpenTmp == true) {
                    $listInfo['tmp_id'] = self::getID();
                }
                $tableTmp = self::$mOpenTmp == true ? $table . '_tmp' : $table;
                self::getTableName($tableTmp)->insert($listInfo);
            }
        }
        return true;
    }

    private static function getTableName($name) {
        if (isset(self::$mTable[$name]) && self::$mTable[$name]) {
            return self::$mTable[$name];
        } else {
            self::$mTable[$name] = self::getDao()->changeTableName($name);
            return self::$mTable[$name];
        }
    }

    private static function handleListToTmp($tableName, $list, $toDay = '') {
        $isCopy = $toDay ? FALSE : TRUE;
        foreach ($list as $key => $info) {
            $oldId = $info['id'];
            if (!$isCopy) {
                unset($info['id']);
                $info['create_time'] = $info['day_id'] = $toDay;
            } elseif (self::$mOpenTmp == true) {
                $info['maininit'] = self::CACHE_SET_IN_MAIN;
            }
            if (self::$mOpenTmp == true) {
                $info['tmp_id'] = self::getID();
            }
            $newTableName = self::$mOpenTmp == true ? $tableName . '_tmp' : $tableName;
            self::getTableName($newTableName)->insert($info);
            $newId = self::getTableName($newTableName)->getLastInsertId();
            if (!$isCopy) {
                $returnMergeList[$oldId] = $newId;
            } else {
                $returnMergeList[$oldId] = $oldId;
            }
        }
        return $returnMergeList;
    }

    private static function getTableField($table, $autoHidden = true) {
        $fieldList = Db_Adapter_Pdo::fetchAll("SHOW COLUMNS FROM `$table`");
        foreach ($fieldList as $key => $value) {
            if (!in_array($value['Field'], array('tmp_id', 'maininit')) || !$autoHidden) {
                $field[] = $value['Field'];
            }
        }
        return $field;
    }

    private static function CleanOldTmpData($table) {
        self::getTableName($table)->deleteBy(array('maininit' => self::CACHE_SET_IN_TMP));
        self::getTableName($table)->deleteBy(array('maininit' => self::CACHE_SET_IN_MAIN));
    }

    private static function SaveToMainTable($table, $list, $showField) {
        $table = str_replace('_tmp', '', $table);
        $list = self::addMutiInfo($list);
        self::getTableName($table)->mutiReplaceInsert($list, $showField);
    }

    private static function addMutiInfo($list) {
        if (!$list)
            return false;
        foreach ($list as $key => $value) {
            if (isset($value['tmp_id'])) {
                unset($list[$key]['tmp_id']);
            }
            if (isset($value['maininit'])) {
                unset($list[$key]['maininit']);
            }
        }
        return $list;
    }

    private static function getDao() {
        return Common::getDao("Game_Dao_H5HomeAutoSave");
    }

}
