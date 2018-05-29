<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class RegisterController extends Admin_BaseController {

    public $pageSize = 20;

    public $actions = array(
        'indexUrl'    => '/Admin/Register/index',
        'editPostUrl' => '/Admin/Register/editpost',
    );

    public $types = array(
        '0' => '否',
        '1' => '是'
    );

    public function indexAction() {
        $postData = $this->getInput(array('is_frozed', 'is_black_user', 'mobile', 'export', 'nickname'));
        $page     = $this->getInput('page');
        $page     = max($page, 1);
        $where    = array();
        if (intval($postData['is_frozed'])) {
            $where['is_frozed'] = $postData['is_frozed'];
        }
        if (intval($postData['is_black_user'])) {
            $where['is_black_user'] = $postData['is_black_user'];
        }

        if (isset($postData['mobile'])) {
            $where['username'] = array('LIKE', "%{$postData['mobile']}%");
        }

        if (isset($postData['nickname'])) {
            $where['nickname'] = array('LIKE', "%{$postData['nickname']}%");
        }

        $pageSize = $this->pageSize;
        if (intval($postData['export'])) {
            $pageSize = Gionee_Service_User::count($where);
        }
        list($total, $dataList) = Gionee_Service_User::getList($page, $pageSize, $where, array('id' => 'DESC'));
        foreach ($dataList as $k => $v) {
            $userScore                      = User_Service_Gather::getBy(array('uid' => $v['id']));
            $dataList[$k]['total_scores']   = $userScore['total_score'];
            $dataList[$k]['remained_score'] = $userScore['remained_score'];
        }
        $this->assign('data', $dataList);
        $this->assign('params', $postData);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?" . http_build_query($postData) . "&"));
    }

    public function editAction() {
        $id   = $this->getInput('id');
        $data = Gionee_Service_User::getUser($id);
        $this->assign('data', $data);
        $this->assign('types', $this->types);

        $visitList = User_Service_UserVisit::getDao()->getsBy(array('uid' => $id));
        $this->assign('visitList', $visitList);
    }

    public function editPostAction() {
        $postData = $this->getInput(array('is_black_user', 'is_frozed', 'id'));
        $user     = Gionee_Service_User::getUser($postData['id']);
        if ($user['is_frozed'] != $postData['is_frozed'] || $user['is_black_user'] != $postData['is_black_user']) {
            $ret = Gionee_Service_User::updateUser($postData, $postData['id']);
            if ($user['is_frozed'] != $postData['is_frozed']) {
                $ret = Common_Service_User::sendInnerMsg(array(
                    'uid'      => $user['id'],
                    'classify' => '6',
                    'status'   => $postData['is_frozed']
                ), 'scores_frozed_tpl');
            }
            $uKey = "USER:INFO:KEY" . $user['id']; //清掉用户信息缓存
            Common::getCache()->delete($uKey);
        }
        $this->output('0', '操作成功');
    }

    public function ajaxResetScoreAction() {
        $id        = $this->getInput('id');
        $scoreInfo = User_Service_Gather::getBy(array('uid' => $id));
        $res       = User_Service_Gather::update(array('remained_score' => 0), $scoreInfo['id']);
        if ($res) {
            $uKey = "USER:INFO:KEY" . $id; //清掉用户信息缓存
            Common::getCache()->delete($uKey);
            Common_Service_User::sendInnerMsg(array(
                'uid'      => $id,
                'classify' => '7',
                'status'   => 1
            ), 'scores_cleanup_tpl');
        }
        $this->output('0', '操作成功');
    }

    private function _export($data, $title) {
        ini_set('memory_limit', '1024M');
        header('Content-Type: application/vnd.ms-excel;charset=GB2312');
        $filename = empty($filename) ? '统计报表' : $filename;
        $filename .= '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
        $out = fopen('php://output', 'w');
        fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($out, array('用户ID', '手机号', '总金币数', '剩余金币数', '冻结金币数'));
        foreach ($data as $k => $v) {
            fputcsv($out, array(
                $v['uid'],
                $v['username'],
                $v['total_score'],
                $v['remained_score'],
                $v['frozed_score']
            ));
        }
    }
}