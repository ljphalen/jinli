<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 统计点击量
 * @author tiansh
 *
 */
class CrController extends Front_BaseController {

    public $actions = array(
        'listUrl' => 'Cr/index',
    );

    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $url = trim($this->getInput('url'));
        $tid = trim($this->getInput('tid'));
        //统计点击量
        if ($url) {

            //v1.06首页点击量统计
            if ($tid) {
                $clicktype = Theme_Service_ClickType::getClicktype($tid);
                if ($clicktype) {
                    $ret = Common::getCache()->getBy('Theme_click');

                    $key = $clicktype['id'];
                    if (!is_array($ret)) {
                        $ret = array();
                    }

                    if (isset($ret[$key])) {
                        $ret[$key]['click'] += 1;
                        $ret[$key]['type_id'] = $clicktype['id'];
                    } else {
                        $ret[$key] = array('click' => 1, 'type_id' => $clicktype['id']);
                    }
                    Common::getCache()->set('Theme_click', $ret);
                }
            } else {
                $category = Theme_Service_Crcategory::getDataByUrl(md5($url));
                if ($category) {
                    $ret = Common::getCache()->get('Theme_cr');

                    $key = md5($url);

                    if (!is_array($ret)) $ret = array();
                    if (isset($ret[$key])) {
                        $ret[$key]['click'] += 1;
                        $ret[$key]['category_id'] = $category['id'];
                    } else {
                        $ret[$key] = array('url' => $url, 'click' => 1, 'category_id' => $category['id']);
                    }
                    Common::getCache()->set('Theme_cr', $ret);
                }
            }
        }
        $this->redirect($url);
    }

}
