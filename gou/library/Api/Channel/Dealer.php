<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * yhd goods api
 * @author ryan
 *
 */
class Api_Channel_Dealer{
    public static $api_id;
    /**
     * 获取第三方商品
     * @param int $api_id
     * @return mixed
     */
    public static function search($api_id){
        if (empty($api_id)){
            return false;
        }
        static::$api_id=$api_id;
        //get all configs
        $configs = Common::getConfig('tejiaConfig');
        $config = $configs[static::$api_id];
        //curl goods lists
        return self::getApiGoods($config);
    }
    /**
     * 从第三方接口获取数据
     * @param array $config
     * @return mixed
     */
    private static function getApiGoods($config){
        $adapter = 'adapter'.ucfirst($config['type']);

        //检查适配器存存不存在
        if(!method_exists(__CLASS__, $adapter)) {
            return false;
        }
        $strlen = mb_strlen($config['name'],'UTF-8');

        $name = str_pad($config['name'],(10-$strlen)*2+$strlen*3,'_',STR_PAD_BOTH);
        echo PHP_EOL;
        echo '|'.$name;
        if(static::$api_id==10||static::$api_id==11){
            $count = str_pad(sprintf('%s','_'),10,'_',STR_PAD_BOTH);
            echo "__{$count}__";
            echo "|";
        }
        $data = self::$adapter($config);
        if (!empty($data)&&is_array($data)) {
            Client_Service_Source::insert($data);
            $count =count($data);
        }else{
            $count = $data;
        }

        if(static::$api_id==10||static::$api_id==11){
            echo PHP_EOL;
            echo '|'.$name;
        }
        $count = str_pad(sprintf('[%s]',$count),10,'_',STR_PAD_BOTH);
        echo "共{$count}条";
        echo "|";
        return false;
    }

    /**
     * 梦芭莎商品数据适配 Moonbasa
     * @param $config
     * @return array
     */
    private static function adapterMoonbasa($config){
        $response = self::getResponse($config['url'], null, $config['isXml']);
        $data = $response['data']['goodsdata']['goods'];

        $rs = array();
        foreach ($data as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['pid'],
              'title'=>$val['title'],
              'category_name'=>$val['feature'],
              'img'=>$val['img_url'],
              'link'=>$val['t_url'],
              'market_price'=>$val['original_price'],
              'sale_price'=>$val['sale_price'],
              'storage'=>$val['storage_num']
            );
        }
        return $rs;
    }
    /**
     * 蜜芽宝贝适配 Miyabaobei
     * @param $config
     * @return array
     */
    private static function adapterMiyabaobei($config){
        $response = self::getResponse($config['url'], null, $config['isXml']);
        if(empty($response['items'])) return array();
        $rs = array();
        foreach ($response['items'] as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['id'],
              'title'=>$val['name'],
              'category_name'=>$val['category_name'],
              'img'=>$val['pic_url'],
              'link'=>$val['url'],
              'market_price'=>$val['market_price'],
              'sale_price'=>$val['sale_price'],
            );
        }
        return $rs;
    }

    /**
     * 移淘
     * @param $config
     * @return array
     */
    private static function adapterYtaow($config){
        $response = self::getResponse($config['url'], null, $config['isXml']);
        $data = $response['goodsdata']['goods'];

        $rs = array();
        foreach ($data as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['pid'],
              'title'=>$val['title'],
              'category_name'=>$val['category_name'],
              'img'=>$val['img_url'],
              'link'=>$val['url'],
              'market_price'=>$val['price'],
              'sale_price'=>$val['discountPrice'],
            );
        }
        return $rs;
    }

    /**
     * 掌购
     * @param array $config
     * @return array
     */
    private static function adapterZg51($config){
        $data = self::getResponse($config['url'], null, $config['isXml']);

        $rs = array();
        foreach ($data as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['id'],
              'title'=>$val['title'],
              'category_name'=>$val['category_name'],
              'img'=>$val['img'],
              'link'=>$val['url'],
              'market_price'=>$val['marketprice'],
              'sale_price'=>$val['price'],
            );
        }
        return $rs;
    }

    private static function adapterSuning($config){

        $data = Api_Channel_SuningAdapter::getData($config);
        return $data;
    }

    /**
     * 麦包包
     * @param $config
     * @return array
     */
    private static function adapterMbaobao($config){
        $response = self::getResponse($config['url'], null, $config['isXml']);
        $data = $response['data']['list'];

        $rs = array();
        foreach ($data as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['id'],
              'title'=>$val['title'],
              'category_name'=>$val['category_name'],
              'img'=>$val['img'],
              'link'=>$val['url'],
              'market_price'=>$val['mbb_price'],
              'sale_price'=>$val['price'],
            );
        }
        return $rs;
    }

    /**
     * 拍鞋网
     * @param $config
     * @return array
     */
    private static function adapterPaixie($config){
        $response = self::getResponse($config['url'], null, $config['isXml']);
        $data = $response['goodsdata']['goods'];

        $rs = array();
        foreach ($data as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['pid'],
              'title'=>$val['title']['@cdata'],
              'category_name'=>$val['category_name'],
              'img'=>$val['img_url'],
              'link'=>$val['url'],
              'market_price'=>$val['market_price'],
              'sale_price'=>$val['price'],
            );
        }
        return $rs;
    }

    /**
     * 密唐网
     * @param array $config
     * @return array
     */
    private static function adapter100mt($config){
        $response = self::getResponse($config['url'], null, $config['isXml']);
        $data = $response['goodsdata']['goods'];

        $rs = array();
        foreach ($data as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['pid'],
              'title'=>$val['title'],
              'category_name'=>$val['category_name'],
              'img'=>$val['img_url'],
              'link'=>str_replace('gionee3', 'gionee8', html_entity_decode($val['url'])),
              'market_price'=>$val['originalPrice'],
              'sale_price'=>$val['price'],
            );
        }
        return $rs;
    }

    /**
     * 买卖宝
     * @param  array $config
     * @return array
     */
    private static function adapterMmbao($config){
        $response = self::getResponse($config['url'], null, $config['isXml']);
        $data = $response['goodsdata']['goods'];

        $rs = array();
        foreach ($data as $val){
            $rs[] = array(
              'supplier'=>static::$api_id,
              'goods_id'=>$val['pid'],
              'title'=>$val['title']['@cdata'],
              'category_name'=>$val['category_name']['@cdata'],
              'img'=>$val['img_url']['@cdata'],
              'link'=>$val['url']['@cdata'],
              'market_price'=>$val['price'],
              'sale_price'=>$val['discount_price'],
            );
        }
        return $rs;
    }

    /**
     * mogujie
     * @param unknown $data
     * @return multitype:multitype:unknown
     */
    /**
     * 蘑菇街
     * @param array $config
     * @return array
     */
    private static function adapterMogujie($config){
        $rs = array();
        for ($i = 1; $i < 6; $i++) {
            $response = self::getResponse($config['url'].$i, null, $config['isXml']);
            $data = $response['items'];
            foreach ($data as $val){
                $img = explode(';', $val['picture']);
                $image = "";
                if(!empty($img[0])){
                    $ext = explode(".",  $img[0]);
                    $image = $img[0].'_320x999.'.end($ext);
                }
                $rs[] = array(
                  'supplier'=>static::$api_id,
                  'goods_id'=>$val['id'],
                  'title'=>$val['title'],
                  'category_name'=>$val['category'],
                  'img'=>$image,
                  'link'=>$val['murl'],
                  'market_price'=>$val['price'],
                  'sale_price'=>$val['nowprice'],
                );
            }
        }
        return $rs;
    }


    /**
     * 一号店 http://m.yhd.com/sale/99821?provinceId=1&tracker_u=103526100
     * tracker_u=103526100
     *
     * Channel
     * @param array $config
     * @return array
     */
    private static function adapterYhd($config){
        return Api_Channel_YhdAdapter::getData($config);
    }

    /**
     *
     * 国美
     * Channel
     * @param array $config
     * @return array
     */
    private static function adapterGome($config){
        return Api_Channel_GomeAdapter::getData($config);
    }

    /**
     * @return string
     */
    public static function getSavePath() {
        return realpath(Common::getConfig("siteConfig", "attachPath"));
    }


    /**
     * 获取接口数据
     * @param string $url
     * @param array $params
     * @param bool $isXml
     * @return mixed | array
     */
    public static function getResponse($url, $params, $isXml = false) {
        $curl = new Util_Http_Curl($url);
        $result = $curl->get($params);
        $res = $curl->getInfo();
        if ($isXml) {
            if($res['http_code']!=200) return array();
            $ret = Util_XML2Array::createArr($result);
        } else {
            $ret = json_decode($result, true);
        }
        return $ret;
    }

    /**
     * 获取统计的短链接
     * @param $version_id
     * @param $value
     * @return string
     */
    public static function getShortUrl($version_id, $value) {
        list($model_id, $channel_id) = explode('_', $value['module_channel']);

        $channel_codes = Stat_Service_Log::$V_ARRAY;
        $configs = Common::getConfig('tejiaConfig');
        $config = $configs[$value['supplier']];
        $channel_code = $config[$channel_codes[$version_id].'_channel_code'];

        $goodsUrl = html_entity_decode($value['link']);
        $goodsUrl .= strpos($goodsUrl, '?') === false ? '?' : '&';	//追加渠道号码

        //需要特殊处理
        if ($value['supplier'] == 6) {
            $goodsUrl = str_replace('gionee3', 'gionee8', html_entity_decode($value['link']));
        }

        http://m.meilishuo.com/share/item/3043108215?nmref=NM_s10452_0_&channel=40106
        if ($value['supplier'] == 9) {
            $goodsUrl = str_replace('NM_s10452_0_', 'NM_s10452_0_'.$config[$channel_codes[$version_id].'_channel_code'], html_entity_decode($value['link']));
        }

        $formart = $config[$channel_codes[$version_id].'_formart'];
        $goodsUrl = sprintf($formart, $goodsUrl);

        return Common::tjurl(Stat_Service_Log::URL_CLICK, $version_id, $model_id, $channel_id, $value['id'], $goodsUrl, $value['title'], $channel_code);
    }

}