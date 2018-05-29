<?php
Doo::loadModel('datamodel/base/IptadPlanBase');

class IptadPlan extends IptadPlanBase{
        /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
    }
    function updatePlan($data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->config_id = $data['config_id'];
        $this->start_time = strtotime($data['start_time']);
        $this->end_time = strtotime($data['end_time']);
        $this->block = $data['block'];
        $this->operator = $currentUser['username'];
        $this->updatedate = time();
        $this->runstatus=2;//临时
        $this->res("IMPLANTABLE_REDIS_CACHE_DEFAULT")->delete("PLAN_".$data['config_id']);
        if(empty($data["plan_id"])){
            $this->createdate = time();
            return $this->insert();
        }else{
            $this->id=$data["plan_id"];
            $this->update();
            return $data["limit_id"];
        }
    }
    function getPlanByConfigid($config_id){
        return $this->find(array("where"=>'config_id ='.$config_id.' and del=1','asArray' => TRUE));
    }
    function getConfigList($config_id,$sdate,$edate,$product=""){
        $whereSql="a.del!=0 ";
        
        if(!empty($config_id)){
            $whereSql.=" and a.config_id='".$config_id."'";
        }
        
        if(!empty($sdate)){
            $whereSql.=" and a.start_time>='".$sdate."'";
        }
        if(!empty($edate)){
            $whereSql.=" and a.end_time<='".$edate."'";
        }
        $res=Doo::db()->query("SELECT *,a.state as state,a.id as plan_id FROM iptad_plan a LEFT JOIN `iptad_config` b ON a.`config_id`=b.id LEFT JOIN iptad_app c ON c.`appkey`=b.`appkey` where ".$whereSql." ORDER BY a.`createdate` DESC ")->fetchAll();
        return $res;
    }
    
    function getPlanProductByConfigid($id){
        $source=Doo::loadModel("datamodel/implantable/IptadSource",TRUE);
        $res=$this->getPlanByConfigid($id);
        foreach($res as $k=>$v){
            if(!empty($v["block"])){
            foreach (json_decode($v["block"])as $x=>$z){
                    $pinfo=$source->sourceMapProduct($z);
                    $res[$k]["planpd"]=array("starttime"=>$v["start_time"],"end_time"=>$v["end_time"],"prduct_name"=>$pinfo["product_name"]);
                }
            }
        }
        return $res;
    }
    function setPlanState($id,$state){
        $this->id=$id;
        $this->state=$state;
        $this->update();
    }
    function planDel($id){
        $this->id=$id;
        $this->del=0;
        $this->update();
    }
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
        $whereArr['select'] = '*,from_unixtime(start_time) as stime,from_unixtime(end_time) as etime,case runstatus when 1 then "未到开始时间" when 2 then "导量中" when 3 then "导量完毕" else "未知" end status';
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
     * 获取一条plan
     * @param Array $conditions
     */
    public function getPlan($id){
        $result=Doo::db()->query("SELECT *,a.id as plan_id,from_unixtime(a.start_time) as stime,from_unixtime(a.end_time) as etime FROM iptad_plan a LEFT JOIN `iptad_config` b ON a.`config_id`=b.id LEFT JOIN iptad_app c ON c.`appkey`=b.`appkey` where a.id=".$id." ORDER BY a.`createdate` DESC ")->fetch();
        return $result;
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
        if(isset($conditions['del']) && $conditions['del']){
            $where[] = "del= '".$conditions['del']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
}