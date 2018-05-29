<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 任务系统-物品子类：A券
 *
 * @author fanch
 *
 */
class Task_Goods_Acoupon extends Task_Goods_Base {
    const ACOUPON_DESC = '游戏大厅赠送';

    /**
     * 发放A券主体方法
     * @param array $data
     */
    public function onSend(){
        $acoupons = $this->mGood;

        $debugMsg = array(
            'msg' => "即将赠送的A券数据",
            'acoupons' => $acoupons
        );
        $this->debug($debugMsg);

        $saveResult = $this->saveSendAcouponToDb($acoupons);

        $debugMsg = array(
            'msg' => "保存A券到数据库的执行结果",
            'result' => $saveResult
        );
        $this->debug($debugMsg);

        if (!$saveResult) {
            return false;
        }
        $paymentResult =  $this->sendAcouponToPayment($acoupons);

        $debugMsg = array(
            'msg' => "发送A券到支付服务器的处理结果",
            'result' => $paymentResult
        );
        $this->debug($debugMsg);

        if($paymentResult === false){
            return false;
        }
        $updateResult = $this->updateSendAcouponToDb($paymentResult);

        $debugMsg = array(
            'msg' => "更新保存到数据库的A券执行结果",
            'result' => $updateResult
        );
        $this->debug($debugMsg);

        return $updateResult;
    }

    /**
     * 保存A券信息到数据库
     * @param array $sendAcoupons
     */
    private function saveSendAcouponToDb($acoupons){
        $ticketData = array();
        foreach ($acoupons as $acouponItem){
            $ticketData[] = array(
                'uuid' => $acouponItem['uuid'],
                'ticket_type' => $acouponItem['ticket_type'],
                'game_id' => $acouponItem['game_id'],
                'aid' => $acouponItem['aid'],
                'denomination' => $acouponItem['denomination'],
                'status'=>0,
                'send_type'=>$acouponItem['send_type'],
                'sub_send_type'=>$acouponItem['sub_send_type'],
                'consume_time'=>$acouponItem['consume_time'],
                'start_time'=> $acouponItem['startTime'],
                'end_time'=> $acouponItem['endTime'],
                'description'=>$acouponItem['desc'],
                'densection'=> isset($acouponItem['densection']) ? $acouponItem['densection'] : " ",
                'send_game_id'=> isset($acouponItem['send_game_id']) ? $acouponItem['send_game_id'] : 0,
                'third_type'=> isset($acouponItem['third_type']) ? $acouponItem['third_type'] : 0,
            );
        }

        $debugMsg = array(
            'msg' => "保存A券到数据库之前的参数",
            'ticketData' => $ticketData
        );
        $this->debug($debugMsg);
        $result = Client_Service_TicketTrade::mutiFieldInsert($ticketData);
        if(!$result){
            $debugMsg = array(
                'msg' => "保存A券到数据库错误",
                'ticketData' => $ticketData
            );
            $this->err($debugMsg);
        }
        return $result;
    }

    /**
     * 更新A券发放后的状态
     * @param array $responseData
     * @return boolean
     */
    private function updateSendAcouponToDb($responseData){
        foreach ($responseData as $item){
            $tradeParams['aid'] = $item['aid'];
            $tradeData['status'] = 1;
            $tradeData['useable'] = 1;
            $tradeData['out_order_id'] = $item['ano'];
            $tradeData['update_time'] = Common::getTime();
            $tradeResult = Client_Service_TicketTrade::updateBy($tradeData, $tradeParams);
            if(!$tradeResult){
                $debugMsg = array(
                    'msg' => "更新发放A券状态到数据库错误",
                    'ticketData' => $tradeData,
                    'tradeParams' => $tradeParams
                );
                $this->err($debugMsg);
                return false;
            }
        }
        return true;
    }

    /**
     * 支付充值测试
     * @param $acoupons
     * @return array|bool
     */
    public function sendDataToPayment($acoupons){
        return $this->sendAcouponToPayment($acoupons);
    }

    /**
     * 发送A券到支付服务器
     * @param array $acoupons
     * @return array|boolean
     */
    private function sendAcouponToPayment($acoupons){
        $request = $this->getPostToPaymentData($acoupons);

        $debugMsg = array(
            'msg' => "发送A券数据到支付服务器之前，参数组装的结果",
            'request' => $request
        );
        $this->debug($debugMsg);

        $responseResult = $this->postToPayment($request);

        $debugMsg = array(
            'msg' => "发送A券后支付服务器的响应后，处理的结果",
            'response' => $responseResult
        );
        $this->debug($debugMsg);

        $response =  $this->verifyPaymentResponse($responseResult);

        $debugMsg = array(
            'msg' => "验证支付服务器响应返回的数据处理结果",
            'response' => $responseResult
        );
        $this->debug($debugMsg);

        return $response;
    }

    /**
     * 组装待发送的A券数据
     * @param array $acoupons
     * @return array
     */
    private function getPostToPaymentData($acoupons){
        $responseData = array();
        foreach ($acoupons as $acouponItem){
            $responseItem = array(
                'aid'=>$acouponItem['aid'],
                'denomination'=>(string)$acouponItem['denomination'],
                'startTime'=>(string)date('YmdHis', $acouponItem['startTime']),
                'endTime'=>(string)date('YmdHis', $acouponItem['endTime']),
                'desc'=>urlencode($acouponItem['desc']),
                'uuid'=>$acouponItem['uuid'],
                'useAppId'=>$acouponItem['useApiKey'],
                'taskId'=>Client_Service_TicketTrade::getTaskId($acouponItem['send_type'], $acouponItem['sub_send_type'])
            );
            $responseData[] = $responseItem;
        }
        return $responseData;
    }

    /**
     * 发送A券请求到支付服务器主体执行方法
     * @param array $request
     * @return mixed
     */
    private function postToPayment($request){
        list($api_key, $url, $ciphertext) = $this->getPaymentConfig();
        $joinStr = $this->getJoinStr($request);
        $token = md5($ciphertext . $api_key . self::ACOUPON_DESC . $joinStr);

        $data['api_key'] = $api_key;
        $data['msg'] = self::ACOUPON_DESC;
        $data['token'] = $token;
        $data['data'] = $request;
        $json_data = json_encode($data);

        $debugMsg = array(
            'sysmsg' => "发送到支付服务器的 HTTP 请求参数",
            'api_key' => $api_key,
            'msg' => self::ACOUPON_DESC,
            'token' => $token,
            'data' => $request
        );
        $this->info($debugMsg);

        $result = Util_Http::post($url, $json_data, array('Content-Type' => 'application/json'));

        $debugMsg = array(
            'msg' => "支付服务器 HTTP 响应结果",
            'response' => $result
        );
        $this->info($debugMsg);

        $response = json_decode($result->data,true);
        return $response;
    }

    /**
     * 支付服务器响应结果校验{支付改为异步通知}
     * @param array $response
     * @return boolean|array
     */
    private  function verifyPaymentResponse($response){
        if(!is_array($response)){ return false; }

        list(, , $ciphertext) = $this->getPaymentConfig();

        if($response['success'] == true){
            $responseToken = $response['token'];
            $responseData  = $response['data'];
            $responseStr   = '';
            foreach ($responseData as $item){
                $responseStr .= $item['aid'] . $item['ano'];
            }

            $token = md5($ciphertext . $response['msg'] . $response['success'] . $responseStr);
            if(strtolower($responseToken) == strtolower($token)){
                return $responseData;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取支付配置信息
     * @return array
     */
    private  function  getPaymentConfig(){
        $paymentConfig = Common::getConfig('paymentConfig','payment_send');
        $api_key    = $paymentConfig['api_key'];
        $url        = $paymentConfig['url'];
        $ciphertext = $paymentConfig['ciphertext'];
        return array($api_key, $url, $ciphertext);
    }

    /**
     * 支付请求参数拼组
     * @param array $acoupons
     * @return string
     */
    private  function getJoinStr($acoupons){
        $str = '';
        foreach ($acoupons as $item){
            $str.= $item['aid'].$item['denomination'].$item['desc'].$item['endTime'].$item['startTime'].$item['uuid'];
        }
        return $str;
    }
}
