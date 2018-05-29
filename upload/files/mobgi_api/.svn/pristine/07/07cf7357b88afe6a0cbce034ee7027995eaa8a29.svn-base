<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2014-12-30 16:26:46
 * $Id: PushData.php 62100 2014-12-30 16:26:46Z hunter.fang $
 */

Doo::loadModel('datamodel/base/PushDataBase');

class PushData extends PushDataBase{
    //根据config_id或者plan_id获取计划消耗数据
    public function get_config_plan_data($conditions){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = 'sum(imp) as imp,sum(click) as click,sum(price) as statprice ';
        $whereArr['desc'] = 'createdate';
        // 当存在条件时。组合条件
        #$conditions["del"]=1;
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        if(!empty($result)){
            $result["imp"]=empty($result["imp"])?0:$result["imp"];
            $result["click"]=empty($result["imp"])?0:$result["click"];
            $result["percent"]=$result["imp"]==0?0:number_format($result["click"]/$result["imp"]*100,2);
            $result["imppirce"]=$result["imp"]==0?0:$result["statprice"]/1000/$result["imp"];
            $result["clickpirce"]=$result["click"]==0?0:$result["statprice"]/$result["click"];
        }
        return $result;
    }
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return "1=1";
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        
        if (isset($conditions['id'])){
            $where[] = "id = ".$conditions['id'];
        }
        if (isset($conditions['config_id']) && $conditions['config_id']){
            $where[] = "config_id = ".$conditions['config_id'];
        }
        if (isset($conditions['plan_id']) && $conditions['plan_id']){
            $where[] = "plan_id = ".$conditions['plan_id'];
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    
    /**
     * 根据配置id获取展示数，点击数，点击率。
     * @param type $configidsarr
     * @return boolean|int
     */
    public function getPushPlanRefreshData($configidsarr){
        if(empty($configidsarr) || !is_array($configidsarr)){
            return false;
        }
        $where = ' config_id in ('. implode(',', $configidsarr) .') ';
        $sql = "select  config_id, sum(imp) as total_imp, sum(click) as total_click from push_data where ". $where . " group by config_id ";
        
        $configlist=Doo::db()->query($sql)->fetchAll();
        if(!empty($configlist)){
            foreach($configlist as $key=>$item){
                //计算点击率
                $configlist[$key]['total_click'] = intval($configlist[$key]['total_click']);
                $configlist[$key]['total_imp'] = intval($configlist[$key]['total_imp']);
                if(!empty($configlist[$key]['total_imp'])){
                    $configlist[$key]['click_percent'] = round($configlist[$key]['total_click']/$configlist[$key]['total_imp'], 4)*100 ."%";
                }else{
                    $configlist[$key]['click_percent'] = 0;
                }
            }
        }
        return $configlist;
    }
}

