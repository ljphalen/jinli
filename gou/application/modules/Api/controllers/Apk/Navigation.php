<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Terry
 * Class Apk_NavigationController
 */
class Apk_NavigationController extends Api_BaseController {

    public $versionName = 'Navigation_Version';

    /**
     * 首页底部导航--主导航
     */
    public function navMainAction(){
        $server_version = Gou_Service_Config::getValue($this->versionName);
        $configs = json_decode(Gou_Service_ConfigTxt::getValue('nav_main_txt'), true);
        $attach = Common::getAttachPath();
        unset($configs['nav_main_switch']);
        foreach($configs as $key => $val){
            if(stripos($key, 'icon_')){
                $data[str_replace('nav_main_', '', $key)] = $val ? $attach . html_entity_decode($val) : '';
            }else{
                $data[str_replace('nav_main_', '', $key)] = $val ? html_entity_decode($val) : '';
            }
        }
        $data['icon_3_link'] = html_entity_decode($configs['nav_main_icon_3_link']);
        $data['version'] = intval($server_version);
//        Common::log($data, 'config.log');
        $this->output(0, '', $data);
    }


}
