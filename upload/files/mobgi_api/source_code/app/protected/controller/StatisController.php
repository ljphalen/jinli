<?php
/**
 * 数据统计
 *
 * @author Intril.leng
 */

Doo::loadController("AppDooController");
class StatisController extends AppDooController {

    private $_statisModel;
    private $api_db = '';
    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_statisModel = Doo::loadModel('Statis', TRUE);
        if(Doo::conf()->APP_URL == "http://backend.mobgi.com/" || Doo::conf()->APP_URL == "http://sb.backend.mobgi.com/"){
            $this->api_db="mobgi_api";
        }else{
            $this->api_db="mobgi";
        }
    }

    /**
     * 广告统计报表
     */
    public function adStatis () {
        // 默认没有数据
        $post = $this->post;
        if (empty($post)){ // 默认进入时没有请求
            $this->data['result'] = array();
            $post['ad_ids'] = array();
        }else{
            $where["pid"] = "pid = ".$post['pid'];
            if (!isset($post['sdate']) || !$post['sdate']){
                $post['sdate'] = date("Y-m-d", strtotime("-7 days"));
            }
            if (!isset($post['edate']) || !$post['edate']){
                $post['edate'] = date("Y-m-d");
            }
            $where["date"] = "stat_date >= '".$post['sdate']." 00:00:00' and stat_date <= '".$post['edate']." 23:59:59'";
            if (!isset($post['ad_ids']) || empty($post['ad_ids'])){
                $post['ad_ids'] = array(0);
            }
            $where["aid"] = "aid in (".  implode(",", $post['ad_ids']).")";
            $whereAll = array('where' => implode(" AND ", $where), 'groupby' => 'aid');
            $impWhereAll = array( 'where' => $where["date"]." AND ".$where["pid"]);
            $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
            $this->data['result'] = $result['result'];
            $this->data['total'] = $result['total'];
        }
        $adList = $this->getAds();
        $this->data['adright'] = array();
        $this->data['adleft'] = array();
        $adListshow = array();
        foreach($adList as $id => $name){
            $this->data['adListShow'][$id] = $name['ad_name'];
            if (in_array($id, $post['ad_ids'])){
                $this->data['adright'][$id] = array('pid' => $name['ad_product_id'], 'name' => $name['ad_name']);
            }else{
                $this->data['adleft'][$id] = array('pid' => $name['ad_product_id'], 'name' => $name['ad_name']);
            }
        }
        // 取产品
        $this->data['product'] = form_select($this->getProduct(), array('name' => 'pid', 'value' => $post['pid'],'class' => 'selectPid','width' => '200px'));
        // 取广告
        $this->data['adListShow']['-1'] = '--';
        $this->data['params'] = $post;
        $this->myrender('statis/adstatis', $this->data);
    }

    /**
     * 通过广告ID取具体一条广告的统计数据
     */
    public function getOneByAdId(){
        $get = $_GET;
        if (!isset($get['aid']) || !$get['aid']){
            $this->redirect("javascript:history.go(-1)","传入参数不正确");
        }
        if (isset($get['type']) && $get['type'] == 'hour') {
            $whereAll = array(
                    'where' => "aid = ".$get['aid']." AND stat_date >= '".date('Y-m-d 00:00:00')."' and stat_date <= '".date('Y-m-d 23:59:59')."' AND pid='".$get['pid']."'",
                    'groupby' => 'stat_date',
                    'order' => 'stat_date',
            );
            $impWhereAll = array(
                    'where' => "stat_date >= '".date('Y-m-d 00:00:00')."' and stat_date <= '".date('Y-m-d 23:59:59')."' AND pid='".$get['pid']."'",
            );
            $result = $this->_statisModel->findAll($whereAll, $impWhereAll, "hour");
        } else {
            $whereAll = array(
                    'where' => "aid = ".$get['aid']." AND stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND pid='".$get['pid']."'",
                    'groupby' => 'date',
                    'order' => 'date',
            );
            $impWhereAll = array(
                    'where' => "stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND pid='".$get['pid']."'",
            );
            $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
        }

        $this->data['result'] = $result['result'];
        $this->data['total'] = $result['total'];
        $this->data['chartValue'] = array();
        $this->data['xlas'] = array();
        if (!empty($this->data['result'])){
            if (isset($get['type']) && $get['type'] == 'hour') {
                $this->data['xlas'] = array_fill(0, 24, "0");
                foreach($this->data['result'] as $key => $value){
                    $hour = (int)date('H', strtotime($value['stat_date']));
                    $this->data['chartValue'][$hour] = (int)$value[$get['field']];
                    $this->data['result'][$key]['date'] = $hour;
                }
                $this->data['chartValue'] = $this->data['chartValue'] + $this->data['xlas'];
                ksort($this->data['chartValue']);
                $this->data['xlas'] = array_keys($this->data['xlas']);
            } else {
                foreach($this->data['result'] as $key => $value){
                    $this->data['chartValue'][] = (int)$value[$get['field']];
                    $this->data['xlas'][] = '"'.$value['date'].'"';
                }
            }

        }
        $ids = $this->getAds();
        $this->data['title'] = $ids[$get['aid']]['ad_name'];
        $this->data['aid'] = $get['aid'];
        $this->data['pid'] = $get['pid'];
        $this->data['field'] = $get['field'];
        $this->data['chartValue'] = "[".implode(",", $this->data['chartValue'])."]";
        $this->data['xlas'] = "[".implode(",", $this->data['xlas'])."]";
        $this->data['chart'] = json_encode($this->data['chart']);
        $this->data['sdate'] = $get['sdate'];
        $this->data['edate'] = $get['edate'];
        $this->data['type'] = $get['type'];
        $this->myRenderWithoutTemplate('statis/adOne', $this->data);
    }

    /**
     * 产品统计报表
     */
    public function productStatis (){
        // 默认没有数据
        $post = $this->post;
        if (empty($post)){
            $this->data['result'] = array();
            $post['p_ids'] = array();
        }else{
            if (!isset($post['sdate']) || !$post['sdate']){
                $post['sdate'] = date("Y-m-d", strtotime("-7 days"));
            }
            if (!isset($post['edate']) || !$post['edate']){
                $post['edate'] = date("Y-m-d");
            }
            $where["date"] = "stat_date >= '".$post['sdate']." 00:00:00' and stat_date <= '".$post['edate']." 23:59:59' AND show_type = 0";
            if (!isset($post['p_ids']) || empty($post['p_ids'])){
                $post['p_ids'] = array(0);
            }
            $where["p_ids"] = "pid in (".  implode(",", $post['p_ids']).")";
            $whereAll = array('where' => implode(" AND ", $where), 'groupby' => 'pid');
            $impWhereAll = array( 'where' => $where["date"]);
            $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
            $this->data['result'] = $result['result'];
            $this->data['total'] = $result['total'];
        }
        $this->data['pList'] = $this->getProduct();
        $this->data['pright'] = array();
        $this->data['pleft'] = array();
        foreach($this->data['pList'] as $id => $name){
            if (in_array($id, $post['p_ids'])){
                $this->data['pright'][$id] = $name;
            }else{
                $this->data['pleft'][$id] = $name;
            }
        }
        // 取广告
        $this->data['package_size'] = $this->getPackageSize();
        $this->data['pList']['-1'] = '--';
        $this->data['params'] = $post;
        $this->myrender('statis/productstatis', $this->data);
    }

    /**
     * 单个产品统计
     */
    public function productApp(){
        // 默认没有数据
        $get = $this->get;
        if (!isset($get['pid']) || !$get['pid']){
            $this->redirect('/Statis/appStatis', '无效参数，请重新操作');
        }
        $appList = $this->getApp();
        if (!isset($get['app_keys']) || !$get['app_keys']){
            $get['app_keys'] = array_keys($appList);
        }
        $where["app_keys"] = "gpkg in ('".  implode("','", $get['app_keys'])."')";
        $where["pid"] = "pid = '".$get['pid']."'";
        $where["date"] = "stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59'  AND show_type = 0";
        // 分页
        $whereAll = array('where' => implode(" AND ", $where), 'groupby' => 'gpkg');
        $impWhereAll = array('where' => $where['date']);
        $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
        $this->data['result'] = $result['result'];
        $this->data['total'] = $result['total'];
        $this->data['appright'] = array();
        $this->data['appleft'] = array();
        foreach($appList as $id => $name){
            if (in_array($id, $get['app_keys'])){
                $this->data['appright'][$id] = $name;
            }else{
                $this->data['appleft'][$id] = $name;
            }
        }
        // 取产品
        $productList = $this->getProduct();
        $this->data['product'] = form_select($productList, array('name' => 'pid', 'value' => $get['pid'],'width' => '200px'));
        $this->data['appList'] = $appList;
        $this->data['pname'] = $productList[$get['pid']];
        $this->data['appList']['-1'] = '--';
        $this->data['params'] = $get;
        $this->myrender('statis/productApp', $this->data);
    }

    public function oneProductStat(){
        $get = $_GET;
        if (!isset($get['pid']) || !$get['pid']){
            $this->redirect("javascript:history.go(-1)","传入参数不正确");
        }
        if (isset($get['type']) && $get['type'] == 'hour') {
            $whereAll = array(
                    'where' => "pid = '".$get['pid']."' AND show_type = 0 AND stat_date >= '".date('Y-m-d 00:00:00')."' and stat_date <= '".date('Y-m-d 23:59:59')."' AND gpkg = '".$get['gpkg']."'",
                    'groupby' => 'stat_date',
                    'order' => 'stat_date',
            );
            $impWhereAll = array('where' => "stat_date >= '".date('Y-m-d 00:00:00')."' and stat_date <= '".date('2013-10-01 23:59:59')."' AND show_type = 0");
            $result = $this->_statisModel->findAllByGpkg($whereAll, $impWhereAll, "hour");
        } else {
            $whereAll = array(
                    'where' => "pid = '".$get['pid']."' AND show_type = 0 AND stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND gpkg = '".$get['gpkg']."'",
                    'groupby' => 'date',
                    'order' => 'date',
            );
            $impWhereAll = array('where' => "stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND show_type = 0");
            $result = $this->_statisModel->findAllByGpkg($whereAll, $impWhereAll);
        }
        $this->data['result'] = $result['result'];
        $this->data['total'] = $result['total'];
        $this->data['chartValue'] = array();
        $this->data['xlas'] = array();
        if (!empty($this->data['result'])){
            if (isset($get['type']) && $get['type'] == 'hour') {
                $this->data['xlas'] = array_fill(0, 24, "0");
                foreach($this->data['result'] as $key => $value){
                    $hour = (int)date('H', strtotime($value['stat_date']));
                    $this->data['chartValue'][$hour] = (int)$value[$get['field']];
                    $this->data['result'][$key]['date'] = $hour;
                }
                $this->data['chartValue'] = $this->data['chartValue'] + $this->data['xlas'];
                ksort($this->data['chartValue']);
                $this->data['xlas'] = array_keys($this->data['xlas']);
            } else {
                foreach($this->data['result'] as $key => $value){
                    $this->data['chartValue'][] = (int)$value[$get['field']];
                    $this->data['xlas'][] = '"'.$value['date'].'"';
                }
            }

        }
        $ids = $this->getProduct();
        $this->data['title'] = $ids[$get['pid']];
        $this->data['pid'] = $get['pid'];
        $this->data['gpkg'] = $get['gpkg'];
        $this->data['field'] = $get['field'];
        $this->data['chartValue'] = "[".implode(",", $this->data['chartValue'])."]";
        $this->data['xlas'] = "[".implode(",", $this->data['xlas'])."]";
        $this->data['chart'] = json_encode($this->data['chart']);
        $this->data['sdate'] = $get['sdate'];
        $this->data['edate'] = $get['edate'];
        $this->data['type'] = $get['type'];
        $this->myRenderWithoutTemplate('statis/productOne', $this->data);
    }

    /**
     * 应用统计报表
     */
    public function appStatis () {
        // 默认没有数据
        $post = $this->post;
        if (empty($post)){
            $this->data['result'] = array();
            $post['app_keys'] = array();
        }else{
            if (!isset($post['sdate']) || !$post['sdate']){
                $post['sdate'] = date("Y-m-d", strtotime("-7 days"));
            }
            if (!isset($post['edate']) || !$post['edate']){
                $post['edate'] = date("Y-m-d");
            }
            $where["date"] = "stat_date >= '".$post['sdate']." 00:00:00' and stat_date <= '".$post['edate']." 23:59:59' AND show_type = 0";
            if (!isset($post['app_keys'])){
                $post['app_keys'] = array('-1000');
            }
            $where["app_keys"] = "gpkg in ('".  implode("','", $post['app_keys'])."') AND show_type = 0  AND show_type = 0";
            $whereAll = array('where' => implode(" AND ", $where), 'groupby' => 'gpkg');
            $impWhereAll = array('where' => implode(" AND ", $where));
            $result = $this->_statisModel->findAllByGpkg($whereAll, $impWhereAll);
            $this->data['result'] = $result['result'];
            $this->data['total'] = $result['total'];
            // 日活跃数
            Doo::loadModel('AdDaus');
            $dauModel = new AdDaus();
            $dateStr = "date >= '".$post['sdate']." 00:00:00' and date <= '".$post['edate']." 23:59:59'";
            $dau = $dauModel->findAll(array('select' => 'sum(num) as num', 'where' => $dateStr, 'asArray' => true));
            if (empty($dau)) {
                $this->data['total']['dau'] = 0;
            }else{
                $this->data['total']['dau'] = $dau[0]['num'];
            }
            // 起动次数
            Doo::loadModel('AdStartUps');
            $adStartUpsModel = new AdStartUps();
            $startUps = $adStartUpsModel->findAll(array('select' => 'sum(num) as num', 'where' => $dateStr, 'asArray' => true));
            if (empty($startUps)) {
                $this->data['total']['AdStartUp'] = 0;
            }else{
                $this->data['total']['AdStartUp'] = $startUps[0]['num'];
            }
        }
        $this->data['appList'] = $this->getApp();
        $this->data['appright'] = array();
        $this->data['appleft'] = array();
        foreach($this->data['appList'] as $id => $name){
            if (in_array($id, $post['app_keys'])){
                $this->data['appright'][$id] = $name;
            }else{
                $this->data['appleft'][$id] = $name;
            }
        }
        // 取广告
        $this->data['appList']['-1'] = '--';
        $this->data['params'] = $post;
        $this->myrender('statis/appstatis', $this->data);
    }

    /**
     * 按应用每天数据情况
     */
    public function appStatisByDay(){
        $get = $_GET;
        if (!isset($get['appkey']) || !$get['appkey']){
            $this->redirect("javascript:history.go(-1)","传入参数不正确");
        }
        $channel = "string2 in ('".str_replace(",", "','", $get['channel_id'])."')";
        if (isset($get['type']) && $get['type'] == 'hour') {
            $whereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND stat_date >= '".date('Y-m-d 00:00:00')."' and stat_date <= '".date("Y-m-d 23:59:59")."' AND ".$channel,
                    'groupby' => 'stat_date',
            );
            $impWhereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND stat_date >= '".date('Y-m-d 00:00:00')."' and stat_date <= '".date('Y-m-d 23:59:59')."' AND ".$channel
            );
            $result = $this->_statisModel->findAllByGpkg($whereAll, $impWhereAll, "hour");
        } else {
            $whereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND ".$channel,
                    'groupby' => 'date',
            );
            $impWhereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND ".$channel
            );
            $result = $this->_statisModel->findAllByGpkg($whereAll, $impWhereAll);
        }

        $this->data['result'] = $result['result'];
        $this->data['total'] = $result['total'];
        $this->data['chartValue'] = array();
        $this->data['xlas'] = array();
        if (!empty($this->data['result'])){
            if (isset($get['type']) && $get['type'] == 'hour') {
                $this->data['xlas'] = array_fill(0, 24, "0");
                foreach($this->data['result'] as $key => $value){
                    $hour = (int)date('H', strtotime($value['stat_date']));
                    $this->data['chartValue'][$hour] = (int)$value[$get['field']];
                    $this->data['result'][$key]['date'] = $hour;
                }
                $this->data['chartValue'] = $this->data['chartValue'] + $this->data['xlas'];
                ksort($this->data['chartValue']);
                $this->data['xlas'] = array_keys($this->data['xlas']);
            } else {
                foreach($this->data['result'] as $key => $value){
                    $this->data['chartValue'][] = $value[$get['field']];
                    $this->data['xlas'][] = '"'.$value['date'].'"';
                }
            }
        }
        $ids = $this->getApp();
        $this->data['title'] = $ids[$get['appkey']];
        $this->data['appkey'] = $get['appkey'];
        $this->data['pid'] = $get['pid'];
        $this->data['field'] = $get['field'];
        $this->data['chartValue'] = "[".implode(",", $this->data['chartValue'])."]";
        $this->data['xlas'] = "[".implode(",", $this->data['xlas'])."]";
        $this->data['chart'] = json_encode($this->data['chart']);
        $this->data['sdate'] = $get['sdate'];
        $this->data['edate'] = $get['edate'];
        $this->data['type'] = $get['type'];
        $this->data['channel_id'] = $get['channel_id'];
        $this->myRenderWithoutTemplate('statis/appStatisByDay', $this->data);
    }

    /**
     * 通过应用Key取产品
     */
    public function getProductByAppKey(){
        // 默认没有数据
        $get = $this->get;
        if (!isset($get['appkey']) || !$get['appkey']){
            $this->redirect('/Statis/appStatis', '无效参数，请重新操作');
        }
        $this->data['productList'] = $this->getProduct();
        if (!isset($get['p_ids']) || !$get['p_ids']){
            $get['p_ids'] = array_keys($this->data['productList']);
            $where["p_ids"] = "pid in ('".  implode("','", $get['p_ids'])."')";
            $where["p_ids"] = "pid in ('".  implode("','", $get['p_ids'])."')";
        }else{
            $where["p_ids"] = "pid in ('".  implode("','", $get['p_ids'])."')";
        }
        if (isset($get['channel_id']) || !$get['channel_id']){
            if (is_array($get['channel_id'])) {
                $where["channel_id"] = "string2 in ('".  implode("','", $get['channel_id'])."')";
                $this->data['channelArr'] = $this->_statisModel->_getChannelById($get['channel_id']);
            } else {
                $where["channel_id"] = "string2 in ('".  str_replace(",","','", $get['channel_id'])."')";
                $this->data['channelArr'] = $this->_statisModel->_getChannelById(explode(",", $get['channel_id']));
            }
        }else{
            $this->data['channelArr'] = array();
        }
        $where["appkey"] = "gpkg = '".$get['appkey']."'";
        $where["date"] = "stat_date >= '".$get['sdate']." 00:00:00' and stat_date <='".$get['edate']." 23:59:59'  AND show_type = 0";
        // 分页
        $whereAll = array('where' => implode(" AND ", $where), 'groupby' => 'pid');
        $impWhereAll = array('where' => "gpkg = '".$get['appkey']."' AND stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59'  AND show_type = 0");
        $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
        $this->data['result'] = $result['result'];
        $this->data['total'] = $result['total'];
        $this->data['pright'] = array();
        $this->data['pleft'] = array();
        foreach($this->data['productList'] as $id => $name){
            if (in_array($id, $get['p_ids'])){
                $this->data['pright'][$id] = $name;
            }else{
                $this->data['pleft'][$id] = $name;
            }
        }
        // 取产品
        $appList = $this->getApp();
        $this->data['app'] = form_select($appList, array('name' => 'appkey', 'value' => $get['appkey'],'width' => '200px'));
        $this->data['productList']['-1'] = '--';
        $this->data['appname'] = $appList[$get['appkey']];
        $this->data['params'] = $get;
        if (is_array($this->data['params']['channel_id'])) {
            $this->data['params']['channel_id'] = implode(",", $get['channel_id']);
        }
        $this->myrender('statis/appskeytoproduct', $this->data);
    }

    /**
     * 单个应用图表
     */
    public function oneAppStat(){
        $get = $_GET;
        if (!isset($get['appkey']) || !$get['appkey']){
            $this->redirect("javascript:history.go(-1)","传入参数不正确");
        }
        $channel = "string2 in ('".str_replace(",", "','", $get['channel_id'])."')";
        if (isset($get['type']) && $get['type'] == 'hour') {
            $whereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND show_type = 0 AND stat_date >= '".date("Y-m-d 00:00:00")."' and stat_date <= '".date("Y-m-d 23:59:59")."' AND show_type = 0 AND pid ='".$get['pid']."' AND ".$channel,
                    'groupby' => 'stat_date',
            );
            $impWhereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND show_type = 0 AND stat_date >= '".date("Y-m-d 00:00:00")."' and stat_date <= '".date("Y-m-d 23:59:59")."' AND show_type = 0 AND ".$channel
            );
            $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
        } else {
            $whereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND show_type = 0 AND stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND show_type = 0 AND pid ='".$get['pid']."' AND ".$channel,
                    'groupby' => 'date',
            );
            $impWhereAll = array(
                    'where' => "gpkg = '".$get['appkey']."' AND show_type = 0 AND stat_date >= '".$get['sdate']." 00:00:00' and stat_date <= '".$get['edate']." 23:59:59' AND show_type = 0 AND ".$channel
            );
            $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
        }

        $this->data['result'] = $result['result'];
        $this->data['total'] = $result['total'];
        $this->data['chartValue'] = array();
        $this->data['xlas'] = array();
        if (!empty($this->data['result'])){
            if (isset($get['type']) && $get['type'] == 'hour') {
                $this->data['xlas'] = array_fill(0, 24, "0");
                foreach($this->data['result'] as $key => $value){
                    $hour = (int)date('H', strtotime($value['stat_date']));
                    $this->data['chartValue'][$hour] = (int)$value[$get['field']];
                    $this->data['result'][$key]['date'] = $hour;
                }
                $this->data['chartValue'] = $this->data['chartValue'] + $this->data['xlas'];
                ksort($this->data['chartValue']);
                $this->data['xlas'] = array_keys($this->data['xlas']);
            } else {
                foreach($this->data['result'] as $key => $value){
                    $this->data['chartValue'][] = $value[$get['field']];
                    $this->data['xlas'][] = '"'.$value['date'].'"';
                }
            }
        }
        $ids = $this->getApp();
        $this->data['title'] = $ids[$get['appkey']];
        $this->data['appkey'] = $get['appkey'];
        $this->data['pid'] = $get['pid'];
        $this->data['field'] = $get['field'];
        $this->data['chartValue'] = "[".implode(",", $this->data['chartValue'])."]";
        $this->data['xlas'] = "[".implode(",", $this->data['xlas'])."]";
        $this->data['chart'] = json_encode($this->data['chart']);
        $this->data['sdate'] = $get['sdate'];
        $this->data['edate'] = $get['edate'];
        $this->data['type'] = $get['edate'];
        $this->data['channel_id'] = $get['channel_id'];
        $this->myRenderWithoutTemplate('statis/appOne', $this->data);
    }

    /**
     * 产品列表广告
     */
    public function productListAd(){
        $appList = $this->getApp();
        // 默认没有数据
        $post = $this->post;
        if (empty($post)){ // 默认进入时没有请求
            $this->data['result'] = array();
            $post['app_keys'] = array();
        }else{
            $where["aid"] = "aid = ".$post['aid'];
            if (!isset($post['sdate']) || !$post['sdate']){
                $post['sdate'] = date("Y-m-d", strtotime("-7 days"));
            }
            if (!isset($post['edate']) || !$post['edate']){
                $post['edate'] = date("Y-m-d");
            }
            $where["date"] = "stat_date >= '".$post['sdate']." 00:00:00' and stat_date <= '".$post['edate']." 23:59:59'";
            if (!isset($post['app_keys']) || empty($post['app_keys'])){
                $post['app_keys'] = array(0);
            }
            $where["gpkg"] = "gpkg in ('".  implode("','", $post['app_keys'])."')";
            $whereAll = array('where' => implode(" AND ", $where), 'groupby' => 'gpkg');
            $impWhereAll = array( 'where' => $where["date"].' AND '.$where["aid"]);
            $result = $this->_statisModel->findAllByGpkg($whereAll, $impWhereAll);
            $this->data['result'] = $result['result'];
            $this->data['total'] = $result['total'];
        }
        $adlist = $this->getListAds();
        $this->data['appright'] = array();
        $this->data['appleft'] = array();
        foreach($appList as $id => $name){
            if (in_array($id, $post['app_keys'])){
                $this->data['appright'][$id] = $name;
            }else{
                $this->data['appleft'][$id] = $name;
            }
        }
        $this->data['params'] = $post;
        $this->data['appkeys'] = $appList;
        $this->data['adsList'] = form_select($adlist, array('name' => 'aid', 'value' => $post['aid'],'width' => '200px'));
        $this->myrender('statis/productlistad', $this->data);
    }

    public function appListAd(){
        $appList = $this->getApp();
        // 默认没有数据
        $post = $this->post;
        if (empty($post)){ // 默认进入时没有请求
            $this->data['result'] = array();
            $post['aid'] = array();
        }else{
            $where["gpkg"] = "gpkg = '".$post['gpkg']."'";
            if (!isset($post['sdate']) || !$post['sdate']){
                $post['sdate'] = date("Y-m-d", strtotime("-7 days"));
            }
            if (!isset($post['edate']) || !$post['edate']){
                $post['edate'] = date("Y-m-d");
            }
            $where["date"] = "stat_date >= '".$post['sdate']." 00:00:00' and stat_date <= '".$post['edate']." 23:59:59'";
            if (!isset($post['aid']) || empty($post['aid'])){
                $post['aid'] = array(0);
            }
            $where["aid"] = "aid in ('".  implode("','", $post['aid'])."')";
            $whereAll = array('where' => implode(" AND ", $where), 'groupby' => 'aid');
            $impWhereAll = array( 'where' => $where["date"]." AND gpkg = '".$post['gpkg']."'");
            $result = $this->_statisModel->findAll($whereAll, $impWhereAll);
            $this->data['result'] = $result['result'];
            $this->data['total'] = $result['total'];
        }
        $this->data['adlist'] = $this->getListAds();
        $this->data['appright'] = array();
        $this->data['appleft'] = array();
        foreach($this->data['adlist'] as $id => $name){
            if (in_array($id, $post['aid'])){
                $this->data['appright'][$id] = $name;
            }else{
                $this->data['appleft'][$id] = $name;
            }
        }
        $this->data['params'] = $post;
        $this->data['appkeys'] = $appList;
        $this->data['gpkgList'] = form_select($appList, array('name' => 'gpkg', 'value' => $post['gpkg'],'width' => '200px'));
        $this->myrender('statis/applistad', $this->data);
    }

    /**
     * 有效转换率
     */
    public function conversionRate(){
        $get = $this->get;
        if (empty($get)){ // 默认进入时没有请求
            $this->data['result'] = array();
            $post['ad_ids'] = array();
        }else{
            $url = 'http://tmp_research.uu.cc/api/get_mobgi_user';
            if (isset ( $get ['sdate'] ) && $get ['sdate']) {
                $url .= "?start=" . $get ['sdate'];
            } else {
                $url .= "?start=" . date ( "Y-m-d", strtotime ( "-7 days" ) );
            }
            if (isset ( $get ['edate'] ) && $get ['edate']) {
                $url .= "&end=" . $get ['edate'];
            } else {
                $url .= "&end=" . date ( "Y-m-d" );
            }
            Doo::loadClass('net/class.curl');
            $curl = new CURL();
            $newPlayerNum = $curl->get($url, 0); // 新用户数
            $playNumArr = json_decode($newPlayerNum, true);
            Doo::loadModel('AdDaus');
            $dauModel = new AdDaus();
            $whereArr = array('where' => "date >='".$get['sdate']."' AND date <='".$get['edate']."'");
            $dau = $dauModel -> findAll($whereArr);
            $dauArr = array();
            $this->data['olChartValue'] = array();
            $this->data['casualChartValue'] = array();
            $this->data['totalChartValue'] = array();
            $this->data['xlas'] = array();
            if (!empty($dau) && !empty($playNumArr)) {
                foreach($dau as $val) {
                    $dauArr[$val['date']] = $val['num'];
                }
                foreach($playNumArr['ol_user'] as $key => $value){
                    if (array_key_exists($value['date'], $dauArr)) {
                        $result[$value['date']] = round(($value['user'] / $dauArr[$value['date']])*1000, 2);
                    }else{
                        $result[$value['date']] = 0;
                    }
                    $this->data['olChartValue'][$value['date']] = $result[$value['date']];
                    $this->data['xlas'][] = '"'.$value['date'].'"';
                }
                foreach($playNumArr['casual_user'] as $key => $value){
                    if (array_key_exists($value['date'], $dauArr)) {
                        $result[$value['date']] = round(($value['user'] / $dauArr[$value['date']])*1000, 2);
                    }else{
                        $result[$value['date']] = 0;
                    }
                    $this->data['casualChartValue'][$value['date']] = $result[$value['date']];
                    if (empty($this->data['olChartValue'][$value['date']])) {
                        $this->data['totalChartValue'][$value['date']] = 0;
                    }else{
                        $this->data['totalChartValue'][$value['date']] = $result[$value['date']] + $this->data['olChartValue'][$value['date']];
                    }

                }
            }
            Doo::db()->reconnect('prod');
            $this->data['olChartValue'] = "[".implode(",", $this->data['olChartValue'])."]";
            $this->data['casualChartValue'] = "[".implode(",", $this->data['casualChartValue'])."]";
            $this->data['totalChartValue'] = "[".implode(",", $this->data['totalChartValue'])."]";
            $this->data['xlas'] = "[".implode(",", $this->data['xlas'])."]";
            $this->data['params'] = array('sdate' => $get['sdate'], 'edate' => $get['edate']);
        }
        $this->myrender('statis/conversionRate', $this->data);
    }

    /**
     *  返回产品list
     * @return type
     */
    public function getProduct(){
        Doo::db()->reconnect('prod');
        $rs = Doo::db()->query("SELECT * FROM ".$this->api_db.".ad_product_info WHERE product_name != ''");
        $result = array();
        while ($row = $rs->fetch()){
            $result[$row['id']] = $row['product_name'];
        }
        return $result;
    }

    /**
     * 返回应用list
     *
     * @return type
     */
    public function getApp() {
        Doo::db()->reconnect('prod');
        $rs = Doo::db()->query("SELECT * FROM ".$this->api_db.".ad_app");
        $result = array();
        while ($row = $rs->fetch()){
            $result[$row['appkey']] = $row['app_name'];
        }
        return $result;
    }

    public function getPackageSize(){
        $rs = Doo::db()->query("SELECT * FROM ".$this->api_db.".ad_product_info WHERE product_name != ''");
        $productArr = array();
        while ($row = $rs->fetch()){
            if (!empty($row['click_type_object'])){
                $tempArr = json_decode($row['click_type_object'], TRUE);
                if (isset($tempArr['inner_install_manage']) && isset($tempArr['inner_install_manage']['package_size'])){
                    $productArr[$row['id']] = $tempArr['inner_install_manage']['package_size'];
                }else{
                    $productArr[$row['id']] = 0;
                }
            }else{
                $productArr[$row['id']] = 0;
            }
        }
        return $productArr;
    }

    /**
     * 返回广告list
     * @return type
     */
    public function getAds(){
        Doo::db()->reconnect('prod');
        $rs = Doo::db()->query("SELECT * FROM ".$this->api_db.".ad_info WHERE ad_name != ''");
        $result = array();
        while ($row = $rs->fetch()){
            $result[$row['id']] = array('ad_name' => $row['ad_name'], 'ad_product_id' => $row['ad_product_id']);
        }
        return $result;
    }

    public function getListAds(){
        Doo::db()->reconnect('prod');
        $rs = Doo::db()->query("SELECT * FROM ".$this->api_db.".ad_embedded_info WHERE type = 1");
        $result = array();
        while ($row = $rs->fetch()){
            $result[$row['ad_info_id']] = $row['ad_name'];
        }
        return $result;
    }

    public function export (){
        $data = $_POST;
        header("Content-type:application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition:attachment;filename=".$data['file_name'].".xls");
        echo preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$data['data']);
        exit();
    }

    public function getAdInfo(){
        $aid = $this->post['aid'];
        Doo::db()->reconnect('prod');
        $rs = Doo::db()->query("SELECT id, pos, type FROM ".$this->api_db.".ad_info WHERE id = ".$aid);
        $adInfo = $rs->fetch();
        if ($adInfo['type'] == 1){
            $table = 'ad_embedded_info';
            $filed = "ad_info_id, type as ctype, ad_pic_url";
        }else{
            $table = 'ad_not_embedded_info';
            $filed = "ad_info_id, type as ctype, ad_pic_url, screen_type";
        }
        $adRs = Doo::db()->query("SELECT ".$filed." FROM ".$this->api_db.".".$table." WHERE ad_info_id = ".$aid);
        $adInfoEm = $adRs->fetch();
        $result = array_merge($adInfoEm, $adInfo);
        $adType = Doo::conf()->AD_TYPE_CATE;
        if (!isset($result['screen_type'])){
            $result['screen_type'] = '';
        }else if ($result['screen_type'] == 1){
            $result['screen_type'] = '竖屏';
        }else if ($result['screen_type'] == 2){
            $result['screen_type'] = '横竖屏都支持';
        }else if ($result['screen_type'] == 0){
            $result['screen_type'] = '横屏';
        }
        $result['stype'] = $adType[$result['type']]['name'].'--'.$adType[$result['type']]['subtype'][$result['ctype']];
        echo json_encode($result);
    }

    /**
     * 监控管理配制
     */
    public function monitorConf (){
        $MonitorConfModel = Doo::loadModel('MonitorConfigs', true);
        $result = $MonitorConfModel->findAll();
        Doo::db()->reconnect('prod');
        $this->data['result'] = $result;
        $this->myrender('statis/monitor', $this->data);
    }

    public function deleteMonitorConfig(){
        $MonitorConfModel = Doo::loadModel('MonitorConfigs', true);
        $get = $this->get;
        if (!empty($get['id'])) {
            $MonitorConfModel->del($get['id']);
            $this->userLogs(array('msg' => json_encode($post), 'title' => '监控配置', 'action' => 'delete'));
        }
        $this->redirect('../Statis/monitorConf');
    }

    public function monitorSave(){
        $post = $this->post;
        $MonitorConfModel = Doo::loadModel('MonitorConfigs', true);
        if (empty($post['time'])) {
            $post['time'] = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24';
        }
        $post['time'] = str_replace(array("，", " "), array(",", ""), $post['time']);
        $post['time'] = trim($post['time']);
        $post['time'] = trim($post['time'],",");
        $post['max'] = (int)$post['max'];
        $post['min'] = (int)$post['min'];
        //操作邮箱，去空去重。
        $post['email'] = trim($post['email']);
        $emails = explode("\r\n", $post['email']);
        $this->remove_empty_item($emails);
        $emails = array_unique( $emails);
        $post['email'] = implode("\r\n", $emails);
        if (!empty($post)) {
            if($MonitorConfModel->upd($post['id'], $post)){
                $MonitorConfModel->update_email($post['email']);
                $this->userLogs(array('msg' => json_encode($post), 'title' => '监控配置'), $post['id']);
                echo json_encode(array('errCode' => 1, 'errMsg' => '数据操作成功'));
                return false;
            }else{
                $MonitorConfModel->update_email($post['email']);
                echo json_encode(array('errCode' => 0, 'errMsg' => '数据未更新'));
                return false;
            }
        }
    }

    public function monitorIsOpen(){
        $MonitorConfModel = Doo::loadModel('MonitorConfigs', true);
        $post = $_POST;
        if (empty($post['id']) || empty($post['isopen'])) {
            echo json_encode(array('errCode' => 0, 'errMsg' => '参数不完整'));
            return false;
        }
        if ($MonitorConfModel->updStatus($post['id'], $post['isopen'])){
            $this->userLogs(array('msg' => json_encode($post), 'title' => '监控配置'), $post['id']);
            echo json_encode(array('errCode' => 1, 'errMsg' => '操作成功'));
            return false;
        }else{
            echo json_encode(array('errCode' => 0, 'errMsg' => '操作失败'));
            return false;
        }
    }
    
    /**
     * 去空值
     * @param type $arr
     * @return type
     */
    public function remove_empty_item(&$arr){
        foreach($arr as $key=>$item){
            if(empty($item)){
                unset($arr[$key]); 
            }
        }
        return $arr;
    }
    
}

?>
