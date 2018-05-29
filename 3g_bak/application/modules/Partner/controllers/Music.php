<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 合作:音乐接口
 *
 */
class MusicController extends Front_BaseController {


    /**
     * 歌手新闻接口
     */
    public function singer_newsAction() {
        $name = $this->getInput('name');
        $list = array();
        if (!empty($name)) {
            $list = Partner_Service_SingerNews::getDao()->getList(0, 100, array('name' => $name), array('date' => 'desc'));
            if (empty($list)) {
                //Partner_Service_SingerNews::grabData($name);
                //$list = Partner_Service_SingerNews::getDao()->getsBy(array('name'=>$name),array('date'=>'desc'));
            }
        }

        $t_bi = $this->getSource();
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PARTNER_PV, '1:singer_list');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_PARTNER_UV, '1:singer_list', $t_bi);

        $data = array();
        foreach ($list as $val) {
            $url    = urlencode($val['link']);
            $crc    = crc32($val['name'] . $val['link'] . 'gionee');
            $data[] = array(
                'title' => $val['title'],
                'date'  => $val['date'],
                'link'  => Common::getCurHost() . '/partner/music/news_to?name=' . $val['name'] . '&url=' . $url . '&crc=' . $crc,
            );
        }

        $this->output(0, '', $data);
    }

    public function news_toAction() {
        $name = $this->getInput('name');
        $url  = $this->getInput('url');
        $crc  = $this->getInput('crc');

        $pcrc = crc32($name . $url . 'gionee');
        if ($pcrc == $crc) {
            $t_bi = $this->getSource();
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PARTNER_PV, $name . ':singer_news');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_PARTNER_UV, $name . ':singer_news', $t_bi);
            Common::redirect($url);
            exit;
        }
        exit;
    }
}