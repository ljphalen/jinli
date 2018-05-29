<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {
	
	public $perpage = 20;

    public $actions = array(
        'dlapkUrl'=>'/front/index/dlapk',
    );
	
    public function indexAction() {


        if(Common::isMobile()) {
            $this->forward('m');
        } else {
            $this->forward('pc');
        }
        return false;
    }

    public function pcAction() {
        $configs = Ygou_Service_Config::getAllConfig();
        $erwm = Common::getConfig('erwmConfig');

        $this->assign('erwm', $erwm);
        $this->assign('configs', $configs);
    }

    public function mAction() {
        $configs = Ygou_Service_Config::getAllConfig();
        $this->assign('configs', $configs);
    }

    public function dlapkAction() {
        $ua = strtolower(Util_Http::getServer('HTTP_USER_AGENT'));
        if (stripos($ua, 'micromessenger') !== false) {
            $this->forward("weixin");
            return false;
        } else if (stripos($ua, 'android') !== false) {
          $url = Ygou_Service_Config::getValue('android_dl_url');
        } else {
            $url = Ygou_Service_Config::getValue('ios_dl_url');
        }
        $this->redirect(html_entity_decode($url));
    }

    public function weixinAction() {

    }
}