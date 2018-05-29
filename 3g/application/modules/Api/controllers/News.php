<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 新闻接口
 * @author tiger
 *
 */
class NewsController extends Api_BaseController {

    /**
     * 金立购接口
     */
    public function indexAction() {
        $tid = $this->getInput("tid");
        $arr = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        if (in_array($tid, $arr)) {
            $webroot   = Common::getCurHost();
            $jh_sohu   = Common::getConfig('apiConfig', 'jh_sohu');
            $jh_ifeng  = Common::getConfig('apiConfig', 'jh_ifeng');
            $jh_gionee = Common::getConfig('apiConfig', 'jh_gionee');
            if (isset($jh_sohu[$tid])) {
                $link = $jh_sohu[$tid]['moreUrl'];
            }
            if (isset($jh_ifeng[$tid])) {
                $link = $jh_ifeng[$tid]['moreUrl'];
            }
            if (isset($jh_gionee[$tid])) {
                $link = $jh_gionee[$tid]['moreUrl'];
            }

            $moreUrl = Common::clickUrl($tid, 'NEWSMORE', $link, $this->getSource());

            $param = array('type_id' => $tid, 'status' => 1);
            if ($tid == 1) {
                $param['is_ad'] = 0;
            }

            $order = array('ontime' => 'DESC', 'sort' => 'DESC', 'id' => 'DESC');
            list(, $list) = Gionee_Service_Jhnews::getsBy($param, $order);
            $list = $this->_cookData($list);

            $json_data = array('list' => array('data' => $list), 'moreUrl' => $moreUrl);
            $this->output(0, '', $json_data);
        }
    }

    public function indexV2Action() {
        $ids = $this->getInput("ids");

        $rc    = Common::getCache();
        $rcKey = __METHOD__ . $ids;
        $data  = $rc->get($rcKey);
        //输出结果
        if ($data) {
            $this->output(0, '', $data);
        }

        $jh_sohu   = Common::getConfig('apiConfig', 'jh_sohu');
        $jh_ifeng  = Common::getConfig('apiConfig', 'jh_ifeng');
        $jh_gionee = Common::getConfig('apiConfig', 'jh_gionee');
        $idArr     = explode(',', $ids);
        $data      = array();
        foreach ($idArr as $tid) {

            if ($tid < 0 || $tid > 13) {
                $this->output(0, '', $data);
            }

            if (isset($jh_sohu[$tid])) {
                $link = $jh_sohu[$tid]['moreUrl'];
            }
            if (isset($jh_ifeng[$tid])) {
                $link = $jh_ifeng[$tid]['moreUrl'];
            }
            if (isset($jh_gionee[$tid])) {
                $link = $jh_gionee[$tid]['moreUrl'];
            }

            $moreUrl = Common::clickUrl($tid, 'NEWSMORE', $link, $this->getSource());

            $param = array('type_id' => $tid, 'status' => 1);
            if ($tid == 1) {
                $param['is_ad'] = 0;
            }

            $sort = array('ontime' => 'DESC', 'sort' => 'DESC', 'id' => 'DESC');
            list(, $list) = Gionee_Service_Jhnews::getsBy($param, $sort);
            $list = $this->_cookData($list);

            $data[] = array(intval($tid), array('list' => array('data' => $list), 'moreUrl' => $moreUrl));
        }

        $rc->set($rcKey, $data, 60);

        $this->output(0, '', $data);
    }

    /**
     * 聚合阅读接口
     */
    public function juheAction() {
        $source_id = $this->getInput("source_id");
        $column    = $this->getInput("column");
        $tj_typeid = $this->getInput("tj_typeid");
        $webroot   = Common::getCurHost();

        $moreUrl = $webroot . '/news/list?id=' . $tj_typeid . '&tj_typeid=' . $tj_typeid;
        $where   = array('source_id' => $source_id, 'status' => 1);
        list(, $list) = Gionee_Service_OutNews::getList(1, 6, $where, array('timestamp' => 'DESC'));

        $data_list = array();
        foreach ($list as $key => $value) {
            $data_list[$key]['title'] = $value['title'];
            $data_list[$key]['url']   = $webroot . '/news/detail?id=' . $value['id'] . '&column=' . $column . '&tj_typeid=' . $tj_typeid;
        }

        $ad = Gionee_Service_Jhtype::get($tj_typeid);
        if (!empty($ad['link'])) {
            $ad_title     = $ad['ad'];
            $ad_link      = Common::clickUrl($value['id'], 'JHAD', $ad['link'], $this->getSource());
            $ad_news      = array('title' => $ad_title, 'url' => $ad_link);
            $data_list[6] = $ad_news;
        }

        $json_data = array('list' => array('data' => $data_list), 'moreUrl' => $moreUrl);
        $this->output(0, '', $json_data);
    }

    /**
     * @param array $data
     */
    private function _cookData($data) {
        $data_list = array();

        $webroot = Common::getCurHost();

        foreach ($data as $key => $value) {
            if ($value['is_ad'] == 1) $value['url'] = Common::clickUrl($value['id'], 'NEWSAD', $value['url'], $this->getSource());
            $data_list[$key]['id']     = $value['id'];
            $data_list[$key]['url']    = $value['url'];
            $data_list[$key]['title']  = $value['title'];
            $data_list[$key]['color']  = $value['color'];
            $data_list[$key]['is_ad']  = $value['is_ad'];
            $data_list[$key]['ontime'] = Gionee_Service_Jhnews::newsTime($value['ontime']);
        }
        return $data_list;
    }

    /**
     * 轮换图片
     */
    public function adAction() {
        $top_ads    = Gionee_Service_Ad::getCanUseAds(1, 4, array('ad_type' => 5));
        $data       = array();
        foreach ($top_ads as $key => $value) {
            $data[$key]['id']    = $value['id'];
            $data[$key]['title'] = $value['title'];
            $data[$key]['img']   = Common::getImgPath() . $value['img'];
            $data[$key]['link']  = Common::clickUrl($value['id'], 'NEWSAD', $value['link'], $this->getSource());
        }
        $this->output(0, '', $data);
    }

}
