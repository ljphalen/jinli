<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_ModelContent {

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

    public static function getDataList($page, $pageSize, $sql, $order) {
        $page     = max($page, 1);
        $count    = self::_getDao()->getCount($sql);
        $dataList = self::_getDao()->getDataList(($page - 1) * $pageSize, $pageSize, $sql, $order);
        return array($count, $dataList);
    }

    public static function edit($params, $id) {
        $data = self::_checkData($params);
        return self::_getDao()->update($data, $id);
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

    public static function getModelData() {

        $modelData = Gionee_Service_ModelContent::getsBy(array());
        $models    = array();
        foreach ($modelData as $k => $v) {
            $temp = '';
            if ($v['model']) {
                $temp .= "机型:{$v['model']} ";
            }
            if ($v['version']) {
                $temp .= "版本:{$v['version']} ";
            }
            if ($v['operator']) {
                $temp .= "运营商:{$v['operator']} ";
            }
            if ($v['province']) {
                $temp .= "省份: {$v['province']} ";
            }
            if ($v['city']) {
                $temp .= "城市:{$v['city']}";
            }
            $models[] = array('id' => $v['id'], 'key' => $temp);
        }
        return $models;
    }

    /**
     * 得到所有机型列表ID
     *
     * @param string $key
     * @param string $model
     * @param string $version
     * @param string $ip
     *
     * @return boolean
     */
    public static function curUserModelIds($model, $version, $ip, $type = 'nav') {
        $key   = "3G:MODEL:IDS:{$type}:";
        $mData = Util_Cookie::get($key);
        if (empty($mData)) {
            $areaMsg = self::getUserArea($ip); //用户所在地
            $mData   = Gionee_Service_ModelContent::getModelMsgIds($areaMsg, $model, $version); //有效机型ID列表
            if ($mData) {
                Util_Cookie::set($key, $mData, true, '/', 24 * 3600);
            } else {
                Util_Cookie::set($key, '-1', true, '/', 600);
            }
        }
        return $mData;
    }

    /**
     * 获得用户所在地
     *
     * @param unknown $key
     *
     * @return Ambigous <string, multitype:, mixed, boolean>
     */
    public static function getUserArea($ip) {
        if (empty($ip)) {
            $ip = Util_Http::getClientIp();
        }
        $area = Vendor_IP::find($ip);
        return $area;
    }


    /**
     * 分机型处理
     */

    public static function getModelMsgIds($area = '', $model = '', $version = '', $operator = '') {
        $models = self::allModelList(true);
        $ids    = array();
        foreach ($models as $k => $v) {
            $id = 0;
            switch ($v['prior']) {
                case 1: {//机型为最高优先级
                    if (in_array($model, explode(',', $v['model']))) { //机型相同时
                        if (!empty($v['city']) && $area[2] == mb_substr($v['city'], 0, -1, 'UTF8')) { //地域处理
                            $id = $v['id'];
                        } elseif (empty($v['city']) && !empty($v['province']) && $area[1] == mb_substr($v['province'], 0, -1, 'UTF8')) {
                            $id = $v['id'];
                        } elseif (empty($v['province'])) {
                            $id = $v['id'];
                        }
                        if (!empty($v['version'])) {//版本
                            $id = 0;
                            if (in_array($version, explode(',', $v['version']))) {
                                $id = $v['id'];
                            }
                        }

                        if (!empty($v['operator'])) { //运营商的处理
                            $id = 0;
                            if (in_array($version, explode(',', $v['operator']))) {
                                $id = $v['id'];
                            }
                        }
                    }
                    break;
                }
                case 2: {
                    if (in_array($version, explode(',', $v['version']))) { //版本优先
                        if (!empty($v['city']) && $area[2] == mb_substr($v['city'], 0, -1, 'UTF8')) { //地域处理
                            $id = $v['id'];
                        } elseif (empty($v['city']) && !empty($v['province']) && $area[1] == mb_substr($v['province'], 0, -1, 'UTF8')) {
                            $id = $v['id'];
                        } elseif (empty($v['province'])) {
                            $id = $v['id'];
                        }

                        if (!empty($v['operator'])) { //运营商的处理
                            $id = 0;
                            if (in_array($version, explode(',', $v['operator']))) {
                                $id = $v['id'];
                            }
                        }

                        if (!empty($v['model'])) {
                            $id = 0;
                            if (in_array($model, explode(',', $v['model']))) {
                                $id = $v['id'];
                            }
                        }
                    }
                    break;
                }
                case 3: {
                    if (in_array($operator, explode(',', $v['operator']))) { // 运营商优先
                        if (!empty($v['city']) && $area[2] == mb_substr($v['city'], 0, -1, 'UTF8')) { //地域处理
                            $id = $v['id'];
                        } elseif (empty($v['city']) && !empty($v['province']) && $area[1] == mb_substr($v['province'], 0, -1, 'UTF8')) {
                            $id = $v['id'];
                        } elseif (empty($v['province'])) {
                            $id = $v['id'];
                        }

                        if (!empty($v['model'])) { //机型
                            $id = 0;
                            if (in_array($model, explode(',', $v['model']))) {
                                $id = $v['id'];
                            }
                        }

                        if (!empty($v['version'])) {//版本
                            $id = 0;
                            if (in_array($version, explode(',', $v['version']))) {
                                $id = $v['id'];
                            }
                        }
                    }
                    break;
                }
                case 4: {
                    if (!empty($v['city']) && $area[2] == mb_substr($v['city'], 0, -1, 'UTF8')) { //地域处理
                        $id = $v['id'];
                    } elseif (empty($v['city']) && !empty($v['province']) && $area[1] == mb_substr($v['province'], 0, -1, 'UTF8')) {
                        $id = $v['id'];
                    } elseif (empty($v['province'])) {
                        $id = $v['id'];
                    }
                    if (intval($id)) {
                        if (!empty($v['version'])) {//版本
                            $id = 0;
                            if (in_array($version, explode(',', $v['version']))) {
                                $id = $v['id'];
                            }
                        }

                        if (!empty($v['model'])) { //机型
                            $id = 0;
                            if (in_array($model, explode(',', $v['model']))) {
                                $id = $v['id'];
                            }
                        }

                        if (!empty($v['operator'])) { //运营商的处理
                            $id = 0;
                            if (in_array($version, explode(',', $v['operator']))) {
                                $id = $v['id'];
                            }
                        }
                    }
                    break;
                }

                default:
                    break;
            }
            if (intval($id)) {
                $ids[] = $id;
            }
        }
        return $ids;
    }

    public static function allModelList($sync = false) {
        $key    = "MODEL:DATA:LIST";
        $rs     = Common::getCache();
        $models = $rs->get($key);
        if ($sync || empty($models)) {
            $models = Gionee_Service_ModelContent::getsBy(array(), array('id' => 'DESC'));
            $rs->set($key, $models, 3600);
        }
        return $models;
    }

    /**
     * @return Gionee_Dao_ModelContent
     */
    private static function _checkData($params) {
        $temp = array();
        if (isset($params['version'])) $temp['version'] = $params['version'];
        if (isset($params['model'])) $temp['model'] = $params['model'];
        if (isset($params['operator'])) $temp['operator'] = $params['operator'];
        if(isset($params['area']))		$temp['area'] = $params['area'];
        if (isset($params['province'])) $temp['province'] = $params['province'];
        if (isset($params['city'])) $temp['city'] = $params['city'];
        if (isset($params['prior'])) $temp['prior'] = $params['prior'];
        return $temp;
    }

    private static function _getDao() {
        return Common::getDao("Gionee_Dao_ModelContent");
    }
}