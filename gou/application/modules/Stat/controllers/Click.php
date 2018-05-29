<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class ClickController
 * @auth rainkid
 */
class ClickController extends Stat_BaseController {

    /**
     *redirect
     */
    public function redirectAction() {
        $hash = $this->getInput('t');
        $data_parm = $this->getInput('data');

        $url = html_entity_decode(Stat_Service_ShortUrl::getUrl($hash));
        $refer = Util_Http::getServer('HTTP_REFERER');
    	 if(!$url) {
    	     $this->redirect($refer);
    	     exit();
    	 }

        //make uid
        $uid = $this->getUniqueUID();

        $data = array($hash, $uid, Common::getIMEI(), Common::getTime(), Util_Http::getClientIp());
        $queue = Common::getQueue();

        //tj data
        $queue->push('tjdata', $data);
        //redirect
        if($data_parm) {
            $url .= '&data='.urlencode($data_parm);
        }
        $this->redirect($url);
        exit();
    }

    /**
     *search
     */
    public function searchAction() {
    	$hash = $this->getInput('t');
    	$keyword = trim($this->getInput('keyword'));
    	if(!$keyword) $keyword = trim($this->getPost('keyword'));
    	
    	if($keyword) {
    	    $info = array(
    	            'keyword'=>$keyword,
    	            'keyword_md5'=>md5($keyword),
    	            'create_time'=>Common::getTime(),
    	            'dateline'=>date('Y-m-d', Common::getTime())
    	    );
    	    Client_Service_KeywordsLog::addKeywordsLog($info);
    	}
    
    	$url = Stat_Service_ShortUrl::getUrl($hash);
    	
    	$refer = Util_Http::getServer('HTTP_REFERER');
        if(!$url) {
    	     $this->redirect($refer);
    	     exit();
    	 }
    
    	//get uid
        $uid = $this->getUniqueUID();
    
    	$data = array($hash, $uid, Common::getIMEI(), Common::getTime(), Util_Http::getClientIp());
    	$queue = Common::getQueue();
    
    	//tj data
    	$queue->push('tjdata', $data);
    	//redirect
    	$redirect_url = html_entity_decode($url).urlencode($keyword);
    	$this->redirect($redirect_url);
    	exit;
    }
    
    
}