<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * yhd goods api
 * @author ryan
 *
 */
class Api_Channel_SuningAdapter{

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
        $indexData = self::getFullIndex($config);
        $total = 0;
        $count = str_pad(sprintf('[%s]',$indexData['total']),10,'_',STR_PAD_BOTH);
        echo PHP_EOL;
        echo "|_______Total________{$count}____|";
        $i=0;
        foreach ($indexData['list'] as $k=> $v) {
            $i++;
            $url= sprintf("%s%s.xml",$indexData['dir'],$v);
            $res = Api_Channel_Dealer::getResponse($url, null, $config['isXml']);

            if(empty($res['items']['item'])) continue ;
            if(empty($res['items']['item'][0])){
                $rows[] = $res['items']['item'];
            }else{
                $rows = $res['items']['item'];
            }
            foreach ($rows as $n => $x) {
                $rs[] = array(
                  'supplier' => 11,
                  'goods_id' => $x['product_id'],
                  'title' => $x['title'],
                  'category_name' => $x['category'],
                  'img' => $x['image'],
                  'link' => $x['wireless_link'],
                  'market_price' => $x['price'],
                  'sale_price' => $x['discount']['dprice'],
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
    private static function getFullIndex($config){
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
        $params = array();
        $items =array();
        $url= sprintf("%sindex/FullIndex.xml",$config['url']);
        $response = Api_Channel_Dealer::getResponse($url, $params, true);
        foreach ($response['root']['item_ids']['outer_id'] as $v) {
            $list[]=$v['@value'];
        }
        unset($response['root']['item_ids']);
        $data= $response['root'];
        $data['list']=$list;

        //获取列表
        Common::log(array("index_list_count"=>count($list)), $config['type'].'_response.log');
        if (!empty($list)) {
            $dataJSON = json_encode($data);
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