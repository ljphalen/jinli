<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 聚合新闻
 */
class NavadController extends Admin_BaseController {

    public $actions = array(
        'listnewsUrl'     => '/Admin/Navad/listnews',
        'editnewsUrl'     => '/Admin/Navad/editnews',
        'listfunUrl'     => '/Admin/Navad/listfun',
        'editfunUrl'     => '/Admin/Navad/editfun',
        'listpicUrl'     => '/Admin/Navad/listpic',
        'editpicUrl'     => '/Admin/Navad/editpic',
        'delUrl'          => '/Admin/Navad/del',
        'listcalendarUrl' => '/Admin/Navad/listcalendar',
        'editcalendarUrl' => '/Admin/Navad/editcalendar',
        'listweatherUrl'  => '/Admin/Navad/listweather',
        'editweatherUrl'  => '/Admin/Navad/editweather',

    );

    public $perpage = 20;


    public function listnewsAction() {

        $get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        $posArr = Nav_Service_NewsAd::getNewsPos();
        
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
            $order          = !empty($get['order']) ? $get['order'] : 'asc';
            $orderBy[$sort] = $order;

            $where = array();

            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }

            if (empty($where['pos'])) {
                $where['pos'] = array('IN', array_keys($posArr));
            }

            $total   = Nav_Service_AdDB::getListDao()->count($where);
            $start   = (max($page, 1) - 1) * $offset;
            $list    = Nav_Service_AdDB::getListDao()->getList($start, $offset, $where, $orderBy);
            $imgPath = Common::getImgPath();
            foreach ($list as $k => $v) {
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
                $list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
                $list[$k]['status']     = Common::$status[$v['status']];
                $list[$k]['pos']        = $posArr[$v['pos']];
                $list[$k]['img']        = $v['img'] ? $imgPath . $v['img'] : '';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
        $this->assign('posArr', $posArr);
    }

    public function editnewsAction() {
        $now        = time();
        $id         = $this->getInput('id');
        $pD         = $this->getPost(array(
            'id',
            'sort',
            'title',
            'pos',
            'link',
            'img',
            'start_time',
            'end_time',
            'status',
            'parter_id',
            'cp_id',
            'bid'
        ));
        $pD['link'] = Gionee_Service_ParterUrl::getLink($pD['parter_id'], $pD['cp_id'], $pD['bid'], $pD['link']);
        if (!empty($pD['title'])) {
            $imgInfo = Common::upload('img', 'ad');
            if (!empty($imgInfo['data'])) {
                $pD['img'] = $imgInfo['data'];
            }

            if (empty($pD['title'])) {
                $this->output(-1, '输入名称');
            }

            if (empty($pD['link'])) {
                $this->output(-1, '输入链接');
            }

            unset($pD['parter_id'], $pD['bid']);

            $pD['start_time'] = strtotime($pD['start_time']);
            $pD['end_time']   = strtotime($pD['end_time']);

            if (empty($pD['id'])) {
                if (empty($pD['img'])) {
                    //$this->output(-1, '输入图片');
                }
                Admin_Service_Access::pass('add');
                $pD['created_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->insert($pD);
            } else {
                if (empty($pD['img'])) {
                    unset($pD['img']);
                }
                Admin_Service_Access::pass('edit');
                $pD['updated_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->update($pD, $pD['id']);
                if (!empty($pD['pos'])) {
                    Nav_Service_NewsAd::getListByPos($pD['pos'], true);
                }
            }

            Admin_Service_Log::op($pD);
            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');

        }

        $info = Nav_Service_AdDB::getListDao()->get(intval($id));

        if (intval($id)) {
            $urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
            $this->assign('urlInfo', $urlInfo);
            $blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
            $this->assign('blist', $blist);
            $urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
            $this->assign('urlList', $urlList);
        }
        $cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
        array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
        $this->assign('info', $info);
        $this->assign('cooperators', $cooperators);

        $posArr = Nav_Service_NewsAd::getNewsPos();
        $this->assign('posArr', $posArr);
    }


    public function listfunAction() {

        $get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        $posArr = Nav_Service_NewsAd::getFunPos();
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
            $order          = !empty($get['order']) ? $get['order'] : 'asc';
            $orderBy[$sort] = $order;

            $where = array();

            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }

            if (empty($where['pos'])) {
                $where['pos'] = array('IN', array_keys($posArr));
            }

            $total   = Nav_Service_AdDB::getListDao()->count($where);
            $start   = (max($page, 1) - 1) * $offset;
            $list    = Nav_Service_AdDB::getListDao()->getList($start, $offset, $where, $orderBy);
            $imgPath = Common::getImgPath();
            foreach ($list as $k => $v) {
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
                $list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
                $list[$k]['status']     = Common::$status[$v['status']];
                $list[$k]['pos']        = $posArr[$v['pos']];
                $list[$k]['img']        = $v['img'] ? $imgPath . $v['img'] : '';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
        $this->assign('posArr', $posArr);
    }

    public function editfunAction() {
        $now        = time();
        $id         = $this->getInput('id');
        $pD         = $this->getPost(array(
            'id',
            'sort',
            'title',
            'pos',
            'link',
            'img',
            'start_time',
            'end_time',
            'status',
            'parter_id',
            'cp_id',
            'bid'
        ));
        $pD['link'] = Gionee_Service_ParterUrl::getLink($pD['parter_id'], $pD['cp_id'], $pD['bid'], $pD['link']);
        if (!empty($pD['title'])) {
            $imgInfo = Common::upload('img', 'ad');
            if (!empty($imgInfo['data'])) {
                $pD['img'] = $imgInfo['data'];
            }

            if (empty($pD['title'])) {
                $this->output(-1, '输入名称');
            }

            if (empty($pD['link'])) {
                $this->output(-1, '输入链接');
            }

            unset($pD['parter_id'], $pD['bid']);

            $pD['start_time'] = strtotime($pD['start_time']);
            $pD['end_time']   = strtotime($pD['end_time']);

            if (empty($pD['id'])) {
                if (empty($pD['img'])) {
                    //$this->output(-1, '输入图片');
                }
                Admin_Service_Access::pass('add');
                $pD['created_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->insert($pD);
            } else {
                if (empty($pD['img'])) {
                    unset($pD['img']);
                }
                Admin_Service_Access::pass('edit');
                $pD['updated_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->update($pD, $pD['id']);
                if (!empty($pD['pos'])) {
                    Nav_Service_NewsAd::getListByPos($pD['pos'], true);
                }
            }

            Admin_Service_Log::op($pD);
            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');

        }

        $info = Nav_Service_AdDB::getListDao()->get(intval($id));

        if (intval($id)) {
            $urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
            $this->assign('urlInfo', $urlInfo);
            $blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
            $this->assign('blist', $blist);
            $urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
            $this->assign('urlList', $urlList);
        }
        $cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
        array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
        $this->assign('info', $info);
        $this->assign('cooperators', $cooperators);

        $posArr = Nav_Service_NewsAd::getFunPos();
        $this->assign('posArr', $posArr);
    }



    public function listpicAction() {

        $get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        $posArr = Nav_Service_NewsAd::getPicPos();
        
 
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
            $order          = !empty($get['order']) ? $get['order'] : 'asc';
            $orderBy[$sort] = $order;

            $where = array();

            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }

            if (empty($where['pos'])) {
                $where['pos'] = array('IN', array_keys($posArr));
            }

            $total   = Nav_Service_AdDB::getListDao()->count($where);
            $start   = (max($page, 1) - 1) * $offset;
            $list    = Nav_Service_AdDB::getListDao()->getList($start, $offset, $where, $orderBy);
            $imgPath = Common::getImgPath();
            foreach ($list as $k => $v) {
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
                $list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
                $list[$k]['status']     = Common::$status[$v['status']];
                $list[$k]['pos']        = $posArr[$v['pos']];
                $list[$k]['img']        = $v['img'] ? $imgPath . $v['img'] : '';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
        $this->assign('posArr', $posArr);
    }

    public function editpicAction() {
        $now        = time();
        $id         = $this->getInput('id');
        $pD         = $this->getPost(array(
            'id',
            'sort',
            'title',
            'pos',
            'link',
            'img',
            'start_time',
            'end_time',
            'status',
            'parter_id',
            'cp_id',
            'bid'
        ));
        $pD['link'] = Gionee_Service_ParterUrl::getLink($pD['parter_id'], $pD['cp_id'], $pD['bid'], $pD['link']);
        if (!empty($pD['title'])) {
            $imgInfo = Common::upload('img', 'ad');
            if (!empty($imgInfo['data'])) {
                $pD['img'] = $imgInfo['data'];
            }

            if (empty($pD['title'])) {
                $this->output(-1, '输入名称');
            }

            if (empty($pD['link'])) {
                $this->output(-1, '输入链接');
            }

            unset($pD['parter_id'], $pD['bid']);

            $pD['start_time'] = strtotime($pD['start_time']);
            $pD['end_time']   = strtotime($pD['end_time']);

            if (empty($pD['id'])) {
                if (empty($pD['img'])) {
                    //$this->output(-1, '输入图片');
                }
                Admin_Service_Access::pass('add');
                $pD['created_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->insert($pD);
            } else {
                if (empty($pD['img'])) {
                    unset($pD['img']);
                }
                Admin_Service_Access::pass('edit');
                $pD['updated_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->update($pD, $pD['id']);
                if (!empty($pD['pos'])) {
                    Nav_Service_NewsAd::getListByPos($pD['pos'], true);
                }
            }

            Admin_Service_Log::op($pD);
            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');

        }

        $info = Nav_Service_AdDB::getListDao()->get(intval($id));

        if (intval($id)) {
            $urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
            $this->assign('urlInfo', $urlInfo);
            $blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
            $this->assign('blist', $blist);
            $urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
            $this->assign('urlList', $urlList);
        }
        $cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
        array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
        $this->assign('info', $info);
        $this->assign('cooperators', $cooperators);

        $posArr = Nav_Service_NewsAd::getPicPos();
        $this->assign('posArr', $posArr);
    }


    public function listcalendarAction() {

        $get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        $posArr = Partner_Service_HistoryToday::$pos;
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
            $order          = !empty($get['order']) ? $get['order'] : 'asc';
            $orderBy[$sort] = $order;

            $where = array();

            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }

            if (empty($where['pos'])) {
                $where['pos'] = array('IN', array_keys($posArr));
            }

            $total   = Nav_Service_AdDB::getListDao()->count($where);
            $start   = (max($page, 1) - 1) * $offset;
            $list    = Nav_Service_AdDB::getListDao()->getList($start, $offset, $where, $orderBy);
            $imgPath = Common::getImgPath();
            foreach ($list as $k => $v) {
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
                $list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
                $list[$k]['status']     = Common::$status[$v['status']];
                $list[$k]['pos']        = $posArr[$v['pos']];
                $list[$k]['img']        = $v['img'] ? $imgPath . $v['img'] : '';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
        $this->assign('posArr', $posArr);
    }

    public function editcalendarAction() {
        $now        = time();
        $id         = $this->getInput('id');
        $pD         = $this->getPost(array(
            'id',
            'sort',
            'title',
            'pos',
            'link',
            'img',
            'start_time',
            'end_time',
            'status',
            'parter_id',
            'cp_id',
            'bid'
        ));
        $pD['link'] = Gionee_Service_ParterUrl::getLink($pD['parter_id'], $pD['cp_id'], $pD['bid'], $pD['link']);
        if (!empty($pD['title'])) {
            $imgInfo = Common::upload('img', 'ad');
            if (!empty($imgInfo['data'])) {
                $pD['img'] = $imgInfo['data'];
            }

            if (empty($pD['title'])) {
                $this->output(-1, '输入名称');
            }

            if (empty($pD['link'])) {
                $this->output(-1, '输入链接');
            }

            unset($pD['parter_id'], $pD['bid']);

            $pD['start_time'] = strtotime($pD['start_time']);
            $pD['end_time']   = strtotime($pD['end_time']);

            if (empty($pD['id'])) {
                if (empty($pD['img'])) {
                    $this->output(-1, '输入图片');
                }
                Admin_Service_Access::pass('add');
                $pD['created_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->insert($pD);
            } else {
                if (empty($pD['img'])) {
                    unset($pD['img']);
                }
                Admin_Service_Access::pass('edit');
                $pD['updated_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->update($pD, $pD['id']);

                if (!empty($pD['pos'])) {
                    Nav_Service_NewsAd::getListByPos($pD['pos'], true);
                }
            }

            Admin_Service_Log::op($pD);
            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');

        }

        $info = Nav_Service_AdDB::getListDao()->get(intval($id));

        if (intval($id)) {
            $urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
            $this->assign('urlInfo', $urlInfo);
            $blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
            $this->assign('blist', $blist);
            $urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
            $this->assign('urlList', $urlList);
        }
        $cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
        array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
        $this->assign('info', $info);
        $this->assign('cooperators', $cooperators);

        $posArr = Partner_Service_HistoryToday::$pos;
        $this->assign('posArr', $posArr);
    }


    public function listweatherAction() {

        $get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order', 'export'));
        $posArr = Partner_Service_Weather::$pos;
        if (!empty($get['togrid'])) {
            $page           = max(intval($get['page']), 1);
            $offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
            $sort           = !empty($get['sort']) ? $get['sort'] : 'sort';
            $order          = !empty($get['order']) ? $get['order'] : 'asc';
            $orderBy[$sort] = $order;

            $where = array();

            foreach ($_POST['filter'] as $k => $v) {
                if (strlen($v) > 0) {
                    $where[$k] = $v;
                }
            }

            if (empty($where['pos'])) {
                $where['pos'] = array('IN', array_keys($posArr));
            }

            $total   = Nav_Service_AdDB::getListDao()->count($where);
            $start   = (max($page, 1) - 1) * $offset;
            $list    = Nav_Service_AdDB::getListDao()->getList($start, $offset, $where, $orderBy);
            $imgPath = Common::getImgPath();
            foreach ($list as $k => $v) {
                $list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
                $list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
                $list[$k]['start_time'] = date('y/m/d H:i', $v['start_time']);
                $list[$k]['end_time']   = date('y/m/d H:i', $v['end_time']);
                $list[$k]['status']     = Common::$status[$v['status']];
                $list[$k]['pos']        = $posArr[$v['pos']];
                $list[$k]['img']        = $v['img'] ? $imgPath . $v['img'] : '';
            }

            $ret = array(
                'total' => $total,
                'rows'  => $list,
            );
            echo Common::jsonEncode($ret);
            exit;
        }
        $this->assign('posArr', $posArr);
    }

    public function editweatherAction() {
        $now        = time();
        $id         = $this->getInput('id');
        $pD         = $this->getPost(array(
            'id',
            'sort',
            'title',
            'pos',
            'link',
            'img',
            'start_time',
            'end_time',
            'status',
            'parter_id',
            'cp_id',
            'bid'
        ));
        $pD['link'] = Gionee_Service_ParterUrl::getLink($pD['parter_id'], $pD['cp_id'], $pD['bid'], $pD['link']);
        if (!empty($pD['title'])) {
            $imgInfo = Common::upload('img', 'ad');
            if (!empty($imgInfo['data'])) {
                $pD['img'] = $imgInfo['data'];
            }

            if (empty($pD['title'])) {
                $this->output(-1, '输入名称');
            }

            if (empty($pD['link'])) {
                $this->output(-1, '输入链接');
            }

            unset($pD['parter_id'], $pD['bid']);

            $pD['start_time'] = strtotime($pD['start_time']);
            $pD['end_time']   = strtotime($pD['end_time']);

            if (empty($pD['id'])) {
                if (empty($pD['img'])) {
                    $this->output(-1, '输入图片');
                }
                Admin_Service_Access::pass('add');
                $pD['created_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->insert($pD);
            } else {
                if (empty($pD['img'])) {
                    unset($pD['img']);
                }
                Admin_Service_Access::pass('edit');
                $pD['updated_at'] = $now;
                $ret              = Nav_Service_AdDB::getListDao()->update($pD, $pD['id']);
                if (!empty($pD['pos'])) {
                    Nav_Service_NewsAd::getListByPos($pD['pos'], true);
                }
            }

            Admin_Service_Log::op($pD);
            if (!$ret) $this->output(-1, '操作失败');
            $this->output(0, '操作成功.');

        }

        $info = Nav_Service_AdDB::getListDao()->get(intval($id));

        if (intval($id)) {
            $urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
            $this->assign('urlInfo', $urlInfo);
            $blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
            $this->assign('blist', $blist);
            $urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
            $this->assign('urlList', $urlList);
        }
        $cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
        array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
        $this->assign('info', $info);
        $this->assign('cooperators', $cooperators);

        $posArr = Partner_Service_Weather::$pos;
        $this->assign('posArr', $posArr);
    }

    public function delAction() {
        $idArr = (array)$this->getInput('id');
        $i     = 0;
        $succ  = array();
        foreach ($idArr as $id) {
            $ret = Nav_Service_AdDB::getListDao()->delete($id);
            if ($ret) {
                $i++;
                $succ[] = $id;
            }
        }

        Admin_Service_Log::op($succ);
        if ($i == count($succ)) {
            $this->output(0, '操作成功');
        } else {
            $this->output(-1, '操作失败', $succ);
        }
    }

    public function statAction() {
        $type = array(
            'news'     => '今日头条',
            'weather'  => '天气',
            'calender' => '日历',
            'fun'      => '段子',
            'pic'      => '美图',
        );

        $pos['news']     = Nav_Service_NewsAd::getNewsPos();
        $pos['weather']  = Partner_Service_Weather::$pos;
        $pos['calender'] = Partner_Service_HistoryToday::$pos;
        $pos['fun']      = Nav_Service_NewsAd::getFunPos();
        $pos['pic']      = Nav_Service_NewsAd::getPicPos();


        $postData = $this->getInput(array('sdate', 'edate', 'page', 'type', 'pos', 'search_type', 'export'));
        !$postData['sdate'] && $postData['sdate'] = date('Y-m-d', strtotime('-8 day'));
        !$postData['edate'] && $postData['edate'] = date('Y-m-d', strtotime('now'));
        $sdate    = date('Ymd', strtotime($postData['sdate']));
        $edate    = date('Ymd', strtotime($postData['edate']));
        $dateList = $ret = array();
        for ($i = strtotime($sdate); $i <= strtotime($edate); $i += 86400) {
            $dateList[] = date('Ymd', $i);
        }

        $vers   = array();
        $tmpAds = Nav_Service_AdDB::getListDao()->getsBy(array('pos' => $postData['pos']), array('sort' => 'asc'));
        foreach ($tmpAds as $v) {
            $vers[$v['id']] = $v['title'];
        }

        $searchType = array(
            'pv' => 'PV',
            'uv' => 'UV',
        );

        $where = array(
            'ver'   => 'ad_pos',
            'stime' => strtotime($postData['sdate']),
            'etime' => strtotime($postData['edate']),
            'key'   => array_keys($vers),
            'type'  => $postData['search_type'] == 'pv' ? 'pv' : 'uv',
        );


        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']] = $val['val'];
        }

        if ($postData['export']) {
            $_data = array(
                'dateList' => $dateList,
                'list'     => $ret,
                'vers'     => $vers,
            );
            $this->_exportTotal($_data, "端午统计数据", $sdate, $edate);
            exit();
        }

        $this->assign('type', $type);
        $this->assign('vers', $vers);
        $this->assign('dateList', $dateList);
        $this->assign('list', $ret);
        $this->assign('pos', $pos);
        $this->assign('params', $postData);
        $this->assign('searchType', $searchType);

    }
}