<?php
/**
 * Created by PhpStorm.
 * User: rainkid
 * Date: 14-6-12
 * Time: 下午2:58
 */
class ApkController extends Api_BaseController {
    public function configAction() {
        $cfg = array(
            'version'=>1,//版本号,1
            'isupload'=>1,//上传开关,1
            'wifi_min'=>0,//WiFi每次最小上传量,0B
            'wifi_max'=>1000000,//WiFi每次最大上传量,100,000 B
            'gprs_min'=>500,//gprs每天上传最小量,500B
            'gprs_max'=>3000,//gprs每天最大上传量,3,000 B
            'sd_size'=>1,//本地最大存储量(M),1M
            'local_st_size'=>3000,//本地最大存储数,5000
            'sdk_listen_num'=>0//SDK监听触发数,0
        );
        $this->clientOutput(0, '', $cfg);
    }

    private function _decode($d) {
        $key = Common::getConfig('siteConfig', 'secretKey');
        $d = Util_RC4::Decrypt($key, base64_decode($d));
        return gzdecode($d);
    }

    public function uploadAction() {
        $data = $this->getInput(array('data_format_version', 'uid', 'encript_imei', 'app_version', 'event_count', 'event_content'));
        $data['event_content'] = $this->_decode($data['event_content']);
        //Common::log($data, 'upload.log');
        $this->clientOutput(0, '', array());
    }

    /**
     * 统计
     */
    public function statisticAction(){
        $data = $this->getPost(array('eventIds', 'sign'));
        $data['eventIds'] = html_entity_decode($data['eventIds']);
        $uid = Common::getAndroidtUid();
        $encrypt_str = $data['eventIds'] . $uid . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';
        if(md5($encrypt_str) !== $data['sign']) $this->output(-1, '非法请求.');

        $data['eventIds'] = json_decode($data['eventIds'], true);
//        Common::log($data, 'stat.log');
//        $uid = 'rt1f1bb6fdb30e5f133bf1eeee017281';
        $version = Common::getAndroidClientVersion();
//        $version = '2.5.6';
        $time = Common::getTime();

        //按1天来统计, 并创建文档表
        $collection_name = $this->_createCollectionName($time);
        $collections = Common::getMongo()->listCollections();
        if(!in_array($collection_name, $collections)) {
            $create_col = Common::getMongo()->createCollection($collection_name);
            if(!$create_col) $this->output(-1, '');
            Common::getMongo()->createIndex($collection_name, array('uid_id' => 1));
        }

        array_walk($data['eventIds'], function(&$v){$v = intval($v);});
        $stat_data = array(
            'uid_id'    => Common::hash_crc32($uid),
            'version'   => $version,
            'time'      => $time,
        );

        $stat_uid = Common::getMongo()->findOne($collection_name, array('uid_id' => $stat_data['uid_id']), array('_id' => 0, 'uid_id' => 0, 'time' => 0));
//        Common::log($stat_uid, 'stat.log');
        if(!empty($stat_uid) && $stat_uid['version'] == $version){
            foreach($data['eventIds'] as $key => $val){
                $stat_uid[$key] += $val;
            }
            $stat_data = array_merge($stat_data, $stat_uid);
            Common::getMongo()->update($collection_name, array('uid_id' => $stat_data['uid_id'], 'version' => $version), $stat_data);
        }else{
            $stat_data = array_merge($stat_data, $data['eventIds']);
            Common::getMongo()->insert($collection_name, $stat_data);
        }

        $this->standOutput(0, '');
    }

    /**
     * 创建文档表名
     * @param $time
     * @return string
     */
    private function _createCollectionName($time){
        return sprintf('gou_apk_statistic_%s', date('Y_m_d', $time));
    }
}