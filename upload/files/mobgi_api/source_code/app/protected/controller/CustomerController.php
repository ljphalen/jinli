<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-4-28 16:59:32
 * $Id: CustomerController.php 62100 2015-4-28 16:59:32Z hunter.fang $
 */
Doo::loadController("AppDooController");

class CustomerController extends AppDooController {
    
    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->customerModel = Doo::loadModel("datamodel/AdPublish", TRUE);
    }
    
    /**
     * 客户列表
     */
    public function customerlist(){
        # START 检查权限
        if (!$this->checkPermission(CUSTOMER, CUSTOMER_VIEW)) {
            $this->displayNoPermission();
        }
        $keyword = $this->get["keyword"];
        $url = "/customer/customerlist?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        $total = $this->customerModel->getCount($keyword);
        
        $page = $this->page($url, $total);
        $limit = $page->limit;
        $this->data["customers"] = $this->customerModel->customerlist($keyword,$limit);
        $this->data["keyword"]=$keyword;
        $this->myrender("customer/customerlist", $this->data);
    }
    
    /**
     * 新增客户
     */
    public function customeradd(){
        # START 检查权限
        if (!$this->checkPermission(CUSTOMER, CUSTOMER_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $this->myrender("customer/customeradd", $this->data);
    }
    
    /**
     * 保存客户信息
     */
    public function customersave(){
        
         # START 检查权限
        if (!$this->checkPermission(CUSTOMER, CUSTOMER_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $post = $_POST;
        //参数校验        
        $compay = $this->removeAllXss($post['compay']);
        if($compay != $post['compay']){
            $this->redirect("javascript:history.go(-1)","公司名称参数错误！");
        }
        
        if(empty($compay)){
            $this->redirect("javascript:history.go(-1)","公司名称不能为空！");
        }
        
        if(empty($post['tel'][1]) && empty($post['conact'][1])){
            unset($post['tel'][1]);
            unset($post['conact'][1]);
        }
        
        $customerid = intval($post['id']);
        $conact= json_encode($this->removeAllXss($post['conact']));
        $tel= json_encode($this->removeAllXss($post['tel']));
        $address = $this->removeAllXss($post['address']);
        
        if(empty($customerid)){
            $result = $this->customerModel->addCustomer($compay, $conact, $tel,  $address);
            if($result){
                $this->redirect("/customer/customerlist","添加成功！");
            }else{
                $this->redirect("/customer/customerlist","添加失败！");
            }
        }else{
            $result = $this->customerModel->updateCustomer($customerid, $compay, $conact, $tel,  $address);
            if($result){
                $this->redirect("/customer/customerlist","更新成功！");
            }else{
                $this->redirect("/customer/customerlist","更新失败！");
            }
        }
    }
    
    /**
     * 删除客户
     */
    public function customerdel(){
        # START 检查权限
        if (!$this->checkPermission(CUSTOMER, CUSTOMER_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $customerId = intval($this->get["customerid"]);
        if(empty($customerId)){
            $this->redirect("javascript:history.go(-1)","参数错误！");
        }
        $result = $this->customerModel->softDelCustomer($customerId);
        if($result){
            $this->redirect("javascript:history.go(-1)","删除成功！");
        }else{
            $this->redirect("javascript:history.go(-1)","删除失败！");
        }
    }
    
    /**
     * 编辑客户信息
     */
    public function customeredit(){
        # START 检查权限
        if (!$this->checkPermission(CUSTOMER, CUSTOMER_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $customerId = intval($this->get["customerid"]);
        if(empty($customerId)){
            $this->redirect("javascript:history.go(-1)","参数错误！");
        }
        
        $customerInfo = $this->customerModel->getCustomerByid($customerId);
        
        if(!empty($customerInfo)){
            $customerInfo['conact'] = json_decode($customerInfo['conact']);
            $customerInfo['tel'] = json_decode($customerInfo['tel']);
        }
        
        $customerInfo['contact_num'] = max(array(count($customerInfo['conact']), count($customerInfo['tel'])));
        $this->data['customer'] = $customerInfo;
        $this->myrender("customer/customeradd", $this->data);
    }
    
    /**
     * 公司列表JSON数据
     */
    public function autoCustomer()
    {
        # START 检查权限
        if (!$this->checkPermission(CUSTOMER, CUSTOMER_VIEW)) {
            $this->displayNoPermission();
        }
        #
        
        $customersTmp = $this->customerModel->customerlist();
        $customers = array();
        if($customersTmp){
            foreach($customersTmp as $customer){
                $tmp = array();
                $tmp['id'] = $customer['id'];
                $tmp['compay'] = $customer['compay'];
                $customers[] = $tmp;
            }
        }
        $this->showData($customers);
    }
    
}

