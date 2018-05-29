<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * (BI数据) 同步imei点击分类数据
 * @author liyf
 *
 */
class Client_Service_CategoryDailySync {
    
    public static function syncBICategoryDaily($dateFilter) {
        
        $logId = 0;
        $start = 0;
        //日志
        $categorySyncResultItem = self::getCategoryDailySyncResultDao()->getBy(array('sync_date' => $dateFilter));
        if (isset($categorySyncResultItem['id']) && $categorySyncResultItem['id'] > 0) {
            //已经运行完毕 过滤
            if ($categorySyncResultItem['status'] != '0') {
                return false;
            }
            $logId = $categorySyncResultItem['id'];
            $start = $categorySyncResultItem['start'];
            $logData = array();
            //标记成功
            $logData['status'] = 2;
            $logData['error_msg'] = "执行中";
            $res = self::getCategoryDailySyncResultDao()->update($logData, $logId);
        } else {
            $logData = array();
            $logData['sync_date'] = $dateFilter;
            //标记成功
            $logData['status'] = 2;
            $logData['error_msg'] = "执行中";
            $res = self::getCategoryDailySyncResultDao()->insert($logData);
            $logId = self::getCategoryDailySyncResultDao()->getLastInsertId();
        }
        
        if ($logId <= 0) {
            //日志写入失败
            return false;
        }
        
        //分页
        $pagesize = 5000;
        $rowCount = self::getBICategoryDailyDao()->count(array('day_id' => $dateFilter));
        //var_dump($rowCount);
        
        //没有数据源
        if ($rowCount <= 0) {
            $logData = array();
            //标记失败
            $logData['status'] = 2;
            $logData['error_msg'] = "BI数据源为空";
            $res = self::getCategoryDailySyncResultDao()->update($logData, $logId);
            return false;
        }
        
        $logData = array();
        $logData['row_count'] = $rowCount;
        $res = self::getCategoryDailySyncResultDao()->update($logData, $logId);
        
        while (true) {
            //var_dump($start);
            $sourceData = self::getBICategoryDailyDao()->getList($start, $pagesize, array('day_id' => $dateFilter), array('imei' => 'asc', 'first_category_id' => 'asc'));
            //var_dump(count($sourceData));
            
            if (count($sourceData) <= 0) {
                //没有数据源
                break;
            }
            
            $groupData = array();
            foreach ($sourceData as $sourceItem) {
                if (!preg_match('/^[0-9a-f]+$/i', $sourceItem['imei'])) {
                    //var_dump($sourceItem['imei']);
                    continue;
                }
                $groupData[$sourceItem['imei']][$sourceItem['first_category_id']] += $sourceItem['pv'];
            }
        
            if (count($groupData) <= 0) {
                //没有有效的数据
                break;
            }
        
            //写入游戏大厅数据库
            foreach ($groupData as $imei => $dateCategoryClick) {
                if (!is_array($dateCategoryClick) || count($dateCategoryClick) <= 0) {
                    //空数据 过滤
                    continue;
                }
                //查询现存数据
                $categoryClickItem = self::getCategoryClickStatisticsDao()->getByImei($imei);
                if (isset($categoryClickItem['id']) && $categoryClickItem['id'] > 0) {
                    //var_dump($imei);
                    $categoryJsonData = json_decode($categoryClickItem['category_json'], true);
                    if (is_array($categoryJsonData) && count($categoryJsonData) > 0) {
                        foreach ($dateCategoryClick as $categoryId => $pv) {
                            if ($pv <= 0) {
                                // 无增加点击 不修改
                                continue;
                            }
                            $categoryJsonData[$categoryId] += $pv;
                        }
                    } else {
                        $categoryJsonData = $dateCategoryClick;
                    }
                    //累加点击数据
                    $upData = array();
                    $upData['category_json'] = json_encode($categoryJsonData);
                    $res = self::getCategoryClickStatisticsDao()->update($upData, $imei, $categoryClickItem['id']);
                } else {
                    //添加点击数据
                    $inData = array();
                    $inData['imei'] = $imei;
                    $inData['category_json'] = json_encode($dateCategoryClick);
                    $id = self::getCategoryClickStatisticsDao()->insert($inData);
                }
            }
        
            unset($sourceData);
            unset($groupData);
        
            //下一页
            $start += $pagesize;
        
            //是否最后一页 是则结束
            if ($start > $rowCount) {
                break;
            }
        
            //log
            $logData = array();
            $logData['start'] = $start;
            $res = self::getCategoryDailySyncResultDao()->update($logData, $logId);
        
        }
        
        //log
        $logData = array();
        $logData['start'] = $start;
        $logData['status'] = 1;
        $logData['error_msg'] = "完成";
        $res = self::getCategoryDailySyncResultDao()->update($logData, $logId);
        
        return true;
    }

	/**
	 * @return Client_Dao_CategoryDailySyncResult
	 */
	public static function getCategoryDailySyncResultDao() {
		return Common_Dao_Factory::getDao("Client_Dao_CategoryDailySyncResult");
	}
	
	/**
	 * @return Client_Dao_BICategoryDaily
	 */
	public static function getBICategoryDailyDao() {
	    return Common_Dao_Factory::getDao("Client_Dao_BICategoryDaily");
	}
	
	/**
	 * @return Client_Dao_CategoryClickStatistics
	 */
	public static function getCategoryClickStatisticsDao() {
	    return Common_Dao_SplitFactory::getDao("Client_Dao_CategoryClickStatistics");
	}
}
