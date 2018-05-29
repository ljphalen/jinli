<?php
Doo::loadModel('datamodel/base/AdOrderBase');

class AdOrder extends AdOrderBase{
    
    /**
     * 根据产品获取订单
     * @param type $productid
     * @return boolean
     */
    public function getOrdersByPid($productid){
        if(empty($productid)){
            return false;
        }
        $orders=$this->find(array("where"=>'pid ='.$productid.' ','asArray' => TRUE)); 
        //加个序号展示
        if(!empty($orders)){
            $fromIndex =1;
            foreach($orders as $key=>$item){
                $orders[$key]['index'] = $fromIndex;
                $fromIndex ++;
            }
        }
        return $orders;
    }
    
    public function saveorder($pid, $orderid, $agreementid, $startdate, $enddate, $paymethod, $price){
        $this->pid = $pid;
        $this->orderid = $orderid; 
        $this->agreementid = $agreementid;
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->paymethod = $paymethod;
        $this->price = $price;
        return $this->insert();
    }
    
    /*
     * 更新订单
     */
    public function upd_order($id=NULL,$data){
        $this->pid = $data['pid'];
        $this->orderid = $data['orderid'];
        $this->agreementid = $data['agreementid'];
        $this->startdate = $data['startdate'];
        $this->enddate = $data['enddate'];
        $this->paymethod = $data['paymethod'];
        $this->price = $data['price'];
        
        if(empty($id)){
            unset($this->id);
            $this->createtime=time();
            $this->updatetime=time();
            return $this->insert();
        }else{
            $this->id=$id;
            $this->updatetime=time();
            return $this->update();
        }
    }
    
}