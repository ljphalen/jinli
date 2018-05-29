<?php

class SampleController extends Front_BaseController {
    
    
    //推送android设备消息
    function indexAction()
    {
        $user_id = '771868165697658174';
        $title = '测试标题';
        $content = '启动客户端';
        $type = 3;
        
        if($type == 2) {
            $custom_content = array('url'=>'http://m.baidu.com');
        }
        
        if($type == 3) {
            $custom_content = array('action'=>'com.gionee.client.CutList');
        }
        //$custom_content = json_encode(array('url'=>'http://m.baidu.com'));
        //$custom_content = json_encode(array('action'=>'com.gionee.client.CutList'));
        
        //$ret = Api_Baidu_Push::pushMessage($user_id, $title, $content);
        //$ret = Api_Baidu_Push::pushMessage($user_id, $title, $content, 2, $custom_content);
        
        $custom_content = array('action'=>'com.gionee.client.BargainGame', 'game_id'=>569);
        $title = '高能预警！速度之王称号被夺！';
        $content = '您在购物大厅的游戏记录已被超越，快去迎战>>';
        $ret  = Api_Baidu_Push::pushMessage('771868165697658174', $title, $content, 3, $custom_content);
        print_r($ret); var_dump('dddddddddd');
        exit;
    }
    
    
    function testAction() {
        
        
        $host = '42.121.237.23';
        $port = '34543';
        $timeout = '90';
        $user = 'gou3g';
        $pass = 'su#7psDq';
        $local_file = '/home/tiansh/Desktop/a.png';
        $remote_file = '/data/www/attachs/gou/attachs/topic/a.png';
               
    
        // initialize
        $sftp = new Util_Sftp();
        
        // connect
        $sftp->connect($host, $port, $timeout);
        
        // login
        $sftp->login($user, $pass);
        
        $sftp->uploadFile($local_file, $remote_file);
        exit;
    }
    
}