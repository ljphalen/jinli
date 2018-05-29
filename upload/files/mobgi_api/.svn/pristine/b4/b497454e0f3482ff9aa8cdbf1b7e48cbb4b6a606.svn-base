<?php
/**
 * 数据分析
 * @author Intril.leng
 */
Doo::loadController ( "AppDooController" );
class AppReportController extends AppDooController {
    /**
     * 应用
     */
    public function app(){
        // 配置数据
        $indexConf = array(
                'index-all' => '总况', 'index-active' => '活跃用户数', 'index-startup' => '启动会话数',
                'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数', 'index-register' => '注册数',
        );
        $get = $this->get;
        // 处理参数
        $result['url']['type'] = empty($get['type']) ? 'index-all' : $get['type'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        if ($result['url']['type'] == 'index-all') { //总况
            $result['index'] = array(
                    array('name' => '活跃用户数', 'field' => 'active'), array('name' => '启动会话数', 'field' => 'startup'),
                    array('name' => '有效用户数', 'field' => 'effective'), array('name' => '广告到达用户数', 'field' => 'ad_arrived'),
                    array('name' => '注册数', 'field' => 'register'),
            );
        } else {
            $result['index'] = array(array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])));
        }
        // 下面图表
        $result['conversionIndex'] = array(
                array('name' => '有效转化率', 'field' => 'conversionRate' ),
                array('name' => '填充率', 'field' => 'fillRate' ),
        );
        if ($result['url']['startDate'] == $result['url']['endDate']) { // 取小时表
            $AnalysisModel = Doo::loadModel('AnalysisAppHours', TRUE);
        } else {
            $AnalysisModel = Doo::loadModel('AnalysisApps', TRUE);
        }
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $result['chart'] = $AnalysisModel->findAll($whereArr);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/app', $this->data);
    }
    /**
     * 应用-活跃到注册
     */
    public function appActiveToRegister() {
        // 配置数据
        $indexConf = array(
                'index-active' => '活跃用户数', 'index-startup' => '启动会话数',
                'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数', 'index-register' => '注册数',
        );
        $get = $this->get;
        $result['url']['type'] = empty($get['type']) ? 'index-active' : $get['type'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        if (!empty($get['needCompare']) || $get['needCompare'] == 1) {
            $result['url']['needCompare'] = 1;
            $result['url']['startCompareDate'] = $get['startCompareDate'];
            $result['url']['endCompareDate'] = $get['endCompareDate'];
            // 上面图表
            $result['index'] = array(
                    array('name' => $result['url']['startDate'].'至'.$result['url']['endDate'], 'field' => str_replace('index-', '', $result['url']['type'])),
                    array('name' => $result['url']['startCompareDate'].'至'.$result['url']['endCompareDate'], 'field' => str_replace('index-', '', $result['url']['type'])."_com"),
            );
            // 下面图表
            $result['conversionIndex'] = array(
                    array('name' => $result['url']['startDate'].'至'.$result['url']['endDate'].'有效转化率', 'field' => 'conversionRate' ),
                    array('name' => $result['url']['startCompareDate'].'至'.$result['url']['endCompareDate'].'有效转化率-对比', 'field' => 'conversionRate_com' ),
                    array('name' => $result['url']['startDate'].'至'.$result['url']['endDate'].'填充率', 'field' => 'fillRate' ),
                    array('name' => $result['url']['startCompareDate'].'至'.$result['url']['endCompareDate'].'填充率-对比', 'field' => 'fillRate_com' ),
            );
        }else{
            $result['url']['needCompare'] = 0;
            // 上面图表
            $result['index'] = array(
                    array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
            );
            // 下面图表
            $result['conversionIndex'] = array(
                    array('name' => '有效转化率', 'field' => 'conversionRate' ),
                    array('name' => '填充率', 'field' => 'fillRate' ),
            );
        }
        $AppModels = Doo::loadModel('Apps', true);
        $result['appkey'] = $AppModels -> getAppInfo(array('app_version', 'channel', 'product'));
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['url']['cid'] = empty($get['cid']) ? array() : $get['cid'];
        $result['url']['version'] = empty($get['version']) ? array() : $get['version'];
        $result['url']['pid'] = empty($get['pid']) ? array() : $get['pid'];
        $AnalysisAppActiveRegistersModel = Doo::loadModel('AnalysisAppActiveRegisters', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'channel' => $result['url']['cid'],
                'app_version' => $result['url']['version'],
                'pid' => $result['url']['pid'],
                'compare' => array('startDate' => $result['url']['startCompareDate'], 'endDate' => $result['url']['endCompareDate']),
                'needCompare' => $result['url']['needCompare'],
        );
        $result['chart'] = $AnalysisAppActiveRegistersModel->findAll($whereArr, $result['url']['type']);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/appActiveToRegister', $this->data);
    }

    /**
     * 应用-活跃
     */
    public function appActive(){
        // 配置数据
        $indexConf = array(
                'index-active' => '活跃用户', 'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数',
        );
        $get = $this->get;
        $result['url']['type'] = empty($get['type']) ? 'AnalysisDaus' : $get['type'];
        $result['url']['ptype'] = empty($get['ptype']) ? 'index-active' : $get['ptype'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        if (!empty($get['needCompare']) || $get['needCompare'] == 1) {
            $result['url']['needCompare'] = 1;
            $result['url']['startCompareDate'] = $get['startCompareDate'];
            $result['url']['endCompareDate'] = $get['endCompareDate'];
            $result['index'] = array(
                    array('name' => $result['url']['startDate'].'至'.$result['url']['endDate'], 'field' => str_replace('index-', '', $result['url']['ptype'])),
                    array('name' => $result['url']['startCompareDate'].'至'.$result['url']['endCompareDate'], 'field' => str_replace('index-', '', $result['url']['ptype'])."_com"),
            );
        }else{
            $result['url']['needCompare'] = 0;
            $result['index'] = array(
                    array('name' => $indexConf[$result['url']['ptype']], 'field' => str_replace('index-', '', $result['url']['ptype'])),
            );
        }
        $AppModels = Doo::loadModel('Apps', true);
        $result['appkey'] = $AppModels -> getAppInfo(array('app_version', 'channel'));
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['url']['cid'] = empty($get['cid']) ? array() : $get['cid'];
        $result['url']['version'] = empty($get['version']) ? array() : $get['version'];
        $AnalysisModel = Doo::loadModel($result['url']['type'], TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'channel' => $result['url']['cid'],
                'app_version' => $result['url']['version'],
                'compare' => array('startDate' => $result['url']['startCompareDate'], 'endDate' => $result['url']['endCompareDate']),
                'needCompare' => $result['url']['needCompare'],
        );
        $result['chart'] = $AnalysisModel->findAll($whereArr);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/appActive', $this->data);
    }

    /**
     * 应用-版本
     */
    public function appVersion(){
        // 配置数据
        $indexConf = array(
                'index-active' => '活跃用户数', 'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数',
        );
        $get = $this->get;
        // 处理参数
        $result['url']['type'] = empty($get['type']) ? 'index-active' : $get['type'];
        $result['field'] = str_replace('index-', '', $result['url']['type']);
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        $AppModels = Doo::loadModel('Apps', true);
        $result['appkey'] = $AppModels -> getAppInfo(array('channel'));
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['url']['cid'] = empty($get['cid']) ? array() : $get['cid'];
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => $result['field']),
                array('name' => '有效用户数', 'field' => 'effective'),
        );
        $AnalysisAppVersionsModel = Doo::loadModel('AnalysisAppVersions', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'channel' => $result['url']['cid'],
        );
        $result['chart'] = $AnalysisAppVersionsModel->findAll($whereArr);
        Doo::db()->reconnect('prod');
        $result['thead'] = array('日期', '总数');
        $field = str_replace('index-', '', $result['url']['type']);
        foreach ($result['chart'] as $value) {
            $result['thead'][$value['app_version']] = $value['app_version'];
            $result['tbody'][$value['date']][$value['app_version']] = $value[$field];
        }
        $this->data['result'] = json_encode($result);
        $this->myrender('report/appVersion', $this->data);
    }

    /**
     * 应用-设备，品牌，分辩率
     */
    public function appDevice(){
        // 配置数据
        $indexConf = array(
                'index-active' => '活跃用户数', 'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数',
        );
        $get = $this->get;
        // 处理参数
        $result['url']['type'] = empty($get['type']) ? 'index-active' : $get['type'];
        $result['url']['ptype'] = empty($get['ptype']) ? 'index-device_brand' : $get['ptype'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        $AppModels = Doo::loadModel('Apps', true);
        $result['appkey'] = $AppModels -> getAppInfo(array('channel','app_version'));
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['url']['version'] = empty($get['version']) ? array() : $get['version'];
        $result['url']['cid'] = empty($get['cid']) ? array() : $get['cid'];
        $result['field'] = str_replace('index-', '', $result['url']['ptype']);
        // 上面图表
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
        );
        $AnalysisAppDevicesModel = Doo::loadModel('AnalysisAppDevices', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'channel' => $result['url']['cid'],
                'app_version' => $result['url']['version'],
        );
        $whereArr['groupby'] = $result['field'];
        $whereArr['desc'] = str_replace('index-', '', $result['url']['type']);
        $result['chart'] = $AnalysisAppDevicesModel->findAll($whereArr);
        $whereArr1['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $whereArr1['groupby'] = $result['field'];
        $whereArr1['select'] = $result['field'];
        $result['selectType'] = $AnalysisAppDevicesModel->getType($whereArr1);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/appDevice', $this->data);
    }

    /**
     * 应用-设备，品牌，分辩率 表格对比
     */
    public function contrastDeviceApp(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['appkey']) || empty($post['ptype']) || empty($post['type']) ||
            empty($post['field']) || empty($post['contrastApp'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $ptype = str_replace('index-', '', $post['ptype']);
        $type = str_replace('index-', '', $post['type']);
        $AnalysisAppDevicesModel = Doo::loadModel('AnalysisAppDevices', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['appkey'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $contrastwhereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['contrastApp'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $result = $AnalysisAppDevicesModel->contrastDevice($whereArr, $contrastwhereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result));
    }

    /**
     * 应用-网络，运营商
     */
    public function appNet(){
        // 配置数据
        $indexConf = array(
                'index-active' => '活跃用户数', 'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数',
        );
        $get = $this->get;
        // 处理参数
        $result['url']['type'] = empty($get['type']) ? 'index-active' : $get['type'];
        $result['url']['ptype'] = empty($get['ptype']) ? 'index-net_type' : $get['ptype'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        $AppModels = Doo::loadModel('Apps', true);
        $result['appkey'] = $AppModels -> getAppInfo(array('channel','app_version'));
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['url']['version'] = empty($get['version']) ? array() : $get['version'];
        $result['url']['cid'] = empty($get['cid']) ? array() : $get['cid'];
        $result['field'] = str_replace('index-', '', $result['url']['ptype']);
        // 上面图表
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
        );
        $AnalysisAppNetsModel = Doo::loadModel('AnalysisAppNets', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'channel' => $result['url']['cid'],
                'app_version' => $result['url']['version'],
        );
        $whereArr['groupby'] = $result['field'];
        $whereArr['desc'] = str_replace('index-', '', $result['url']['type']);
        $result['chart'] = $AnalysisAppNetsModel->findAll($whereArr);
        $whereArr1['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $whereArr1['groupby'] = $result['field'];
        $whereArr1['select'] = $result['field'];
        $result['selectType'] = $AnalysisAppNetsModel->getType($whereArr1);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/appNet', $this->data);
    }

    public function contrastNet(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['appkey']) || empty($post['ptype']) || empty($post['type']) ||
        empty($post['field']) || empty($post['contrastApp'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $ptype = str_replace('index-', '', $post['ptype']);
        $type = str_replace('index-', '', $post['type']);
        $AnalysisAppNetsModel = Doo::loadModel('AnalysisAppNets', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['appkey'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $contrastwhereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['contrastApp'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $result = $AnalysisAppNetsModel->contrastNet($whereArr, $contrastwhereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result));
    }

    /**
     * 应用-渠道
     */
    public function appChannel(){
        // 配置数据
        $indexConf = array(
                'index-active' => '活跃用户数', 'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数',
        );
        $get = $this->get;
        // 处理参数
        $result['url']['type'] = empty($get['type']) ? 'index-active' : $get['type'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        $AppModels = Doo::loadModel('Apps', true);
        $result['appkey'] = $AppModels -> getAppInfo(array('app_version'));
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['url']['version'] = empty($get['version']) ? array() : $get['version'];
        $result['field'] = 'channel';
        // 上面图表
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
        );
        $AnalysisAppChannelsModel = Doo::loadModel('AnalysisAppChannels', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'app_version' => $result['url']['version'],
        );
        $whereArr['groupby'] = $result['field'];
        $whereArr['desc'] = str_replace('index-', '', $result['url']['type']);
        $result['chart'] = $AnalysisAppChannelsModel->findAll($whereArr);
        $whereArr1['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $whereArr1['groupby'] = $result['field'];
        $whereArr1['select'] = $result['field'];

        $result['selectType'] = $AnalysisAppChannelsModel->getType($whereArr1);
        $result['selectType'] = Doo::conf()->report_channel_conf;
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/appChannel', $this->data);
    }

    public function contrastChannel(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['appkey']) || empty($post['type']) ||
        empty($post['field']) || empty($post['contrastApp'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $type = str_replace('index-', '', $post['type']);
        $AnalysisAppChannelsModel = Doo::loadModel('AnalysisAppChannels', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['appkey'],
                'changeField' => "channel = '" . $post['field']."'",
        );
        $contrastwhereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['contrastApp'],
                'changeField' => "channel = '" . $post['field']."'",
        );
        $result = $AnalysisAppChannelsModel->contrastChannel($whereArr, $contrastwhereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result));
    }

    /**
     * 应用-城市
     */
    public function appCity(){
        // 配置数据
        $indexConf = array(
                'index-active' => '活跃用户数', 'index-effective' => '有效用户数', 'index-ad_arrived' => '广告到达用户数',
        );
        $get = $this->get;
        // 处理参数
        $result['url']['type'] = empty($get['type']) ? 'index-active' : $get['type'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        $AppModels = Doo::loadModel('Apps', true);
        $result['appkey'] = $AppModels -> getAppInfo(array('app_version','channel'));
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['url']['version'] = empty($get['version']) ? array() : $get['version'];
        $result['url']['cid'] = empty($get['cid']) ? array() : $get['cid'];
        $result['field'] = 'province';
        // 上面图表
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
        );
        $AnalysisAppCitiesModel = Doo::loadModel('AnalysisAppCities', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'channel' => $result['url']['cid'],
                'app_version' => $result['url']['version'],
        );
        $whereArr['groupby'] = $result['field'];
        $whereArr['desc'] = str_replace('index-', '', $result['url']['type']);
        $result['chart'] = $AnalysisAppCitiesModel->findAll($whereArr);
        $whereArr1['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $result['selectType'] = $AnalysisAppCitiesModel->getType($whereArr1);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/appCity', $this->data);
    }

    public function contrastCityApp(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['appkey']) || empty($post['type']) ||
        empty($post['field']) || empty($post['contrastApp'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $type = str_replace('index-', '', $post['type']);
        $AnalysisAppCitiesModel = Doo::loadModel('AnalysisAppCities', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['appkey'],
                'changeField' => "city = '" . $post['field']."'",
        );
        $contrastwhereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'appkey' => $post['contrastApp'],
                'changeField' => "city = '" . $post['field']."'",
        );
        $result = $AnalysisAppCitiesModel->contrastCity($whereArr, $contrastwhereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result));

    }
}