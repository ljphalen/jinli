<?php
Doo::loadModel('datamodel/base/AdProductAcountingBase');

class AdProductAcounting extends AdProductAcountingBase{
    
    /**
     * 获取最近三个月的结算方式
     * @param type $pid
     * @return type
     */
    public function getRecentlyAccounting($pid){
        $nextMonth = date('Ym', strtotime("+1 month"));
        $thisMonth = date('Ym');
        $lastMonth = date('Ym', strtotime("-1 month"));
        
        $whereSql = " pid = '". $pid. "' and month in (". implode(',', array($nextMonth, $thisMonth, $lastMonth)) . ") ";
        $order = ' order by month desc ';
        $sql="select * from ad_product_acounting where 1=1 and  $whereSql" . $order;
        $result =  Doo::db()->query($sql)->fetchAll();
        //默认CPA,,核算方式1:CPM 2:CPC 3:CPA 4:CPI,5:CPD,6:CPS
        if(empty($result)){
            $default_arr = array(
                array("show_month"=>$this->getMonth($nextMonth), "month"=>$nextMonth, "acounting_method"=>3, "denominated"=>number_format(300/100.0, 2)), 
                array("show_month"=>$this->getMonth($thisMonth), "month"=>$thisMonth, "acounting_method"=>3, "denominated"=>number_format(300/100.0, 2)), 
                array("show_month"=>$this->getMonth($lastMonth), "month"=>$lastMonth, "acounting_method"=>3, "denominated"=>number_format(300/100.0, 2)), 
                );
            return $default_arr;
        }else{
            $month_array = array($nextMonth, $thisMonth, $lastMonth);
            $return_month_array = array();
            $return_arr = array();
            //整合数据
            foreach($result as $item){
                $returnItem = array();
                $returnItem['show_month'] = $this->getMonth($item['month']);
                $returnItem['month'] = $item['month'];
                $returnItem['acounting_method'] = $item['acounting_method'];
                $returnItem['denominated'] = number_format($item['denominated']/100.0, 2);
                $return_arr[] = $returnItem;
                $return_month_array[] = $item['month'];
            }
            //若数据库中不存在某个月的结算方式,则存在的这个月的结算方式设置一个默认值返回.
            $arraydiff = array_diff($month_array, $return_month_array);
            if(!empty($arraydiff)){
                foreach($arraydiff as $month){
                    $returnItem = array();
                    $returnItem['show_month'] = $this->getMonth($month);
                    $returnItem['month'] = $month;
                    $returnItem['acounting_method'] = 3;
                    $returnItem['denominated'] = number_format(300/100.0, 2);
                    $return_arr[] = $returnItem;
                }
            }
            return $return_arr;
        }
    }
    
    /**
     * 把年月转成月如:201409 201410这样的字符串转成9 10
     * @param type $year_month
     * @return type
     */
    public function getMonth($year_month){
        $month = substr($year_month, 4);
        return intval($month);
    }
    
    /**
     * 保存某一个月份的结算方式与单价
     * @param type $product_id
     * @param type $month
     * @param type $acounting_method
     * @param type $denominated
     * @return type
     */
    public function saveAccounting($product_id, $month, $acounting_method, $denominated){
        $result=  self::getOne(array("select"=>"*","where"=>"pid='$product_id' and month='$month'","asArray"=>true));
        $save_result = false;
        //记录已存在则更新
        if($result){
            $time= time();
            $sql = "update ad_product_acounting set acounting_method='$acounting_method', denominated='$denominated', updated='$time' where pid='$product_id' and month='$month';";
            $save_result=Doo::db()->query($sql);
            if($month==date("Ym")){
                //更新到ad_pid_in_show
                Doo::db()->query("update mobgi_api.ad_pid_in_show set denominated='".$denominated."',acounting_method=".$acounting_method." where id=".$product_id)->execute();
            }
            
        }
        //记录不存在则新增
        else{
            $time= time();
            $sql = "insert into ad_product_acounting (pid, month, acounting_method, denominated, created,updated) values($product_id, $month, $acounting_method, $denominated, $time, $time );";
            $save_result=Doo::db()->query($sql);
        }
        return $save_result;
    }
    
}