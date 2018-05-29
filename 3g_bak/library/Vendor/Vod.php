<?php

/**
 * Class Vendor_Vod
 * 中国移动合作视频接口
 */
class Vendor_Vod {

    private $sign_key       = 'gauihgeq';
    private $callerId       = 'jinli';
    private $sign_channelId = '200900060050001';
    private $api_channelId  = '04100200-99000-200900060050001';
    private $sign_url       = 'http://sdk2.cmvideo.cn/portal-keyt/getOpenApiEnptyCode?data=';
    private $api_url        = 'http://sdk2.cmvideo.cn/open-api/';
    private $api_key        = '';

    public function __construct() {
        if (empty($this->api_key)) {
            $data          = $this->encrypt_key("callerId={$this->callerId}&channelId={$this->sign_channelId}&nodeType=mv");
            $url           = $this->sign_url . $data;
            $serverKey     = file_get_contents($url);
            $result        = $this->decrypt_key($serverKey);
            $arr           = json_decode($result, true);
            $this->api_key = substr($arr['key'], 0, 8);
        }
    }

    public function getLivingList($nodeId, $day = 0) {
        $val       = "data/api/livingList.jsp?nodeId={$nodeId}&day={$day}&channelId={$this->api_channelId}&callerId={$this->callerId}";
        $data      = $this->encrypt_api($val, $this->api_key);
        $url       = $this->api_url . bin2hex($data);
        $serverKey = file_get_contents($url);
        $result    = $this->decrypt_api($serverKey, $this->api_key);
        $arr       = json_decode($result, true);
        return $arr;
    }

    public function getPlayUrl($nodeId, $contId, $code, $playerType = 1) {

        $val       = "data/api/playUrl.jsp?nodeId={$nodeId}&contId=&liveId={$contId}&channelId={$this->api_channelId}&playerType={$playerType}&callerId={$this->callerId}&uc={$code['uc']}&mt={$code['mt']}";
        $data      = $this->encrypt_api($val, $this->api_key);
        $url       = $this->api_url . bin2hex($data);
        $serverKey = file_get_contents($url);
        $result    = $this->decrypt_api($serverKey, $this->api_key);
        $arr       = json_decode($result, true);
        return $arr;
    }

    public function getMediaList($nodeId, $contId) {
        $val       = "data/api/mediaList.jsp?nodeId={$nodeId}&contId=&liveId={$contId}&channelId={$this->api_channelId}&playerType=4&callerId={$this->callerId}";
        $data      = $this->encrypt_api($val, $this->api_key);
        $url       = $this->api_url . bin2hex($data);
        $serverKey = file_get_contents($url);
        $result    = $this->decrypt_api($serverKey, $this->api_key);
        $arr       = json_decode($result, true);
        return $arr;
    }

    private function encrypt_key($str) {
        # Add PKCS7 padding.
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);

        return base64_encode(mcrypt_encrypt(MCRYPT_DES, $this->sign_key, $str, MCRYPT_MODE_ECB));
    }

    private function decrypt_key($str) {
        $str = str_replace(' ', '+', $str);
        $str = str_replace('|', '/', $str);
        $str = mcrypt_decrypt(MCRYPT_DES, $this->sign_key, base64_decode($str), MCRYPT_MODE_ECB);
        $pad = ord($str[($len = strlen($str)) - 1]);
        return substr($str, 0, strlen($str) - $pad);
    }

    private function encrypt_api($str, $key) {
        # Add PKCS7 padding.
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
    }

    private function decrypt_api($str, $key) {
        $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        $pad = ord($str[($len = strlen($str)) - 1]);
        return substr($str, 0, strlen($str) - $pad);
    }

    public function getListByChannelId($channelId) {
        $ret  = $this->getLivingList($channelId, 0);
        $list = array();
        foreach ($ret['programs'] as $val) {
            $c   = $this->getMediaList($channelId, $val['contId']);
            $url = array();
            foreach ($c['mediaList'] as $cc) {
                $tt    = $this->getPlayUrl($channelId, $val['contId'], $cc, 4);
                $url[] = $tt['playUrl'];
            }
            $list[$val['startTime']] = array(
                'channel_id' => $channelId,
                'cont_id'    => $val['contId'],
                'name'       => $val['name'],
                'day'        => date('Y-m-d', strtotime($val['startTime'])),
                'start_time' => $val['startTime'],
                'end_time'   => $val['endTime'],
                'play_url'   => Common::jsonEncode($url),
                'media_list' => Common::jsonEncode($c['mediaList']),
                'created_at' => time(),
                'updated_at' => time(),
            );
        }
        ksort($list);
        return $list;
    }
}