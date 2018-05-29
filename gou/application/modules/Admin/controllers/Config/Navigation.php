<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * @class Config_NavigationController
 * @author Milo
 * @package Admin
 */
class Config_NavigationController extends Admin_BaseController {

    public $actions = array(
        'indexUrl' => '/Admin/Config_Navigation/index',
        'editPostUrl' => '/Admin/Config_Navigation/edit_post',
        'uploadUrl' => '/Admin/Config_Navigation/upload',
        'uploadPostUrl' => '/Admin/Config_Navigation/upload_post',
    );

    public $versionName = 'Navigation_Version';

    public function indexAction(){
        $configs = json_decode(Gou_Service_ConfigTxt::getValue('nav_main_txt'), true);
        $this->assign('configs', $configs);
        $this->cookieParams();
    }

    public function edit_postAction(){
        $configs = $this->getPost(array(
            'nav_main_icon_1_1',
            'nav_main_icon_1_2',
            'nav_main_icon_2_1',
            'nav_main_icon_2_2',
            'nav_main_icon_3_1',
            'nav_main_icon_3_2',
            'nav_main_icon_3_link',
            'nav_main_icon_4_1',
            'nav_main_icon_4_2',
            'nav_main_icon_5_1',
            'nav_main_icon_5_2',
            'nav_main_txt_color_1',
            'nav_main_txt_color_2',
            'nav_main_tab_bg',
            'nav_main_switch',
        ));
        $configs = $this->_cookData($configs);
        Gou_Service_ConfigTxt::setValue('nav_main_txt', json_encode($configs));
        Gou_Service_Config::setValue('Config_Version', Common::getTime());
        $this->output(0, '操作成功');
    }

    /**
     * 参数过滤
     * @param array $info
     * @return array
     */
    private function _cookData($configs) {
        return $configs;
    }

    /**
     * 上传页面
     */
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }
    /**
     *
     * 上传
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'config');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

}
