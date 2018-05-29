<?php

/**
 * 苏宁开放平台接口 - 查询业务类基类
 *
 * @author ryan
 */
class Open_Suning_Request
{
    protected $apiParams = array();
    /**
     * 页码。取值范围:大于零的整数;默认值为1，即返回第一页数据
     */
    protected $pageNo;


    /**
     * 每页条数。取值范围:大于零的整数;最大值：50。默认值：10
     */
    protected $pageSize;
    protected $bizName;
    protected $apiMethodName;
    /**
     * 是否参数校验(默认false,测试及生产建议为true)
     */
    protected $checkParam = false;

    public function getCheckParam()
    {
        return $this->checkParam;
    }

    public function setCheckParam($checkParam)
    {
        $this->checkParam = $checkParam;
    }

    public function setPageNo($pageNo)
    {
        $this->pageNo = $pageNo;
        $this->apiParams["pageNo"] = $pageNo;
    }
    public function setParam($key,$val)
    {
        $this->$key = $val;
        $this->apiParams[$key] = $val;
    }

    public function getPageNo()
    {
        return $this->pageNo;
    }

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        $this->apiParams["pageSize"] = $pageSize;
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function getApiMethodName(){
        return $this->apiMethodName;
    }

    public function setApiMethodName($api_name){
        return $this->apiMethodName = $api_name;
//        return 'suning.netalliance.order.query';
    }

    public function getApiParams(){
        return $this->apiParams;
    }


    public function check($pageNoMin = 1, $pageNoMax = 99999, $pageSizeMin = 10, $pageSizeMax = 50)
    {
        if(empty($this->pageNo)) $this->pageNo = $pageNoMin;
        if(empty($this->pageSize)) $this->pageSize = $pageSizeMin;;
    }

    public function getBizName(){
        return $this->bizName;
    }
    public function setBizName($bizName){
        return $this->bizName = $bizName;
    }

}