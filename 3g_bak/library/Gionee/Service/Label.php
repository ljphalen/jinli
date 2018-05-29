<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Label {

    public static function  add($params) {
        $data = self::_checkData($params);
        return self::_getDao()->insert($data);
    }

    public static function getList($page, $pageSize, $where, $order) {
        $page = max($page, 1);
        return array(
            self::_getDao()->count($where),
            self::_getDao()->getList(($page - 1) * $pageSize, $pageSize, $where, $order)
        );
    }

    public static function get($id) {
        return self::_getDao()->get($id);
    }

    public static function getAll($order) {
        return self::_getDao()->getAll($order);
    }

    public static function edit($params, $id) {
        $data = self::_checkData($params);
        return self::_getDao()->update($data, $id);
    }

    public static function editBy($params = array(), $where = array()) {
        if (!is_array($params) || !is_array($where)) return false;
        return self::_getDao()->updateBy($params, $where);
    }

    public static function getBy($params) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    public static function getsBy($params, $order = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getsBy($params, $order);
    }

    public static function delete($id) {
        return self::_getDao()->delete($id);
    }

    public static function getSortedLabelData($selectedList =array()){
    	$jsonData  = $sorted = array();
    	$items = Gionee_Service_Label::getAll(array('id'=>'asc'));
    	foreach ($items as $item){
    		$sorted[$item['id']] =array(
    				'id'	=>$item['id'],
    				'text'=>$item['name'],
    				'parent_id'=>$item['parent_id'],
    				'checked'	=>in_array($item['id'], $selectedList)?true:false,
    		);
    	}
    	foreach ($sorted as $v){
    		if(isset($sorted[$v['parent_id']])){
    			$sorted[$v['parent_id']]['state'] = 'closed';
    			$sorted[$v['parent_id']]['children'][] = &$sorted[$v['id']];
    		}else{
    			$jsonData[] = &$sorted[$v['id']];
    		}
    	}
    	return $jsonData;
    }
    
    public function getLabelList($where=array()){
    	$ret = array();
    	$data  = self::_getDao()->getsBy($where,array('id'=>'DESC'));
    	foreach ($data as $k=>$v){
    		if(intval($v['has_subset'])){
    			$ret[$k]['text'] ='' ;
    		}
    	}
    }
    
    public static function getCatList($params, $group) {
        $ids  = self::_getDao()->getCatIdList($params, $group);
        $data = array();
        foreach ($ids as $k => $v) {
            $info             = Gionee_Service_Label::getBy(array('parent_id' => $v['id']));
            $data[$k]['id']   = $info['id'];
            $data[$k]['name'] = $info['name'];
        }
        return $data;
    }

    public static function checkImei($filename, $imei) {
        $dir          = Common::staticDir();
        $fullFilename = $dir . $filename;
        $flag         = false;
        if (file_exists($fullFilename) && is_readable($fullFilename)) {
            $handle = fopen($fullFilename, 'r');
            while ($contents = fread($handle, 4096)) {
                $pos = strpos($contents, $imei);
                if ($pos !== false) {
                    $flag = true;
                    break;
                }
            }
            fclose($handle);
        }

        if ($flag == false) {
            $tmp = Gionee_Service_Config::getValue('filter_label_imeis');
            if (!empty($tmp)) {
                $tmpArr   = json_decode($tmp, true);
                $cfgImeis = array();
                foreach ($tmpArr as $v) {
                    $cfgImeis[] = trim($v);
                }

                if (in_array($imei, $cfgImeis)) {
                    $flag = true;
                }
            }
        }

        if ($flag) {

            $path = '/data/3g_log/imei_data/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $str = date('Y/m/d H:i:s') . ' - ' . $imei . "\n";
            error_log($str, 3, $path . $filename);
        }

        return $flag;
    }


    public static function getImeiData($id) {
        $key  = "3G:IMEI:DATA:{$id}";
        $data = Common::getCache()->get($key);
        if (empty($data)) {
            $data = self::getLabelImeiDataDao()->get($id);
            Common::getCache()->set($key, $data, 600);
        }
        return $data;
    }

    private static function _checkData($params) {
        $data = array();
        if (isset($params['parent_id'])) $data['parent_id'] = $params['parent_id'];
        if (isset($params['name'])) $data['name'] = $params['name'];
        if (isset($params['status'])) $data['status'] = $params['status'];
        if (isset($params['level'])) $data['level'] = $params['level'];
        if (isset($params['add_time'])) $data['add_time'] = $params['add_time'];
        if (isset($params['has_subset'])) $data['has_subset'] = $params['has_subset'];
        return $data;
    }


    static public function checkCacheImei($modelId, $imei) {
        static $data = array();
        if (!isset($data[$imei][$modelId])) {
            $info                  = Gionee_Service_Label::getImeiData($modelId);
            $flag                  = Gionee_Service_Label::checkImei($info['file_path'], $imei);
            $data[$imei][$modelId] = $flag;
        }
        return isset($data[$imei][$modelId]) ? $data[$imei][$modelId] : false;
    }

    /**
     * 设置 imei精准投放ID数据到cookie
     *
     * @param $imei
     * @param $data
     */
    static public function setCookieImeiData($imei, $data) {
        $key = crc32($imei);
        Util_Cookie::set($key, json_encode($data), true, Common::getTime() + Common::T_TEN_MIN);
    }

    /**
     * 从cookie中获取精准投放ID数据
     *
     * @param $imei
     *
     * @return array
     */
    static public function getCookieImeiData($imei) {
        $key = crc32($imei);
        $ret = array();
        $tmp = Util_Cookie::get($key, true);
        if (!empty($tmp)) {
            $ret = json_decode($tmp, true);
        }
        return $ret;
    }

    /**
     *
     * @return Gionee_Dao_Label
     */
    private static function _getDao() {
        return Common::getDao("Gionee_Dao_Label");
    }

    /**
     *
     * @return Gionee_Dao_LabelImeiData
     */
    static public function getLabelImeiDataDao() {
        return Common::getDao("Gionee_Dao_LabelImeiData");
    }


    /**
     * 精准投放广告列表数据
     *
     * @param $type
     * @param $modelArr
     * @param $data
     */
    static public function filterADData($type, $modelArr, &$data) {
        $ua      = Util_Http::ua();
        $model   = $ua['model'];
        $version = $ua['app_ver'];
        $ip      = $ua['ip'];
        $imei    = $ua['uuid_real'];

        $debugImei = filter_input(INPUT_GET, 'debugimei');
        if (empty($imei) && !empty($debugImei)) {
            $imei = $debugImei;
        }

        if (!empty($modelArr[1])) {
            $mids = Gionee_Service_ModelContent::curUserModelIds($model, $version, $ip, $type);
            foreach ($modelArr[1] as $m => $n) {
                if (!empty($mids) && in_array($n['model_id'], $mids)) {
                    $data[] = $n;
                }
            }
        }

        if (!empty($modelArr[2]) && !empty($imei)) {
            $imeiData    = Gionee_Service_Label::getCookieImeiData($imei);
            $oldimeiData = $imeiData;
            foreach ($modelArr[2] as $m => $n) {
                if (!empty($imeiData[$type][$n['id']])) {
                    $data[] = $n;
                } else {
                    $flag = Gionee_Service_Label::checkCacheImei($n['model_id'], $imei);
                    if ($flag) {
                        $data[] = $n;
                    }
                    //写Cookie
                    if (!isset($imeiData[$type][$n['id']])) {
                        $imeiData[$type][$n['id']] = $flag ? 1 : 0;
                    }
                }
            }
            if ($oldimeiData != $imeiData) {
                Gionee_Service_Label::setCookieImeiData($imei, $imeiData);
            }

        }
    }

    /**
     * 精准投放 热门站点 数据过滤
     *
     * @param        $hotData
     * @param string $type
     */
    public static function  filterHotSiteData(&$hotData, $type = 'nav') {
        $ua      = Util_Http::ua();
        $model   = $ua['model'];
        $version = $ua['app_ver'];
        $ip      = $ua['ip'];
        $imei    = $ua['uuid_real'];

        $debugImei = filter_input(INPUT_GET, 'debugimei');
        if (empty($imei) && !empty($debugImei)) {
            $imei = $debugImei;
        }

        $modelArr = array();
        foreach ($hotData as $k => $v) {
            $v['model_type'] = !empty($v['model_type']) ? $v['model_type'] : 0;
            $modelArr[$v['model_type']][$v['sort']] = $v;
        }
        //分机型

        if (!empty($modelArr[1])) {
            $mIds = Gionee_Service_ModelContent::curUserModelIds($model, $version, $ip, $type);
            if (!empty($mIds)) {
                foreach ($modelArr[1] as $m => $n) {
                    if (isset($modelArr[0][$m]) && in_array($n['model_id'], $mIds)) {
                        $modelArr[0][$m] = $n;
                    }
                }
            }
        }

        //精准运营发
        if (!empty($modelArr[2]) && !empty($imei)) {
            $imeiData    = Gionee_Service_Label::getCookieImeiData($imei);
            $oldimeiData = $imeiData;
            foreach ($modelArr[2] as $s => $t) {
                if (!empty($imeiData[$type][$t['id']]) && isset($modelArr[0][$s])) {
                    $modelArr[0][$s] = $t;
                } else {
                    $flag = Gionee_Service_Label::checkCacheImei($t['model_id'], $imei);
                    if ($flag) {
                        $modelArr[0][$s] = $t;
                    }
                    if (!isset($imeiData[$type][$t['id']])) {
                        $imeiData[$type][$t['id']] = $flag ? 1 : 0;
                    }
                }
            }
            if ($oldimeiData != $imeiData) {
                Gionee_Service_Label::setCookieImeiData($imei, $imeiData);
            }
        }
        $hotData = array_values($modelArr[0]);
    }
}