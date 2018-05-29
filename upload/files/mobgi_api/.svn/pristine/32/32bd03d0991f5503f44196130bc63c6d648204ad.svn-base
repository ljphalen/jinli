<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2014-10-20 15:07:30
 * $Id: CategoryController.php 62100 2014-10-20 15:07:30Z hunter.fang $
 */

//使用:后台设置crontab,每十分钟执行一次脚本 curl http://hunter.backend.mobgi.com/Category/updateCategory (用来抓取服务器上的数据到本地的数据库.)
//根据类型获取分类信息:http://hunter.backend.mobgi.com/Category/getCategoryByType?type=1&callbackparam=CsCategorywidget_sdksuccess_jsonpCallback
//根据分类id获取该分类ＩＤ下的所有渠道信息：http://hunter.backend.mobgi.com/Category/getChannelData?callbackparam=success_jsonpCallback&classIds=-999,3999631,3999651

class CategoryController extends DooController {
    public function __construct(){

    }
    
    public function index() {
        
    }
    
    /**
     * 获取分类信息
     * 新：http://hunter.backend.mobgi.com/Category/getCategoryByType?type=1&callbackparam=CsCategorywidget_sdksuccess_jsonpCallback
     * 原：http://backend.idreamsky.com/sys_groups/getCategoryListCombo?type=1&callbackparam=CsCategorywidget_sdksuccess_jsonpCallback
     */
    public function getCategoryByType(){
//        $get = $this->get;
        $get = $_GET;
        $type = intval($get['type']);
        $selids = $get['selids'];
        if(!empty($selids)){
            $selidsArr = explode(',', $selids);
        }
        $categoryModel = Doo::loadModel('datamodel/Category', TRUE);
        $categoryArr = $categoryModel->findAll(array('type' => $type));
        if(!empty($categoryArr)){
            foreach($categoryArr as $key=>$item)
            {
                unset($categoryArr[$key]['type']);
                unset($categoryArr[$key]['channelData']);
                if(in_array($item['id'], $selidsArr)){
                    $categoryArr[$key]['checked'] = 'true';
                }
                if(empty($categoryArr[$key]['parentId'])){
                    unset($categoryArr[$key]['parentId']);
                }
            }
        }else{
            $categoryArr = array();
        }
        $json_data = json_encode($categoryArr);
        $result = '';
        if($get['callbackparam']){
            $result = $get['callbackparam']."(".$json_data.")";
        }else{
            $result = $json_data;
        }
        echo $result;
    }
    
    /**
     * 获取渠道信息
     * 新：http://hunter.backend.mobgi.com/Category/getChannelData?callbackparam=success_jsonpCallback&classIds=-999,3999631,3999651
     * 原：http://backend.idreamsky.com/channels_in_groups/GetLinkChannels?callbackparam=success_jsonpCallback&classIds=-999,3999631,3999651
     */
    public function getChannelData(){
//        $get = $this->get;
        $get = $_GET;
        $classIds = $get['classIds'];
        $categoryModel = Doo::loadModel('datamodel/Category', TRUE);
        $classIdsArr = explode(',', $classIds);
        $idsArr = array();
        $ids = '';
        if(!empty($classIdsArr)){
            foreach($classIdsArr as $classId){
                $idsArr[] = "'".intval($classId)."'";
            }
        }
        $ids = implode(',', $idsArr);
        $categoryArr = $categoryModel->findAll(array('ids' => $ids));
        $arr_channels = array();
        if(!empty($categoryArr)){
            foreach($categoryArr as $categoryItem){
                $channelItem = json_decode($categoryItem['channelData'], true);
                foreach($channelItem as $channelinfo){
                    //渠道不存在的才新增(去重)
                    if(!in_array($channelinfo, $arr_channels)){
                        $arr_channels[] = $channelinfo;
                    }
                }
            }
        }
        $arr_data = array();
        $json_data = json_encode($arr_channels);
        $result = '';
        if($get['callbackparam']){
            $result = $get['callbackparam']."(".$json_data.")";
        }else{
            $result = $json_data;
        }
        echo $result;
        
    }
    
    /**
     * 从服务器拉取渠道的分类信息，并根据分类ＩＤ抓取各个分类ＩＤ属下的所有渠道信息．
     * 
     */
    public function updateCategory(){
        set_time_limit(0);
        //获取渠道分类树
        $url = 'backend.idreamsky.com/sys_groups/getCategoryListCombo';
        $typeArr  = array(1, 2);
        $totalData = array();
        $RemoteDataArr  = array();
        foreach($typeArr as $type){
            $curl_url = $url . '?type=' . $type;
            $ch = curl_init($curl_url) ;  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回  
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回  
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置超时时间30秒
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0');
            $jsonResult = curl_exec($ch) ;  
            $arrResult = json_decode($jsonResult, true);
            //附上当前type信息
            foreach($arrResult as $key=>$item){
                $arrResult[$key]['type'] = $type;
            }
            $RemoteDataArr = array_merge($RemoteDataArr, $arrResult);
        }
        
        //抓取渠道的详细信息
        foreach($RemoteDataArr as $key=>$categoryItem){
            if(empty($categoryItem['parentId'])){
                $RemoteDataArr[$key]['channelData'] = '';
            }else{
                $url = 'http://backend.idreamsky.com/channels_in_groups/GetLinkChannels';
                $curl_url = $url . '?classIds=-999,' . $categoryItem['parentId'].','.$categoryItem['id']."&_=".time();
                $ch = curl_init($curl_url) ;  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回  
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回  
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置超时时间30秒
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0');
                $jsonChannelResult = curl_exec($ch) ;  
//                var_dump($jsonChannelResult);die;
                $jsonChannelResult = substr($jsonChannelResult, 1, strlen($jsonChannelResult)-2);
                $arrChannelResult = json_decode($jsonChannelResult, true);
                $channelDataTemp = array();
                foreach($arrChannelResult as $channelDataitem){
                    $channelDataTempItem = array();
                    $channelDataTempItem['channels']['realname'] = $channelDataitem['channels']['realname'];
                    $channelDataTempItem['channels']['identifier'] = $channelDataitem['channels']['identifier'];
                    $channelDataTemp[] = $channelDataTempItem;
                }
                $RemoteDataArr[$key]['channelData'] = json_encode($channelDataTemp);
            }
        }
        
        $categoryModel = Doo::loadModel('datamodel/Category', TRUE);
        if($RemoteDataArr){
            $truncateResult = $categoryModel->truncateData();
            $addResult = $categoryModel->addData($RemoteDataArr);
            if($truncateResult && $addResult){
                echo json_encode($RemoteDataArr);echo "\r\n";
                echo "update success!";
            }else{
                echo "update failed";
            }
        }else{
            echo 'no change!';
        }
        
    }
}

