<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class UserPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        var_dump('SamplePlugin_routerStartup');
    }
    
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        var_dump('routerShutdown');
    }
    
    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        var_dump('SamplePlugin_dispatchLoopStartup');
    }
    
    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        var_dump('SamplePlugin_preDispatch');
    }
    
    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        var_dump('SamplePlugin_postDispatch');
    }
    
    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        var_dump('SamplePlugin_dispatchLoopShutdown');
    }
    
}
