<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh  test
 *
 */
class Apk_TestController extends Api_BaseController {
	
	public function indexAction() {
        $url = 'http://m.lefeng.com/index.php/goods/detail/type/1/goodsId/6372460';
    
        $dom = new DOMDocument('1.0', 'UTF8');
        $d = Util_Http::get($url, array('User-Agent'=>'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.94 Safari/537.36'));
    
        //$d = str_replace(array('/\s/', '/bgcolor=".*?"/'), array("", ''), $d->data);
         
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $d->data);
    
        $xpath = new DOMXPath($dom);
    
        //$dom->preserveWhiteSpace = false;
        //$dom->normalizeDocument();
        //$dom->normalize();
    
        $result = array();
    
        $tag = $xpath->query('//div[@style="padding:0px"]/p/font');
        print_r(trim($tag->item(0)->nodeValue));die;
	}
	
	
    
    function getData() {
        //$url = 'http://jingyan.baidu.com/article/ab69b270c8b1452ca7189f23.html';
        $url = 'shop.m.taobao.com/shop/shop_index.htm?spm=0.0.0.0&shop_id=33322277&sid=1a951e35930bd3e7d909a8b0c1defd79';
        //$url = 'http://jingyan.baidu.com/article/495ba841102b4838b20ede5b.html';
    
        $dom = new DOMDocument('1.0', 'UTF8');
        $d = Util_Http::get($url, array('User-Agent'=>'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.94 Safari/537.36'));
    
        //$d = str_replace(array('/\s/', '/bgcolor=".*?"/'), array("", ''), $d->data);
         
        $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $d->data);
    
        $xpath = new DOMXPath($dom);
    
        //$dom->preserveWhiteSpace = false;
        //$dom->normalizeDocument();
        //$dom->normalize();
    
        $result = array();
    
        //title
        $title = $xpath->query('<!-- data -->');
        //$shop_id = trim($title->item(0)->nodeValue);
        print_r($title);die;
    
        $tag = $xpath->query('//div[@alog-group="exp-content"]/div/h2');
        
    }
    
}
