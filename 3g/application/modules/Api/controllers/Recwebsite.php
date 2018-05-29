<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 推荐站点
 * @author huwei
 *
 */
class RecwebsiteController extends Api_BaseController {

    /*
     * 推荐网址
     */
    public function urlAction() {
        $t_bi = $this->getSource();
        Gionee_Service_Log::pvLog('3g_recweb_url');
        $ver  = Gionee_Service_Config::getValue('ver_recweb_url');
        $list = Gionee_Service_RecWebsite::getListByType('url');
        $tmp  = array();
        foreach ($list as $val) {
            $icon  = $this->_getIcon($val['icon']);
            $tmp[] = array(
                'name' => $val['title'],
                'url'  => Common::clickUrl($val['id'], 'recwebsite', $val['url'], $t_bi),
                'icon' => $icon,
            );
        }
        $data = array(
            'urlInfos' => $tmp,
            'version'  => intval($ver),
        );
        echo json_encode($data);
        exit;
    }

    /*
     * 推荐站点
     */
    public function siteAction() {
        $t_bi = $this->getSource();
        Gionee_Service_Log::pvLog('3g_recweb_site');
        $gVer  = $this->getInput('version');
        $gIcon = $this->getInput('icon');
        $ver   = Gionee_Service_Config::getValue('ver_recweb_site');

        if ($ver == $gVer) {
            exit;
        }

        $list = Gionee_Service_RecWebsite::getListByType('site');
        $tmp  = array();
        foreach ($list as $val) {
            $icon = '';
            if ($gIcon == 'true') {
                $icon = $this->_getIcon($val['icon']);
            }
            $tmp[$val['group_name']][] = array(
                'name' => $val['title'],
                'url'  => Common::clickUrl($val['id'], 'recwebsite', $val['url'], $t_bi),
                'icon' => $icon,
            );
        }

        $list = array();
        foreach ($tmp as $name => $v) {
            $list[] = array('name' => $name, 'sites' => $v);
        }

        $data = array(
            'id'      => 1,
            'version' => intval($ver),
            'groups'  => $list
        );

        echo json_encode($data);
        exit;
    }

    /*
     * 推荐站点
     */
    public function iconAction() {

        $url = $this->getInput('url');
        if (empty($url)) {
            exit;
        }
        $info = Gionee_Service_RecWebsite::getBy(array('url' => $url), array('sort' => 'asc'));
        if ($info['id']) {
            $data = array(
                'icon' => $this->_getIcon($info['icon']),
            );
        }
        echo json_encode($data);
        exit;

    }

    /**
     * 获取图片base64数据
     *
     * @param $val
     *
     * @return string
     */
    private function _getIcon($val) {
        if (empty($val)) {
            return '';
        }

        $data = Common::getCache()->get($val);
        if (empty($data)) {
            $content = file_get_contents(Common::getImgPath() . $val);
            $data    = base64_encode($content);
            Common::getCache()->set($val, $data, Common::T_ONE_DAY);
        }
        return $data;
    }


    public function tourlAction() {
        $m = $this->getInput('m');
        $b = $this->getInput('b');
        if ($m != 'uc' && is_numeric($b)) {
            $m = 'wap';
        }
        $key  = "{$m}_{$b}";
        $info = Gionee_Service_Inbuilt::getByKey($key);
        if (!empty($info['tourl'])) {
            header("Location:" . $info['tourl']);
            exit;
        }
        header("Location:" . Common::getCurHost() . '/nav');
        exit;
    }
}