<?php
/**
 * 数据分析
 * @author Intril.leng
 */
Doo::loadController ( "AppDooController" );
class ProductReportController extends AppDooController {

    public function indexConfig(){
        return array(
                'index-impressions' => '展示用户数', 'index-clicks' => '点击用户数', 'index-start_downloads' => '开始下载用户数',
                'index-finish_downloads' => '下载完成用户数', 'index-installs' => '安装用户数', 'index-startup' => '启动用户数',
                'index-register' => '注册用户数', 'index-pay' => '付费用户数', 'index-income' => '收入',
        );
    }

    /**
     * 产品
     */
    public function product(){
        // 配置数据
        $indexConf = $this->indexConfig();
        $get = $this->get;
        $result['url']['date'] = empty($get['date']) ? date('Y-m-d') : $get['date'];
        // 取产品
        $AdProductsModel = Doo::loadModel('AdProducts', true);
        $productInfo = $AdProductsModel->findAll(array('asArray'=>true, 'select'=>'id,product_name,appkey'));
        $result['product'] = listArray($productInfo, 'id', 'product_name');
        $defaultPid = array_keys($result['product']);
        $result['url']['pid'] = empty($get['pid']) ? $defaultPid[0] : $get['pid'];
        $result['url']['type'] = empty($get['type']) ? 'index-impressions' : $get['type'];
        $result['url']['ctype'] = empty($get['ctype']) ? 'index-clicks_rate' : $get['ctype'];
        $field = str_replace('index-', '', $result['url']['type']);
        $cfield = str_replace('index-', '', $result['url']['ctype']);
        $result['index'] = array(
                array('name' => '今天', 'field' => 'today'),
                array('name' => '昨天', 'field' => 'yesterday'),
                array('name' => '前天', 'field' => 'lastday'),
                array('name' => '7天前', 'field' => 'weekago'),
        );
        $AdProductsProductModel = Doo::loadModel('AnalysisProducts', true);
        $whereArr['where'] = array(
                'date' => $result['url']['date'],
                'pid' => $result['url']['pid'],
        );
        $whereArr['field'] = $field;
        $result['chart'] = $AdProductsProductModel->findAll($whereArr);
        $whereArr['field'] = $cfield;
        $result['conversionIndex'] = array(
                array('name' => '今天', 'field' => 'today'),
                array('name' => '昨天', 'field' => 'yesterday'),
                array('name' => '前天', 'field' => 'lastday'),
                array('name' => '7天前', 'field' => 'weekago'),
        );
        $result['conversionRateChart'] = $AdProductsProductModel->findAllRate($whereArr);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/product', $this->data);
    }

    /**
     * 产品-网络、运营商
     */
    public function productNet(){
        $indexConf = $this->indexConfig();
        $get = $this->get;
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        $result['url']['type'] = empty($get['type']) ? 'index-impressions' : $get['type'];
        $result['url']['ptype'] = empty($get['ptype']) ? 'index-net_type' : $get['ptype'];
        // 取产品
        $AdProductsModel = Doo::loadModel('AdProducts', true);
        $productInfo = $AdProductsModel->findAll(array('asArray'=>true, 'select'=>'id,product_name,appkey'));
        $result['product'] = listArray($productInfo, 'id', 'product_name');
        $defaultPid = array_keys($result['product']);
        $result['url']['pid'] = empty($get['pid']) ? $defaultPid[0] : $get['pid'];
        // 取应用
        $AppModels = Doo::loadModel('Apps', true);
        $appInfo = $AppModels -> findAll();
        $result['appkey'] = listArray($appInfo, 'appkey', 'app_name');
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['field'] = str_replace('index-', '', $result['url']['ptype']);
        // 上面图表
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
        );
        $whereArr['where'] = array(
                'date' => array('sdate' => $result['url']['startDate'], 'edate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'pid' => $result['url']['pid'],
        );
        $whereArr['groupby'] = $result['field'];
        $whereArr['desc'] = str_replace('index-', '', $result['url']['type']);
        $AnalysisProductNetsModel = Doo::loadModel('AnalysisProductNets', true);
        $result['chart'] = $AnalysisProductNetsModel->findAll($whereArr);
        $whereArr1['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $whereArr1['groupby'] = $result['field'];
        $whereArr1['select'] = $result['field'];
        $result['selectType'] = $AnalysisProductNetsModel->getType($whereArr1);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/productNet', $this->data);
    }

    /**
     * 对比网络
     * @return boolean
     */
    public function contrastNet(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['pid']) || empty($post['ptype']) || empty($post['type']) ||
        empty($post['field']) || empty($post['contrastPid'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $ptype = str_replace('index-', '', $post['ptype']);
        $type = str_replace('index-', '', $post['type']);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'pid' => $post['pid'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $contrastwhereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'pid' => $post['contrastPid'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $AnalysisProductNetsModel = Doo::loadModel('AnalysisProductNets', true);
        $result = $AnalysisProductNetsModel->contrastNet($whereArr, $contrastwhereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result));
    }

    /**
     * 产品-设备，品牌，分辩率
     */
    public function productDevice(){
        $indexConf = $this->indexConfig();
        $get = $this->get;
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        $result['url']['type'] = empty($get['type']) ? 'index-impressions' : $get['type'];
        $result['url']['ptype'] = empty($get['ptype']) ? 'index-device_brand' : $get['ptype'];
        // 取产品
        $AdProductsModel = Doo::loadModel('AdProducts', true);
        $productInfo = $AdProductsModel->findAll(array('asArray'=>true, 'select'=>'id,product_name,appkey'));
        $result['product'] = listArray($productInfo, 'id', 'product_name');
        $defaultPid = array_keys($result['product']);
        $result['url']['pid'] = empty($get['pid']) ? $defaultPid[0] : $get['pid'];
        // 取应用
        $AppModels = Doo::loadModel('Apps', true);
        $appInfo = $AppModels -> findAll();
        $result['appkey'] = listArray($appInfo, 'appkey', 'app_name');
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['field'] = str_replace('index-', '', $result['url']['ptype']);
        // 上面图表
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
        );
        $whereArr['where'] = array(
                'date' => array('sdate' => $result['url']['startDate'], 'edate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'pid' => $result['url']['pid'],
        );
        $whereArr['groupby'] = $result['field'];
        $whereArr['desc'] = str_replace('index-', '', $result['url']['type']);
        $AnalysisProductDevicesModel = Doo::loadModel('AnalysisProductDevices', true);
        $result['chart'] = $AnalysisProductDevicesModel->findAll($whereArr);
        $whereArr1['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $whereArr1['groupby'] = $result['field'];
        $whereArr1['select'] = $result['field'];
        $result['selectType'] = $AnalysisProductDevicesModel->getType($whereArr1);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/productDevice', $this->data);
    }

    public function contrastDevice(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['pid']) || empty($post['ptype']) || empty($post['type']) ||
        empty($post['field']) || empty($post['contrastPid'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $ptype = str_replace('index-', '', $post['ptype']);
        $type = str_replace('index-', '', $post['type']);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'pid' => $post['pid'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $contrastwhereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'pid' => $post['contrastPid'],
                'changeField' => $ptype . " = '" . $post['field']."'",
        );
        $AnalysisProductDevicesModel = Doo::loadModel('AnalysisProductDevices', true);
        $result = $AnalysisProductDevicesModel->contrastNet($whereArr, $contrastwhereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result));
    }

    /**
     * 产品-城市
     */
    public function productCity(){
        $indexConf = $this->indexConfig();
        $get = $this->get;
        // 处理参数
        $result['url']['type'] = empty($get['type']) ? 'index-impressions' : $get['type'];
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        // 取产品
        $AdProductsModel = Doo::loadModel('AdProducts', true);
        $productInfo = $AdProductsModel->findAll(array('asArray'=>true, 'select'=>'id,product_name,appkey'));
        $result['product'] = listArray($productInfo, 'id', 'product_name');
        $defaultPid = array_keys($result['product']);
        $result['url']['pid'] = empty($get['pid']) ? $defaultPid[0] : $get['pid'];
        // 取应用
        $AppModels = Doo::loadModel('Apps', true);
        $appInfo = $AppModels -> findAll();
        $result['appkey'] = listArray($appInfo, 'appkey', 'app_name');
        $defaultAppkey = array_keys($result['appkey']);
        $result['url']['appkey'] = empty($get['appkey']) ? $defaultAppkey[0] : $get['appkey'];
        $result['field'] = 'province';
        // 上面图表
        $result['index'] = array(
                array('name' => $indexConf[$result['url']['type']], 'field' => str_replace('index-', '', $result['url']['type'])),
        );
        $AnalysisProductCitiesModel = Doo::loadModel('AnalysisProductCities', TRUE);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'appkey' => $result['url']['appkey'],
                'pid' => $result['url']['pid'],
        );
        $whereArr['groupby'] = $result['field'];
        $whereArr['desc'] = str_replace('index-', '', $result['url']['type']);
        $result['chart'] = $AnalysisProductCitiesModel->findAll($whereArr);
        $whereArr1['where'] = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
        );
        $result['selectType'] = $AnalysisProductCitiesModel->getType($whereArr1);
        Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/productCity', $this->data);
    }

    public function contrastCity(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['pid']) || empty($post['type']) ||
        empty($post['province']) || empty($post['appkey']) || empty($post['_t'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $type = str_replace('index-', '', $post['type']);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'pid' => $post['pid'],
                'appkey' => $post['appkey'],
                'province' => $post['province'],
        );
        if (!empty($post['city'])){
            $whereArr['where']['city'] = $post['city'];
        }
        $AnalysisProductCitiesModel = Doo::loadModel('AnalysisProductCities', TRUE);
        $result = $AnalysisProductCitiesModel->listTable($whereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result, '_t' => $post['_t']));
    }

    /**
     * 产品-转化率
     */
    public function productConversionRate(){
        $get = $this->get;
        // 处理参数
        if (!empty($get['date'])) {
            $date = explode('至', $get['date']);
            $get['startDate'] = trim($date[0]);
            $get['endDate'] = trim($date[1]);
        }
        $result['url']['startDate'] = empty($get['startDate']) ? date('Y-m-d', strtotime("-7 days")) : $get['startDate'];
        $result['url']['endDate'] = empty($get['endDate']) ? date('Y-m-d') : $get['endDate'];
        // 取产品
        $AdProductsModel = Doo::loadModel('AdProducts', true);
        $productInfo = $AdProductsModel->findAll(array('asArray'=>true, 'select'=>'id,product_name,appkey'));
        $GameModel = Doo::loadModel('Game', true);
        $result['select']['gameArr'] = $GameModel->findAll(array('where' => array('appKey' => $productInfo)), true);
        // 取渠道
        $result['select']['channelArr'] = Doo::conf()->report_channel_conf;
        $gameIds = array_keys($result['select']['gameArr']);
        $channelIds = array_keys($result['select']['channelArr']);
        $result['url']['game_id'] = empty($get['game_id']) ? $gameIds[0] : $get['game_id'];
        $result['url']['cid'] = empty($get['cid']) ? $channelIds[0] : $get['cid'];
        $whereArr = array(
                'date' => array('startDate' => $result['url']['startDate'], 'endDate' => $result['url']['endDate']),
                'cid' => str_replace(",","','", $result['url']['cid']),
                'game_id' => $result['url']['game_id'],
        );
        // 上面图表
        $result['index'] = array(
                array('name' => '1日留存', 'field' => 'first_day_rate'),
                array('name' => '3日留存', 'field' => 'third_day_rate'),
                array('name' => '7日留存', 'field' => 'seventh_day_rate'),
                array('name' => '14日留存', 'field' => 'fifteenth_day_rate'),
                array('name' => '30日留存', 'field' => 'thirtieth_day_rate'),
                array('name' => '60日留存', 'field' => 'sixtieth_day_rate'),
        );
        $UserRetentionRateReportsModel = Doo::loadModel('UserRetentionRateReports', true);
        $result['result'] = $UserRetentionRateReportsModel->findAll($whereArr);
         Doo::db()->reconnect('prod');
        $this->data['result'] = json_encode($result);
        $this->myrender('report/productConversionRate', $this->data);
    }

    /**
     * 转化率 表格对比
     */
    public function contrastConversionRate(){
        $post = $this->post;
        if (empty($post['stime']) || empty($post['etime']) || empty($post['game_id']) || empty($post['cid']) || empty($post['conField'])){
            echo json_encode(array('errorCode' => -1, 'errorMsg' => '参数不完整'));
            return false;
        }
        $UserRetentionRateReportsModel = Doo::loadModel('UserRetentionRateReports', true);
        // 构造查询条件
        $whereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'game_id' => $post['game_id'],
                'channel_id' => $post['cid'],
        );
        $contrastwhereArr['where'] = array(
                'date' => array('startDate' => $post['stime'], 'endDate' => $post['etime']),
                'game_id' => $post['game_id'],
                'channel_id' => $post['conField'],
        );
        $result = $UserRetentionRateReportsModel->contrastConversionRate($whereArr, $contrastwhereArr);
        echo json_encode(array('errorCode' => 1, 'data' => $result));
    }
}