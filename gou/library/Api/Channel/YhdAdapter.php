<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * yhd goods api
 * @author ryan
 *
 */
class Api_Channel_YhdAdapter{

    protected static $cacheTime = 86400;
    /**
     * @param array $price
     * @return array
     */
    private static function dealYhdPrice(array $price){
        array_walk($price,function(&$v){$v= $v['@value'];});
        return array('min'=>floatval(min($price)),'max'=>floatval(max($price)));
    }

    private static function getYhdStorage(array $storage){
        array_walk($storage,function(&$v){$v= $v['@value'];});
        return array_sum($storage);
    }

    /**
     *
     * Channel
     * @param array $config
     * @return array
     */
    public static function getData($config){
        $rs = array();
        $cat = self::getYhdCat($config);
        $list = self::getYhdIndex($config);
        $total = 0;
        $count = str_pad(sprintf('[%s]',count($list)),10,'_',STR_PAD_BOTH);
        echo PHP_EOL;
        echo "|_______Total________{$count}____|";
        $i=0;
        foreach ($list as $k=> $v) {
            $i++;
            $params = Api_Channel_Client::init()->setXml($v)->getParams();
            $res = Api_Channel_Dealer::getResponse($config['url'], $params, $config['isXml']);

            if(empty($res['products']['product'])) continue ;
            if(empty($res['products']['product'][0])){
                $rows[] = $res['products']['product'];
            }else{
                $rows = $res['products']['product'];
            }
            foreach ($rows as $n => $x) {
                $original_price = self::dealYhdPrice($x['original_price']['region']);
                $sale_price = self::dealYhdPrice($x['sale_price']['region']);
                $storage = self::getYhdStorage($x['storage']['region']);
                if(!$storage) continue ;
                $rs[] = array(
                  'supplier' => 10,
                  'goods_id' => $x['product_id'],
                  'title' => $x['title']['@cdata'],
                  'category_name' => $cat[$x['category_id']],
                  'img' => $x['pic_url'],
                  'storage' => $storage,
                  'link' => $x['product_url_mw'],
                  'market_price' => $original_price['max'],
                  'sale_price' => $sale_price['max'],
                  'sale_price_min' => $sale_price['min'],
                );
            }
            if(!empty($rs)){
                Client_Service_Source::insert($rs);
            }
            $time = str_pad(sprintf('[%s]',$i),10,'_',STR_PAD_RIGHT);
            $count = str_pad(sprintf('[%s]',count($rs)),10,'_',STR_PAD_LEFT);
            echo PHP_EOL;
            echo "|___{$time}_____{$count}______|";
            $total +=count($rs);
            $rs=array();
            sleep(0.1);
        }
        return $total;
    }
    private static function getYhdIndex($config){
        //缓存文件
        $cacheFile = self::getSavePath() . "/cache_{$config['type']}_index.bin";
        if(file_exists($cacheFile)) {
            $fileCreateTime = filectime($cacheFile);
            if(time() - $fileCreateTime <= self::$cacheTime){	//缓存1天。过期后从接口重新拿数据
                $dataJSON = Util_File::read($cacheFile);
                if(!empty($dataJSON)){
                    return json_decode($dataJSON, true);
                }
            }
        }
        $params = Api_Channel_Client::init()->setXml()->getParams();
        $items =array();
        $response = Api_Channel_Dealer::getResponse($config['url'], $params, $config['isXml']);

        //获取列表
        $data = $response['root']['products']['category'];
        $list = array();
        foreach($data as $k =>$v){
            if(empty($v['product'])) continue;
            if(is_array($v['product'])) $list=array_merge($list,$v['product']);
            if(is_string($v['product'])) {$list=array_merge($list,array($v['product']));}

        }
        Common::log(array("index_list_count"=>count($list)), 'yhd_response.log');
        if (!empty($list)) {
            $dataJSON = json_encode($list);
            Util_File::del($cacheFile);
            Util_File::write($cacheFile, $dataJSON);
            return $data;
        }
    }

    private static function getYhdCat($config){
        //缓存文件
        $cacheFile = self::getSavePath() . "/cache_{$config['type']}_cat.bin";
        if(file_exists($cacheFile)) {
            $fileCreateTime = filectime($cacheFile);
            if(time() - $fileCreateTime <= self::$cacheTime){	//缓存1天。过期后从接口重新拿数据
                $dataJSON = Util_File::read($cacheFile);
                if(!empty($dataJSON)){
                    return json_decode($dataJSON, true);
                }
            }
        }
        $params = Api_Channel_Client::init()->setXml('category.xml')->getParams();
        $response = Api_Channel_Dealer::getResponse($config['url'], $params, $config['isXml']);
        //获取列表
        $data = Common::resetKey($response['categories']['category'],'cid');
        array_walk($data,function(&$v){$v=$v['cname']['@cdata'];});
        if (!empty($data)) {
            Common::log(array("cat_list_count"=>count($data)), 'yhd_response.log');
            $dataJSON = json_encode($data);
            Util_File::del($cacheFile);
            Util_File::write($cacheFile, $dataJSON);
            return $data;
        }

    }


    public static function getSavePath() {
        return realpath(Common::getConfig("siteConfig", "attachPath"));
    }

}