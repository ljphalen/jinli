<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 浏览器接口
 *
 */
class BrowserController extends Front_BaseController {

    public function localnavAction() {
        $id = $this->getInput('id');
        $id = intval($id);
        if ($id <= 0) {
            exit;
        }

        $apcKey = 'Browser_localnav:' . $id;
        $data   = Cache_APC::get($apcKey);
        $data = '';
        if (empty($data)) {
            $data = Gionee_Service_LocalNavList::getData($id,true);
            Cache_APC::set($apcKey, $data,10);
        }
      //  $this->output('0','',$data);
        $this->assign('title', $data['info']['name']);
        $this->assign('list', $data['list']);
        $this->assign('initData', $data['initJSData']);
        $this->assign('id', $id);
    }


    public function overseasAction() {
        //统计导航pv
        Gionee_Service_Log::pvLog('3g_nav');
        //统计导航UV
        $t_bi = $this->getSource();
        Gionee_Service_Log::uvLog('3g_nav', $t_bi);

        $pageData = Gionee_Service_Ng::getIndexData();

        $this->assign('pageData', $pageData['content']);

        $words = Gionee_Service_Baidu::getNavIndexWrods();
        $this->assign('baidu_hotword', $words);
        //百度渠道号
        $baidu_num = Gionee_Service_Baidu::getFromNo();
        $this->assign('baidu_num', $baidu_num);
    }

    public function globalnavAction() {
        $id         = $this->getInput('id');
        $info       = Gionee_Service_LocalNavType::get($id);
        $ret        = array();
        $orderBy    = array('sort' => 'ASC', 'id' => 'desc');
        $columnList = Gionee_Service_LocalNavColumn::getsBy(array('type_id' => $info['id']), $orderBy);
        foreach ($columnList as $val) {
            if (isset(Gionee_Service_LocalNavList::$limitOutArr[$val['id']])) {
                $tmpList = Gionee_Service_LocalNavList::getOutData($val['id']);
                list($limit, $pageSize) = Gionee_Service_LocalNavList::$limitOutArr[$val['id']];
                if ($val['id'] == 102) {
                    //后台人工添加数据从这2个数组中合并
                    $arr = array();
                    foreach ($tmpList as $v) {
                        $arr[$v['t']][] = $v;
                    }
                    $imgList = $arr['img'];
                    $txtList = array_slice($arr['txt'], 0, $pageSize);
                    $list    = array('img' => $imgList, 'txt' => $txtList);
                } else {
                    $list = array_slice($tmpList, 0, $pageSize);
                }
            } else {
                $list = Gionee_Service_LocalNavList::getCommonData($val['id']);
            }
            if (!empty($list)) {
                $ret[$val['id']] = array(
                    'id'    => $val['id'],
                    'style' => $val['style'],
                    'stat'  => array('type' => $info['name'], 'column' => $val['name']),
                    'list'  => $list
                );
            }
        }
        $this->assign('title', $info['name']);
        $this->assign('list', $ret);
        $this->assign('id', $id);
    }

    public function moreAction() {
        $id      = $this->getInput('id');
        $pos     = $this->getInput('pos');
        $now     = time();
        $info    = Gionee_Service_LocalNavColumn::getInfo($id);
        $tmpList = Gionee_Service_LocalNavList::getOutData($id);
        list($limit, $pageSize) = Gionee_Service_LocalNavList::$limitOutArr[$id];
        if ($id == 102) {//去掉第一个图片默认数据
            $arr = array();
            foreach ($tmpList as $v) {
                $arr[$v['t']][] = $v;
            }
            $tmpList = $arr['txt'];
        }
        $outData = array_chunk($tmpList, $pageSize);
        $maxPos  = count($outData);
        $o       = floor($pos % $maxPos);
        $list    = array();
        foreach ($outData[$o] as $val) {
            $tmp = array(
                'id'    => $val['id'],
                'link'  => $val['link'],
                'title' => $val['name'],
            );

            if ($info['style'] == 'fun_text') {
                unset($tmp['link']);
                unset($tmp['title']);
                $tmp['text'] = $val['ext'];
            }

            if (!empty($val['img'])) {
                $tmp['img'] = $val['img'];
            }

            $list[] = $tmp;
        }

        $ret = array(
            'timestamp' => $now,
            'list'      => $list,
        );
        $this->output(0, '', $ret);
    }


    public function moreallAction() {
        $id  = $this->getInput('id');
        $id  = intval($id);
        $ver = $this->getInput('ver');

        $apcKey = 'Browser_moreall:' . $id;
        $ret    = Cache_APC::get($apcKey);

        if (empty($ret['timestamp'])) {
            $info    = Gionee_Service_LocalNavColumn::getInfo($id);
            $tmpList = Gionee_Service_LocalNavList::getOutData($id);
            $dataVer = crc32(Common::jsonEncode($tmpList));
            $list    = array();
            $imgs    = array();

            if ($id == 102) {//去掉第一个图片默认数据
                $arr = array();
                foreach ($tmpList as $v) {
                    $arr[$v['t']][] = $v;
                }

                foreach ($arr['img'] as $val) {
                    $imgs[] = array(
                        'id'    => intval($val['id']),
                        'link'  => $val['link'],
                        'title' => $val['name'],
                        'img'   => $val['img'],
                    );
                }

                $tmpList = $arr['txt'];
            }

            foreach ($tmpList as $val) {
                $tmp = array(
                    'id'    => intval($val['id']),
                    'link'  => $val['link'],
                    'title' => $val['name'],
                );

                if ($info['style'] == 'fun_text') {
                    unset($tmp['link']);
                    unset($tmp['title']);
                    $tmp['text'] = $val['ext'];
                }

                if (!empty($val['img'])) {
                    $tmp['img'] = $val['img'];
                }

                $list[] = $tmp;
            }


            $ret = array(
                'timestamp' => $dataVer,
                'img'       => $imgs,
                'list'      => $list,
            );

            Cache_APC::set($apcKey, $ret);

        }

        if ($ret['timestamp'] == $ver) {
            $ret = array(
                'timestamp' => $ret['timestamp'],
                'img'       => array(),
                'list'      => array(),
            );
        }

        $this->output(0, '', $ret);
    }


    public function appcAction() {
        $cn = 'Front_Browser_localnav';
        Yaf_Dispatcher::getInstance()->disableView();
        header('Content-Type: text/cache-manifest');
        echo "CACHE MANIFEST\n";
        $v = Gionee_Service_Config::getValue('APPC_' . $cn);
        echo "\n#version:" . $v . "\n\n";
        $caches = Common::getConfig('cacheConfig', $cn);
        foreach ($caches as $key => $value) {
            echo sprintf("\n\n%s:\n", $key);
            echo implode("\n", $value);
        }
    }

    public function loginAction() {
        $act = $this->getInput('act');
        if ($act == 'redirect') {
            $login = Common_Service_User::checkLogin('/user/index/index');        //检测登陆状态
            if (!$login['key']) {
                Common::redirect($login['keyMain']);
            }
            $userInfo = $login['keyMain'];
        } else {
            $userInfo = Gionee_Service_User::getCurUserInfo();
        }

        if (!empty($userInfo['id'])) {
            $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);
            //$userScoreInfo          = Common_Service_User::getUserScore($userInfo['id']);
            $userInfo['score'] = $userScoreInfo['remained_score'];
        }
        $this->assign('userInfo', $userInfo);
    }

}