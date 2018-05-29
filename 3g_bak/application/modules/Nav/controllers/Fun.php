<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 二级栏目-段子
 */
class FunController extends Front_BaseController {

    public function indexAction() {
        $appId = $this->getInput('appId');
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'index:nav_fun');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'index:nav_fun');
        $columns = Nav_Service_NewsData::getColumnList('fun');
        $appId   = !empty($appId) ? $appId : $columns[0]['appId'];
        $this->assign('columns', $columns);
        $this->assign('appId', $appId);
    }

    public function listAction() {
        $args = array(
            'appId'  => FILTER_VALIDATE_INT,
            'nextId' => FILTER_VALIDATE_INT,
            'limit'  => FILTER_VALIDATE_INT,
            'ver'    => FILTER_VALIDATE_INT,
        );

        $params   = filter_input_array(INPUT_GET, $args);
        $columnId = $params['appId'];
        $nextId   = intval($params['nextId']);
        $ver      = $params['ver'];
        if (empty($columnId)) {
            exit;
        }

        $this->_statList($columnId, $nextId);
        $page = max($nextId, 1);

        $key     = "NAV_FUN:{$columnId}_{$page}_{$ver}";
        $ret = Cache_APC::get($key);
        if (empty($ret)) {//主要目的 发push的时候  redis承受不住带宽压力  所以添加apc缓存60s
            $ret  = Nav_Service_NewsData::getList($columnId, $page, $ver);
            Cache_APC::set($key, $ret);
        }

        foreach ($ret['list'] as $k => $val) {

            $pos = strpos($val['title'], '，');
            if ($pos > 0) {
                $ret['list'][$k]['title'] = mb_substr($val['title'], 0, $pos);;
            }

            $rcKey                      = 'NAV_FUN_OP:' . intval($val['detailId']);
            $opInfo                     = Common::getCache()->hGetAll($rcKey);
            $ret['list'][$k]['op_1']    = max(intval($opInfo['op_1']), 0);
            $ret['list'][$k]['op_2']    = max(intval($opInfo['op_2']), 0);
            $ret['list'][$k]['c_num']   = max(intval($opInfo['c_num']), 0);
            $ret['list'][$k]['content'] = Common::substr($val['content'], 200);
            //$ret['list'][$k]['op_1_flag'] = 0;
            //$ret['list'][$k]['op_2_flag'] = 0;
        }

        $tmp         = $this->_adList($columnId, $page);
        $ret['list'] = array_merge($tmp, $ret['list']);

        $this->output(0, '', $ret);
    }

    public function columnAction() {

        $ret = array(
            'list' => Nav_Service_NewsData::getColumnList('fun'),
        );
        $this->output(0, '', $ret);
    }

    public function detailAction() {
        $args   = array(
            'id'  => FILTER_VALIDATE_INT,
            'act' => FILTER_SANITIZE_STRING,
        );
        $params = filter_input_array(INPUT_GET, $args);
        $id     = $params['id'];
        $act    = $params['act'];
        $info   = Nav_Service_NewsData::getRecordInfo($id);

        if (empty($info['id'])) {
            exit;
        }

        $columnId = $info['column_id'];
        $this->_statDetail($columnId, $act);

        $content = '';
        foreach ($info['content'] as $v) {
            if ($v['type'] == 1) {
                $content .= '<p class="ptxt">' . $v['value'] . '</p>';
            } else if ($v['type'] == 2) {
                $content .= "<p class=\"pimg\"><img src=\"{$v['value']}\"></p>";
            }
        }
        $info['content'] = $content;

        $rcKey         = 'NAV_FUN_OP:' . intval($info['id']);
        $opInfo        = Common::getCache()->hGetAll($rcKey);
        $info['op_1']  = intval($opInfo['op_1']);
        $info['op_2']  = intval($opInfo['op_2']);
        $info['c_num'] = intval($opInfo['c_num']);

        $userInfo  = Gionee_Service_User::getCurUserInfo();
        $op_1_flag = $op_2_flag = 0;
        if (!empty($userInfo['id'])) {
            list($opInfo, $crcId) = $this->_getOpInfo($info['id'], $userInfo['id']);

            if (!empty($opInfo['op_type'])) {
                $op_1_flag = $opInfo['op_type'] == 1 ? 1 : 0;
                $op_2_flag = $opInfo['op_type'] == 2 ? 1 : 0;
            }
        }
        $info['op_1_flag'] = $op_1_flag;
        $info['op_2_flag'] = $op_2_flag;

        $prevUrl = '/nav/fun/index?appId=' . $columnId;
        $this->assign('prevUrl', $prevUrl);

        $pos = strpos($info['title'], '，');
        if ($pos > 0) {
            $info['title'] = mb_substr($info['title'], 0, $pos);;
        }

        $this->assign('info', $info);

        $banner = Nav_Service_NewsAd::getListByPos('nav_fun_content_' . $columnId);
        $this->assign('banner', $banner);


        //$this->output(0, '', $ret);
    }

    public function opAction() {
        $args   = array(
            'id' => FILTER_VALIDATE_INT,
            't'  => FILTER_VALIDATE_INT,
            'v'  => FILTER_VALIDATE_INT,
        );
        $params = filter_input_array(INPUT_GET, $args);
        $id     = intval($params['id']);
        $t      = intval($params['t']);
        $v      = $params['v'];

        $login = Common_Service_User::checkLogin('/nav/fun/detail?id=' . $id);
        if (!$login['key']) {
            $this->output(0, '', array('redirect' => $login['keyMain']));
        }

        $userInfo = $login['keyMain'];
        list($opInfo, $crcId) = $this->_getOpInfo($id, $userInfo['id']);

        $rcKey = 'NAV_FUN_OP:' . $id;
        if (!empty($opInfo['op_type'])) {
            if ($v == 0 && $t == $opInfo['op_type']) {
                $where = array('record_id' => $id, 'group' => 'fun', 'op_type' => $t);
                $ret   = Nav_Service_NewsDB::getOpDao()->delete($opInfo['id']);
                if ($ret) {
                    $total = Nav_Service_NewsDB::getOpDao()->count($where);
                    $k     = 'op_' . $t;
                    Nav_Service_NewsDB::getRecordDao()->update(array($k => $total), $id);
                    Common::getCache()->hSet($rcKey, $k, $total);
                    Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $v . '_' . $k . ':nav_fun');
                    Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $v . '_' . $k . ':nav_fun');
                    $this->output(0, '成功');
                }
            }
        } else {
            if ($v == 1) {
                $addData = array(
                    'record_id'  => intval($id),
                    'uid'        => intval($userInfo['id']),
                    'group'      => 'fun',
                    'crc_id'     => $crcId,
                    'op_type'    => $t,
                    'created_at' => Common::getTime(),
                );

                $ret = Nav_Service_NewsDB::getOpDao()->insert($addData);
                if ($ret) {
                    $where = array('record_id' => $id, 'group' => 'fun', 'op_type' => $t);
                    $total = Nav_Service_NewsDB::getOpDao()->count($where);
                    $k     = 'op_' . $t;
                    Nav_Service_NewsDB::getRecordDao()->update(array($k => $total), $id);
                    Common::getCache()->hSet($rcKey, $k, $total);
                    Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $v . '_' . $k . ':nav_fun');
                    Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $v . '_' . $k . ':nav_fun');
                    $this->output(0, '成功');
                }
            }
        }

        $this->output(-1, '错误请求');

    }


    private function _getOpInfo($id, $uid) {
        $where = array(
            'record_id' => intval($id),
            'uid'       => intval($uid),
            'group'     => 'fun'
        );

        $crcId  = crc32(implode('_', array_values($where)));
        $opInfo = Nav_Service_NewsDB::getOpDao()->getBy(array('crc_id' => $crcId));

        return array($opInfo, $crcId);
    }

    private function _statList($columnId, $nextId) {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $columnId . ':nav_fun');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $columnId . ':nav_fun');
    }

    private function _adList($columnId, $page) {
        $listads = Nav_Service_NewsAd::getListByPos('nav_fun_list_txt_' . $columnId);
        $tmp     = array();
        if (!empty($listads) && $page == 1) {
            foreach ($listads as $v) {
                $tmp        = array();
                $formatTime = Common::formatDate($v['created_at']);
                $tmp[]      = array(
                    'cdnimgs'    => array($v['img']),
                    'appId'      => $columnId,
                    'detailId'   => $v['id'],
                    //'outId'      => $val['out_id'],
                    'title'      => $v['title'],
                    'postTime'   => $v['created_at'],
                    'formatTime' => $formatTime,
                    //'skip'       => false,
                    'url'        => $v['url'],
                );
            }
        }
        return $tmp;
    }

    private function _statDetail($columnId, $act) {
        $val = '';
        if ($act == 'list') {
            $val = 'detail_list_' . $columnId;
        } else if ($act == 'rec') {
            $val = 'detail_page_' . $columnId;
        } else if ($act == 'card') {
            $val = 'detail_card_' . $columnId;
        }
        if (!empty($val)) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $val . ':nav_fun');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $val . ':nav_fun');
        }

        $val = 'detail_' . $columnId;
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $val . ':nav_fun');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $val . ':nav_fun');
    }

    public function toAction() {
        $args = array(
            'column_id' => FILTER_VALIDATE_INT,
            'url'       => FILTER_SANITIZE_STRING,
            'crc'       => FILTER_VALIDATE_INT,
        );

        $params    = filter_input_array(INPUT_GET, $args);
        $column_id = $params['column_id'];
        $url       = urldecode($params['url']);

        if (crc32($column_id . $url) == $params['crc']) {
            $this->_statUrl($column_id);
            Common::redirect($url);
        }
        exit;
    }

    private function _statUrl($column_id) {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, 'to_' . $column_id . ':nav_fun');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, 'to_' . $column_id . ':nav_fun');
    }
}