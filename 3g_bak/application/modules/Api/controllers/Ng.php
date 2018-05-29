<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 导航接口
 */
class NgController extends Api_BaseController {
    /**
     * 分类展开接口
     */
    public function listAction() {
        $type_id   = $this->getInput('type_id');
        $clientVer = $this->getInput('ver'); //客户端版本号
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NG_LIST, $type_id); //统计数据
        $verKey     = Gionee_Service_Ng::KEY_NG_TYPE_VER . $type_id;
        $serviceVer = Common::getCache()->get($verKey);
        if ($clientVer != $serviceVer) {
            $data = Gionee_Service_Ng::getTypeData($type_id);
            if (empty($data['content'])) {
                $this->output(0, '', array('data' => '-1', 'ver' => $clientVer));
            }
            $this->output(0, '', array('data' => $data['content'], 'ver' => $serviceVer));
        }
        $this->output(0, '', array('data' => '-1', 'ver' => $serviceVer));
    }

    //顶部广告接口
    public function adAction() {
        $rc   = Common::getCache();
        $cVer = $this->getInput('ver');
        $vKey = Gionee_Service_Ng::KEY_NG_AD; //版本号相关的缓存内容
        $sVer = $rc->get($vKey);
        $t_bi = $this->getSource();
        Gionee_Service_Log::uvLog('3g_ad', $t_bi);
        if ($cVer != $sVer) {

            $result = array();
            $ads    = Gionee_Service_Ng::getAds(); //所有的广告内容
            if (!empty($ads['nor_ad'])) {
                foreach ($ads['nor_ad'] as $s => $t) {
                    $result[] = $t;
                }
            }

            Gionee_Service_Label::filterADData('nav', $ads['model_ad'], $result);

            $this->output(0, '', array('msg' => $result, 'ver' => $sVer, 'act11' => 0));
        }
        $this->output(0, '', array('msg' => '-1', 'ver' => $sVer, 'act11' => 0));
    }


    //百度关键字
    public function baiduAction() {
        $json_data = Gionee_Service_Baidu::apiKeys();
        //shuffle($result);//将数组打乱
        $this->output(0, '', $json_data);
    }

    //导航子页接口
    public function cateAction() {
        $type_id = intval($this->getInput('type_id'));
        $orderBy = array('sort' => 'ASC', 'id' => 'ASC');

        $where = array('id' => $type_id, 'page_id' => 2, 'status' => 1);
        $rcKey = 'API_NG_CATE:' . crc32(json_encode($where));
        $type  = Common::getCache()->get($rcKey);
        if ($type === false) {
            $type = Gionee_Service_NgType::getBy($where, $orderBy);
            Common::getCache()->set($rcKey, $type, 600);
        }

        if (!$type_id || !$type) {
            Common::redirect($this->actions['indexUrl']);
        }
        $pageData['cinner'] = array('data' => Gionee_Service_Ng::getTypeData($type_id));

        $this->output(0, '', $pageData);
    }


    //新闻资讯头条
    public function newsAction() {

        $inputArr = $this->getInput(array('ngid', 'switchCnt', 'limit'));
        $switch   = empty($inputArr['switchCnt']) ? 1 : intval($inputArr['switchCnt']);
        $limit    = empty($inputArr['limit']) ? 20 : intval($inputArr['limit']);

        $rcKey    = Gionee_Service_Ng::KEY_NG_NEWS_LIST . $inputArr['ngid'] . $limit;
        $newsList = Common::getCache()->get($rcKey);
        if (!empty($newsList)) {
            $this->output(0, '', $newsList);
        }

        $ngInfo = Gionee_Service_Ng::get($inputArr['ngid']);
        if (empty($ngInfo)) {
            $this->output(-1, '没有这栏目');
        }
        $ext                   = json_decode($ngInfo['ext'], true);
        $inputArr['source_id'] = $ext['newsSourceId'];
        $tempNewsList          = Gionee_Service_Ng::getnewslist($ext['newsSourceId'], $limit);
        $list                  = array();
        foreach ($tempNewsList as $k => $tempNews) {
            $list[] = array(
                'id'    => $tempNews['id'],
                'pos'   => $k + 1,
                'link'  => Common::clickUrl($ngInfo['id'], 'NAVNEWS', $tempNews['link'], $this->getSource()),
                'title' => $tempNews['title'],
            );
        }

        $newsList['list'] = $list;

        Common::getCache()->set($rcKey, $newsList, Common::T_TEN_MIN);

        $this->output(0, '', $newsList);
    }


}