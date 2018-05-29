<?php
Doo::loadModel('datamodel/base/RtbPlanBase');

class RtbPlan extends RtbPlanBase{
    
    /**
     * 返回记录数
     * @param type $conditions
     * @return type
     */
    public function records($conditions = NULL){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->count($whereArr);
        return $result;
    }
    
     /**
     * 查询列表-多条记录
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*,from_unixtime(start_time) as stime,from_unixtime(end_time) as etime,case go_method when 1 then "一次性有效" when 2 then "周期性有效" else "未知" end go_method,case method when 1 then "CPM" when 2 then "CPC" else "未知" end method,case runstatus when 1 then "未到开始时间" when 2 then "导量中" when 3 then "导量完毕" else "未知" end status';
        $whereArr['desc'] = 'state,runstatus ,createdate ';
        // 当存在条件时。组合条件
        #$conditions["del"]=1;
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->find($whereArr);
        return $result;
    }

    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*,from_unixtime(start_time) as stime,from_unixtime(end_time) as etime';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        return $result;
    }
    /**
     * 更新一条记录
     * @param Array $conditions
     */
    public function upd($conditions){
        foreach($conditions as $k=>$v){
            if($k==="start_time" || $k==="end_time"){
                $v=strtotime($v);
            }
            if(in_array($k,$this->_fields)){
                $this->$k=$v;
            }
        }

        try{
            $this->updatedate=time();
            if(isset($conditions["id"]) && !empty($conditions["id"])){
                if(!isset($conditions["maxprice"])){$this->maxprice=0;}
                if(!isset($conditions["maximp"])){$this->maximp=0;}
                if(!isset($conditions["maxclick"])){$this->maxclick=0;}
                return $this->update();
            }else{
                $this->createdate=time();
                return $this->insert();
            }
            return true;
        }  catch (Exception $e){
            return false;
        }
    }
    /*
     * 获取当前计划的导量状态
     * @param int id
     * @return 1未到开始时间,2导量中,3导量完毕
     */
    public function get_plan_state($planid){
        $plan=$this->findOne(array("id"=>$planid,"del"=>1));
        if(empty($plan))return 0;
        //maximp;||maxclick;&&maxprice
        if($this->parseMaxlimit($plan))return 3;
        if($plan["start_time"]>time())return 1;
        return 2;
    }
    /**
     * 根据配置id获取展示数，点击数，点击率。
     * @param type $configidsarr
     * @return boolean|int
     */
    public function getRtbPlanRefreshData($configidsarr){
        if(empty($configidsarr) || !is_array($configidsarr)){
            return false;
        }
        $where = ' config_id in ('. implode(',', $configidsarr) .') ';
        $sql = "select  config_id, sum(statimp) as total_statimp, sum(statclick) as total_statclick from rtb_plan where ". $where . " group by config_id ";
        $configlist=Doo::db()->query($sql)->fetchAll();
        if(!empty($configlist)){
            foreach($configlist as $key=>$item){
                //计算点击率
                $configlist[$key]['total_statclick'] = intval($configlist[$key]['total_statclick']);
                $configlist[$key]['total_statimp'] = intval($configlist[$key]['total_statimp']);
                if(!empty($configlist[$key]['total_statimp'])){
                    $configlist[$key]['click_percent'] = round($configlist[$key]['total_statclick']/$configlist[$key]['total_statimp'], 4)*100 ."%";
                }else{
                    $configlist[$key]['click_percent'] = 0;
                }
            }
        }
        return $configlist;
    }
    /*
     * 解析限制条件
     */
    private function parseMaxlimit($plan){
        if(empty($plan)){return false;}
        $condtion = explode(";", $plan["maxcondition"]);
        $currentmaxprice = $plan["maxprice"];
        $currentmaximp = $plan["maximp"];
        $currentmaxclick = $plan["maxclick"];
        $flag=true;
        foreach ($condtion as $k => $v){
            $or = strstr($v, "||");
            $and = strstr($v, "&&");
            $con = empty($or) ? $and : $or;
            $c=  str_replace($v,array("&&","||"),"");
            switch ($c){
                case "maxprice":
                    if($currentmaxprice>$maxlimit["maxprice"]){
                        $flag=false;
                    }
                    break;
                case "maximp":
                    if($currentmaximp>$maxlimit["maximp"]){
                        $flag=false;
                    }
                    break;
                case "maxclick":
                    if($currentmaxclick>$maxlimit["maxclick"]){
                        $flag=false;
                    }
                    break;
                default :
                    break;
            }
            if(isset($pre)){
                switch ($con){
                    case "||":
                        $flag=($pre || $flag);
                        break;
                    case "&&":
                        $flag=($pre && $flag);
                        break;
                    default :
                        $flag=$flag;
                        break;
                }
            }
            $pre=$flag;
        }
        return $pre;
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
        
        if (isset($conditions['ustart_time']) && $conditions['ustart_time'] && isset($conditions['uend_time']) && $conditions['uend_time']){
            $where[] = "((start_time <= UNIX_TIMESTAMP('".$conditions['ustart_time']."') and UNIX_TIMESTAMP('".$conditions['ustart_time']."') <=end_time and end_time < UNIX_TIMESTAMP('".$conditions['uend_time']."')) or ".
                "(start_time >= UNIX_TIMESTAMP('".$conditions['ustart_time']."') and UNIX_TIMESTAMP('".$conditions['uend_time']."') >=start_time and end_time >=UNIX_TIMESTAMP('".$conditions['uend_time']."')) or ".
                "(start_time <= UNIX_TIMESTAMP('".$conditions['ustart_time']."') and end_time >= UNIX_TIMESTAMP('".$conditions['uend_time']."')) or".
                "(UNIX_TIMESTAMP('".$conditions['ustart_time']."') <=start_time and end_time <= UNIX_TIMESTAMP('".$conditions['uend_time']."')))";
        }
        if (isset($conditions['start_time']) && $conditions['start_time']){
            $where[] = "start_time >= UNIX_TIMESTAMP('".$conditions['start_time']."')";
        }
        if (isset($conditions['end_time']) && $conditions['end_time']){
            $where[] = "end_time <= UNIX_TIMESTAMP('".$conditions['end_time']."')";
        }
        if (isset($conditions['go_method']) && $conditions['go_method']){
            $where[] = "go_method ='".$conditions['go_method']."'";
        }
        if(isset($conditions['ad_type'])){
            $where[] = "ad_type = '".$conditions['ad_type']."'";
        }
        if(isset($conditions['method']) && $conditions['method']){
            $where[] = "method = '".$conditions['method']."'";
        }
        if(isset($conditions['runstate']) && $conditions['runstate']){
            if($conditions["runstate"]==="3"){
                $where[] = "(runstatus != '2' or state=2)";
            }else{
                $where[] = "(runstatus=2 and state=1)";
            }
        }
        if(isset($conditions['runstatus']) && $conditions['runstatus']){
            $where[] = "runstatus = '".$conditions['runstatus']."'";
        }
        if(isset($conditions['price']) && $conditions['price']){
            $where[] = "price = '".$conditions['price']."'";
        }
        if(isset($conditions['maxprice']) && $conditions['maxprice']){
            $where[] = "maxprice >='".$conditions['maxprice']."'";
        }
        if(isset($conditions['maximp']) && $conditions['maximp']){
            $where[] = "maximp >='".$conditions['maximp']."'";
        }
        if(isset($conditions['maxclick']) && $conditions['maxclick']){
            $where[] = "maxclick >='".$conditions['maxclick']."'";
        }
        if(isset($conditions['maxcondition']) && $conditions['maxcondition']){
            $where[] = "maxcondition ='".$conditions['maxcondition']."'";
        }
        if(isset($conditions['del']) && $conditions['del']){
            $where[] = "del= '".$conditions['del']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}
