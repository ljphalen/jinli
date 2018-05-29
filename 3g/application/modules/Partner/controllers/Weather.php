<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 合作:天气接口
 *
 */
class WeatherController extends Front_BaseController {
    /**
     * 浏览器配置接口
     */
    public function listAction() {
        $args   = array(
            'nextId' => FILTER_VALIDATE_INT,
            'ver'    => FILTER_VALIDATE_INT,
        );
        $t_bi   = $this->getSource();
        $params = filter_input_array(INPUT_GET, $args);
        $nextId = intval($params['nextId']);
        $ver    = $params['ver'];
        $page   = max($nextId, 1);
        //@todo 直接数据库
        $ret         = Nav_Service_NewsData::getWeatherList($page, $ver, true);
        $tmp         = $this->_adList($page);
        $ret['list'] = array_merge($tmp, $ret['list']);

        $banner = Nav_Service_NewsAd::getListByPos('partner_weather_list', true);
        $this->assign('banner', $banner);
        $this->assign('list', $ret['list']);
        $this->assign('dataver', $ret['ver']);

        if (!empty($nextId)) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PARTNER_PV, '2:weather_list');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_PARTNER_UV, '2:weather_list', $t_bi);
            $this->output(0, '', $ret);
        } else {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PARTNER_PV, '1:weather_list');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_PARTNER_UV, '1:weather_list', $t_bi);
        }
    }

    public function detailAction() {
        $args   = array(
            'id'  => FILTER_VALIDATE_INT,
            'act' => FILTER_SANITIZE_STRING,
        );
        $t_bi   = $this->getSource();
        $params = filter_input_array(INPUT_GET, $args);
        $id     = $params['id'];
        //@todo 直接数据库
        $info = Nav_Service_NewsData::getWeatherRecordInfo($id, true);

        if (empty($info['id'])) {
            exit;
        }
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PARTNER_PV, $info['id'] . ':weather_detail');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_PARTNER_UV, $info['id'] . ':weather_detail', $t_bi);

        $this->assign('info', $info);

        if (empty($info['content'])) {
            Common::redirect($info['link']);
            exit;
        }

        $banner = Nav_Service_NewsAd::getListByPos('partner_weather_content', true);
        $this->assign('banner', $banner);
        $reclink = Nav_Service_NewsAd::getListByPos('partner_weather_reclink', true);
        $this->assign('reclink', $reclink);

        //$this->output(0, '', $ret);
    }

    private function _adList($page) {
        $listads = Nav_Service_NewsAd::getListByPos('partner_weather_list_txt');
        $tmp     = array();
        if (!empty($listads) && $page == 1) {
            foreach ($listads as $v) {
                $tmp        = array();
                $formatTime = Common::formatDate($v['created_at']);
                $tmp[]      = array(
                    'cdnimgs'    => array($v['img']),
                    'appId'      => 0,
                    'detailId'   => $v['id'],
                    //'outId'      => $val['out_id'],
                    'title'      => $v['title'],
                    'postTime'   => $v['created_at'],
                    'formatTime' => $formatTime,
                    //'skip'       => false,
                    'url'        => $v['url'],
                );
            }
        }
        return $tmp;
    }

}