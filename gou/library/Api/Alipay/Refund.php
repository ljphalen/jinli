<?php
if (! defined ( 'BASE_PATH' ))
    exit ( 'Access Denied!' );
/**
 * 支付宝接口 -- 退款
 *
 * @author tiansh
 *        
 */
class Api_Alipay_Refund extends Api_Alipay_Base {
    
    /**
     * 构造方法
     */
    public function initParams() {
        $this->gatewayUrl = "https://mapi.alipay.com/gateway.do?";
        $this->sign_type = 'MD5';
    }
    /**
     * refund
     */
    public function refund($params) {
        $data = array (
            "partner" => $this->partner,
            "notify_url" => $params ['notify_url'],
            'service' => 'refund_fastpay_by_platform_pwd',
            "_input_charset" => $this->_input_charset,
            'seller_email' => $this->accountName,
            'seller_user_id' => $this->partner,
            'refund_date' => date ( 'Y-m-d H:i:s', Common::getTime()),
            'batch_no' => $params ['refund_no'], // 退款批次号，即退款订单号
            'batch_num' => 1, // 批量退款订单数量
            'detail_data' => $params ['out_trade_no'] . '^' . $params ['refund_total'] . '^' . $params ['reason'], // 原付款支付宝交易号^退款总金额^退款理由
            "sign_type" => $this->sign_type
        );
        
        $submit = $this->buildRequestForm($data,"get", "确认");
        exit($submit);
    }
}