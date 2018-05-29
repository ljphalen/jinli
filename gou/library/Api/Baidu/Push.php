<?php
class Api_Baidu_Push {

    public static $apiKey = "";
    public static $secretKey = "";

    /**
     * 推送android设备消息
     * @param string $user_id 百度 user_id
     * @param string $title push 标题
     * @param string $content push 内容
     * @param int $type 类型 1:启动应用  2:启动应用并打开url, 3启动应用并打开本地activity
     * @param $custom_content json格式 如url或者activity
     * @param bool $is_msg
     * @param int $message_type 消息类型 0：消息（透传给应用的消息体）1：通知（对应设备上的消息通知）默认值为0。
     * @return bool
     * @throws Api_Baidu_ChannelException
     */
    public static function pushMessage($user_id, $title, $content, $type = 1, $custom_content, $is_msg = false, $message_type = 0)
    {
        //读取baidu push的key
        $config = Common::getConfig('pushConfig', 'baidu');
        self::$apiKey = $config['apiKey'];
        self::$secretKey = $config['secretKey'];

        if (!self::$apiKey || !self::$secretKey) {
            Common::log('key error', 'baidupush_error.log');
            return false;
        }

        $channel = new Api_Baidu_Channel (self::$apiKey, self::$secretKey);

        //推送消息到某个user，设置push_type = 1;
        //推送消息到一个tag中的全部user，设置push_type = 2;
        //推送消息到该app中的全部user，设置push_type = 3;
        $push_type = 1; //推送单播消息
        $optional[Api_Baidu_Channel::USER_ID] = strval($user_id); //如果推送单播消息，需要指定user
        //optional[Api_Baidu_Channel::TAG_NAME] = "xxxx";  //如果推送tag消息，需要指定tag_name

        //指定发到android设备
        $optional[Api_Baidu_Channel::DEVICE_TYPE] = 3;
        //指定消息类型为通知
        $optional[Api_Baidu_Channel::MESSAGE_TYPE] = $message_type;

        //通知类型的内容必须按指定内容发送，示例如下：
        $message = array(
            'title' => $title,
            'description' => $content,
            'style' => 0,
            'is_msg' => $is_msg,
            'notice' => true
        );

        if (($type == 2 || $type == 3) && $custom_content) {
            $message = array_merge($message, $custom_content);
        }
        $message = json_encode($message);

        $message_key = "msg_key";
        $ret = $channel->pushMessage($push_type, $message, $message_key, $optional);
        //Common::log($ret, 'push.log');
        if (false === $ret) Common::log(array('ERROR NUMBER:' => $channel->errno(), 'ERROR MESSAGE: ' => $channel->errmsg(), 'REQUEST ID: ' => $channel->getRequestId()), 'baidupush_error.log');
    }
}