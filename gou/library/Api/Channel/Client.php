<?php
class Api_Channel_Client
{

    private  $tracker_u;
    private  $secret_key;
    /**
     * @var int 1 代表一号店
     */
    private $siteType=1;
    private $xmlName;
    private $params;

    /**
     * for yhd
     * @param array $params
     * @return string
     */
    private function  generateSign($params)
    {
        ksort($params);
        $sign = '';
        foreach ($params as $k => $v) {
            $sign .= "$k=$v";
        }
        unset($k, $v);
        $sign = $sign . $this->secret_key;
        return md5($sign);
    }

    public function setXml($xml='index.xml'){
        $this->xmlName=$xml;
        return $this;
    }

    public function getParams(){
        $this->config();
        $params['time'] =time();
        $params['siteType'] =1;
        $params['tracker_u'] =$this->tracker_u;
        $params['xmlName'] = $this->xmlName;
        $params['sign'] = $this->generateSign($params);
        return $params;
    }
    private function config(){
        $this->tracker_u = Common::getConfig('apiConfig', 'yhd_tracker_u');
        $this->secret_key = Common::getConfig('apiConfig', 'yhd_secret_key');
    }

    /**
     * @param string $xml
     */
    public static function init(){
        return new self();
    }
}