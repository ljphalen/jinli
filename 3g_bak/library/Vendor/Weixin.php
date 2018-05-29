<?php

/**
 * Class Vendor_Weixin
 * 微信接口
 */
class Vendor_Weixin {

    private $token        = '';
    private $appid        = '';
    private $secret       = '';
    private $api_url      = '';
    private $aes_key      = '';
    private $access_token = '';
    private $_log         = array();

    public function __construct($appId = '', $secret = '') {
        $now           = time();
        $info          = Common::getConfig('weixinConfig', 'conf');
        $this->token   = $info['token'];
        $this->appid   = !empty($appId) ? $appId : $info['appid'];
        $this->secret  = !empty($secret) ? $secret : $info['secret'];
        $this->api_url = $info['api_url'];
        $this->aes_key = $info['aes_key'];
        $wxTokenStr    = Gionee_Service_Config::getValue('weixin_token');
        $wxToken       = json_decode($wxTokenStr, true);
        $access_token  = $wxToken['v'];
        $this->_log[]  = Common::jsonEncode($wxToken);
        if (empty($wxToken['v']) || $wxToken['t'] < $now || $wxToken['appid'] != $this->appid) {
            $tokenInfo = $this->accessToken();
            $tmp       = array('v' => $tokenInfo['access_token'], 't' => $now + 3600, 'appid' => $this->appid);
            Gionee_Service_Config::setValue('weixin_token', Common::jsonEncode($tmp));
            $access_token = $tokenInfo['access_token'];
        }
        $this->access_token = $access_token;
    }

    public function __destruct() {
        $this->outLog();
    }


    public function checkSignature($signature, $timestamp, $nonce) {
        $token  = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取access token
     * @return array array("access_token":"ACCESS_TOKEN","expires_in":7200)
     */
    public function accessToken() {
        $url     = $this->api_url . 'token?grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->secret;
        $curl    = new Util_Http_Curl($url);
        $content = $curl->get();
        return !empty($content) ? json_decode($content, true) : false;
    }

    /**
     * 接收数据
     */
    private function _rep() {
        libxml_disable_entity_loader(true);
        $inData = file_get_contents("php://input");
        $ret    = (array)simplexml_load_string($inData, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $ret;
    }

    /**
     * 获取微信服务器ip地址列表
     * @return array array("ip_list":["127.0.0.1","127.0.0.1"])
     */
    public function ipList() {
        $url     = $this->api_url . 'getcallbackip?access_token=' . $this->access_token;
        $curl    = new Util_Http_Curl($url);
        $content = $curl->get();
        return !empty($content) ? json_decode($content, true) : false;
    }

    public function getUserInfo($openid) {
        $url     = $this->api_url . 'user/info?access_token=' . $this->access_token . '&openid=' . $openid . '&lang=zh_CN';
        $curl    = new Util_Http_Curl($url);
        $content = $curl->get();
        return !empty($content) ? json_decode($content, true) : false;
    }

    public function makeQrcode($sceneStr = '1') {
        $url               = $this->api_url . 'qrcode/create?access_token=' . $this->access_token;
        $data              = array(
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => array('scene' => array('scene_str' => $sceneStr))
        );
        $postStr           = Common::jsonEncode($data);
        $content           = $this->_post($url, $postStr);
        $this->_log[]      = $postStr;
        $this->_log[]      = $content;
        $ret               = !empty($content) ? json_decode($content, true) : false;
        $ret['qrcode_url'] = $this->_getTicket($ret['ticket']);
        return $ret;
    }

    private function _getTicket($val) {
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $val;
        return $url;
    }

    /**
     * 接收消息
     */
    public function call() {
        $inData = $this->_rep();
        if ($inData['MsgType'] == 'event') {
            $func = '_event_' . $inData['Event'];
        } else {
            $func = '_msg_' . $inData['MsgType'];
        }
        $this->_log[] = $func . '-' . Common::jsonEncode($inData);
        if (method_exists($this, $func)) {
            $ret          = call_user_func_array(array($this, $func), array($inData));
            $this->_log[] = $ret;
            return $ret;
        }
        return false;
    }

    /**
     * 建立菜单
     */
    public function menuCreate() {
        $url     = $this->api_url . 'menu/create?access_token=' . $this->access_token;
        $data    = Common::getConfig('weixinConfig', 'menu');
        $postStr = Common::jsonEncode($data);
        //$curl    = new Util_Http_Curl($url);
        //$content = $curl->post($postStr);
        $content      = $this->_post($url, $postStr);
        $this->_log[] = $postStr;
        $this->_log[] = $content;
        return !empty($content) ? json_decode($content, true) : false;
    }

    private function _post($url, $data) {
        $ch      = curl_init();
        $timeout = 300;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $o = curl_exec($ch);
        curl_close($ch);
        return $o;
    }

    /**
     * 获取菜单
     */
    public function menuGet() {
        $url     = $this->api_url . 'menu/get?access_token=' . $this->access_token;
        $curl    = new Util_Http_Curl($url);
        $content = $curl->get();
        return !empty($content) ? json_decode($content, true) : false;
    }

    /**
     * 删除菜单
     */
    public function menuDelete() {
        $url     = $this->api_url . 'menu/delete?access_token=' . $this->access_token;
        $curl    = new Util_Http_Curl($url);
        $content = $curl->get();
        return !empty($content) ? json_decode($content, true) : false;
    }

    /**
     * 发送消息
     *
     * @param $data
     *
     * @return array
     */
    public function send($data) {
        $url     = $this->api_url . 'message/custom/send?access_token=' . $this->access_token;
        $curl    = new Util_Http_Curl($url);
        $postStr = Common::jsonEncode($data);
        $content = $curl->post($postStr);
        return !empty($content) ? json_decode($content, true) : false;
    }

    public function outLog() {
        $path = '/data/3g_log/weixin/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        foreach ($this->_log as $val) {
            error_log(date('Y-m-d H:i:s') . ' : ' . $val . "\n", 3, $path . date('Ymd'));
        }
    }


    /**
     *文本消息
     */
    private function _msg_text($inData) {
        $tmp      = Gionee_Service_Config::getValue('weixin_conf');
        $confData = json_decode($tmp, true);
        $now      = time();
        $info     = array(
            'ToUserName'   => $inData['FromUserName'],
            'FromUserName' => $inData['ToUserName'],
            'CreateTime'   => $inData['CreateTime'],
        );

        $type     = 1;//文本
        $userInfo = Gionee_Service_WxUser::getUserInfo($inData);
        $content  = '';
        if ($userInfo['flag'] == 1) {//问题反馈状态
            $content = $confData['interact_1_to'];
            $msg     = array(
                'key'        => 'interact_1',
                'openid'     => $inData['FromUserName'],
                'content'    => $inData['Content'],
                'msg_id'     => $inData['MsgId'],
                'created_at' => $now
            );
            Gionee_Service_WxFeedback::add($msg);
            Gionee_Service_WxUser::set(array('flag' => 0), $userInfo['id']);
        } else if ($userInfo['flag'] == 2) {//功能建议状态
            $content = $confData['interact_2_to'];
            $msg     = array(
                'key'        => 'interact_2',
                'openid'     => $inData['FromUserName'],
                'content'    => $inData['Content'],
                'msg_id'     => $inData['MsgId'],
                'created_at' => $now
            );
            Gionee_Service_WxFeedback::add($msg);
            Gionee_Service_WxUser::set(array('flag' => 0), $userInfo['id']);
        } elseif ($userInfo['flag'] == '3') { //关注领金币
            $up = array(
                'flag' => 0
            );
            //判断用户是否已刮开
            $userMsg = User_Service_Gather::getBy(array(
                'scratch_num' => strtoupper($inData['Content']),
                'is_scratch'  => 1
            ));
            if (empty($userMsg)) {
                $content = '对不起，您输入的验证码不正确，请在' . Lang::_('USER_CENTER') . '核对后重新输入，谢谢！';
            } else {
                $number = Gionee_Service_WxFeedback::count(array(
                    'key'     => 'activity_1',
                    'content' => strtoupper($inData['Content'])
                ));
                if ($number > 0) {
                    $content = '您已经领过金币啦~请不要输入相同验证码。';
                } else {
                    $content = $confData['activity_1_to'];// 写日志
                    $msg     = array(
                        'key'        => 'activity_1',
                        'openid'     => $inData['FromUserName'],
                        'content'    => strtoupper($inData['Content']),
                        'msg_id'     => $inData['MsgId'],
                        'created_at' => $now,
                    );
                    Gionee_Service_Feedback::add($msg);//写反馈日志
                    $up['uid'] = $userMsg['uid'];
                    Gionee_Service_WxUser::set(array('flag' => 0, 'uid' => $userMsg['uid']), $userInfo['id']);//更新状态
                    User_Service_Gather::updateBy(array('is_scratch' => 2), array('uid' => $userMsg['uid']));//更新刮奖状态
                    User_Service_Gather::getInfoByUid($userMsg['uid'], true);
                    $getScores = Gionee_Service_Config::getValue('user_wx_content_rewards');
                    $res       = User_Service_Gather::changeScoresAndWriteLog($userMsg['uid'], $getScores, 105, 2);//给用户赚送金币
                }
            }
            Gionee_Service_WxUser::set($up, $userInfo['id']);//更新状态
        } else {
            $data = Gionee_Service_WxKey::getBy(array('key' => $inData['Content'], 'status' => 1));
            if (!empty($data)) {
                $content = $data['val'];
                $type    = $data['type'];
                if ($type == 3) {
                    $info['sub'] = array(
                        array(
                            'title' => $data['title'],
                            'desc'  => $data['val'],
                            'img'   => $data['img'],
                            'url'   => $data['link'],
                        )
                    );
                    $info['num'] = 1;
                }
            }
        }

        $ret = '';
        if (!empty($content)) {
            $info['Content'] = $content;
            if ($type == 2) {
                $ret = $this->_send_image($info);
            } else if ($type == 1) {
                $ret = $this->_send_text($info);
            } else if ($type == 3) {
                $ret = $this->_send_news($info);
            }

        }
        return $ret;

    }

    /**
     * 图片消息
     */
    private function _msg_image($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['PicUrl'],
            $inData['MediaId'],
            $inData['MsgId'],
        );
    }

    /**
     * 语音消息
     */
    private function _msg_voice($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['Format'],
            $inData['MediaId'],
            $inData['MsgId'],
        );
    }

    /**
     * 视频消息
     */
    private function _msg_video($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['ThumbMediaId'],
            $inData['MediaId'],
            $inData['MsgId'],
        );

    }

    /**
     * 地理位置消息
     */
    private function _msg_location($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['Location_X'],
            $inData['Location_Y'],
            $inData['Scale'],
            $inData['Label'],
            $inData['MediaId'],
            $inData['MsgId'],
        );
    }

    /**
     * 链接消息
     */
    private function _msg_link($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['Description'],
            $inData['Title'],
            $inData['Url'],
            $inData['MsgId'],
        );
    }

    /**
     * 关注事件
     */
    private function _event_subscribe($inData) {
        $userInfo = Gionee_Service_WxUser::getUserInfo($inData);
        $tmp      = Gionee_Service_Config::getValue('weixin_conf');
        $confData = json_decode($tmp, true);
        $info     = array(
            'ToUserName'   => $inData['FromUserName'],
            'FromUserName' => $inData['ToUserName'],
            'CreateTime'   => $inData['CreateTime'],
            'Content'      => $confData['subscribe'],//'欢迎关注微信公众账号“集装箱”'
        );

        $this->_log[] = $inData['EventKey'];
        return $this->_send_text($info);
    }

    /**
     * 取消关注事件
     */
    private function _event_unsubscribe($inData) {
        $arr          = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
        );
        $tmp          = Gionee_Service_Config::getValue('weixin_conf');
        $data         = json_decode($tmp, true);
        $info         = array(
            'ToUserName'   => $inData['FromUserName'],
            'FromUserName' => $inData['ToUserName'],
            'CreateTime'   => $inData['CreateTime'],
            'Content'      => $data['unsubscribe'],//'请关注微信公众账号“集装箱”',
        );
        $this->_log[] = $inData['EventKey'];
        return $this->_send_text($info);

    }

    /**
     * 扫描带参数二维码事件
     */
    private function _event_SCAN($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['EventKey'],
            $inData['Ticket'],
        );
    }

    /**
     * 上报地理位置事件
     */
    private function _event_LOCATION($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['Latitude'],
            $inData['Longitude'],
            $inData['Precision'],
        );

    }

    /**
     * 点击菜单拉取消息时的事件推送
     */
    private function _event_CLICK($inData) {

        $tmp      = Gionee_Service_Config::getValue('weixin_conf');
        $confData = json_decode($tmp, true);

        $info = array(
            'ToUserName'   => $inData['FromUserName'],
            'FromUserName' => $inData['ToUserName'],
            'CreateTime'   => $inData['CreateTime'],
        );

        $userInfo = Gionee_Service_WxUser::getUserInfo($inData);
        $i        = 0;
        if ($inData['EventKey'] == 'interact_1') {
            Gionee_Service_WxUser::set(array('flag' => 1), $userInfo['id']);
            $info['Content'] = $confData['interact_1_in'];
        } else if ($inData['EventKey'] == 'interact_2') {
            Gionee_Service_WxUser::set(array('flag' => 2), $userInfo['id']);
            $info['Content'] = $confData['interact_2_in'];
        } else if ($inData['EventKey'] == 'interact_3') {
            $info['Content'] = $confData['interact_3'];
        } else if ($inData['EventKey'] == 'activity_1') {//关注领金币
            $data = Gionee_Service_WxFeedback::getBy(array(
                'key'    => 'activity_1',
                'openid' => $inData['FromUserName']
            )); //如果没有发送信息
            if (empty($data)) {
                $info['Content'] = $confData['activity_1_in'];
                Gionee_Service_WxUser::set(array('flag' => 3), $userInfo['id']);
            } else {
                $info['Content'] = '您已绑定账号.';
                Gionee_Service_WxUser::set(array('flag' => 0), $userInfo['id']);
            }
        } else if (in_array($inData['EventKey'], array('product_1', 'product_2', 'activity_2'))) {
            $tmp = Gionee_Service_WxMsg::getBy(array('key' => $inData['EventKey']));
            if (!empty($tmp['id'])) {
                if (!empty($tmp['img'])) {
                    $i++;
                } else {
                    $info['Content'] = $tmp['desc'];
                }
                $head = array(
                    'title' => $tmp['title'],
                    'desc'  => $tmp['desc'],
                    'url'   => $tmp['url'],
                    'img'   => $tmp['img'],
                );
                $sub  = json_decode($tmp['sub_msg'], true);
                array_unshift($sub, $head);
                $info['sub'] = $sub;
                $info['num'] = count($sub);
            }
        }

        $this->_log[] = Common::jsonEncode($info);

        if (!empty($info)) {
            if ($i > 0) {
                return $this->_send_news($info);
            } else {
                return $this->_send_text($info);
            }
        }

        return '';
    }

    /**
     * 点击菜单跳转链接时的事件推送
     */
    private function _event_VIEW($inData) {
        $arr = array(
            $inData['ToUserName'],
            $inData['FromUserName'],
            $inData['CreateTime'],
            $inData['EventKey'],
        );
    }

    /**
     * 发送文本消息
     */
    private function _send_text($info) {
        $str = '<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
				</xml>';

        $content = html_entity_decode($info['Content']);
        return sprintf($str, $info['ToUserName'], $info['FromUserName'], $info['CreateTime'], $content);
    }

    private function _send_image($info) {
        $str = '<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[image]]></MsgType>
					<Image>
						<MediaId><![CDATA[%s]]></MediaId>
					</Image>
				</xml>';
        return sprintf($str, $info['ToUserName'], $info['FromUserName'], $info['CreateTime'], $info['Content']);
    }

    private function _send_voice() {
    }

    private function _send_video() {
    }

    private function _send_music() {
    }

    /**
     * 发送图文消息
     */
    private function _send_news($info) {
        $imgPath = Common::getImgPath();
        $str     = <<<EOF
<xml>
<ToUserName><![CDATA[{$info['ToUserName']}]]></ToUserName>
<FromUserName><![CDATA[{$info['FromUserName']}]]></FromUserName>
<CreateTime>{$info['CreateTime']}</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>{$info['num']}</ArticleCount>
<Articles>
EOF;
        foreach ($info['sub'] as $val) {
            $img = $imgPath . $val['img'];
            $url = html_entity_decode($val['url']);
            $str .= <<<EOF
<item>
<Title><![CDATA[{$val['title']}]]></Title>
<Description><![CDATA[{$val['desc']}]]></Description>
<PicUrl><![CDATA[{$img}]]></PicUrl>
<Url><![CDATA[{$url}]]></Url>
</item>
EOF;
        }

        $str .= <<<EOF
</Articles>
</xml>
EOF;

        return $str;
    }

    public function uploadFile($file, $type = 'image') {
        //要上传的文件
        $fields['f'] = '@' . $file;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->access_token.'&type=" . $type);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        ob_start();
        $ret = curl_exec($ch);
        curl_close($ch);
        $out = ob_get_contents();
        ob_end_clean();

        return json_decode($out, true);
    }


}