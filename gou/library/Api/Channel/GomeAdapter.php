<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * yhd goods api
 * @author ryan
 *
 */
class Api_Channel_GomeAdapter{

    private  $app_key;
    private  $app_secret;
    private  $format='json';
    private  $params;
    public static $cacheTime=84600;

    public function format($format='json'){
        $this->format=$format;
    }

    public function getParams(&$params=array()){
        $this->config();
        $params['app_key'] =$this->app_key;
        $params['app_secret'] =$this->app_secret;
        $params['format'] =$this->format;
        return $params;
    }

    private function config(){
        $this->app_key    = Common::getConfig('apiConfig', 'gome_app_key');
        $this->app_secret = Common::getConfig('apiConfig', 'gome_app_secret');
    }

    public static function init(){
        return new self();
    }


    /**
     *
     * Channel
     * @param array $config
     * @return array
     */
    public static function getData($config){
        $cats = static::getAllCat($config);
        $params['api_name']= 'gome.items.page.get';

        $params['page_size']=1000;
//        $params['stock_status']=1;
        $total = 0;
        $count = str_pad(sprintf('[%s]',count($cats[3])),10,'_',STR_PAD_BOTH);
        echo PHP_EOL;
        echo "|_______Total________{$count}____|";
        $i=1;

        foreach ($cats[3] as $k=> $v) {
            $time = str_pad(sprintf('[%s]',$i),10,'_',STR_PAD_RIGHT);
            $params['category_id']= $v['category_id'];
            $t=1;
            $sum = 0;
            while($t){
                $rows=array();
                $params['page_no']=$t;
                $params = static::init()->getParams($params);
                $res = Api_Channel_Dealer::getResponse($config['url'], $params, $config['isXml']);
                if($t==1){
                    echo PHP_EOL;
                    $total = str_pad(sprintf('[%s]', $res['total_count']),10,'_',STR_PAD_BOTH);
                    echo "|_______{$time}___{$total}____|";
                }
                echo PHP_EOL;
                $count = str_pad(sprintf('[%s]', count($res['items'])),10,'_',STR_PAD_BOTH);
                echo "|_______Items________{$count}____|";
                foreach ($res['items'] as $x) {
                    $img = explode(',',$x['picture_url']);
                    if(!$x['sale_price'])$x['sale_price']=$x['list_price'];
                    $rows[] = array(
                      'supplier' => 12,
                      'goods_id' => $x['product_id'],
                      'title' => $x['sku_name'],
                      'category_name' => $v['category_name'],
                      'img' => $img[0],
                      'storage' => $x['stock_status'],
                      'link' => $x['product_url_wap'],
                      'market_price' => $x['list_price'],
                      'sale_price' => $x['list_price'],
                    );
                }
                if(!empty($rows)){
                    Client_Service_Source::insert($rows);
                }
                $sum += count($rows);
                if($res['total_count']<=$t*1000) break;
                $t++;
            }

            $count = str_pad(sprintf('[%s]',$sum),10,'_',STR_PAD_BOTH);
            echo PHP_EOL;
            echo "|_______{$time}___{$count}____|";
            $total +=count($rows);
            $rs=array();
            sleep(0.2);
            $i++;
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

    private static function getAllCat($config){
        //缓存文件
        $cacheFile = self::getSavePath() . "/cache_{$config['type']}_cat.bin";
        $data = static::getCache($cacheFile);
        if(!empty($data)) return $data;

        $params['api_name']= 'gome.categorys.get';

        $params = static::init()->getParams($params);
        $res = Api_Channel_Dealer::getResponse($config['url'], $params, $config['isXml']);
        if(empty($res['categorys'])) return false;

        $data = array();
        foreach ($res['categorys'] as $k=>$v) {
            $data[$v['category_grade']][$k]=$v;
        }
       return  static::writeCache($data,$cacheFile);
    }

    private static function  writeCache($data,$cacheFile){
        if (!empty($data)) {
            Common::log(array("cat_list_count"=>count($data[3])), 'gome_response.log');
            $dataJSON = json_encode($data);
            Util_File::del($cacheFile);
            Util_File::write($cacheFile, $dataJSON);
            return $data;
        }
    }
    private static function  getCache($cacheFile){

        if(!file_exists($cacheFile)) return false;

        $fileCreateTime = filectime($cacheFile);
        if(time() - $fileCreateTime > self::$cacheTime)return false;

        $dataJSON = Util_File::read($cacheFile);
        if(empty($dataJSON)) return false;
        return json_decode($dataJSON, true);
    }


    public static function getSavePath() {
        return realpath(Common::getConfig("siteConfig", "attachPath"));
    }

}