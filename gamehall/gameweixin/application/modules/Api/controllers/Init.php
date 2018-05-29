<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class InitController extends Api_BaseController {
    
//     public function testUserGiftAction() {
//         $result = WeiXin_Msg_Center::getUserGiftCode('oIV9Ot1J3LrbPf0XlK42qn7R3EjI', '9513EC45268447378196A3102499D7C5');
//         print_r($result);
//         exit();
//     }
    
//     public function accessTokenAction() {
//         $result = WeiXin_Service_Base::accessToken();
//         echo WeiXin_Config::getAppID().'<br>';
//         exit($result);
//     }
    
//     public function getTokenAction() {
//         $redis = Common::getCache();
// 		$rvalue = $redis->get(WeiXin_Config::ACCESSTOKEN_CACHE_KEY);
//         exit($rvalue);
//     }
    
//     public function getGroupNameAction() {
//         $result = WeiXin_Server_User::getGroupName(0);
//         exit('result='.$result);
//     }
    
//     public function syncUserAction() {
//         $count = WeiXin_Service_User::syncAllUserInfo();
//         exit('count='.$count);
//     }
    
//     public function createMenuAction() {
//         WeiXin_Service_Menu::createMenu();
//         exit();
//     }
    
//     public function giftDebugAction() {
//         $uuid = trim($this->getInput('uuid'));
//         $debug = trim($this->getInput('debug'));
//         if (!$uuid) {
//         	exit('uuid == null');
//         }
//         $cache = Common::getCache();
//         $cacheKey = 'weixin_gift_debug'.$uuid;
//         if ($debug) {
//             $cache->set($cacheKey, 1, 86400);
//             echo $cacheKey.' = '.$cache->get($cacheKey).'<br>';
//             exit($cacheKey.' : debug open');
//         } else {
//             $cache->delete($cacheKey);
//             echo $cacheKey.' = '.$cache->get($cacheKey).'<br>';
//             exit($cacheKey.' : debug close');
//         }
//     }
}

?>