<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 *
 */
class Apk_SearchController extends Api_BaseController {
    /**
     * 关键字
     */
    public function keywordsAction() {
        list(,$list) =  Client_Service_Keywords::getList(1, 10, array('status'=>1));
        $data = array();
        foreach ($list as $key=>$value) {
            $data[] = $value['keyword'];
        }
    
        $keyword = Gou_Service_Config::getValue('gou_client_keyword');
    
        //hash
        $apk_taobao_search =  json_decode(Gou_Service_Config::getValue('apk_taobao_search'), true);
        $action = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_APK, $apk_taobao_search['module_id'],
        $apk_taobao_search['channel_id'], 0, $apk_taobao_search['url'], '预装版淘宝搜索', $apk_taobao_search['channel_code']);
    
        $this->output(0, '',  array('keywords'=>$data, 'keyword'=>$keyword, 'taobao_search_url'=>$action.'&keyword='));
    }
    
    
    /**
     * 关键字 随机显示10个
     */
    public function randkeywordsAction() {
        $list =  Client_Service_Keywords::getRandList(1, 10, array('status'=>1));
        $data = array();
        foreach ($list as $key=>$value) {
            $data[] = $value['keyword'];
        }
    
        $keyword = Gou_Service_Config::getValue('gou_client_keyword');
    
        //hash
        $apk_taobao_search =  json_decode(Gou_Service_Config::getValue('apk_taobao_search'), true);
        $action = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_APK, $apk_taobao_search['module_id'],
                $apk_taobao_search['channel_id'], 0, $apk_taobao_search['url'], '预装版淘宝搜索', $apk_taobao_search['channel_code']);
    
        $this->output(0, '',  array('keywords'=>$data, 'keyword'=>$keyword, 'taobao_search_url'=>$action.'&keyword='));
    }
    
    
    /**
     * 搜索
     */
    public function searchAction() {
        $keyword = trim($this->getPost('keyword'));
    
        /*if($keyword) {
         $info = array(
         'keyword'=>$keyword,
         'keyword_md5'=>md5($keyword),
         'create_time'=>Common::getTime(),
         'dateline'=>date('Y-m-d', Common::getTime())
         );
        Client_Service_KeywordsLog::addKeywordsLog($info);
        }*/
    
        //$url = sprintf('http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=%s', urlencode($keyword));
        $this->output(0, '',  'success');
    }
    
    /**
     * 搜索
     */
    public function submitAction() {
        $keyword = trim($this->getInput('keyword'));
    
        $apk_taobao_search =  json_decode(Gou_Service_Config::getValue('apk_taobao_search'), true);
        $s_url = Common::tjurl(Stat_Service_Log::URL_SEARCH, Stat_Service_Log::V_APK, $apk_taobao_search['module_id'],
        $apk_taobao_search['channel_id'], 0, $apk_taobao_search['url'], '预装版淘宝搜索', $apk_taobao_search['channel_code']);
        $url = sprintf('%s&keyword=%s', $s_url, $keyword);
        $this->redirect($url);
    }
    
    
    /**
     * 头部搜索默认关键字
     */
    public function keywordAction(){
        $keyword = Gou_Service_Config::getValue('gou_search_default_keyword');
        $this->output(0,'',array('keyword'=>$keyword));
    }

}
