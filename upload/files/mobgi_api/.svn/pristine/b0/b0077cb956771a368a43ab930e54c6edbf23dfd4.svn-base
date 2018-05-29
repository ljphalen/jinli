<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-4-29 18:26:16
 * $Id: OrderController.php 62100 2015-4-29 18:26:16Z hunter.fang $
 */

Doo::loadController("AppDooController");

class OrderController extends AppDooController {
    
    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->orderModel = Doo::loadModel("datamodel/AdOrder", TRUE);
    }
    
    /**
     * 生成订单号
     */
    public function createOrderKey(){
        Doo::loadPlugin("function");
        $orderkey=createappkey();
        die(json_encode(array("result"=>0,"key"=>$orderkey)));
    }
    
    /**
     * 保存订单
     */
    public function saveorder(){
        $post = $this->removeAllXss($_POST);
        $pid = $post['pid'];
        $orderid = $post['orderid'];
        $agreementid = $post['agreementid'];
        $startdate = $post['startdate'];
        $enddate = $post['enddate'];
        $paymethod = $post['paymethod'];
        $price = $post['price'];
        
        $result = $this->orderModel->saveorder($pid,$orderid, $agreementid, $startdate, $enddate, $paymethod, $price);
        if($result){
            die(json_encode(array("result"=>1)));
        }else{
            die(json_encode(array("result"=>0)));
        }
        
    }
    
    
}

