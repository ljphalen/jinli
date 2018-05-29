<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 导航
 */
class NavController extends Front_BaseController {

    public $actions = array(
        'indexUrl' => '/nav/index',
        'tjUrl'    => '/index/tj',
        'newsUrl'  => '/index/news',
        'listUrl'  => '/nav/list',
    );

    public function indexAction() {
        //统计导航pv
        Gionee_Service_Log::pvLog('3g_nav');
        //统计导航UV
        $t_bi = $this->getSource();
        Gionee_Service_Log::uvLog('3g_nav', $t_bi);
        $apcKey   = 'NAV_INDEX';
        $pageData = Cache_APC::get($apcKey);
        if (empty($pageData)) {
            $pageData = Gionee_Service_Ng::getIndexData();
            Cache_APC::set($apcKey, $pageData);
        }

        Gionee_Service_Label::filterHotSiteData($pageData['content']['hot_nav_pic']);
        $this->assign('pageData', $pageData['content']);


        $words = Gionee_Service_Baidu::getNavIndexWrods();
        $this->assign('baidu_hotword', $words);
        //百度渠道号
        $baidu_num = Gionee_Service_Baidu::getFromNo();
        $this->assign('baidu_num', $baidu_num);

        //百度分享统计验证代码
        $baidu_stat_no = Gionee_Service_Config::getValue('baidu_stat_no');
        $this->assign('baidu_stat_no', $baidu_stat_no);

        $this->assign('searchTpl', self::_getSerarchTpl());
    }


    private function _getSerarchTpl() {
        $key  = "3G_SEARCH_TPL";
        $data = Common::getCache()->get($key);
        if (empty($data)) {
            $data = Gionee_Service_Config::getValue('3g_hotwords_tpl');
            Common::getCache()->set($key, $data, 600);
        }
        return $data;
    }

    /**
     * 导航二级分类页面
     */
    public function cateAction() {
        //导航二级页面（首页page_id=1，子页面 page_id=2）
        $navHeader = Gionee_Service_NgType::getListByPageId(2);

        $this->assign('navHeader', $navHeader);
    }

    public function modelAction() {
        $ua       = Util_Http::ua();
        $model    = $ua['model'];
        $version  = $ua['app_ver'];
        $ip       = $this->getInput('ip');
        $area     = Gionee_Service_Ng::getUserArea($ip);
        $modelIds = Gionee_Service_Ng::getModelData($model, '', $ip);
        $data     = sprintf("%s <br/> %s <br/> %s <br/> %s <br/> %s", $ua, $model, $version, $area, $modelIds);
        var_dump($data);
        exit;
    }

    /**
     * list
     */
    public function listAction() {
        Common::redirect($this->actions['indexUrl']);
        exit;
    }

    //收入统计信息
    public function  incomeAction() {
        $dataList = Gionee_Service_Income::getLastMothIncome();
        $this->assign('dataList', $dataList);
    }

    /**
     * 得到热门站点的栏目ID
     * @return number
     */
    private function getHotColumnId() {
        if (ENV == 'test') {
            $columnId = 2;
        } else {
            $columnId = 525;
        }
        return $columnId;
    }
}