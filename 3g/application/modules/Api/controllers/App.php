<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 添加应用接口
 * @author tiger
 *
 */
class AppController extends Api_BaseController {

    public function indexAction() {
        $id  = intval($this->getInput('id'));
        $app = Gionee_Service_App::getApp($id);
        if (!$id || !$app) $this->output(-1, '非法请求');
        $data = array(
            'appid' => $app['id'],
            'title' => $app['name'],
            'icon'  => Common::getImgContent($app['img']),
            'url'   => html_entity_decode($app['link'])
        );
        $this->output(0, '', $data);
    }

    public function bookAction() {
        $id     = intval($this->getInput('id'));
        $appver = $this->getInput('app_ver');
        $arr    = explode('.', $appver);

        $app = Gionee_Service_WebApp::getApp($id);
        if (!$id || !$app) $this->output(-1, '非法请求');

        if (!empty($appver) && $arr[0] == 4) {
            if ($app['icon2']) {
                $icon = $app['icon2'];
            } else {
                $icon = $app['img'];
            }
        } else {
            if ($app['default_icon']) {
                $icon = Gionee_Service_Config::getValue('default_icon');
            } elseif ($app['icon']) {
                $icon = $app['icon'];
            } else {
                $icon = $app['img'];
            }
        }

        Gionee_Service_WebApp::updateTJ($id);
        $data = array(
            'appid' => $app['id'],
            'title' => $app['name'],
            'color' => $app['color'],
            'icon'  => Common::getImgContent($icon),
            'url'   => html_entity_decode($app['link'])
        );
        $this->output(0, '', $data);
    }
}